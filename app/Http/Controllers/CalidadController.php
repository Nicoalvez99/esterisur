<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Control;
use App\Models\Esterilizacion;
use App\Models\HistorialLote;
use App\Models\Liberacion;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalidadController extends Controller
{
    /**
     * Panel principal: lotes en control de calidad.
     */
    public function index()
    {
        $lotes = Lote::with(['recepcion.institucion', 'liberacion'])
            ->where('estado_actual', 'control_calidad')
            ->orderByRaw("FIELD(prioridad, 'critica', 'urgente', 'normal')")
            ->latest()
            ->paginate(15);

        $stats = [
            'pendientes'  => Lote::where('estado_actual', 'control_calidad')->count(),
            'liberados_hoy' => Liberacion::whereDate('fecha_liberacion', today())
                ->where('resultado', 'liberado')->count(),
            'retenidos'   => Lote::where('estado_actual', 'retenido')->count(),
            'rechazados'  => Lote::where('estado_actual', 'rechazado')->count(),
        ];

        return view('calidad.index', compact('lotes', 'stats'));
    }

    /**
     * Pantalla de revisión y liberación de un lote.
     */
    public function show(Lote $lote)
    {
        // Cargamos el ciclo de esterilización más reciente del lote
        $esterilizacion = Esterilizacion::whereHas('lotes', fn($q) => $q->where('lotes.id', $lote->id))
            ->with(['equipo', 'controles.operario', 'lotes'])
            ->latest()
            ->firstOrFail();

        $lote->load([
            'recepcion.institucion',
            'acondicionamiento',
            'liberacion.responsable',
            'historial.user',
        ]);

        // Verificaciones previas automáticas
        $controlesCompletos = $esterilizacion->controles->isNotEmpty()
            && $esterilizacion->controles->every(fn($c) => $c->resultado !== 'pendiente');

        $todosConformes = $esterilizacion->controles->isNotEmpty()
            && $esterilizacion->controles->every(fn($c) => $c->resultado === 'conforme');

        $hayNoConforme = $esterilizacion->controles->contains('resultado', 'no_conforme');
        $hayPendiente  = $esterilizacion->controles->contains('resultado', 'pendiente');

        // Post proceso
        $postProcesoOk = match($lote->metodo_esterilizacion) {
            'vapor' => true, // enfriamiento se asume si llegó a control_calidad
            'eto'   => $esterilizacion->aireacion_fin !== null,
            default => true,
        };

        return view('calidad.show', compact(
            'lote',
            'esterilizacion',
            'controlesCompletos',
            'todosConformes',
            'hayNoConforme',
            'hayPendiente',
            'postProcesoOk',
        ));
    }

    /**
     * Actualizar resultado de un control (biológico pendiente, etc).
     */
    public function actualizarControl(Request $request, Control $control)
    {
        $request->validate([
            'resultado'    => ['required', 'in:conforme,no_conforme'],
            'descripcion'  => ['nullable', 'string', 'max:150'],
            'observaciones'=> ['nullable', 'string', 'max:500'],
        ]);

        $control->update([
            'resultado'     => $request->resultado,
            'descripcion'   => $request->descripcion ?? $control->descripcion,
            'observaciones' => $request->observaciones,
            'fecha_lectura' => now(),
            'operario_id'   => auth()->id(),
        ]);

        // Actualizar resultado general del ciclo
        $esterilizacion = $control->esterilizacion;
        $controles      = $esterilizacion->controles()->get();

        $resultadoCiclo = 'pendiente';
        if ($controles->every(fn($c) => $c->resultado === 'conforme')) {
            $resultadoCiclo = 'conforme';
        } elseif ($controles->contains('resultado', 'no_conforme')) {
            $resultadoCiclo = 'no_conforme';
        }

        $esterilizacion->update(['resultado' => $resultadoCiclo]);

        // Registrar en historial del lote
        $loteId = $esterilizacion->lotes()->first()?->id;
        if ($loteId) {
            HistorialLote::create([
                'lote_id'        => $loteId,
                'user_id'        => auth()->id(),
                'accion'         => "Control {$control->tipo_label} actualizado: {$request->resultado}",
                'estado_origen'  => 'control_calidad',
                'estado_destino' => 'control_calidad',
                'detalle'        => json_encode([
                    'control_id' => $control->id,
                    'resultado'  => $request->resultado,
                ]),
            ]);
        }

        $lote = Lote::find($loteId);
        return redirect()
            ->route('calidad.show', $lote->id)
            ->with('success', 'Control actualizado correctamente.');
    }

    /**
     * Registrar la decisión de liberación.
     */
    public function liberar(Request $request, Lote $lote)
    {
        $request->validate([
            'esterilizacion_id'       => ['required', 'exists:esterilizacions,id'],
            'resultado'               => ['required', 'in:liberado,retenido,rechazado'],
            'controles_completos'     => ['required', 'boolean'],
            'post_proceso_ok'         => ['required', 'boolean'],
            'sin_incidencias_abiertas'=> ['required', 'boolean'],
            'observaciones'           => ['nullable', 'string', 'max:1000'],
        ], [
            'resultado.required' => 'Seleccioná una decisión.',
        ]);

        // Bloqueos de seguridad
        if ($request->resultado === 'liberado') {
            if (!$request->boolean('controles_completos')) {
                return back()->with('error', 'No se puede liberar: hay controles incompletos o no conformes.');
            }
            if (!$request->boolean('post_proceso_ok')) {
                return back()->with('error', 'No se puede liberar: el post-proceso (aireación/enfriamiento) no está completo.');
            }
        }

        DB::transaction(function () use ($request, $lote) {
            // Crear registro de liberación
            Liberacion::updateOrCreate(
                ['lote_id' => $lote->id],
                [
                    'esterilizacion_id'       => $request->esterilizacion_id,
                    'resultado'               => $request->resultado,
                    'controles_completos'     => $request->boolean('controles_completos'),
                    'post_proceso_ok'         => $request->boolean('post_proceso_ok'),
                    'sin_incidencias_abiertas'=> $request->boolean('sin_incidencias_abiertas'),
                    'responsable_id'          => auth()->id(),
                    'observaciones'           => $request->observaciones,
                    'fecha_liberacion'        => now(),
                ]
            );

            // Nuevo estado del lote
            $nuevoEstado = match($request->resultado) {
                'liberado'  => 'almacenamiento',
                'retenido'  => 'retenido',
                'rechazado' => 'rechazado',
            };

            $lote->update([
                'estado_actual'     => $nuevoEstado,
                'usuario_actual_id' => auth()->id(),
            ]);

            // Historial
            HistorialLote::create([
                'lote_id'        => $lote->id,
                'user_id'        => auth()->id(),
                'accion'         => match($request->resultado) {
                    'liberado'  => '✅ Lote LIBERADO por responsable de calidad',
                    'retenido'  => '⚠️ Lote RETENIDO por responsable de calidad',
                    'rechazado' => '❌ Lote RECHAZADO por responsable de calidad',
                },
                'estado_origen'  => 'control_calidad',
                'estado_destino' => $nuevoEstado,
                'detalle'        => json_encode([
                    'resultado'           => $request->resultado,
                    'controles_completos' => $request->boolean('controles_completos'),
                    'post_proceso_ok'     => $request->boolean('post_proceso_ok'),
                    'observaciones'       => $request->observaciones,
                ]),
            ]);
        });

        $mensaje = match($request->resultado) {
            'liberado'  => "Lote {$lote->numero_lote} liberado correctamente.",
            'retenido'  => "Lote {$lote->numero_lote} retenido. Requiere atención.",
            'rechazado' => "Lote {$lote->numero_lote} rechazado.",
        };

        return redirect()
            ->route('calidad.index')
            ->with('success', $mensaje);
    }
}
