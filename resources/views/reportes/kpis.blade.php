@extends('layouts.modulo')
@section('title', 'KPIs')
@section('modulo-label', 'Reportes')
@section('page-title', 'KPIs operativos')
@section('page-subtitle', 'Indicadores de productividad y calidad')
@section('sidebar-nav')
    <a href="{{ route('reportes.index') }}" class="nav-op">← Panel</a>
@endsection

@section('content')

{{-- Filtros --}}
<form method="GET" class="mb-6 rounded-xl bg-white border border-slate-200 p-4 flex flex-wrap gap-3">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Desde</label>
        <input type="date" name="desde" value="{{ $desde }}"
               class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Hasta</label>
        <input type="date" name="hasta" value="{{ $hasta }}"
               class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors">
            Filtrar
        </button>
        <button type="button" onclick="window.print()"
                class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
            🖨️ Imprimir
        </button>
    </div>
</form>

{{-- KPIs principales --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4 mb-6">
    @foreach([
        ['label' => 'Total lotes',        'value' => $kpis['total_lotes'],                                'color' => 'text-slate-700',  'bg' => 'bg-slate-50',   'icon' => '📦'],
        ['label' => 'Finalizados',         'value' => $kpis['finalizados'],                               'color' => 'text-green-600',  'bg' => 'bg-green-50',   'icon' => '✅'],
        ['label' => 'Rechazados',          'value' => $kpis['rechazados'],                                'color' => 'text-red-600',    'bg' => 'bg-red-50',     'icon' => '❌'],
        ['label' => 'Retenidos',           'value' => $kpis['retenidos'],                                 'color' => 'text-amber-600',  'bg' => 'bg-amber-50',   'icon' => '⚠️'],
        ['label' => 'Vapor',               'value' => $kpis['vapor'],                                     'color' => 'text-blue-600',   'bg' => 'bg-blue-50',    'icon' => '♨️'],
        ['label' => 'ETO',                 'value' => $kpis['eto'],                                       'color' => 'text-purple-600', 'bg' => 'bg-purple-50',  'icon' => '🧪'],
        ['label' => 'Tasa de rechazo',     'value' => $kpis['tasa_rechazo'] . '%',                       'color' => $kpis['tasa_rechazo'] > 5 ? 'text-red-600' : 'text-green-600', 'bg' => 'bg-slate-50', 'icon' => '📊'],
        ['label' => 'Entrega a tiempo',    'value' => ($kpis['cumplimiento_entregas'] ?? '—') . ($kpis['cumplimiento_entregas'] !== null ? '%' : ''), 'color' => 'text-teal-600', 'bg' => 'bg-teal-50', 'icon' => '🚚'],
    ] as $k)
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs font-medium text-slate-500">{{ $k['label'] }}</p>
                    <p class="mt-1 text-2xl font-bold {{ $k['color'] }}">{{ $k['value'] }}</p>
                </div>
                <span class="text-2xl">{{ $k['icon'] }}</span>
            </div>
        </div>
    @endforeach
</div>

{{-- Tiempos y calidad --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
    <div class="rounded-xl bg-white border border-slate-200 p-5 text-center">
        <p class="text-xs font-medium text-slate-500 mb-1">Tiempo promedio proceso</p>
        <p class="text-3xl font-bold text-slate-700">
            {{ $kpis['tiempo_promedio_horas'] ? $kpis['tiempo_promedio_horas'] . 'h' : '—' }}
        </p>
        <p class="text-xs text-slate-400 mt-1">Desde recepción hasta finalización</p>
    </div>
    <div class="rounded-xl bg-white border border-slate-200 p-5 text-center">
        <p class="text-xs font-medium text-slate-500 mb-1">Controles no conformes</p>
        <p class="text-3xl font-bold {{ $kpis['tasa_no_conforme'] > 2 ? 'text-red-600' : 'text-green-600' }}">
            {{ $kpis['tasa_no_conforme'] }}%
        </p>
        <p class="text-xs text-slate-400 mt-1">{{ $kpis['controles_no_conformes'] }} de {{ $kpis['controles_total'] }} controles</p>
    </div>
    <div class="rounded-xl bg-white border border-slate-200 p-5 text-center">
        <p class="text-xs font-medium text-slate-500 mb-1">Distribución métodos</p>
        <div class="flex items-center gap-2 mt-2 justify-center">
            @if($kpis['total_lotes'] > 0)
                <div class="flex-1 h-6 rounded-full overflow-hidden bg-purple-100 flex">
                    <div class="bg-blue-500 h-full rounded-full transition-all"
                         style="width: {{ $kpis['total_lotes'] > 0 ? round($kpis['vapor'] / $kpis['total_lotes'] * 100) : 0 }}%"></div>
                </div>
            @endif
        </div>
        <div class="flex justify-center gap-4 mt-2 text-xs">
            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-blue-500"></span> Vapor {{ $kpis['vapor'] }}</span>
            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-purple-400"></span> ETO {{ $kpis['eto'] }}</span>
        </div>
    </div>
</div>

{{-- Gráfico de lotes por día --}}
@if($lotesPorDia->isNotEmpty())
    <div class="rounded-xl bg-white border border-slate-200 p-5">
        <h2 class="text-sm font-semibold text-slate-800 mb-4">Lotes recibidos por día</h2>
        @php $maxDia = $lotesPorDia->max('total'); @endphp
        <div class="space-y-2">
            @foreach($lotesPorDia as $dia)
                <div class="flex items-center gap-3">
                    <span class="w-20 shrink-0 text-xs text-slate-500 text-right">
                        {{ \Carbon\Carbon::parse($dia->dia)->locale('es')->isoFormat('D MMM') }}
                    </span>
                    <div class="flex-1 h-6 rounded-md bg-slate-100 overflow-hidden">
                        <div class="bg-teal-500 h-full rounded-md transition-all flex items-center pl-2"
                             style="width: {{ $maxDia > 0 ? ($dia->total / $maxDia * 100) : 0 }}%">
                        </div>
                    </div>
                    <span class="w-6 shrink-0 text-xs font-bold text-slate-700 text-right">{{ $dia->total }}</span>
                </div>
            @endforeach
        </div>
    </div>
@endif

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    @media print { form, button { display:none!important; } }
</style>

@endsection