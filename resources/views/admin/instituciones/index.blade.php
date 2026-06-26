@extends('layouts.admin')

@section('title', 'Instituciones')
@section('page-title', 'Instituciones')
@section('page-subtitle', 'Clientes y centros de salud')

@section('content')

{{-- Header --}}
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
    <p class="text-xs text-slate-500">
        {{ $instituciones->total() }} institución{{ $instituciones->total() !== 1 ? 'es' : '' }} registrada{{ $instituciones->total() !== 1 ? 's' : '' }}
    </p>
    <a href="{{ route('admin.instituciones.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nueva institución
    </a>
</div>

{{-- Filtros --}}
<form method="GET" class="mb-5 flex flex-col gap-3 sm:flex-row rounded-xl bg-white border border-slate-200 p-4">
    <div class="flex-1">
        <input type="text" name="buscar" value="{{ request('buscar') }}"
               placeholder="Buscar por nombre, CUIT o código..."
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500" />
    </div>
    <div>
        <select name="activo" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
            <option value="">Todas</option>
            <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activas</option>
            <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivas</option>
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors">
            Filtrar
        </button>
        <a href="{{ route('admin.instituciones.index') }}"
           class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
            Limpiar
        </a>
    </div>
</form>

{{-- Tabla --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Institución</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">CUIT</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Ciudad</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Contacto</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($instituciones as $inst)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-xs font-bold text-slate-600">
                                    {{ strtoupper(substr($inst->nombre, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-semibold text-slate-800 text-xs">{{ $inst->nombre }}</p>
                                    @if($inst->codigo)
                                        <p class="text-xs text-slate-400 font-mono">{{ $inst->codigo }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="font-mono text-xs text-slate-600">{{ $inst->cuit ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-600">
                            {{ collect([$inst->ciudad, $inst->provincia])->filter()->implode(', ') ?: '—' }}
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <div class="text-xs text-slate-600">
                                @if($inst->telefono)
                                    <p>{{ $inst->telefono }}</p>
                                @endif
                                @if($inst->email)
                                    <p class="text-slate-400">{{ $inst->email }}</p>
                                @endif
                                @if(!$inst->telefono && !$inst->email)
                                    <span class="text-slate-400">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium',
                                'bg-green-100 text-green-700' => $inst->activo,
                                'bg-slate-100 text-slate-500' => !$inst->activo,
                            ])>
                                <span @class([
                                    'h-1.5 w-1.5 rounded-full',
                                    'bg-green-500' => $inst->activo,
                                    'bg-slate-400' => !$inst->activo,
                                ])></span>
                                {{ $inst->activo ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.instituciones.edit', $inst->id) }}"
                                   class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">
                                    Editar
                                </a>
                                @if(!$inst->activo)
                                    <form method="POST" action="{{ route('admin.instituciones.destroy', $inst->id) }}"
                                          onsubmit="return confirm('¿Eliminar esta institución?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">
                                            Eliminar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                                </svg>
                                <p class="text-sm font-medium text-slate-500">No hay instituciones registradas</p>
                                <a href="{{ route('admin.instituciones.create') }}" class="text-xs font-medium text-teal-600 hover:underline">
                                    Crear la primera institución
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($instituciones->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">
            {{ $instituciones->links() }}
        </div>
    @endif
</div>

@endsection