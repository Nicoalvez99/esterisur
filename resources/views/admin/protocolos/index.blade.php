@extends('layouts.admin')
@section('title', 'Protocolos')
@section('page-title', 'Protocolos')
@section('page-subtitle', 'Configuración de entrega por institución')

@section('content')

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="institucion_id" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
            <option value="">Todas las instituciones</option>
            @foreach($instituciones as $inst)
                <option value="{{ $inst->id }}" {{ request('institucion_id') == $inst->id ? 'selected' : '' }}>{{ $inst->nombre }}</option>
            @endforeach
        </select>
        <select name="metodo" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
            <option value="">Todos los métodos</option>
            <option value="vapor" {{ request('metodo') === 'vapor' ? 'selected' : '' }}>Vapor</option>
            <option value="eto"   {{ request('metodo') === 'eto'   ? 'selected' : '' }}>ETO</option>
            <option value="ambos" {{ request('metodo') === 'ambos' ? 'selected' : '' }}>Ambos</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors">Filtrar</button>
        <a href="{{ route('admin.protocolos.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">Limpiar</a>
    </form>
    <a href="{{ route('admin.protocolos.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nuevo protocolo
    </a>
</div>

<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Institución</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Método</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Empaque</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Traslado</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Vencimiento</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($protocolos as $protocolo)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3">
                            <p class="text-xs font-semibold text-slate-800">{{ $protocolo->institucion->nombre }}</p>
                            @if($protocolo->nombre)
                                <p class="text-xs text-slate-400">{{ $protocolo->nombre }}</p>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <span @class([
                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-blue-100 text-blue-700'    => $protocolo->metodo_permitido === 'vapor',
                                'bg-purple-100 text-purple-700'=> $protocolo->metodo_permitido === 'eto',
                                'bg-teal-100 text-teal-700'    => $protocolo->metodo_permitido === 'ambos',
                            ])>
                                {{ $protocolo->metodo_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-slate-600">
                            {{ $protocolo->getEmpaqueLabel() }}
                            @if($protocolo->empaque_detalle)
                                <span class="text-slate-400">· {{ $protocolo->empaque_detalle }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-600">
                            {{ $protocolo->getTrasladoLabel() }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-center text-xs text-slate-600">
                            {{ $protocolo->vencimiento_dias }} días
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium',
                                'bg-green-100 text-green-700' => $protocolo->activo,
                                'bg-slate-100 text-slate-500' => !$protocolo->activo,
                            ])>
                                <span @class(['h-1.5 w-1.5 rounded-full', 'bg-green-500' => $protocolo->activo, 'bg-slate-400' => !$protocolo->activo])></span>
                                {{ $protocolo->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.protocolos.edit', $protocolo->id) }}"
                                   class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">Editar</a>
                                <form method="POST" action="{{ route('admin.protocolos.destroy', $protocolo->id) }}"
                                      onsubmit="return confirm('¿Eliminar este protocolo?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <span class="text-4xl">📋</span>
                                <p class="text-sm font-medium text-slate-500">No hay protocolos configurados</p>
                                <a href="{{ route('admin.protocolos.create') }}" class="text-xs font-medium text-teal-600 hover:underline">Crear el primer protocolo</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($protocolos->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">{{ $protocolos->links() }}</div>
    @endif
</div>

@endsection