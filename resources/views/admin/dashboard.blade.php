@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Resumen operativo del día')

@section('content')

{{-- ===================== MÉTRICAS PRINCIPALES ===================== --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-2 lg:grid-cols-4 lg:gap-4 mb-6">

    {{-- Lotes activos --}}
    <div class="rounded-xl bg-white border border-slate-200 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Lotes activos</p>
                <p class="mt-1.5 text-2xl font-bold text-slate-800">{{ $lotesActivos ?? 0 }}</p>
                <p class="mt-1 text-xs text-slate-400">En proceso ahora</p>
            </div>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-50">
                <svg class="h-4 w-4 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622
                             a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4
                             M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5
                             c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5
                             c0 .621.504 1.125 1.125 1.125z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Listos para entrega --}}
    <div class="rounded-xl bg-white border border-slate-200 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Para entregar</p>
                <p class="mt-1.5 text-2xl font-bold text-slate-800">{{ $lotesListos ?? 0 }}</p>
                <p class="mt-1 text-xs text-slate-400">Listos para despacho</p>
            </div>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-green-50">
                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Retenidos / con alerta --}}
    <div class="rounded-xl bg-white border border-slate-200 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Retenidos</p>
                <p class="mt-1.5 text-2xl font-bold text-amber-600">{{ $lotesRetenidos ?? 0 }}</p>
                <p class="mt-1 text-xs text-slate-400">Requieren atención</p>
            </div>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50">
                <svg class="h-4 w-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71
                             c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0
                             L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Usuarios activos --}}
    <div class="rounded-xl bg-white border border-slate-200 p-4 lg:p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Usuarios</p>
                <p class="mt-1.5 text-2xl font-bold text-slate-800">{{ $totalUsuarios ?? 0 }}</p>
                <p class="mt-1 text-xs text-slate-400">Personal registrado</p>
            </div>
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-blue-50">
                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0
                             004.121-.952 4.125 4.125 0 00-7.533-2.493
                             M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07
                             M15 19.128v.106A12.318 12.318 0 018.624 21
                             c-2.331 0-4.512-.645-6.374-1.766l-.001-.109
                             a6.375 6.375 0 0111.964-3.07M12 6.375
                             a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z" />
                </svg>
            </div>
        </div>
    </div>
</div>

