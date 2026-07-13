<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use App\Models\Remito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FacturacionController extends Controller
{
    public function index(Request $request)
    {
        // Filtros
        $desde        = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta        = $request->get('hasta', now()->toDateString());
        $institucionId= $request->get('institucion_id');
        $metodo       = $request->get('metodo');
        $facturado    = $request->get('facturado'); // '' | '0' | '1'

        $query = Remito::with(['lote', 'institucion', 'operario'])
            ->whereIn('estado', ['despachado', 'entregado'])
            ->whereBetween('fecha_despacho', [
                $desde . ' 00:00:00',
                $hasta . ' 23:59:59',
            ]);

        if ($institucionId) {
            $query->where('institucion_id', $institucionId);
        }

        if ($metodo) {
            $query->whereHas('lote', fn($q) => $q->where('metodo_esterilizacion', $metodo));
        }

        if ($facturado !== null && $facturado !== '') {
            $query->where('facturado', (bool) $facturado);
        }

        $remitos = $query->orderBy('fecha_despacho')->paginate(20)->withQueryString();

        // Totales del período filtrado (sin paginar)
        $totales = $this->calcularTotales(clone $query->getQuery(), $desde, $hasta, $institucionId, $metodo, $facturado);

        // Resumen por institución
        $porInstitucion = $this->resumenPorInstitucion($desde, $hasta, $institucionId, $metodo, $facturado);

        $instituciones = Institucion::where('activo', true)->orderBy('nombre')->get();

        $stats = [
            'total_remitos'    => $remitos->total(),
            'sin_facturar'     => Remito::whereIn('estado', ['despachado', 'entregado'])
                ->where('facturado', false)
                ->whereBetween('fecha_despacho', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
                ->count(),
            'facturados'       => Remito::whereIn('estado', ['despachado', 'entregado'])
                ->where('facturado', true)
                ->whereBetween('fecha_despacho', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
                ->count(),
        ];

        return view('facturacion.index', compact(
            'remitos', 'totales', 'porInstitucion',
            'instituciones', 'desde', 'hasta', 'stats'
        ));
    }

    /**
     * Marcar uno o varios remitos como facturados.
     */
    public function marcarFacturado(Request $request)
    {
        $request->validate([
            'remito_ids'   => ['required', 'array', 'min:1'],
            'remito_ids.*' => ['exists:remitos,id'],
        ]);

        Remito::whereIn('id', $request->remito_ids)
            ->where('facturado', false)
            ->update([
                'facturado'          => true,
                'fecha_facturacion'  => now(),
                'facturado_por'      => auth()->id(),
            ]);

        $cantidad = count($request->remito_ids);

        return back()->with('success', "{$cantidad} remito(s) marcado(s) como facturado(s).");
    }

    /**
     * Desmarcar facturado (por error).
     */
    public function desmarcarFacturado(Remito $remito)
    {
        $remito->update([
            'facturado'         => false,
            'fecha_facturacion' => null,
            'facturado_por'     => null,
        ]);

        return back()->with('success', "Remito {$remito->numero} desmarcado.");
    }

    // -----------------------------------------------------------------------
    // Helpers privados
    // -----------------------------------------------------------------------

    private function calcularTotales($baseQuery, $desde, $hasta, $institucionId, $metodo, $facturado): array
    {
        $query = Remito::whereIn('estado', ['despachado', 'entregado'])
            ->whereBetween('fecha_despacho', [$desde . ' 00:00:00', $hasta . ' 23:59:59']);

        if ($institucionId) $query->where('institucion_id', $institucionId);
        if ($metodo)        $query->whereHas('lote', fn($q) => $q->where('metodo_esterilizacion', $metodo));
        if ($facturado !== null && $facturado !== '') $query->where('facturado', (bool) $facturado);

        return $query->selectRaw('
            COUNT(*) as total_remitos,
            SUM(cant_cajas_chicas)   as total_cajas_chicas,
            SUM(cant_cajas_medianas) as total_cajas_medianas,
            SUM(cant_cajas_grandes)  as total_cajas_grandes,
            SUM(cant_bultos)         as total_bultos,
            SUM(cant_unidades)       as total_unidades,
            SUM(cant_equipos_ropa)   as total_equipos_ropa,
            SUM(cant_litros)         as total_litros
        ')->first()->toArray();
    }

    private function resumenPorInstitucion($desde, $hasta, $institucionId, $metodo, $facturado)
    {
        $query = Remito::with('institucion')
            ->whereIn('estado', ['despachado', 'entregado'])
            ->whereBetween('fecha_despacho', [$desde . ' 00:00:00', $hasta . ' 23:59:59'])
            ->selectRaw('
                institucion_id,
                COUNT(*) as total_remitos,
                SUM(CASE WHEN facturado = 0 THEN 1 ELSE 0 END) as sin_facturar,
                SUM(cant_cajas_chicas + cant_cajas_medianas + cant_cajas_grandes) as total_cajas,
                SUM(cant_bultos)      as total_bultos,
                SUM(cant_unidades)    as total_unidades,
                SUM(cant_equipos_ropa)as total_equipos_ropa,
                SUM(cant_litros)      as total_litros
            ')
            ->groupBy('institucion_id');

        if ($institucionId) $query->where('institucion_id', $institucionId);
        if ($metodo)        $query->whereHas('lote', fn($q) => $q->where('metodo_esterilizacion', $metodo));
        if ($facturado !== null && $facturado !== '') $query->where('facturado', (bool) $facturado);

        return $query->get();
    }
}