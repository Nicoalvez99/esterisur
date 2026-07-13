<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use App\Models\Lote;
use App\Models\Recepcion;
use App\Models\HistorialLote;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class RecepcionController extends Controller
{
    /**
     * Listado de recepciones del día (o con filtros).
     */
    public function index(Request $request)
    {
        $query = Recepcion::with(['lote', 'institucion', 'operario'])
            ->latest();

        if ($request->filled('institucion_id')) {
            $query->where('institucion_id', $request->institucion_id);
        }

        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        } else {
            // Por defecto muestra el día de hoy
            $query->whereDate('created_at', today());
        }

        $recepciones   = $query->paginate(15)->withQueryString();
        $instituciones = Institucion::where('activo', true)->orderBy('nombre')->get();

        return view('recepcion', compact('recepciones', 'instituciones'));
    }

    /**
     * Formulario de nueva recepción.
     */
    public function create()
    {
        $instituciones = Institucion::where('activo', true)->orderBy('nombre')->get();
        return view('recepcion.create', compact('instituciones'));
    }

    /**
     * Guarda la recepción y crea el lote.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'institucion_id'       => ['required', 'exists:institucions,id'],
            'chofer_nombre'        => ['nullable', 'string', 'max:100'],
            'chofer_transporte'    => ['nullable', 'string', 'max:100'],
            'tiene_remito'         => ['required', 'boolean'],
            'remito_numero'        => ['required_if:tiene_remito,1', 'nullable', 'string', 'max:50'],
            'cant_cajas'           => ['required', 'integer', 'min:0'],
            'cant_bultos'          => ['required', 'integer', 'min:0'],
            'cant_unidades'        => ['required', 'integer', 'min:0'],
            'cant_equipos_ropa'    => ['required', 'integer', 'min:0'],
            'cant_litros'          => ['required', 'numeric', 'min:0'],
            'metodo'               => ['required', 'in:vapor,eto'],
            'estado_empaque'       => ['required', 'in:empaquetado,sin_empaquetar'],
            'fecha_entrega_pactada'=> ['required', 'date', 'after_or_equal:today'],
            'prioridad'            => ['required', 'in:normal,urgente,critica'],
            'observaciones'        => ['nullable', 'string', 'max:1000'],
        ], [
            'institucion_id.required'        => 'Seleccioná una institución.',
            'institucion_id.exists'          => 'La institución no es válida.',
            'tiene_remito.required'          => 'Indicá si hay remito.',
            'remito_numero.required_if'      => 'El número de remito es obligatorio si hay remito.',
            'metodo.required'                => 'Seleccioná el método de esterilización.',
            'estado_empaque.required'        => 'Indicá el estado del empaque.',
            'fecha_entrega_pactada.required' => 'La fecha de entrega es obligatoria.',
            'fecha_entrega_pactada.after_or_equal' => 'La fecha de entrega no puede ser anterior a hoy.',
        ]);
        $user = Auth::user()->id;

        // Determinar estado inicial según empaque
        // Empaquetado -> va directo a esterilización
        // Sin empaquetar -> va a acondicionamiento
        $estadoInicial = $validated['estado_empaque'] === 'empaquetado'
            ? $validated['metodo']           // 'vapor' o 'eto'
            : 'acondicionamiento';

        // Crear el lote
        $lote = Lote::create([
            'numero_lote'          => $this->generarNumeroLote(),
            'uuid'                 => Str::uuid(),
            'instituciones_id'     => $validated['institucion_id'],
            'estado_actual'        => 'recepcion',
            'metodo_esterilizacion'=> $validated['metodo'],
            'fecha_recepcion'      => now(),
            'fecha_entrega_pactada'=> $validated['fecha_entrega_pactada'],
            'usuario_actual_id'    => $user,
            'prioridad'            => $validated['prioridad'],
            'observaciones'        => $validated['observaciones'],
        ]);

        // Crear la recepción vinculada al lote
        Recepcion::create([
            'lote_id'              => $lote->id,
            'institucion_id'       => $validated['institucion_id'],
            'chofer_nombre'        => $validated['chofer_nombre'],
            'chofer_transporte'    => $validated['chofer_transporte'],
            'tiene_remito'         => $validated['tiene_remito'],
            'remito_numero'        => $validated['tiene_remito'] ? $validated['remito_numero'] : null,
            'cant_cajas'           => $validated['cant_cajas'],
            'cant_bultos'          => $validated['cant_bultos'],
            'cant_unidades'        => $validated['cant_unidades'],
            'cant_equipos_ropa'    => $validated['cant_equipos_ropa'],
            'cant_litros'          => $validated['cant_litros'],
            'metodo'               => $validated['metodo'],
            'estado_empaque'       => $validated['estado_empaque'],
            'fecha_entrega_pactada'=> $validated['fecha_entrega_pactada'],
            'prioridad'            => $validated['prioridad'],
            'operario_id'          => $user,
            'observaciones'        => $validated['observaciones'],
        ]);

        // Registrar en historial
        HistorialLote::create([
            'lote_id'        => $lote->id,
            'user_id'        => $user,
            'accion'         => 'Recepción registrada',
            'estado_origen'  => null,
            'estado_destino' => 'recepcion',
            'detalle'        => json_encode([
                'institucion'   => $lote->instituciones_id,
                'metodo'        => $validated['metodo'],
                'estado_empaque'=> $validated['estado_empaque'],
                'tiene_remito'  => $validated['tiene_remito'],
                'remito_numero' => $validated['remito_numero'] ?? null,
            ]),
        ]);

        // Avanzar estado si va directo a esterilización
        if ($estadoInicial !== 'recepcion') {
            $lote->update(['estado_actual' => $estadoInicial]);

            HistorialLote::create([
                'lote_id'        => $lote->id,
                'user_id'        => $user,
                'accion'         => 'Derivado automáticamente (material empaquetado)',
                'estado_origen'  => 'recepcion',
                'estado_destino' => $estadoInicial,
                'detalle'        => 'Material ingresó empaquetado, se saltea acondicionamiento.',
            ]);
        }

        return redirect()
            ->route('recepcion.show', $lote->id)
            ->with('success', "Lote {$lote->numero_lote} registrado correctamente.");
    }

    /**
     * Detalle de un lote recibido.
     */
    public function show(Lote $lote)
    {
        $lote->load(['recepcion.institucion', 'recepcion.operario', 'historial.user']);
        return view('recepcion.show', compact('lote'));
    }

    // -----------------------------------------------------------------------

    private function generarNumeroLote(): string
    {
        $prefijo = 'LOT-' . now()->format('Ymd') . '-';
        $ultimo  = Lote::where('numero_lote', 'like', $prefijo . '%')
            ->orderByDesc('id')
            ->value('numero_lote');

        $siguiente = $ultimo
            ? (int) substr($ultimo, -4) + 1
            : 1;

        return $prefijo . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
        // Ejemplo: LOT-20250615-0001
    }
}
