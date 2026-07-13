@extends('layouts.modulo')

@section('title', 'No conformidades')
@section('modulo-label', 'No conformidades')
@section('page-title', 'No conformidades')
@section('page-subtitle', 'Incidencias y acciones correctivas')

@section('sidebar-nav')
    <a href="{{ route('no-conformidades.index') }}"
       class="nav-op {{ request()->routeIs('no-conformidades.index') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
        </svg>
        Listado
    </a>
    <a href="{{ route('no-conformidades.create') }}"
       class="nav-op {{ request()->routeIs('no-conformidades.create') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nueva
    </a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-5">
    @foreach([
        ['label' => 'Abiertas',      'value' => $stats['abiertas'],     'color' => 'text-red-600',    'bg' => 'bg-red-50',    'icon' => '🔴'],
        ['label' => 'En proceso',    'value' => $stats['en_proceso'],   'color' => 'text-amber-600',  'bg' => 'bg-amber-50',  'icon' => '⏳'],
        ['label' => 'Cerradas hoy',  'value' => $stats['cerradas_hoy'],'color' => 'text-green-600',  'bg' => 'bg-green-50',  'icon' => '✅'],
        ['label' => 'Total',         'value' => $stats['total'],        'color' => 'text-slate-600',  'bg' => 'bg-slate-50',  'icon' => '📋'],
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

{{-- Filtros --}}
<form method="GET" class="mb-5 rounded-xl bg-white border border-slate-200 p-4 flex flex-wrap gap-3">
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Estado</label>
        <select name="estado"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
            <option value="">Todos</option>
            <option value="abierta"    {{ request('estado') === 'abierta'    ? 'selected' : '' }}>🔴 Abiertas</option>
            <option value="en_proceso" {{ request('estado') === 'en_proceso' ? 'selected' : '' }}>⏳ En proceso</option>
            <option value="cerrada"    {{ request('estado') === 'cerrada'    ? 'selected' : '' }}>✅ Cerradas</option>
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Tipo</label>
        <select name="tipo"
                class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
            <option value="">Todos</option>
            @foreach(\App\Models\NoConformidad::TIPOS as $key => $label)
                <option value="{{ $key }}" {{ request('tipo') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Desde</label>
        <input type="date" name="desde" value="{{ request('desde') }}"
               class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
    </div>
    <div>
        <label class="block text-xs font-medium text-slate-500 mb-1">Hasta</label>
        <input type="date" name="hasta" value="{{ request('hasta') }}"
               class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
    </div>
    <div class="flex items-end gap-2">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors">Filtrar</button>
        <a href="{{ route('no-conformidades.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">Limpiar</a>
    </div>
    <div class="flex items-end ml-auto">
        <a href="{{ route('no-conformidades.create') }}"
           class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Nueva incidencia
        </a>
    </div>
</form>

{{-- Tabla --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Incidencia</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Institución</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Registrado por</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Ver</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($noConformidades as $nc)
                    <tr @class(['hover:bg-slate-50/60 transition-colors', 'bg-red-50/30' => $nc->estado === 'abierta'])>
                        <td class="px-5 py-3">
                            <p class="text-xs font-semibold text-slate-800">{{ $nc->tipo_label }}</p>
                            <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $nc->descripcion }}</p>
                            <p class="text-xs text-slate-400">{{ $nc->created_at->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <p class="font-mono text-xs font-bold text-slate-700">{{ $nc->lote?->numero_lote ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-slate-600">
                            {{ $nc->lote?->institucion?->nombre ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-600">
                            {{ $nc->registradoPor?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-red-100 text-red-700'    => $nc->estado === 'abierta',
                                'bg-amber-100 text-amber-700'=> $nc->estado === 'en_proceso',
                                'bg-green-100 text-green-700'=> $nc->estado === 'cerrada',
                            ])>
                                {{ match($nc->estado) { 'abierta' => '🔴 Abierta', 'en_proceso' => '⏳ En proceso', 'cerrada' => '✅ Cerrada', default => $nc->estado } }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('no-conformidades.show', $nc->id) }}"
                               class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">
                                Ver →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <span class="text-4xl block mb-2">✅</span>
                            <p class="text-sm text-slate-400">Sin incidencias para este filtro</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($noConformidades->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">{{ $noConformidades->links() }}</div>
    @endif
</div>

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    .nav-op.active { background:rgb(20 184 166/.1); color:rgb(15 118 110); }
</style>

@endsection