<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Acondicionamiento;
use App\Models\AcondicionamientoItem;
use App\Models\HistorialLote;
use App\Models\Lote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AcondicionamientoController extends Controller
{
    /**
     * Listado de lotes en acondicionamiento.
     */
    public function index()
    {
        $lotes = Lote::with(['recepcion.institucion', 'acondicionamiento'])
            ->where('estado_actual', 'acondicionamiento')
            ->orderByRaw("FIELD(prioridad, 'critica', 'urgente', 'normal')")
            ->latest()
            ->paginate(15);

        // Resumen del día
        $stats = [
            'pendientes'  => Lote::where('estado_actual', 'acondicionamiento')->count(),
            'completados' => Acondicionamiento::whereDate('created_at', today())->count(),
            'con_devoluciones' => Acondicionamiento::whereDate('created_at', today())
                ->where('resultado', 'con_devoluciones')->count(),
            'retenidos'   => Lote::where('estado_actual', 'retenido')
                ->whereHas('acondicionamiento')->count(),
        ];

        return view('acondicionamiento.index', compact('lotes', 'stats'));
    }

    /**
     * Formulario de acondicionamiento para un lote.
     */
    public function create(Lote $lote)
    {
        // Solo lotes en estado acondicionamiento
        if ($lote->estado_actual !== 'acondicionamiento') {
            return redirect()
                ->route('acondicionamiento.index')
                ->with('error', "El lote {$lote->numero_lote} no está en etapa de acondicionamiento.");
        }

        $lote->load(['recepcion.institucion', 'items']);

        return view('acondicionamiento.create', compact('lote'));
    }

    /**
     * Guarda el acondicionamiento.
     */
    public function store(Request $request, Lote $lote)
    {
        $user = Auth::user()->id;
        if ($lote->estado_actual !== 'acondicionamiento') {
            return redirect()->route('acondicionamiento.index')
                ->with('error', 'Este lote no está disponible para acondicionamiento.');
        }

        $request->validate([
            'tiene_planilla'  => ['required', 'boolean'],
            'cant_declarada'  => ['required', 'integer', 'min:0'],
            'tipo_empaque'    => ['required', 'in:bolsa_simple,doble_bolsa,caja,bulto,otro'],
            'cant_empaque'    => ['required', 'integer', 'min:1'],
            'empaque_detalle' => ['nullable', 'string', 'max:100'],
            'resultado'       => ['required', 'in:acondicionado,con_devoluciones,retenido'],
            'observaciones'   => ['nullable', 'string', 'max:1000'],

            // Items dinámicos
            'items'                       => ['nullable', 'array'],
            'items.*.nombre'              => ['required_with:items', 'string', 'max:150'],
            'items.*.cant_declarada'      => ['required_with:items', 'integer', 'min:0'],
            'items.*.cant_real'           => ['required_with:items', 'integer', 'min:0'],
            'items.*.estado_limpieza'     => ['required_with:items', 'in:limpio,sucio'],
            'items.*.estado_integridad'   => ['required_with:items', 'in:integro,roto'],
            'items.*.accion'              => ['required_with:items', 'in:procesar,devolver,retener'],
            'items.*.motivo_devolucion'   => ['nullable', 'string', 'max:200'],
        ], [
            'tipo_empaque.required'   => 'Seleccioná el tipo de empaque.',
            'cant_empaque.required'   => 'Indicá la cantidad de empaques.',
            'cant_empaque.min'        => 'Debe haber al menos 1 empaque.',
            'resultado.required'      => 'Indicá el resultado del acondicionamiento.',
        ]);

        DB::transaction(function () use ($request, $lote) {
            $user = Auth::user()->id;
            $items   = $request->input('items', []);
            $cantReal = collect($items)->sum('cant_real') ?: $request->input('cant_real_total', 0);

            // Calcular conteos desde ítems
            $cantLimpio   = collect($items)->where('estado_limpieza', 'limpio')->count();
            $cantSucio    = collect($items)->where('estado_limpieza', 'sucio')->count();
            $cantIntegro  = collect($items)->where('estado_integridad', 'integro')->count();
            $cantRoto     = collect($items)->where('estado_integridad', 'roto')->count();
            $cantDevuelto = collect($items)->where('accion', 'devolver')->count();

            $declarada   = (int) $request->cant_declarada;
            $diferencia  = $cantReal - $declarada;

            // Crear registro de acondicionamiento
            $acond = Acondicionamiento::create([
                'lote_id'         => $lote->id,
                'tiene_planilla'  => $request->boolean('tiene_planilla'),
                'cant_declarada'  => $declarada,
                'cant_real'       => $cantReal,
                'diferencia'      => $diferencia,
                'cant_limpio'     => $cantLimpio,
                'cant_sucio'      => $cantSucio,
                'cant_integro'    => $cantIntegro,
                'cant_roto'       => $cantRoto,
                'cant_devuelto'   => $cantDevuelto,
                'tipo_empaque'    => $request->tipo_empaque,
                'cant_empaque'    => $request->cant_empaque,
                'empaque_detalle' => $request->empaque_detalle,
                'resultado'       => $request->resultado,
                'operario_id'     => $user,
                'fecha_inicio'    => now()->subMinutes(30), // estimado; se puede hacer más preciso con JS
                'fecha_fin'       => now(),
                'observaciones'   => $request->observaciones,
            ]);

            // Guardar ítems
            foreach ($items as $item) {
                AcondicionamientoItem::create([
                    'acondicionamiento_id' => $acond->id,
                    'nombre'               => $item['nombre'],
                    'cant_declarada'       => $item['cant_declarada'],
                    'cant_real'            => $item['cant_real'],
                    'estado_limpieza'      => $item['estado_limpieza'],
                    'estado_integridad'    => $item['estado_integridad'],
                    'accion'               => $item['accion'],
                    'motivo_devolucion'    => $item['accion'] === 'devolver' ? ($item['motivo_devolucion'] ?? null) : null,
                    'observaciones'        => $item['observaciones'] ?? null,
                ]);
            }

            // Determinar nuevo estado del lote
            $nuevoEstado = match($request->resultado) {
                'retenido'        => 'retenido',
                'acondicionado',
                'con_devoluciones' => $lote->metodo_esterilizacion, // 'vapor' o 'eto'
            };

            $lote->update([
                'estado_actual'    => $nuevoEstado,
                'usuario_actual_id'=> $user,
            ]);

            // Historial
            HistorialLote::create([
                'lote_id'        => $lote->id,
                'user_id'        => $user,
                'accion'         => 'Acondicionamiento completado',
                'estado_origen'  => 'acondicionamiento',
                'estado_destino' => $nuevoEstado,
                'detalle'        => json_encode([
                    'resultado'      => $request->resultado,
                    'cant_declarada' => $declarada,
                    'cant_real'      => $cantReal,
                    'diferencia'     => $diferencia,
                    'cant_devuelto'  => $cantDevuelto,
                    'tipo_empaque'   => $request->tipo_empaque,
                    'cant_empaque'   => $request->cant_empaque,
                ]),
            ]);

            // Si hubo devoluciones, registrar incidencia en historial
            if ($cantDevuelto > 0) {
                HistorialLote::create([
                    'lote_id'        => $lote->id,
                    'user_id'        => $user,
                    'accion'         => "Devolución de {$cantDevuelto} ítem(s) registrada",
                    'estado_origen'  => 'acondicionamiento',
                    'estado_destino' => $nuevoEstado,
                    'detalle'        => 'Ver detalle de ítems en el registro de acondicionamiento.',
                ]);
            }
        });

        return redirect()
            ->route('acondicionamiento.show', $lote->id)
            ->with('success', "Lote {$lote->numero_lote} acondicionado correctamente.");
    }

    /**
     * Detalle del acondicionamiento de un lote.
     */
    public function show(Lote $lote)
    {
        $lote->load([
            'recepcion.institucion',
            'acondicionamiento.items',
            'acondicionamiento.operario',
            'historial.user',
        ]);

        return view('acondicionamiento.show', compact('lote'));
    }
}
