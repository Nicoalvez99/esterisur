@extends('layouts.modulo')

@section('title', 'Reportes')
@section('modulo-label', 'Reportes')
@section('page-title', 'Reportes')
@section('page-subtitle', 'Trazabilidad e informes del sistema')

@section('sidebar-nav')
    <a href="{{ route('reportes.index') }}" class="nav-op {{ request()->routeIs('reportes.index') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
        </svg>
        Panel
    </a>
    <a href="{{ route('reportes.trazabilidad') }}" class="nav-op {{ request()->routeIs('reportes.trazabilidad') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        Trazabilidad por lote
    </a>
    <a href="{{ route('reportes.por-institucion') }}" class="nav-op {{ request()->routeIs('reportes.por-institucion') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
        </svg>
        Por institución
    </a>
    <a href="{{ route('reportes.controles') }}" class="nav-op {{ request()->routeIs('reportes.controles') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Controles
    </a>
    <a href="{{ route('reportes.devoluciones') }}" class="nav-op {{ request()->routeIs('reportes.devoluciones') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
        </svg>
        Devoluciones
    </a>
    <a href="{{ route('reportes.kpis') }}" class="nav-op {{ request()->routeIs('reportes.kpis') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
        </svg>
        KPIs
    </a>
@endsection

@section('content')

{{-- KPIs del mes --}}
<div class="mb-6">
    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">
        Resumen del mes — {{ now()->locale('es')->isoFormat('MMMM YYYY') }}
    </p>
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        @foreach([
            ['label' => 'Lotes totales',   'value' => $kpis['total_lotes'],      'color' => 'text-slate-700',  'bg' => 'bg-slate-50'],
            ['label' => 'Finalizados',      'value' => $kpis['finalizados'],      'color' => 'text-green-600',  'bg' => 'bg-green-50'],
            ['label' => 'Rechazados',       'value' => $kpis['rechazados'],       'color' => 'text-red-600',    'bg' => 'bg-red-50'],
            ['label' => 'Tasa rechazo',     'value' => $kpis['tasa_rechazo'].'%', 'color' => 'text-red-500',   'bg' => 'bg-red-50'],
            ['label' => 'Entrega a tiempo', 'value' => ($kpis['cumplimiento_entregas'] ?? '—').'%', 'color' => 'text-teal-600', 'bg' => 'bg-teal-50'],
            ['label' => 'Tiempo promedio',  'value' => ($kpis['tiempo_promedio_horas'] ?? '—').'h', 'color' => 'text-blue-600', 'bg' => 'bg-blue-50'],
        ] as $k)
            <div class="rounded-xl bg-white border border-slate-200 p-4 text-center">
                <p class="text-xs font-medium text-slate-500">{{ $k['label'] }}</p>
                <p class="mt-1 text-2xl font-bold {{ $k['color'] }}">{{ $k['value'] }}</p>
            </div>
        @endforeach
    </div>
</div>

{{-- Accesos directos a reportes --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">

    {{-- Trazabilidad --}}
    <a href="{{ route('reportes.trazabilidad') }}"
       class="group rounded-xl bg-white border border-slate-200 p-5 hover:border-teal-300 hover:shadow-sm transition-all">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-teal-50 group-hover:bg-teal-100 transition-colors">
                <svg class="h-5 w-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">Trazabilidad por lote</p>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                    Historial completo de un lote desde recepción hasta entrega. Buscá por número de lote o UUID.
                </p>
            </div>
        </div>
    </a>

    {{-- Por institución --}}
    <a href="{{ route('reportes.por-institucion') }}"
       class="group rounded-xl bg-white border border-slate-200 p-5 hover:border-teal-300 hover:shadow-sm transition-all">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 group-hover:bg-blue-100 transition-colors">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">Informe por institución</p>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                    Material procesado por cliente en un período. Totales de lotes, métodos y estados.
                </p>
            </div>
        </div>
    </a>

    {{-- Controles --}}
    <a href="{{ route('reportes.controles') }}"
       class="group rounded-xl bg-white border border-slate-200 p-5 hover:border-teal-300 hover:shadow-sm transition-all">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-green-50 group-hover:bg-green-100 transition-colors">
                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">Controles de calidad</p>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                    Físicos, químicos y biológicos. Conformes, no conformes y pendientes por período.
                </p>
            </div>
        </div>
    </a>

    {{-- Devoluciones --}}
    <a href="{{ route('reportes.devoluciones') }}"
       class="group rounded-xl bg-white border border-slate-200 p-5 hover:border-teal-300 hover:shadow-sm transition-all">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 group-hover:bg-amber-100 transition-colors">
                <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">Devoluciones</p>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                    Ítems devueltos en acondicionamiento: motivos, cantidades e instituciones.
                </p>
            </div>
        </div>
    </a>

    {{-- KPIs --}}
    <a href="{{ route('reportes.kpis') }}"
       class="group rounded-xl bg-white border border-slate-200 p-5 hover:border-teal-300 hover:shadow-sm transition-all">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-purple-50 group-hover:bg-purple-100 transition-colors">
                <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">KPIs operativos</p>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">
                    Tiempos de proceso, tasas de rechazo, cumplimiento de entregas y productividad.
                </p>
            </div>
        </div>
    </a>

</div>

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    .nav-op.active { background:rgb(20 184 166/.1); color:rgb(15 118 110); }
</style>

@endsection