{{-- ===================== ESTADOS DE LOTES + ACTIVIDAD ===================== --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-3 mb-6">

    {{-- Pipeline de estados --}}
    <div class="lg:col-span-2 rounded-xl bg-white border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-800">Estado del pipeline</h2>
            <span class="text-xs text-slate-400">Lotes en curso hoy</span>
        </div>
        <div class="space-y-2.5">
            @php
                $estados = [
                    ['label' => 'Recepción',         'key' => 'recepcion',         'color' => 'bg-blue-400'],
                    ['label' => 'Acondicionamiento', 'key' => 'acondicionamiento', 'color' => 'bg-indigo-400'],
                    ['label' => 'Esterilización',    'key' => 'vapor',             'color' => 'bg-teal-500'],
                    ['label' => 'Control calidad',   'key' => 'control_calidad',   'color' => 'bg-purple-400'],
                    ['label' => 'Almacenamiento',    'key' => 'almacenamiento',    'color' => 'bg-green-400'],
                    ['label' => 'Entrega',           'key' => 'entrega',           'color' => 'bg-emerald-500'],
                ];
                $maxLotes = max(array_values($lotesPorEstado ?? [1]), 1);
            @endphp
            @foreach($estados as $estado)
                @php $count = $lotesPorEstado[$estado['key']] ?? 0; @endphp
                <div class="flex items-center gap-3">
                    <span class="w-32 shrink-0 text-xs text-slate-500">{{ $estado['label'] }}</span>
                    <div class="flex-1 h-5 rounded-md bg-slate-100 overflow-hidden">
                        <div
                            class="{{ $estado['color'] }} h-full rounded-md transition-all duration-500"
                            style="width: 0%"
                        ></div>
                    </div>
                    <span class="w-6 shrink-0 text-right text-xs font-semibold text-slate-700">{{ $count }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Lotes urgentes / críticos --}}
    <div class="rounded-xl bg-white border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-800">Prioridad alta</h2>
            <span class="inline-flex items-center rounded-full bg-red-50 px-2 py-0.5 text-xs font-medium text-red-700">
                {{ ($lotesUrgentes ?? 0) + ($lotesCriticos ?? 0) }} alertas
            </span>
        </div>
        @if(isset($lotesAlertaDetalle) && count($lotesAlertaDetalle) > 0)
            <ul class="space-y-2">
                @foreach($lotesAlertaDetalle as $lote)
                    <li class="flex items-center justify-between rounded-lg bg-slate-50 px-3 py-2">
                        <div>
                            <p class="text-xs font-semibold text-slate-700">{{ $lote->numero_lote }}</p>
                            <p class="text-xs text-slate-400">{{ $lote->institucion->nombre ?? '—' }}</p>
                        </div>
                        <span @class([
                            'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium',
                            'bg-red-100 text-red-700' => $lote->prioridad === 'critica',
                            'bg-amber-100 text-amber-700' => $lote->prioridad === 'urgente',
                        ])>
                            {{ ucfirst($lote->prioridad) }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @else
            <div class="flex flex-col items-center justify-center h-24 text-center">
                <svg class="h-7 w-7 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs text-slate-400">Sin alertas activas</p>
            </div>
        @endif
    </div>
</div>

{{-- ===================== GESTIÓN DE USUARIOS + ACTIVIDAD ===================== --}}
<div class="grid grid-cols-1 gap-4 lg:grid-cols-5">

    {{-- Tabla de usuarios --}}
    <div class="lg:col-span-3 rounded-xl bg-white border border-slate-200 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-800">Personal del sistema</h2>
            <a href=""
               class="inline-flex items-center gap-1.5 rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-700 transition-colors">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Nuevo usuario
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide">Usuario</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Rol</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Estado</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wide">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($usuarios ?? [] as $usuario)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600">
                                        {{ strtoupper(substr($usuario->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-800 text-xs">{{ $usuario->name }}</p>
                                        <p class="text-xs text-slate-400 hidden sm:block">{{ $usuario->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell">
                                @php
                                    $rolColors = [
                                        'administrador'  => 'bg-purple-100 text-purple-700',
                                        'recepcion'      => 'bg-blue-100 text-blue-700',
                                        'esterilizacion' => 'bg-teal-100 text-teal-700',
                                        'calidad'        => 'bg-green-100 text-green-700',
                                        'despacho'       => 'bg-orange-100 text-orange-700',
                                        'facturacion'    => 'bg-yellow-100 text-yellow-700',
                                        'auditor'        => 'bg-slate-100 text-slate-700',
                                    ];
                                    $rol = $usuario->role ?? 'sin_rol';
                                    $colorClass = $rolColors[$rol] ?? 'bg-slate-100 text-slate-600';
                                @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $colorClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $rol)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <span @class([
                                    'inline-flex h-1.5 w-1.5 rounded-full mr-1.5',
                                    'bg-green-500' => $usuario->activo ?? true,
                                    'bg-slate-300' => !($usuario->activo ?? true),
                                ])></span>
                                <span class="text-xs text-slate-500">
                                    {{ ($usuario->activo ?? true) ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                                   class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">
                                    Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="h-8 w-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                              d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0
                                                 007.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0
                                                 A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                    </svg>
                                    <p class="text-sm text-slate-500">No hay usuarios registrados</p>
                                    <a href="{{ route('admin.usuarios.create') }}"
                                       class="text-xs font-medium text-teal-600 hover:underline">
                                        Crear el primer usuario
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(isset($usuarios) && $usuarios->hasPages())
            <div class="border-t border-slate-100 px-5 py-3">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>

    {{-- Actividad reciente --}}
    <div class="lg:col-span-2 rounded-xl bg-white border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-slate-800">Actividad reciente</h2>
        </div>
        @if(isset($historialReciente) && count($historialReciente) > 0)
            <ol class="relative border-l border-slate-200 space-y-4 ml-2">
                @foreach($historialReciente as $item)
                    <li class="pl-4">
                        <div class="absolute -left-1 mt-1 h-2 w-2 rounded-full bg-teal-400 border-2 border-white"></div>
                        <p class="text-xs font-semibold text-slate-700">{{ $item->accion }}</p>
                        <p class="text-xs text-slate-400">
                            Lote <span class="font-medium text-slate-500">{{ $item->lote->numero_lote ?? '—' }}</span>
                            · {{ $item->user->name ?? '—' }}
                        </p>
                        <p class="text-xs text-slate-400">{{ $item->created_at->diffForHumans() }}</p>
                    </li>
                @endforeach
            </ol>
        @else
            <div class="flex flex-col items-center justify-center h-32 text-center">
                <svg class="h-7 w-7 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-xs text-slate-400">Sin actividad reciente</p>
            </div>
        @endif
    </div>

</div>

@endsection