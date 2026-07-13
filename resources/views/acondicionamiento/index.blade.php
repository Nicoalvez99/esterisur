@extends('layouts.modulo')

@section('title', 'Acondicionamiento')
@section('modulo-label', 'Acondicionamiento')
@section('page-title', 'Acondicionamiento')
@section('page-subtitle', 'Lotes pendientes de control y empaque')

@section('sidebar-nav')
    <a href="{{ route('acondicionamiento.index') }}"
       class="nav-op {{ request()->routeIs('acondicionamiento.index') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
        </svg>
        Pendientes
    </a>
@endsection

@section('content')

{{-- Stats del día --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-6">
    @foreach([
        ['label' => 'Pendientes',       'value' => $stats['pendientes'],       'color' => 'text-blue-600',   'bg' => 'bg-blue-50'],
        ['label' => 'Completados hoy',  'value' => $stats['completados'],      'color' => 'text-green-600',  'bg' => 'bg-green-50'],
        ['label' => 'Con devoluciones', 'value' => $stats['con_devoluciones'], 'color' => 'text-amber-600',  'bg' => 'bg-amber-50'],
        ['label' => 'Retenidos',        'value' => $stats['retenidos'],        'color' => 'text-red-600',    'bg' => 'bg-red-50'],
    ] as $s)
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs font-medium text-slate-500">{{ $s['label'] }}</p>
            <p class="mt-1 text-2xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
        </div>
    @endforeach
</div>

{{-- Tabla de lotes pendientes --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-semibold text-slate-800">Lotes para acondicionar</h2>
        <span class="text-xs text-slate-400">{{ $lotes->total() }} lote{{ $lotes->total() !== 1 ? 's' : '' }}</span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Institución</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Método</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Recibido</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Entrega</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Prioridad</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($lotes as $lote)
                    <tr class="hover:bg-slate-50/60 transition-colors {{ $lote->prioridad === 'critica' ? 'bg-red-50/40' : ($lote->prioridad === 'urgente' ? 'bg-amber-50/40' : '') }}">
                        <td class="px-5 py-3">
                            <p class="font-mono text-xs font-semibold text-slate-800">{{ $lote->numero_lote }}</p>
                            <p class="text-xs text-slate-400">{{ $lote->fecha_recepcion?->format('H:i') }}h</p>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <p class="text-xs font-medium text-slate-700">
                                {{ $lote->recepcion?->institucion?->nombre ?? '—' }}
                            </p>
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
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-500">
                            {{ $lote->fecha_recepcion?->diffForHumans() ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @php
                                $entrega = $lote->recepcion?->fecha_entrega_pactada;
                                $vence   = $entrega && $entrega->isPast();
                                $hoy     = $entrega && $entrega->isToday();
                            @endphp
                            @if($entrega)
                                <span @class([
                                    'text-xs font-medium',
                                    'text-red-600'    => $vence,
                                    'text-amber-600'  => $hoy && !$vence,
                                    'text-slate-600'  => !$hoy && !$vence,
                                ])>
                                    {{ $entrega->format('d/m/Y') }}
                                    @if($vence) ⚠️ @elseif($hoy) • Hoy @endif
                                </span>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($lote->prioridad === 'critica')
                                <span class="inline-flex items-center rounded-full bg-red-600 px-2 py-0.5 text-xs font-bold text-white">CRÍTICA</span>
                            @elseif($lote->prioridad === 'urgente')
                                <span class="inline-flex items-center rounded-full bg-amber-500 px-2 py-0.5 text-xs font-bold text-white">URGENTE</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">Normal</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('acondicionamiento.create', $lote->id) }}"
                               class="inline-flex items-center gap-1 rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-700 transition-colors">
                                Iniciar →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-14 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-sm font-medium text-slate-500">No hay lotes pendientes</p>
                                <p class="text-xs text-slate-400">Todos los lotes están al día 🎉</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($lotes->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">
            {{ $lotes->links() }}
        </div>
    @endif
</div>

<style>
    .nav-op {
        display: flex; align-items: center; gap: 0.625rem;
        padding: 0.5rem 0.75rem; border-radius: 0.5rem;
        font-size: 0.875rem; font-weight: 500;
        color: rgb(100 116 139); transition: all 0.15s; text-decoration: none;
    }
    .nav-op:hover { background: rgb(241 245 249); color: rgb(30 41 59); }
    .nav-op.active { background: rgb(20 184 166 / 0.1); color: rgb(15 118 110); }
</style>

@endsection