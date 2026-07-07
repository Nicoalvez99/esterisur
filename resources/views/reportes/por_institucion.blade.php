@extends('layouts.modulo')
@section('title', 'Por institución')
@section('modulo-label', 'Reportes')
@section('page-title', 'Informe por institución')
@section('page-subtitle', 'Material procesado por cliente')
@section('sidebar-nav')
    <a href="{{ route('reportes.index') }}" class="nav-op">← Panel</a>
@endsection

@section('content')

<form method="GET" class="mb-5 rounded-xl bg-white border border-slate-200 p-4">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Desde <span class="text-red-500">*</span></label>
            <input type="date" name="desde" value="{{ $desde ?? now()->startOfMonth()->toDateString() }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Hasta <span class="text-red-500">*</span></label>
            <input type="date" name="hasta" value="{{ $hasta ?? now()->toDateString() }}" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1">Institución</label>
            <select name="institucion_id" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
                <option value="">Todas</option>
                @foreach($instituciones as $inst)
                    <option value="{{ $inst->id }}" {{ ($institucionId ?? '') == $inst->id ? 'selected' : '' }}>{{ $inst->nombre }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors">Generar informe</button>
        <button type="button" onclick="window.print()" class="ml-auto rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">🖨️ Imprimir</button>
    </div>
</form>

@if(isset($porInstitucion))
    @if($porInstitucion->isEmpty())
        <div class="rounded-xl bg-white border border-slate-200 p-10 text-center">
            <p class="text-sm text-slate-400">Sin datos para este período</p>
        </div>
    @else
        <div class="space-y-5">
            @foreach($porInstitucion as $data)
                <div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 bg-slate-50/60">
                        <div>
                            <p class="text-sm font-bold text-slate-800">{{ $data['institucion']?->nombre ?? '—' }}</p>
                            @if($data['institucion']?->cuit)
                                <p class="text-xs text-slate-400 font-mono">CUIT: {{ $data['institucion']->cuit }}</p>
                            @endif
                        </div>
                        <div class="flex gap-3 text-xs">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 font-semibold text-slate-700">{{ $data['total'] }} lotes</span>
                            <span class="rounded-full bg-green-100 px-2.5 py-1 font-semibold text-green-700">{{ $data['finalizados'] }} entregados</span>
                            @if($data['rechazados'] > 0)
                                <span class="rounded-full bg-red-100 px-2.5 py-1 font-semibold text-red-700">{{ $data['rechazados'] }} rechazados</span>
                            @endif
                        </div>
                    </div>
                    <div class="px-5 py-4">
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-4">
                            @foreach([
                                ['label' => 'Total lotes', 'value' => $data['total'], 'color' => 'text-slate-700'],
                                ['label' => 'Vapor',       'value' => $data['vapor'], 'color' => 'text-blue-600'],
                                ['label' => 'ETO',         'value' => $data['eto'],   'color' => 'text-purple-600'],
                                ['label' => 'Retenidos',   'value' => $data['retenidos'], 'color' => 'text-amber-600'],
                            ] as $s)
                                <div class="rounded-lg bg-slate-50 p-3 text-center">
                                    <p class="text-xs text-slate-400">{{ $s['label'] }}</p>
                                    <p class="text-lg font-bold {{ $s['color'] }} mt-0.5">{{ $s['value'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Tabla de lotes --}}
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="border-b border-slate-100">
                                        <th class="pb-2 text-left font-semibold text-slate-500 uppercase tracking-wide">Lote</th>
                                        <th class="pb-2 text-left font-semibold text-slate-500 uppercase tracking-wide">Método</th>
                                        <th class="pb-2 text-left font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Recepción</th>
                                        <th class="pb-2 text-left font-semibold text-slate-500 uppercase tracking-wide hidden sm:table-cell">Entrega pactada</th>
                                        <th class="pb-2 text-center font-semibold text-slate-500 uppercase tracking-wide">Estado</th>
                                        <th class="pb-2 text-center font-semibold text-slate-500 uppercase tracking-wide hidden md:table-cell">Remito</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50">
                                    @foreach($data['lotes'] as $lote)
                                        <tr class="hover:bg-slate-50/60">
                                            <td class="py-2 font-mono font-bold text-slate-800">{{ $lote->numero_lote }}</td>
                                            <td class="py-2">
                                                <span @class(['inline-flex rounded-full px-1.5 py-0.5 font-semibold', 'bg-blue-100 text-blue-700' => $lote->metodo_esterilizacion === 'vapor', 'bg-purple-100 text-purple-700' => $lote->metodo_esterilizacion === 'eto'])>
                                                    {{ strtoupper($lote->metodo_esterilizacion) }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-slate-600 hidden sm:table-cell">{{ $lote->fecha_recepcion?->format('d/m/Y') }}</td>
                                            <td class="py-2 hidden sm:table-cell">
                                                @if($lote->fecha_entrega_pactada)
                                                    <span @class(['font-medium', 'text-red-600' => $lote->entregaVencida() && $lote->estado_actual !== 'finalizado', 'text-slate-600' => !($lote->entregaVencida() && $lote->estado_actual !== 'finalizado')])>
                                                        {{ $lote->fecha_entrega_pactada->format('d/m/Y') }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-400">—</span>
                                                @endif
                                            </td>
                                            <td class="py-2 text-center">
                                                <span @class(['inline-flex rounded-full px-2 py-0.5 font-semibold', 'bg-green-100 text-green-700' => $lote->estado_actual === 'finalizado', 'bg-red-100 text-red-700' => $lote->estado_actual === 'rechazado', 'bg-amber-100 text-amber-700' => $lote->estado_actual === 'retenido', 'bg-slate-100 text-slate-600' => !in_array($lote->estado_actual, ['finalizado','rechazado','retenido'])])>
                                                    {{ ucfirst(str_replace('_',' ',$lote->estado_actual)) }}
                                                </span>
                                            </td>
                                            <td class="py-2 text-center hidden md:table-cell font-mono text-slate-600">
                                                {{ $lote->remito?->numero ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endif

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    @media print { form, button { display:none!important; } }
</style>

@endsection