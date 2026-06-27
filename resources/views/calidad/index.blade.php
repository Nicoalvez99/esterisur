@extends('layouts.modulo')

@section('title', 'Control de calidad')
@section('modulo-label', 'Control de calidad')
@section('page-title', 'Control de calidad')
@section('page-subtitle', 'Lotes pendientes de liberación')

@section('sidebar-nav')
    <a href="{{ route('calidad.index') }}"
       class="nav-op {{ request()->routeIs('calidad.index') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Panel
    </a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-6">
    @foreach([
        ['label' => 'Pendientes',     'value' => $stats['pendientes'],    'color' => 'text-yellow-600', 'bg' => 'bg-yellow-50', 'icon' => '⏳'],
        ['label' => 'Liberados hoy',  'value' => $stats['liberados_hoy'], 'color' => 'text-green-600',  'bg' => 'bg-green-50',  'icon' => '✅'],
        ['label' => 'Retenidos',      'value' => $stats['retenidos'],     'color' => 'text-amber-600',  'bg' => 'bg-amber-50',  'icon' => '⚠️'],
        ['label' => 'Rechazados',     'value' => $stats['rechazados'],    'color' => 'text-red-600',    'bg' => 'bg-red-50',    'icon' => '❌'],
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

{{-- Tabla --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-semibold text-slate-800">Lotes para revisar</h2>
        <span class="text-xs text-slate-400">{{ $lotes->total() }} en cola</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Institución</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Método</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Entrega</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Prioridad</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($lotes as $lote)
                    <tr @class([
                        'hover:bg-slate-50/60 transition-colors',
                        'bg-red-50/40'    => $lote->prioridad === 'critica',
                        'bg-amber-50/40'  => $lote->prioridad === 'urgente',
                    ])>
                        <td class="px-5 py-3">
                            <p class="font-mono text-xs font-bold text-slate-800">{{ $lote->numero_lote }}</p>
                            <p class="text-xs text-slate-400">Recibido {{ $lote->fecha_recepcion?->diffForHumans() }}</p>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-xs text-slate-700">
                            {{ $lote->recepcion?->institucion?->nombre ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span @class([
                                'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-blue-100 text-blue-700'     => $lote->metodo_esterilizacion === 'vapor',
                                'bg-purple-100 text-purple-700' => $lote->metodo_esterilizacion === 'eto',
                            ])>
                                {{ strtoupper($lote->metodo_esterilizacion) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @php
                                $entrega = $lote->recepcion?->fecha_entrega_pactada;
                                $vence   = $entrega && $entrega->isPast();
                            @endphp
                            @if($entrega)
                                <span class="text-xs font-medium {{ $vence ? 'text-red-600' : 'text-slate-600' }}">
                                    {{ $entrega->format('d/m/Y') }}
                                    @if($vence) ⚠️ @endif
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
                            <a href="{{ route('calidad.show', $lote->id) }}"
                               class="inline-flex items-center gap-1 rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-700 transition-colors">
                                Revisar →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <span class="text-4xl">✅</span>
                                <p class="text-sm font-medium text-slate-500">No hay lotes pendientes de revisión</p>
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

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    .nav-op.active { background:rgb(20 184 166/.1); color:rgb(15 118 110); }
</style>

@endsection