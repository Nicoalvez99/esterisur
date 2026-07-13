<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Control;
use App\Models\Equipo;
use App\Models\Esterilizacion;
use App\Models\HistorialLote;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EsterilizacionController extends Controller
{
    /**
     * Listado de lotes listos para esterilizar + ciclos del día.
     */
    public function index()
    {
        // Lotes esperando ir a vapor o ETO
        $lotesVapor = Lote::with(['recepcion.institucion'])
            ->where('estado_actual', 'vapor')
            ->orderByRaw("FIELD(prioridad, 'critica', 'urgente', 'normal')")
            ->get();

        $lotesEto = Lote::with(['recepcion.institucion'])
            ->where('estado_actual', 'eto')
            ->orderByRaw("FIELD(prioridad, 'critica', 'urgente', 'normal')")
            ->get();

        // Ciclos del día
        $ciclosHoy = Esterilizacion::with(['equipo', 'operario', 'lotes', 'controles'])
            ->whereDate('fecha_inicio', today())
            ->latest()
            ->get();

        $stats = [
            'pendientes_vapor' => $lotesVapor->count(),
            'pendientes_eto'   => $lotesEto->count(),
            'ciclos_hoy'       => $ciclosHoy->count(),
            'en_aireacion'     => Lote::where('estado_actual', 'eto')
                //->whereHas('esterilizacion', fn($q) => $q->whereNotNull('aireacion_inicio')->whereNull('aireacion_fin'))
                ->count(),
        ];

        return view('esterilizacion.index', compact('lotesVapor', 'lotesEto', 'ciclosHoy', 'stats'));
    }

    /**
     * Formulario para registrar un nuevo ciclo.
     */
    public function create(Request $request)
    {
        $metodo = $request->get('metodo', 'vapor'); // vapor|eto

        $equipos = Equipo::where('metodo', $metodo)
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        // Lotes disponibles para este método
        $lotes = Lote::with(['recepcion.institucion'])
            ->where('estado_actual', $metodo)
            ->orderByRaw("FIELD(prioridad, 'critica', 'urgente', 'normal')")
            ->get();

        return view('esterilizacion.create', compact('metodo', 'equipos', 'lotes'));
    }

    /**
     * Guarda el ciclo con sus lotes y controles.
     */
    public function store(Request $request)
    {
        $metodo = $request->metodo;

        $rules = [
            'equipo_id'     => ['required', 'exists:equipos,id'],
            'metodo'        => ['required', 'in:vapor,eto'],
            'lote_ids'      => ['required', 'array', 'min:1'],
            'lote_ids.*'    => ['exists:lotes,id'],
            'fecha_inicio'  => ['required', 'date'],
            'fecha_fin'     => ['required', 'date', 'after:fecha_inicio'],
            'observaciones' => ['nullable', 'string', 'max:1000'],

            // Controles
            'controles'              => ['required', 'array'],
            'controles.*.tipo'       => ['required', 'in:fisico,quimico,biologico'],
            'controles.*.resultado'  => ['required', 'in:conforme,no_conforme,pendiente'],
            'controles.*.descripcion'=> ['nullable', 'string', 'max:150'],
        ];

        // Parámetros según método
        if ($metodo === 'vapor') {
            $rules['temperatura']    = ['required', 'numeric', 'min:100', 'max:200'];
            $rules['tiempo_minutos'] = ['required', 'integer', 'min:1'];
            $rules['presion']        = ['nullable', 'numeric', 'min:0'];
        } else {
            $rules['temperatura']     = ['required', 'numeric', 'min:30', 'max:80'];
            $rules['concentracion']   = ['required', 'numeric', 'min:0'];
            $rules['tiempo_minutos']  = ['required', 'integer', 'min:1'];
            $rules['aireacion_inicio']= ['required', 'date'];
        }

        $request->validate($rules, [
            'equipo_id.required'    => 'Seleccioná un equipo.',
            'lote_ids.required'     => 'Seleccioná al menos un lote.',
            'lote_ids.min'          => 'Debe haber al menos un lote en el ciclo.',
            'fecha_inicio.required' => 'Ingresá la hora de inicio del ciclo.',
            'fecha_fin.required'    => 'Ingresá la hora de fin del ciclo.',
            'fecha_fin.after'       => 'El fin debe ser posterior al inicio.',
            'temperatura.required'  => 'La temperatura es obligatoria.',
            'tiempo_minutos.required'=> 'El tiempo del ciclo es obligatorio.',
            'concentracion.required' => 'La concentración es obligatoria para ETO.',
            'aireacion_inicio.required' => 'El inicio de aireación es obligatorio para ETO.',
            'controles.required'    => 'Registrá al menos un control.',
        ]);

        DB::transaction(function () use ($request, $metodo) {

            // Crear el ciclo
            $esterilizacion = Esterilizacion::create([
                'equipo_id'        => $request->equipo_id,
                'metodo'           => $metodo,
                'operario_id'      => auth()->id(),
                'fecha_inicio'     => $request->fecha_inicio,
                'fecha_fin'        => $request->fecha_fin,
                'temperatura'      => $request->temperatura,
                'presion'          => $request->presion,
                'tiempo_minutos'   => $request->tiempo_minutos,
                'concentracion'    => $request->concentracion,
                'aireacion_inicio' => $request->aireacion_inicio ?? null,
                'aireacion_fin'    => null, // se completa después para ETO
                'resultado'        => 'pendiente',
                'observaciones'    => $request->observaciones,
            ]);

            // Asociar lotes al ciclo y actualizar su estado
            $nuevoEstado = 'control_calidad';

            foreach ($request->lote_ids as $loteId) {
                $esterilizacion->lotes()->attach($loteId, [
                    'observaciones' => null,
                ]);

                $lote = Lote::find($loteId);
                $lote->update([
                    'estado_actual'     => $nuevoEstado,
                    'usuario_actual_id' => auth()->id(),
                ]);

                HistorialLote::create([
                    'lote_id'        => $loteId,
                    'user_id'        => auth()->id(),
                    'accion'         => "Ciclo de esterilización {$metodo} registrado",
                    'estado_origen'  => $metodo,
                    'estado_destino' => $nuevoEstado,
                    'detalle'        => json_encode([
                        'esterilizacion_id' => $esterilizacion->id,
                        'equipo'            => $request->equipo_id,
                        'temperatura'       => $request->temperatura,
                        'tiempo_minutos'    => $request->tiempo_minutos,
                    ]),
                ]);
            }

            // Guardar controles
            $hayNoConforme = false;
            foreach ($request->controles as $ctrl) {
                Control::create([
                    'esterilizacion_id' => $esterilizacion->id,
                    'tipo'              => $ctrl['tipo'],
                    'resultado'         => $ctrl['resultado'],
                    'descripcion'       => $ctrl['descripcion'] ?? null,
                    'observaciones'     => $ctrl['observaciones'] ?? null,
                    'operario_id'       => auth()->id(),
                    'fecha_lectura'     => now(),
                ]);

                if ($ctrl['resultado'] === 'no_conforme') $hayNoConforme = true;
            }

            // Resultado general del ciclo
            $resultadoCiclo = $hayNoConforme ? 'no_conforme' : 'pendiente';
            // Si hay biológico pendiente → pendiente; si todos conformes → conforme
            $todosPendientes = collect($request->controles)->every(fn($c) => $c['resultado'] !== 'no_conforme');
            $todosConformes  = collect($request->controles)->every(fn($c) => $c['resultado'] === 'conforme');

            $esterilizacion->update([
                'resultado' => $hayNoConforme ? 'no_conforme' : ($todosConformes ? 'conforme' : 'pendiente'),
            ]);

            // Si hay controles no conformes → retener lotes
            if ($hayNoConforme) {
                foreach ($request->lote_ids as $loteId) {
                    $lote = Lote::find($loteId);
                    $lote->update(['estado_actual' => 'retenido']);

                    HistorialLote::create([
                        'lote_id'        => $loteId,
                        'user_id'        => auth()->id(),
                        'accion'         => 'Lote RETENIDO por control no conforme',
                        'estado_origen'  => 'control_calidad',
                        'estado_destino' => 'retenido',
                        'detalle'        => 'Control no conforme en ciclo #' . $esterilizacion->id,
                    ]);
                }
            }
        });

        return redirect()
            ->route('esterilizacion.index')
            ->with('success', 'Ciclo de esterilización registrado correctamente.');
    }

    /**
     * Detalle de un ciclo.
     */
    public function show(Esterilizacion $esterilizacion)
    {
        $esterilizacion->load(['equipo', 'operario', 'lotes.recepcion.institucion', 'controles.operario']);
        return view('esterilizacion.show', compact('esterilizacion'));
    }

    /**
     * Registrar fin de aireación ETO.
     */
    public function finAireacion(Request $request, Esterilizacion $esterilizacion)
    {
        $request->validate([
            'aireacion_fin' => ['required', 'date', 'after:' . $esterilizacion->aireacion_inicio],
        ], [
            'aireacion_fin.required' => 'Ingresá la hora de fin de aireación.',
            'aireacion_fin.after'    => 'El fin debe ser posterior al inicio de aireación.',
        ]);

        $esterilizacion->update(['aireacion_fin' => $request->aireacion_fin]);

        // Registrar en historial de cada lote del ciclo
        foreach ($esterilizacion->lotes as $lote) {
            HistorialLote::create([
                'lote_id'        => $lote->id,
                'user_id'        => auth()->id(),
                'accion'         => 'Aireación ETO finalizada',
                'estado_origen'  => $lote->estado_actual,
                'estado_destino' => $lote->estado_actual,
                'detalle'        => json_encode([
                    'aireacion_inicio' => $esterilizacion->aireacion_inicio,
                    'aireacion_fin'    => $request->aireacion_fin,
                    'horas'            => $esterilizacion->aireacionHoras(),
                ]),
            ]);
        }

        return redirect()
            ->route('esterilizacion.show', $esterilizacion->id)
            ->with('success', 'Aireación registrada correctamente.');
    }
}
