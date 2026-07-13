@extends('layouts.modulo')

@section('title', 'Acondicionamiento — ' . $lote->numero_lote)
@section('modulo-label', 'Acondicionamiento')
@section('page-title', $lote->numero_lote)
@section('page-subtitle', 'Detalle del acondicionamiento')

@section('sidebar-nav')
    <a href="{{ route('acondicionamiento.index') }}" class="nav-op">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver al listado
    </a>
@endsection

@section('content')

@php $acond = $lote->acondicionamiento; @endphp

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- COLUMNA PRINCIPAL --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Estado y resultado --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $resultados = [
                        'acondicionado'    => ['bg-green-100 text-green-700', 'bg-green-500', '✅ Acondicionado'],
                        'con_devoluciones' => ['bg-amber-100 text-amber-700', 'bg-amber-500', '⚠️ Con devoluciones'],
                        'retenido'         => ['bg-red-100 text-red-700',     'bg-red-500',   '🔴 Retenido'],
                    ];
                    [$badge, $dot, $label] = $resultados[$acond?->resultado ?? 'acondicionado'];
                @endphp
                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold {{ $badge }}">
                    <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
                    {{ $label }}
                </span>

                {{-- Estado actual del lote --}}
                <span class="text-xs text-slate-400">
                    Lote ahora en: <strong class="text-slate-700 uppercase">{{ str_replace('_', ' ', $lote->estado_actual) }}</strong>
                </span>
            </div>
        </div>

        {{-- Resumen de conteo --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800">Conteo</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-4">
                @foreach([
                    ['label' => 'Declarado',  'value' => $acond?->cant_declarada ?? 0, 'color' => 'text-slate-700', 'bg' => 'bg-slate-50'],
                    ['label' => 'Real',        'value' => $acond?->cant_real ?? 0,       'color' => 'text-slate-700', 'bg' => 'bg-slate-50'],
                    ['label' => 'Diferencia',
                     'value' => ($acond?->diferencia >= 0 ? '+' : '') . ($acond?->diferencia ?? 0),
                     'color' => ($acond?->diferencia ?? 0) === 0 ? 'text-green-600' : (($acond?->diferencia ?? 0) < 0 ? 'text-red-600' : 'text-amber-600'),
                     'bg'    => ($acond?->diferencia ?? 0) === 0 ? 'bg-green-50' : (($acond?->diferencia ?? 0) < 0 ? 'bg-red-50' : 'bg-amber-50')],
                    ['label' => 'Devueltos',  'value' => $acond?->cant_devuelto ?? 0,   'color' => 'text-amber-700', 'bg' => 'bg-amber-50'],
                ] as $c)
                    <div class="rounded-lg {{ $c['bg'] }} border border-slate-100 p-3 text-center">
                        <p class="text-xs text-slate-500 font-medium">{{ $c['label'] }}</p>
                        <p class="mt-1 text-xl font-bold {{ $c['color'] }}">{{ $c['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 text-center text-xs">
                <div class="rounded-md bg-green-50 py-2">
                    <p class="text-green-600 font-semibold">{{ $acond?->cant_limpio ?? 0 }} limpios</p>
                </div>
                <div class="rounded-md bg-red-50 py-2">
                    <p class="text-red-600 font-semibold">{{ $acond?->cant_sucio ?? 0 }} sucios</p>
                </div>
                <div class="rounded-md bg-blue-50 py-2">
                    <p class="text-blue-600 font-semibold">{{ $acond?->cant_integro ?? 0 }} íntegros</p>
                </div>
                <div class="rounded-md bg-slate-50 py-2">
                    <p class="text-slate-600 font-semibold">{{ $acond?->cant_roto ?? 0 }} rotos</p>
                </div>
            </div>
        </div>

        {{-- Detalle de ítems --}}
        @if($acond?->items->count())
            <div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-semibold text-slate-800">Ítems registrados</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/60">
                                <th class="px-4 py-2.5 text-left font-semibold text-slate-500 uppercase tracking-wide">Dispositivo</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-slate-500 uppercase tracking-wide">Decl.</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-slate-500 uppercase tracking-wide">Real</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Limpieza</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Integridad</th>
                                <th class="px-3 py-2.5 text-center font-semibold text-slate-500 uppercase tracking-wide">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($acond->items as $item)
                                <tr @class([
                                    'hover:bg-slate-50/60 transition-colors',
                                    'bg-amber-50/50' => $item->accion === 'devolver',
                                    'bg-red-50/50'   => $item->accion === 'retener',
                                ])>
                                    <td class="px-4 py-2.5">
                                        <p class="font-medium text-slate-800">{{ $item->nombre }}</p>
                                        @if($item->motivo_devolucion)
                                            <p class="text-slate-400 mt-0.5">↳ {{ $item->motivo_devolucion }}</p>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2.5 text-center text-slate-600">{{ $item->cant_declarada }}</td>
                                    <td class="px-3 py-2.5 text-center font-semibold {{ $item->cant_real !== $item->cant_declarada ? 'text-amber-600' : 'text-slate-700' }}">
                                        {{ $item->cant_real }}
                                    </td>
                                    <td class="px-3 py-2.5 text-center hidden sm:table-cell">
                                        <span @class([
                                            'inline-flex items-center rounded-full px-2 py-0.5 font-medium',
                                            'bg-green-100 text-green-700' => $item->estado_limpieza === 'limpio',
                                            'bg-red-100 text-red-700'     => $item->estado_limpieza === 'sucio',
                                        ])>
                                            {{ $item->estado_limpieza === 'limpio' ? '✅ Limpio' : '🔴 Sucio' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center hidden sm:table-cell">
                                        <span @class([
                                            'inline-flex items-center rounded-full px-2 py-0.5 font-medium',
                                            'bg-green-100 text-green-700' => $item->estado_integridad === 'integro',
                                            'bg-red-100 text-red-700'     => $item->estado_integridad === 'roto',
                                        ])>
                                            {{ $item->estado_integridad === 'integro' ? '✅ Íntegro' : '🔴 Roto' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2.5 text-center">
                                        <span @class([
                                            'inline-flex items-center rounded-full px-2 py-0.5 font-medium',
                                            'bg-teal-100 text-teal-700'   => $item->accion === 'procesar',
                                            'bg-amber-100 text-amber-700' => $item->accion === 'devolver',
                                            'bg-red-100 text-red-700'     => $item->accion === 'retener',
                                        ])>
                                            {{ ucfirst($item->accion) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Historial --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800">Historial del lote</h2>
            @if($lote->historial->count())
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

        {{-- Número de lote --}}
        <div class="rounded-xl bg-slate-900 text-white p-5 text-center">
            <p class="text-xs text-slate-400 mb-1">Lote</p>
            <p class="font-mono text-xl font-bold tracking-wider">{{ $lote->numero_lote }}</p>
        </div>

        {{-- Empaque --}}
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs font-semibold text-slate-600 mb-3">Empaque aplicado</p>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <dt class="text-slate-400">Tipo</dt>
                    <dd class="font-semibold text-slate-700">{{ ucfirst(str_replace('_', ' ', $acond?->tipo_empaque ?? '—')) }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Cantidad</dt>
                    <dd class="font-semibold text-slate-700">{{ $acond?->cant_empaque ?? 0 }}</dd>
                </div>
                @if($acond?->empaque_detalle)
                    <div>
                        <dt class="text-slate-400 mb-0.5">Detalle</dt>
                        <dd class="text-slate-600">{{ $acond->empaque_detalle }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Operario y fechas --}}
        <div class="rounded-xl bg-white border border-slate-200 p-4 space-y-2 text-xs">
            <p class="font-semibold text-slate-600">Registro</p>
            <p class="text-slate-500">Operario: <span class="font-medium text-slate-700">{{ $acond?->operario?->name ?? '—' }}</span></p>
            <p class="text-slate-500">Fecha: <span class="font-medium text-slate-700">{{ $acond?->created_at?->format('d/m/Y H:i') ?? '—' }}</span></p>
        </div>

        @if($acond?->observaciones)
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-600 mb-1">Observaciones</p>
                <p class="text-xs text-slate-600 leading-relaxed">{{ $acond->observaciones }}</p>
            </div>
        @endif

        <a href="{{ route('acondicionamiento.index') }}"
           class="block w-full rounded-xl border border-slate-200 px-6 py-2.5 text-center text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
            ← Volver al listado
        </a>
    </div>
</div>

<style>
    .nav-op {
        display: flex; align-items: center; gap: .625rem; padding: .5rem .75rem;
        border-radius: .5rem; font-size: .875rem; font-weight: 500;
        color: rgb(100 116 139); transition: all .15s; text-decoration: none;
    }
    .nav-op:hover { background: rgb(241 245 249); color: rgb(30 41 59); }
</style>

@endsection