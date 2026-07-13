<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Esterilizacion;
use App\Models\Institucion;
use App\Models\Lote;
use App\Models\Remito;
use Illuminate\Http\Request;

class AuditorController extends Controller
{
    /**
     * Panel principal del auditor con resumen general.
     */
    public function index()
    {
        $stats = [
            'total_lotes'     => Lote::count(),
            'finalizados'     => Lote::where('estado_actual', 'finalizado')->count(),
            'en_proceso'      => Lote::whereNotIn('estado_actual', ['finalizado', 'rechazado'])->count(),
            'rechazados'      => Lote::where('estado_actual', 'rechazado')->count(),
            'total_remitos'   => Remito::count(),
            'instituciones'   => Institucion::where('activo', true)->count(),
        ];

        $ultimosLotes = Lote::with(['institucion', 'recepcion'])
            ->latest()
            ->limit(10)
            ->get();

        return view('auditor.index', compact('stats', 'ultimosLotes'));
    }

    /**
     * Listado de lotes con filtros avanzados.
     */
    public function lotes(Request $request)
    {
        $query = Lote::with(['institucion', 'recepcion', 'liberacion', 'remito'])
            ->latest();

        if ($request->filled('numero_lote')) {
            $query->where('numero_lote', 'like', '%' . $request->numero_lote . '%');
        }

        if ($request->filled('institucion_id')) {
            $query->where('instituciones_id', $request->institucion_id);
        }

        if ($request->filled('estado')) {
            $query->where('estado_actual', $request->estado);
        }

        if ($request->filled('metodo')) {
            $query->where('metodo_esterilizacion', $request->metodo);
        }

        if ($request->filled('desde')) {
            $query->whereDate('fecha_recepcion', '>=', $request->desde);
        }

        if ($request->filled('hasta')) {
            $query->whereDate('fecha_recepcion', '<=', $request->hasta);
        }

        $lotes         = $query->paginate(20)->withQueryString();
        $instituciones = Institucion::where('activo', true)->orderBy('nombre')->get();

        return view('auditor.lotes', compact('lotes', 'instituciones'));
    }

    /**
     * Detalle completo de un lote — solo lectura.
     */
    public function show(Lote $lote)
    {
        $lote->load([
            'institucion',
            'recepcion.operario',
            'acondicionamiento.items',
            'acondicionamiento.operario',
            'liberacion.responsable',
            'remito.operario',
            'historial.user',
        ]);

        $esterilizacion = Esterilizacion::with(['equipo', 'controles.operario', 'operario'])
            ->whereHas('lotes', fn($q) => $q->where('lotes.id', $lote->id))
            ->latest()
            ->first();

        return view('auditor.show', compact('lote', 'esterilizacion'));
    }

    /**
     * Historial de acciones filtrable.
     */
    public function historial(Request $request)
    {
        $desde = $request->get('desde', today()->toDateString());
        $hasta = $request->get('hasta', today()->toDateString());

        $historial = \App\Models\HistorialLote::with(['lote.institucion', 'user'])
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->when($request->filled('user_id'), fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('numero_lote'), fn($q) =>
                $q->whereHas('lote', fn($lq) => $lq->where('numero_lote', 'like', '%' . $request->numero_lote . '%'))
            )
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $usuarios = \App\Models\User::where('activo', true)->orderBy('name')->get();

        return view('auditor.historial', compact('historial', 'usuarios', 'desde', 'hasta'));
    }
}