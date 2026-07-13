<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HistorialLote;
use App\Models\Lote;
use App\Models\NoConformidad;
use Illuminate\Http\Request;

class NoConformidadController extends Controller
{
    public function index(Request $request)
    {
        $query = NoConformidad::with(['lote.institucion', 'registradoPor', 'cerradoPor'])
            ->latest();

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $noConformidades = $query->paginate(20)->withQueryString();

        $stats = [
            'abiertas'    => NoConformidad::where('estado', 'abierta')->count(),
            'en_proceso'  => NoConformidad::where('estado', 'en_proceso')->count(),
            'cerradas_hoy'=> NoConformidad::where('estado', 'cerrada')->whereDate('fecha_cierre', today())->count(),
            'total'       => NoConformidad::count(),
        ];

        return view('no_conformidades.index', compact('noConformidades', 'stats'));
    }

    public function create(Request $request)
    {
        // Puede venir con lote pre-seleccionado
        $loteId = $request->get('lote_id');
        $lote   = $loteId ? Lote::with('institucion')->find($loteId) : null;

        // Lotes activos para el selector
        $lotes = Lote::with('institucion')
            ->whereNotIn('estado_actual', ['finalizado'])
            ->orderByDesc('created_at')
            ->get();

        return view('no_conformidades.create', compact('lote', 'lotes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'lote_id'          => ['required', 'exists:lotes,id'],
            'tipo'             => ['required', 'in:' . implode(',', array_keys(NoConformidad::TIPOS))],
            'descripcion'      => ['required', 'string', 'max:1000'],
            'cantidad_afectada'=> ['nullable', 'integer', 'min:1'],
            'accion_tomada'    => ['nullable', 'in:' . implode(',', array_keys(NoConformidad::ACCIONES))],
        ], [
            'lote_id.required'     => 'Seleccioná un lote.',
            'tipo.required'        => 'Seleccioná el tipo de no conformidad.',
            'descripcion.required' => 'Describí el problema.',
        ]);

        $nc = NoConformidad::create([
            'lote_id'          => $request->lote_id,
            'tipo'             => $request->tipo,
            'estado'           => 'abierta',
            'accion_tomada'    => $request->accion_tomada,
            'descripcion'      => $request->descripcion,
            'cantidad_afectada'=> $request->cantidad_afectada,
            'registrado_por'   => auth()->id(),
        ]);

        // Registrar en historial del lote
        HistorialLote::create([
            'lote_id'        => $request->lote_id,
            'user_id'        => auth()->id(),
            'accion'         => "No conformidad registrada: {$nc->tipo_label}",
            'estado_origen'  => Lote::find($request->lote_id)->estado_actual,
            'estado_destino' => Lote::find($request->lote_id)->estado_actual,
            'detalle'        => json_encode([
                'no_conformidad_id' => $nc->id,
                'tipo'              => $nc->tipo,
                'descripcion'       => $request->descripcion,
            ]),
        ]);

        return redirect()
            ->route('no-conformidades.show', $nc->id)
            ->with('success', 'No conformidad registrada correctamente.');
    }

    public function show(NoConformidad $noConformidad)
    {
        $noConformidad->load(['lote.institucion', 'lote.historial.user', 'registradoPor', 'cerradoPor']);
        return view('no_conformidades.show', compact('noConformidad'));
    }

    public function cerrar(Request $request, NoConformidad $noConformidad)
    {
        $request->validate([
            'accion_correctiva'    => ['required', 'string', 'max:1000'],
            'observaciones_cierre' => ['nullable', 'string', 'max:500'],
        ], [
            'accion_correctiva.required' => 'Describí la acción correctiva tomada.',
        ]);

        $noConformidad->update([
            'estado'               => 'cerrada',
            'accion_correctiva'    => $request->accion_correctiva,
            'observaciones_cierre' => $request->observaciones_cierre,
            'cerrado_por'          => auth()->id(),
            'fecha_cierre'         => now(),
        ]);

        HistorialLote::create([
            'lote_id'        => $noConformidad->lote_id,
            'user_id'        => auth()->id(),
            'accion'         => "No conformidad cerrada: {$noConformidad->tipo_label}",
            'estado_origen'  => $noConformidad->lote->estado_actual,
            'estado_destino' => $noConformidad->lote->estado_actual,
            'detalle'        => json_encode([
                'accion_correctiva' => $request->accion_correctiva,
            ]),
        ]);

        return redirect()
            ->route('no-conformidades.show', $noConformidad->id)
            ->with('success', 'No conformidad cerrada correctamente.');
    }
}