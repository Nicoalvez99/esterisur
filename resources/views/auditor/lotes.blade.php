@extends('layouts.modulo')

@section('title', 'Lotes')
@section('modulo-label', 'Auditoría')
@section('page-title', 'Consulta de lotes')
@section('page-subtitle', 'Solo lectura')

@section('sidebar-nav')
    <a href="{{ route('auditor.index') }}" class="nav-op {{ request()->routeIs('auditor.index') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
        </svg>
        Panel
    </a>
    <a href="{{ route('auditor.lotes') }}" class="nav-op {{ request()->routeIs('auditor.lotes') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
        </svg>
        Lotes
    </a>
    <a href="{{ route('auditor.historial') }}" class="nav-op {{ request()->routeIs('auditor.historial') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        Historial
    </a>
@endsection

@section('content')

{{-- Filtros --}}
<form method="GET" class="mb-5 rounded-xl bg-white border border-slate-200 p-4">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6 mb-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">N° de lote</label>
            <input type="text" name="numero_lote" value="{{ request('numero_lote') }}"
                   placeholder="LOT-..."
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Institución</label>
            <select name="institucion_id"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
                <option value="">Todas</option>
                @foreach($instituciones as $inst)
                    <option value="{{ $inst->id }}" {{ request('institucion_id') == $inst->id ? 'selected' : '' }}>
                        {{ $inst->nombre }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Estado</label>
            <select name="estado"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
                <option value="">Todos</option>
                @foreach(\App\Models\Lote::ESTADOS as $key => $label)
                    <option value="{{ $key }}" {{ request('estado') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Método</label>
            <select name="metodo"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
                <option value="">Todos</option>
                <option value="vapor"  {{ request('metodo') === 'vapor' ? 'selected' : '' }}>♨️ Vapor</option>
                <option value="eto"    {{ request('metodo') === 'eto'   ? 'selected' : '' }}>🧪 ETO</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Desde</label>
            <input type="date" name="desde" value="{{ request('desde') }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Hasta</label>
            <input type="date" name="hasta" value="{{ request('hasta') }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
        </div>
    </div>
    <div class="flex gap-2">
        <button type="submit"
                class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors">
            Filtrar
        </button>
        <a href="{{ route('auditor.lotes') }}"
           class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
            Limpiar
        </a>
        <span class="ml-auto text-xs text-slate-400 flex items-center">
            {{ $lotes->total() }} resultado{{ $lotes->total() !== 1 ? 's' : '' }}
        </span>
    </div>
</form>

{{-- Tabla --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Institución</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Método</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Recepción</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Entrega pactada</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Ver</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($lotes as $lote)
                    @php
                        $estadoColors = [
                            'recepcion'        => 'bg-blue-100 text-blue-700',
                            'acondicionamiento'=> 'bg-indigo-100 text-indigo-700',
                            'vapor'            => 'bg-teal-100 text-teal-700',
                            'eto'              => 'bg-purple-100 text-purple-700',
                            'control_calidad'  => 'bg-yellow-100 text-yellow-700',
                            'almacenamiento'   => 'bg-green-100 text-green-700',
                            'finalizado'       => 'bg-slate-100 text-slate-600',
                            'retenido'         => 'bg-amber-100 text-amber-700',
                            'rechazado'        => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3">
                            <p class="font-mono text-xs font-bold text-slate-800">{{ $lote->numero_lote }}</p>
                            @if($lote->prioridad !== 'normal')
                                <span @class(['inline-flex rounded-full px-1.5 py-0.5 text-xs font-bold mt-0.5', 'bg-amber-500 text-white' => $lote->prioridad === 'urgente', 'bg-red-600 text-white' => $lote->prioridad === 'critica'])>
                                    {{ ucfirst($lote->prioridad) }}
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-xs text-slate-600">
                            {{ $lote->institucion?->nombre ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span @class(['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', 'bg-blue-100 text-blue-700' => $lote->metodo_esterilizacion === 'vapor', 'bg-purple-100 text-purple-700' => $lote->metodo_esterilizacion === 'eto'])>
                                {{ strtoupper($lote->metodo_esterilizacion) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-500">
                            {{ $lote->fecha_recepcion?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($lote->fecha_entrega_pactada)
                                <span @class(['text-xs font-medium', 'text-red-600' => $lote->entregaVencida(), 'text-slate-600' => !$lote->entregaVencida()])>
                                    {{ $lote->fecha_entrega_pactada->format('d/m/Y') }}
                                    @if($lote->entregaVencida()) ⚠️ @endif
                                </span>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
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
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <span class="text-3xl block mb-2">🔍</span>
                            <p class="text-sm text-slate-400">Sin resultados para este filtro</p>
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