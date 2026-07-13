<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Lote;
use App\Models\HistorialLote;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   public function index()
    {
        // --- Métricas de lotes ---
        $estadosActivos = ['recepcion', 'acondicionamiento', 'vapor', 'eto', 'control_calidad', 'almacenamiento'];
 
        $lotesActivos   = Lote::whereIn('estado_actual', $estadosActivos)->count();
        $lotesListos    = Lote::whereIn('estado_actual', ['entrega'])->count();
        $lotesRetenidos = Lote::where('estado_actual', 'retenido')->count();
        $totalUsuarios  = User::count();
 
        // Conteo por estado para el pipeline
        $lotesPorEstado = Lote::selectRaw('estado_actual, count(*) as total')
            ->groupBy('estado_actual')
            ->pluck('total', 'estado_actual')
            ->toArray();
 
        // Lotes urgentes y críticos (con detalles)
        $lotesUrgentes = Lote::where('prioridad', 'urgente')->whereIn('estado_actual', $estadosActivos)->count();
        $lotesCriticos = Lote::where('prioridad', 'critica')->whereIn('estado_actual', $estadosActivos)->count();
 
        $lotesAlertaDetalle = Lote::with('institucion')
            ->whereIn('prioridad', ['urgente', 'critica'])
            ->whereIn('estado_actual', $estadosActivos)
            ->orderByRaw("FIELD(prioridad, 'critica', 'urgente')")
            ->limit(5)
            ->get();
 
        // --- Usuarios para la tabla ---
        $usuarios = User::orderBy('name')->paginate(8);
 
        // --- Actividad reciente ---
        $historialReciente = HistorialLote::with(['lote', 'user'])
            ->latest()
            ->limit(8)
            ->get();
 
        return view('admin.dashboard', compact(
            'lotesActivos',
            'lotesListos',
            'lotesRetenidos',
            'totalUsuarios',
            'lotesPorEstado',
            'lotesUrgentes',
            'lotesCriticos',
            'lotesAlertaDetalle',
            'usuarios',
            'historialReciente',
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
