<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HistorialLote;
use App\Models\Lote;
use App\Models\Remito;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DespachoController extends Controller
{
    public function index()
    {
        // Lotes liberados esperando despacho
        $lotes = Lote::with(['recepcion.institucion', 'liberacion', 'remito'])
            ->where('estado_actual', 'almacenamiento')
            ->orderByRaw("FIELD(prioridad, 'critica', 'urgente', 'normal')")
            ->latest()
            ->paginate(15);

        // Remitos del día
        $remitosHoy = Remito::with(['lote', 'institucion', 'operario'])
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $stats = [
            'listos'          => Lote::where('estado_actual', 'almacenamiento')->count(),
            'despachados_hoy' => Remito::whereDate('fecha_despacho', today())->where('estado', 'despachado')->count(),
            'en_preparacion'  => Remito::where('estado', 'preparacion')->count(),
            'entregados_hoy'  => Remito::whereDate('fecha_entrega_confirmada', today())->where('estado', 'entregado')->count(),
        ];

        return view('despacho.index', compact('lotes', 'remitosHoy', 'stats'));
    }

    public function create(Lote $lote)
    {
        if ($lote->estado_actual !== 'almacenamiento') {
            return redirect()->route('despacho.index')
                ->with('error', "El lote {$lote->numero_lote} no está listo para despacho.");
        }

        if ($lote->remito) {
            return redirect()->route('despacho.show', $lote->remito->id)
                ->with('error', 'Este lote ya tiene un remito generado.');
        }

        $lote->load(['recepcion.institucion.protocolo', 'liberacion', 'acondicionamiento']);

        return view('despacho.create', compact('lote'));
    }

    public function store(Request $request, Lote $lote)
    {
        if ($lote->estado_actual !== 'almacenamiento') {
            return redirect()->route('despacho.index')
                ->with('error', 'Este lote no está disponible para despacho.');
        }

        $request->validate([
            'chofer_nombre'       => ['nullable', 'string', 'max:100'],
            'chofer_transporte'   => ['nullable', 'string', 'max:100'],
            'cant_cajas_chicas'   => ['required', 'integer', 'min:0'],
            'cant_cajas_medianas' => ['required', 'integer', 'min:0'],
            'cant_cajas_grandes'  => ['required', 'integer', 'min:0'],
            'cant_bultos'         => ['required', 'integer', 'min:0'],
            'cant_unidades'       => ['required', 'integer', 'min:0'],
            'cant_equipos_ropa'   => ['required', 'integer', 'min:0'],
            'cant_litros'         => ['required', 'numeric', 'min:0'],
            'observaciones'       => ['nullable', 'string', 'max:1000'],
        ]);

        // Validar que haya al menos algo para despachar
        $totalBultos = $request->cant_cajas_chicas + $request->cant_cajas_medianas
            + $request->cant_cajas_grandes + $request->cant_bultos
            + $request->cant_unidades + $request->cant_equipos_ropa;

        if ($totalBultos === 0 && floatval($request->cant_litros) === 0.0) {
            return back()->withErrors(['cant_cajas_chicas' => 'Ingresá al menos una unidad para despachar.'])->withInput();
        }

        DB::transaction(function () use ($request, $lote) {
            $remito = Remito::create([
                'numero'              => $this->generarNumeroRemito(),
                'lote_id'             => $lote->id,
                'institucion_id'      => $lote->instituciones_id,
                'chofer_nombre'       => $request->chofer_nombre,
                'chofer_transporte'   => $request->chofer_transporte,
                'cant_cajas_chicas'   => $request->cant_cajas_chicas,
                'cant_cajas_medianas' => $request->cant_cajas_medianas,
                'cant_cajas_grandes'  => $request->cant_cajas_grandes,
                'cant_bultos'         => $request->cant_bultos,
                'cant_unidades'       => $request->cant_unidades,
                'cant_equipos_ropa'   => $request->cant_equipos_ropa,
                'cant_litros'         => $request->cant_litros,
                'estado'              => 'despachado',
                'fecha_despacho'      => now(),
                'operario_id'         => auth()->id(),
                'observaciones'       => $request->observaciones,
            ]);

            $lote->update([
                'estado_actual'     => 'finalizado',
                'fecha_finalizacion'=> now(),
                'usuario_actual_id' => auth()->id(),
            ]);

            HistorialLote::create([
                'lote_id'        => $lote->id,
                'user_id'        => auth()->id(),
                'accion'         => "Remito {$remito->numero} generado — lote despachado",
                'estado_origen'  => 'almacenamiento',
                'estado_destino' => 'finalizado',
                'detalle'        => json_encode([
                    'remito'        => $remito->numero,
                    'chofer'        => $request->chofer_nombre,
                    'fecha_despacho'=> now()->toDateTimeString(),
                ]),
            ]);
        });

        $remito = $lote->fresh()->remito;

        return redirect()
            ->route('despacho.show', $remito->id)
            ->with('success', "Remito {$remito->numero} generado. Lote despachado.");
    }

    public function show(Remito $remito)
    {
        $remito->load(['lote.recepcion.institucion', 'lote.liberacion.responsable', 'lote.historial.user', 'operario']);
        return view('despacho.show', compact('remito'));
    }

    private function generarNumeroRemito(): string
    {
        $prefijo  = 'REM-' . now()->format('Ymd') . '-';
        $ultimo   = Remito::where('numero', 'like', $prefijo . '%')
            ->orderByDesc('id')->value('numero');
        $siguiente = $ultimo ? (int) substr($ultimo, -4) + 1 : 1;
        return $prefijo . str_pad($siguiente, 4, '0', STR_PAD_LEFT);
    }
}