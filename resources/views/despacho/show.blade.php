@extends('layouts.modulo')

@section('title', 'Remito ' . $remito->numero)
@section('modulo-label', 'Despacho')
@section('page-title', $remito->numero)
@section('page-subtitle', 'Remito de despacho · ' . ($remito->institucion?->nombre ?? ''))

@section('sidebar-nav')
    <a href="{{ route('despacho.index') }}" class="nav-op">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver al panel
    </a>
@endsection

@section('content')

@php $lote = $remito->lote; @endphp

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- COLUMNA PRINCIPAL --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Estado --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <div class="flex flex-wrap items-center gap-3">
                <span @class([
                    'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold',
                    'bg-green-100 text-green-700' => $remito->estado === 'despachado',
                    'bg-blue-100 text-blue-700'   => $remito->estado === 'preparacion',
                    'bg-teal-100 text-teal-700'   => $remito->estado === 'entregado',
                ])>
                    <span @class([
                        'h-2 w-2 rounded-full',
                        'bg-green-500' => $remito->estado === 'despachado',
                        'bg-blue-500'  => $remito->estado === 'preparacion',
                        'bg-teal-500'  => $remito->estado === 'entregado',
                    ])></span>
                    {{ match($remito->estado) {
                        'despachado'  => '🚚 Despachado',
                        'preparacion' => '⏳ En preparación',
                        'entregado'   => '✅ Entregado',
                        default       => ucfirst($remito->estado),
                    } }}
                </span>
                <span class="text-xs text-slate-400 ml-auto">
                    Generado por {{ $remito->operario?->name ?? '—' }}
                    · {{ $remito->created_at?->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>

        {{-- Documento imprimible --}}
        <div class="rounded-xl bg-white border border-slate-200 overflow-hidden" id="remito-imprimible">

            {{-- Header del remito --}}
            <div class="bg-slate-900 text-white px-6 py-5">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-slate-400 uppercase tracking-widest">Remito de despacho</p>
                        <p class="font-mono text-2xl font-bold mt-1">{{ $remito->numero }}</p>
                    </div>
                    <div class="text-right text-xs text-slate-300">
                        <p>Fecha: <strong>{{ $remito->fecha_despacho?->format('d/m/Y') ?? now()->format('d/m/Y') }}</strong></p>
                        <p>Hora: <strong>{{ $remito->fecha_despacho?->format('H:i') ?? now()->format('H:i') }}</strong></p>
                        <p class="mt-1 font-mono text-slate-400">Lote: {{ $lote?->numero_lote }}</p>
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-5">

                {{-- Institución --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 pb-5 border-b border-slate-100">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Cliente / Institución</p>
                        <p class="text-base font-bold text-slate-800">{{ $remito->institucion?->nombre ?? '—' }}</p>
                        @if($remito->institucion?->cuit)
                            <p class="text-xs text-slate-500 font-mono">CUIT: {{ $remito->institucion->cuit }}</p>
                        @endif
                        @if($remito->institucion?->direccion)
                            <p class="text-xs text-slate-500">{{ $remito->institucion->direccion }}</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Transporte</p>
                        <p class="text-sm font-semibold text-slate-800">{{ $remito->chofer_nombre ?? '—' }}</p>
                        @if($remito->chofer_transporte)
                            <p class="text-xs text-slate-500">{{ $remito->chofer_transporte }}</p>
                        @endif
                    </div>
                </div>

                {{-- Cantidades --}}
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">Detalle del material despachado</p>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                        @foreach([
                            ['label' => 'Cajas chicas',   'value' => $remito->cant_cajas_chicas,   'icon' => '📦'],
                            ['label' => 'Cajas medianas',  'value' => $remito->cant_cajas_medianas,  'icon' => '📦'],
                            ['label' => 'Cajas grandes',   'value' => $remito->cant_cajas_grandes,   'icon' => '📦'],
                            ['label' => 'Bultos',          'value' => $remito->cant_bultos,          'icon' => '🗃️'],
                            ['label' => 'Unidades',        'value' => $remito->cant_unidades,        'icon' => '🔧'],
                            ['label' => 'Equipos ropa',    'value' => $remito->cant_equipos_ropa,    'icon' => '👕'],
                            ['label' => 'Litros',          'value' => $remito->cant_litros,          'icon' => '💧'],
                        ] as $c)
                            @if($c['value'] > 0)
                                <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-center">
                                    <p class="text-xl">{{ $c['icon'] }}</p>
                                    <p class="text-xl font-bold text-slate-800 mt-1">{{ $c['value'] }}</p>
                                    <p class="text-xs text-slate-500">{{ $c['label'] }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Total --}}
                    <div class="mt-3 flex items-center justify-between rounded-lg bg-slate-900 text-white px-4 py-3">
                        <span class="text-sm font-semibold">Total bultos / piezas</span>
                        <span class="text-xl font-bold">{{ $remito->totalBultos() }}</span>
                    </div>
                </div>

                @if($remito->observaciones)
                    <div class="rounded-lg bg-slate-50 border border-slate-200 px-4 py-3">
                        <p class="text-xs font-semibold text-slate-500 mb-1">Observaciones</p>
                        <p class="text-sm text-slate-700">{{ $remito->observaciones }}</p>
                    </div>
                @endif

                {{-- Firmas --}}
                <div class="grid grid-cols-2 gap-8 pt-5 border-t border-slate-100">
                    <div class="text-center">
                        <div class="h-14 border-b border-slate-300 mb-2"></div>
                        <p class="text-xs text-slate-500">Firma operario</p>
                        <p class="text-xs font-semibold text-slate-700 mt-0.5">{{ $remito->operario?->name }}</p>
                    </div>
                    <div class="text-center">
                        <div class="h-14 border-b border-slate-300 mb-2"></div>
                        <p class="text-xs text-slate-500">Firma chofer / receptor</p>
                        <p class="text-xs font-semibold text-slate-700 mt-0.5">{{ $remito->chofer_nombre ?? '_______________' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Historial del lote --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800">Historial del lote</h2>
            @if($lote?->historial->count())
                <ol class="relative border-l border-slate-200 space-y-4 ml-2">
                    @foreach($lote->historial->sortByDesc('created_at') as $item)
                        <li class="pl-5">
                            <div class="absolute -left-1.5 mt-1 h-3 w-3 rounded-full border-2 border-white bg-teal-400"></div>
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-xs font-semibold text-slate-800">{{ $item->accion }}</p>
                                    @if($item->estado_origen && $item->estado_destino)
                                        <p class="text-xs text-slate-400">
                                            {{ ucfirst(str_replace('_', ' ', $item->estado_origen)) }}
                                            → {{ ucfirst(str_replace('_', ' ', $item->estado_destino)) }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-slate-400">por {{ $item->user?->name ?? '—' }}</p>
                                </div>
                                <span class="shrink-0 text-xs text-slate-400">{{ $item->created_at->format('d/m H:i') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @else
                <p class="text-sm text-slate-400">Sin historial.</p>
            @endif
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="space-y-4">

        {{-- Acciones --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5 space-y-3">
            <button onclick="window.print()"
                    class="w-full rounded-xl bg-slate-800 px-6 py-3 text-sm font-bold text-white hover:bg-slate-700 active:scale-95 transition-all">
                🖨️ Imprimir remito
            </button>
            <a href="{{ route('despacho.index') }}"
               class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">
                ← Volver al panel
            </a>
        </div>

        {{-- Info del lote --}}
        <div class="rounded-xl bg-white border border-slate-200 p-4 space-y-2 text-xs">
            <p class="font-semibold text-slate-600">Datos del despacho</p>
            <div class="space-y-1.5 text-slate-500">
                <p>Lote: <span class="font-mono font-medium text-slate-700">{{ $lote?->numero_lote }}</span></p>
                <p>Despacho: <span class="font-medium text-slate-700">{{ $remito->fecha_despacho?->format('d/m/Y H:i') ?? '—' }}</span></p>
                <p>Método: <span class="font-medium text-slate-700 uppercase">{{ $lote?->metodo_esterilizacion }}</span></p>
                <p>Liberado por: <span class="font-medium text-slate-700">{{ $lote?->liberacion?->responsable?->name ?? '—' }}</span></p>
            </div>
        </div>
    </div>

</div>

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }

    @media print {
        body * { visibility: hidden; }
        #remito-imprimible, #remito-imprimible * { visibility: visible; }
        #remito-imprimible { position: absolute; inset: 0; }
    }
</style>

@endsection