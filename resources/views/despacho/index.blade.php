@extends('layouts.modulo')

@section('title', 'Despacho')
@section('modulo-label', 'Despacho')
@section('page-title', 'Despacho')
@section('page-subtitle', 'Preparación y remitos de salida')

@section('sidebar-nav')
    <a href="{{ route('despacho.index') }}"
       class="nav-op {{ request()->routeIs('despacho.index') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
        </svg>
        Panel
    </a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-6">
    @foreach([
        ['label' => 'Listos',           'value' => $stats['listos'],          'color' => 'text-teal-600',   'icon' => '📦'],
        ['label' => 'Despachados hoy',  'value' => $stats['despachados_hoy'], 'color' => 'text-green-600',  'icon' => '🚚'],
        ['label' => 'En preparación',   'value' => $stats['en_preparacion'],  'color' => 'text-blue-600',   'icon' => '⏳'],
        ['label' => 'Entregados hoy',   'value' => $stats['entregados_hoy'],  'color' => 'text-slate-600',  'icon' => '✅'],
    ] as $s)
        <div class="rounded-xl bg-white border border-slate-200 p-4 flex items-center gap-3">
            <span class="text-2xl">{{ $s['icon'] }}</span>
            <div>
                <p class="text-xs font-medium text-slate-500">{{ $s['label'] }}</p>
                <p class="text-xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
            </div>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 gap-5 lg:grid-cols-5">

    {{-- Lotes listos para despachar --}}
    <div class="lg:col-span-3 rounded-xl bg-white border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-800">Lotes listos para despachar</h2>
            <span class="text-xs text-slate-400">{{ $lotes->total() }} en espera</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Lote</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Institución</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Entrega</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Prioridad</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($lotes as $lote)
                        <tr @class([
                            'hover:bg-slate-50/60 transition-colors',
                            'bg-red-50/40'   => $lote->prioridad === 'critica',
                            'bg-amber-50/40' => $lote->prioridad === 'urgente',
                        ])>
                            <td class="px-5 py-3">
                                <p class="font-mono text-xs font-bold text-slate-800">{{ $lote->numero_lote }}</p>
                                <p class="text-xs text-slate-400 uppercase">{{ $lote->metodo_esterilizacion }}</p>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell text-xs text-slate-700">
                                {{ $lote->recepcion?->institucion?->nombre ?? '—' }}
                            </td>
                            <td class="px-4 py-3 hidden lg:table-cell">
                                @php
                                    $entrega = $lote->recepcion?->fecha_entrega_pactada;
                                    $vence   = $entrega && $entrega->isPast();
                                    $hoy     = $entrega && $entrega->isToday();
                                @endphp
                                @if($entrega)
                                    <span class="text-xs font-medium {{ $vence ? 'text-red-600' : ($hoy ? 'text-amber-600' : 'text-slate-600') }}">
                                        {{ $entrega->format('d/m/Y') }}
                                        @if($vence) ⚠️ @elseif($hoy) • Hoy @endif
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($lote->prioridad === 'critica')
                                    <span class="inline-flex rounded-full bg-red-600 px-2 py-0.5 text-xs font-bold text-white">CRÍTICA</span>
                                @elseif($lote->prioridad === 'urgente')
                                    <span class="inline-flex rounded-full bg-amber-500 px-2 py-0.5 text-xs font-bold text-white">URGENTE</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Normal</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('despacho.create', $lote->id) }}"
                                   class="inline-flex items-center gap-1 rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-700 transition-colors">
                                    Despachar →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="text-4xl">✅</span>
                                    <p class="text-sm font-medium text-slate-500">No hay lotes esperando despacho</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($lotes->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">{{ $lotes->links() }}</div>
        @endif
    </div>

    {{-- Remitos del día --}}
    <div class="lg:col-span-2 rounded-xl bg-white border border-slate-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-800">Remitos de hoy</h2>
        </div>
        @forelse($remitosHoy as $remito)
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50/60 transition-colors">
                <div>
                    <p class="font-mono text-xs font-bold text-slate-800">{{ $remito->numero }}</p>
                    <p class="text-xs text-slate-500">{{ $remito->institucion?->nombre ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $remito->fecha_despacho?->format('H:i') }}h · {{ $remito->operario?->name }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <span @class([
                        'inline-flex rounded-full px-2 py-0.5 text-xs font-semibold',
                        'bg-green-100 text-green-700'  => $remito->estado === 'despachado',
                        'bg-blue-100 text-blue-700'    => $remito->estado === 'preparacion',
                        'bg-teal-100 text-teal-700'    => $remito->estado === 'entregado',
                    ])>
                        {{ ucfirst($remito->estado) }}
                    </span>
                    <a href="{{ route('despacho.show', $remito->id) }}"
                       class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">Ver →</a>
                </div>
            </div>
        @empty
            <div class="px-5 py-8 text-center">
                <p class="text-sm text-slate-400">Sin remitos hoy todavía</p>
            </div>
        @endforelse
    </div>

</div>

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    .nav-op.active { background:rgb(20 184 166/.1); color:rgb(15 118 110); }
</style>

@endsection