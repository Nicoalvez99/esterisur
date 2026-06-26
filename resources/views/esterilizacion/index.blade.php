@extends('layouts.modulo')

@section('title', 'Esterilización')
@section('modulo-label', 'Esterilización')
@section('page-title', 'Esterilización')
@section('page-subtitle', 'Ciclos de vapor y ETO')

@section('sidebar-nav')
    <a href="{{ route('esterilizacion.index') }}"
       class="nav-op {{ request()->routeIs('esterilizacion.index') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
        </svg>
        Panel
    </a>
    <a href="{{ route('esterilizacion.create', ['metodo' => 'vapor']) }}"
       class="nav-op {{ request()->is('esterilizacion/nuevo*') && request('metodo') === 'vapor' ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nuevo ciclo vapor
    </a>
    <a href="{{ route('esterilizacion.create', ['metodo' => 'eto']) }}"
       class="nav-op {{ request()->is('esterilizacion/nuevo*') && request('metodo') === 'eto' ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nuevo ciclo ETO
    </a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-6">
    @foreach([
        ['label' => 'Pendientes vapor', 'value' => $stats['pendientes_vapor'], 'color' => 'text-blue-600',   'bg' => 'bg-blue-50',   'icon' => '♨️'],
        ['label' => 'Pendientes ETO',   'value' => $stats['pendientes_eto'],   'color' => 'text-purple-600', 'bg' => 'bg-purple-50', 'icon' => '🧪'],
        ['label' => 'Ciclos hoy',       'value' => $stats['ciclos_hoy'],       'color' => 'text-teal-600',   'bg' => 'bg-teal-50',   'icon' => '✅'],
        ['label' => 'En aireación',     'value' => $stats['en_aireacion'],     'color' => 'text-amber-600',  'bg' => 'bg-amber-50',  'icon' => '💨'],
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

<div class="grid grid-cols-1 gap-5 lg:grid-cols-2 mb-6">

    {{-- Lotes pendientes VAPOR --}}
    <div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-blue-50/50">
            <h2 class="text-sm font-semibold text-blue-800 flex items-center gap-2">
                ♨️ Pendientes — Vapor
            </h2>
            <a href="{{ route('esterilizacion.create', ['metodo' => 'vapor']) }}"
               class="rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-blue-700 transition-colors">
                + Nuevo ciclo
            </a>
        </div>
        @forelse($lotesVapor as $lote)
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50/60 transition-colors {{ $lote->prioridad === 'critica' ? 'bg-red-50/40' : ($lote->prioridad === 'urgente' ? 'bg-amber-50/40' : '') }}">
                <div>
                    <p class="font-mono text-xs font-bold text-slate-800">{{ $lote->numero_lote }}</p>
                    <p class="text-xs text-slate-500">{{ $lote->recepcion?->institucion?->nombre ?? '—' }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($lote->prioridad !== 'normal')
                        <span @class([
                            'rounded-full px-2 py-0.5 text-xs font-bold',
                            'bg-amber-500 text-white' => $lote->prioridad === 'urgente',
                            'bg-red-600 text-white'   => $lote->prioridad === 'critica',
                        ])>{{ strtoupper(substr($lote->prioridad, 0, 3)) }}</span>
                    @endif
                    <span class="text-xs text-slate-400">{{ $lote->fecha_recepcion?->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <div class="px-5 py-8 text-center">
                <p class="text-sm text-slate-400">Sin lotes pendientes para vapor ✅</p>
            </div>
        @endforelse
    </div>

    {{-- Lotes pendientes ETO --}}
    <div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-purple-50/50">
            <h2 class="text-sm font-semibold text-purple-800 flex items-center gap-2">
                🧪 Pendientes — ETO
            </h2>
            <a href="{{ route('esterilizacion.create', ['metodo' => 'eto']) }}"
               class="rounded-lg bg-purple-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-purple-700 transition-colors">
                + Nuevo ciclo
            </a>
        </div>
        @forelse($lotesEto as $lote)
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 last:border-0 hover:bg-slate-50/60 transition-colors">
                <div>
                    <p class="font-mono text-xs font-bold text-slate-800">{{ $lote->numero_lote }}</p>
                    <p class="text-xs text-slate-500">{{ $lote->recepcion?->institucion?->nombre ?? '—' }}</p>
                </div>
                <span class="text-xs text-slate-400">{{ $lote->fecha_recepcion?->diffForHumans() }}</span>
            </div>
        @empty
            <div class="px-5 py-8 text-center">
                <p class="text-sm text-slate-400">Sin lotes pendientes para ETO ✅</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Ciclos del día --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-slate-800">Ciclos registrados hoy</h2>
        <span class="text-xs text-slate-400">{{ today()->locale('es')->isoFormat('D [de] MMMM') }}</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Ciclo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Equipo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Horario</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Lotes</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Resultado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Ver</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($ciclosHoy as $ciclo)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <span class="text-base">{{ $ciclo->metodo === 'vapor' ? '♨️' : '🧪' }}</span>
                                <div>
                                    <p class="text-xs font-bold text-slate-800">#{{ $ciclo->id }}</p>
                                    <p class="text-xs text-slate-400 uppercase">{{ $ciclo->metodo }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-xs text-slate-700">
                            {{ $ciclo->equipo?->nombre ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-slate-600">
                            {{ $ciclo->fecha_inicio?->format('H:i') }} — {{ $ciclo->fecha_fin?->format('H:i') ?? '…' }}
                            @if($ciclo->fecha_fin)
                                <span class="text-slate-400">({{ $ciclo->duracionMinutos() }} min)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <div class="flex flex-wrap gap-1">
                                @foreach($ciclo->lotes as $l)
                                    <span class="font-mono text-xs bg-slate-100 text-slate-600 rounded px-1.5 py-0.5">{{ $l->numero_lote }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700'  => $ciclo->resultado === 'conforme',
                                'bg-red-100 text-red-700'      => $ciclo->resultado === 'no_conforme',
                                'bg-amber-100 text-amber-700'  => $ciclo->resultado === 'pendiente',
                            ])>
                                {{ match($ciclo->resultado) {
                                    'conforme'     => '✅ Conforme',
                                    'no_conforme'  => '❌ No conforme',
                                    'pendiente'    => '⏳ Pendiente',
                                    default        => $ciclo->resultado,
                                } }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('esterilizacion.show', $ciclo->id) }}"
                               class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">
                                Ver →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400">
                            No hay ciclos registrados hoy todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    .nav-op.active { background:rgb(20 184 166/.1); color:rgb(15 118 110); }
</style>

@endsection