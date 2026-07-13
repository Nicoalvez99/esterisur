<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Acondicionamiento;
use App\Models\AcondicionamientoItem;
use App\Models\Control;
use App\Models\Esterilizacion;
use App\Models\HistorialLote;
use App\Models\Institucion;
use App\Models\Liberacion;
use App\Models\Lote;
use App\Models\Remito;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    // -----------------------------------------------------------------------
    // Panel principal de reportes
    // -----------------------------------------------------------------------
    public function index()
    {
        $instituciones = Institucion::where('activo', true)->orderBy('nombre')->get();

        // KPIs generales del mes actual
        $kpis = $this->calcularKpis(
            now()->startOfMonth()->toDateString(),
            now()->toDateString()
        );

        return view('reportes.index', compact('instituciones', 'kpis'));
    }

    // -----------------------------------------------------------------------
    // Trazabilidad completa de un lote
    // -----------------------------------------------------------------------
    public function trazabilidad(Request $request)
    {
        $request->validate([
            'numero_lote' => ['required', 'string'],
        ], [
            'numero_lote.required' => 'Ingresá el número de lote.',
        ]);

        $lote = Lote::with([
            'institucion',
            'recepcion.operario',
            'acondicionamiento.items',
            'acondicionamiento.operario',
            'liberacion.responsable',
            'remito.operario',
            'historial.user',
        ])
        ->whereHas('historial') // que tenga actividad
        ->where(function ($q) use ($request) {
            $q->where('numero_lote', $request->numero_lote)
              ->orWhere('uuid', $request->numero_lote);
        })
        ->first();

        // Ciclo de esterilización
        $esterilizacion = null;
        if ($lote) {
            $esterilizacion = Esterilizacion::with(['equipo', 'controles.operario', 'operario'])
                ->whereHas('lotes', fn($q) => $q->where('lotes.id', $lote->id))
                ->latest()
                ->first();
        }

        return view('reportes.trazabilidad', compact('lote', 'esterilizacion', 'request'));
    }

    // -----------------------------------------------------------------------
    // Informe por institución
    // -----------------------------------------------------------------------
    public function porInstitucion(Request $request)
    {
        $request->validate([
            'desde'          => ['required', 'date'],
            'hasta'          => ['required', 'date', 'after_or_equal:desde'],
            'institucion_id' => ['nullable', 'exists:instituciones,id'],
        ]);

        $desde        = $request->desde;
        $hasta        = $request->hasta;
        $institucionId= $request->institucion_id;

        $query = Lote::with(['institucion', 'remito'])
            ->whereBetween('fecha_recepcion', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);

        if ($institucionId) {
            $query->where('instituciones_id', $institucionId);
        }

        $lotes = $query->orderBy('fecha_recepcion')->get();

        // Agrupar por institución
        $porInstitucion = $lotes->groupBy('instituciones_id')->map(function ($lotesInst) {
            $inst = $lotesInst->first()->institucion;
            return [
                'institucion'  => $inst,
                'total'        => $lotesInst->count(),
                'finalizados'  => $lotesInst->where('estado_actual', 'finalizado')->count(),
                'rechazados'   => $lotesInst->where('estado_actual', 'rechazado')->count(),
                'retenidos'    => $lotesInst->where('estado_actual', 'retenido')->count(),
                'vapor'        => $lotesInst->where('metodo_esterilizacion', 'vapor')->count(),
                'eto'          => $lotesInst->where('metodo_esterilizacion', 'eto')->count(),
                'lotes'        => $lotesInst,
            ];
        });

        $instituciones = Institucion::where('activo', true)->orderBy('nombre')->get();

        return view('reportes.por_institucion', compact(
            'porInstitucion', 'instituciones', 'desde', 'hasta', 'institucionId'
        ));
    }

    // -----------------------------------------------------------------------
    // Informe de controles
    // -----------------------------------------------------------------------
    public function controles(Request $request)
    {
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', now()->toDateString());

        $controles = Control::with(['esterilizacion.equipo', 'esterilizacion.lotes.institucion', 'operario'])
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->orderByDesc('created_at')
            ->paginate(25)
            ->withQueryString();

        $resumen = Control::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->selectRaw('tipo, resultado, COUNT(*) as total')
            ->groupBy('tipo', 'resultado')
            ->get()
            ->groupBy('tipo');

        return view('reportes.controles', compact('controles', 'resumen', 'desde', 'hasta'));
    }

    // -----------------------------------------------------------------------
    // Informe de devoluciones
    // -----------------------------------------------------------------------
    public function devoluciones(Request $request)
    {
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', now()->toDateString());

        $items = AcondicionamientoItem::with([
            'acondicionamiento.lote.institucion',
            'acondicionamiento.operario',
        ])
        ->where('accion', 'devolver')
        ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
        ->orderByDesc('created_at')
        ->paginate(25)
        ->withQueryString();

        $totalDevoluciones = AcondicionamientoItem::where('accion', 'devolver')
            ->whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->count();

        return view('reportes.devoluciones', compact('items', 'totalDevoluciones', 'desde', 'hasta'));
    }

    // -----------------------------------------------------------------------
    // KPIs
    // -----------------------------------------------------------------------
    public function kpis(Request $request)
    {
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', now()->toDateString());

        $kpis = $this->calcularKpis($desde, $hasta);

        // Lotes por día del período
        $lotesPorDia = Lote::whereBetween('fecha_recepcion', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->selectRaw('DATE(fecha_recepcion) as dia, COUNT(*) as total')
            ->groupBy('dia')
            ->orderBy('dia')
            ->get();

        return view('reportes.kpis', compact('kpis', 'lotesPorDia', 'desde', 'hasta'));
    }

    // -----------------------------------------------------------------------
    // Helper: calcular KPIs para un período
    // -----------------------------------------------------------------------
    private function calcularKpis(string $desde, string $hasta): array
    {
        $base = Lote::whereBetween('fecha_recepcion', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);

        $totalLotes      = (clone $base)->count();
        $finalizados     = (clone $base)->where('estado_actual', 'finalizado')->count();
        $rechazados      = (clone $base)->where('estado_actual', 'rechazado')->count();
        $retenidos       = (clone $base)->where('estado_actual', 'retenido')->count();
        $vapor           = (clone $base)->where('metodo_esterilizacion', 'vapor')->count();
        $eto             = (clone $base)->where('metodo_esterilizacion', 'eto')->count();

        // Cumplimiento de entregas (despachados antes de la fecha pactada)
        $conFechaPactada = (clone $base)
            ->whereNotNull('fecha_entrega_pactada')
            ->whereHas('remito')
            ->count();

        $entregadosATiempo = (clone $base)
            ->whereNotNull('fecha_entrega_pactada')
            ->whereHas('remito', fn($q) =>
                $q->whereColumn('fecha_despacho', '<=', 'lotes.fecha_entrega_pactada')
            )
            ->count();

        $cumplimientoEntregas = $conFechaPactada > 0
            ? round($entregadosATiempo / $conFechaPactada * 100, 1)
            : null;

        // Tiempo promedio recepción → finalización
        $tiempoPromedio = Lote::whereBetween('fecha_recepcion', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->where('estado_actual', 'finalizado')
            ->whereNotNull('fecha_finalizacion')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, fecha_recepcion, fecha_finalizacion)) as promedio_horas')
            ->value('promedio_horas');

        // Controles no conformes
        $controlesTotal      = Control::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])->count();
        $controlesNoConformes= Control::whereBetween('created_at', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->where('resultado', 'no_conforme')->count();

        return [
            'total_lotes'           => $totalLotes,
            'finalizados'           => $finalizados,
            'rechazados'            => $rechazados,
            'retenidos'             => $retenidos,
            'vapor'                 => $vapor,
            'eto'                   => $eto,
            'tasa_rechazo'          => $totalLotes > 0 ? round($rechazados / $totalLotes * 100, 1) : 0,
            'cumplimiento_entregas' => $cumplimientoEntregas,
            'tiempo_promedio_horas' => $tiempoPromedio ? round($tiempoPromedio, 1) : null,
            'controles_total'       => $controlesTotal,
            'controles_no_conformes'=> $controlesNoConformes,
            'tasa_no_conforme'      => $controlesTotal > 0 ? round($controlesNoConformes / $controlesTotal * 100, 1) : 0,
        ];
    }
}