@extends('layouts.modulo')

@section('title', 'Auditor')
@section('modulo-label', 'Auditoría')
@section('page-title', 'Panel de auditoría')
@section('page-subtitle', 'Vista de solo lectura del sistema')

@section('sidebar-nav')
    <a href="{{ route('auditor.index') }}"
       class="nav-op {{ request()->routeIs('auditor.index') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
        </svg>
        Panel
    </a>
    <a href="{{ route('auditor.lotes') }}"
       class="nav-op {{ request()->routeIs('auditor.lotes') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
        </svg>
        Lotes
    </a>
    <a href="{{ route('auditor.historial') }}"
       class="nav-op {{ request()->routeIs('auditor.historial') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Historial de acciones
    </a>
@endsection

@section('content')

{{-- Aviso solo lectura --}}
<div class="mb-5 flex items-center gap-3 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3">
    <svg class="h-5 w-5 text-blue-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
    </svg>
    <p class="text-xs font-medium text-blue-700">
        Modo auditoría — <strong>solo lectura</strong>. Podés consultar toda la información pero no modificar nada.
    </p>
</div>

{{-- Stats generales --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6 mb-6">
    @foreach([
        ['label' => 'Total lotes',    'value' => $stats['total_lotes'],   'color' => 'text-slate-700',  'icon' => '📦'],
        ['label' => 'Finalizados',    'value' => $stats['finalizados'],   'color' => 'text-green-600',  'icon' => '✅'],
        ['label' => 'En proceso',     'value' => $stats['en_proceso'],    'color' => 'text-blue-600',   'icon' => '⚙️'],
        ['label' => 'Rechazados',     'value' => $stats['rechazados'],    'color' => 'text-red-600',    'icon' => '❌'],
        ['label' => 'Remitos emitidos','value' => $stats['total_remitos'],'color' => 'text-teal-600',  'icon' => '📄'],
        ['label' => 'Instituciones',  'value' => $stats['instituciones'], 'color' => 'text-purple-600', 'icon' => '🏥'],
    ] as $s)
        <div class="rounded-xl bg-white border border-slate-200 p-4 text-center">
            <span class="text-2xl">{{ $s['icon'] }}</span>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ $s['label'] }}</p>
            <p class="text-xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
        </div>
    @endforeach
</div>

{{-- Accesos rápidos --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
    <a href="{{ route('auditor.lotes') }}"
       class="group rounded-xl bg-white border border-slate-200 p-5 hover:border-teal-300 hover:shadow-sm transition-all">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-50 group-hover:bg-teal-100 transition-colors">
                <svg class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">Consultar lotes</p>
                <p class="text-xs text-slate-500 mt-0.5">Buscá y filtrá lotes por estado, institución, método y fecha.</p>
            </div>
        </div>
    </a>

    <a href="{{ route('auditor.historial') }}"
       class="group rounded-xl bg-white border border-slate-200 p-5 hover:border-teal-300 hover:shadow-sm transition-all">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 group-hover:bg-blue-100 transition-colors">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">Historial de acciones</p>
                <p class="text-xs text-slate-500 mt-0.5">Todas las acciones del sistema con usuario, fecha y hora.</p>
            </div>
        </div>
    </a>

    <a href="{{ route('reportes.trazabilidad') }}"
       class="group rounded-xl bg-white border border-slate-200 p-5 hover:border-teal-300 hover:shadow-sm transition-all">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-50 group-hover:bg-purple-100 transition-colors">
                <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">Trazabilidad</p>
                <p class="text-xs text-slate-500 mt-0.5">Historial completo de un lote desde recepción hasta entrega.</p>
            </div>
        </div>
    </a>
</div>

{{-- Últimos lotes --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-semibold text-slate-800">Últimos lotes ingresados</h2>
        <a href="{{ route('auditor.lotes') }}" class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">
            Ver todos →
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Institución</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Método</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Recepción</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Ver</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($ultimosLotes as $lote)
                    @php
                        $estadoColors = ['recepcion' => 'bg-blue-100 text-blue-700', 'acondicionamiento' => 'bg-indigo-100 text-indigo-700', 'vapor' => 'bg-teal-100 text-teal-700', 'eto' => 'bg-purple-100 text-purple-700', 'control_calidad' => 'bg-yellow-100 text-yellow-700', 'almacenamiento' => 'bg-green-100 text-green-700', 'finalizado' => 'bg-slate-100 text-slate-600', 'retenido' => 'bg-amber-100 text-amber-700', 'rechazado' => 'bg-red-100 text-red-700'];
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-mono text-xs font-bold text-slate-800">{{ $lote->numero_lote }}</p>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-xs text-slate-600">
                            {{ $lote->institucion?->nombre ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span @class(['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', 'bg-blue-100 text-blue-700' => $lote->metodo_esterilizacion === 'vapor', 'bg-purple-100 text-purple-700' => $lote->metodo_esterilizacion === 'eto'])>
                                {{ strtoupper($lote->metodo_esterilizacion) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-slate-600">
                            {{ $lote->fecha_recepcion?->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $estadoColors[$lote->estado_actual] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ ucfirst(str_replace('_', ' ', $lote->estado_actual)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('auditor.show', $lote->id) }}"
                               class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">
                                Ver →
                            </a>
                        </td>
                    </tr>
                @endforeach
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