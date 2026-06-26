@extends('layouts.admin')

@section('title', 'Equipos')
@section('page-title', 'Equipos')
@section('page-subtitle', 'Autoclaves y equipos ETO')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-5">
    @foreach([
        ['label' => 'Total',          'value' => $stats['total'],         'color' => 'text-slate-700',  'bg' => 'bg-slate-50'],
        ['label' => 'Activos',        'value' => $stats['activos'],       'color' => 'text-green-600',  'bg' => 'bg-green-50'],
        ['label' => 'Mantenimiento',  'value' => $stats['mantenimiento'], 'color' => 'text-amber-600',  'bg' => 'bg-amber-50'],
        ['label' => 'Val. vencida',   'value' => $stats['val_vencida'],   'color' => 'text-red-600',    'bg' => 'bg-red-50'],
    ] as $s)
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs font-medium text-slate-500">{{ $s['label'] }}</p>
            <p class="mt-1 text-2xl font-bold {{ $s['color'] }}">{{ $s['value'] }}</p>
        </div>
    @endforeach
</div>

{{-- Header + filtros --}}
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-4">
    <form method="GET" class="flex flex-wrap gap-2">
        <select name="metodo" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
            <option value="">Todos los métodos</option>
            <option value="vapor"  {{ request('metodo') === 'vapor' ? 'selected' : '' }}>♨️ Vapor</option>
            <option value="eto"    {{ request('metodo') === 'eto' ? 'selected' : '' }}>🧪 ETO</option>
        </select>
        <select name="estado" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
            <option value="">Todos los estados</option>
            <option value="activo"           {{ request('estado') === 'activo' ? 'selected' : '' }}>Activo</option>
            <option value="inactivo"         {{ request('estado') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            <option value="en_mantenimiento" {{ request('estado') === 'en_mantenimiento' ? 'selected' : '' }}>En mantenimiento</option>
        </select>
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors">Filtrar</button>
        <a href="{{ route('admin.equipos.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">Limpiar</a>
    </form>
    <a href="{{ route('admin.equipos.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nuevo equipo
    </a>
</div>

{{-- Tabla --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Equipo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Método</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Marca / Modelo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Validación</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($equipos as $equipo)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div @class([
                                    'flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-lg',
                                    'bg-blue-50'   => $equipo->metodo === 'vapor',
                                    'bg-purple-50' => $equipo->metodo === 'eto',
                                ])>
                                    {{ $equipo->metodo === 'vapor' ? '♨️' : '🧪' }}
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800">{{ $equipo->nombre }}</p>
                                    @if($equipo->numero_interno)
                                        <p class="text-xs font-mono text-slate-400">{{ $equipo->numero_interno }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <span @class([
                                'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold',
                                'bg-blue-100 text-blue-700'     => $equipo->metodo === 'vapor',
                                'bg-purple-100 text-purple-700' => $equipo->metodo === 'eto',
                            ])>
                                {{ strtoupper($equipo->metodo) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-slate-600">
                            {{ collect([$equipo->marca, $equipo->modelo])->filter()->implode(' · ') ?: '—' }}
                            @if($equipo->capacidad)
                                <span class="text-slate-400">· Cap. {{ $equipo->capacidad }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            @if($equipo->fecha_proxima_validacion)
                                <div class="text-xs">
                                    <p @class([
                                        'font-semibold',
                                        'text-red-600'   => $equipo->validacionVencida(),
                                        'text-green-600' => !$equipo->validacionVencida(),
                                    ])>
                                        {{ $equipo->fecha_proxima_validacion->format('d/m/Y') }}
                                        @if($equipo->validacionVencida()) ⚠️ Vencida @endif
                                    </p>
                                    @if($equipo->fecha_ultima_validacion)
                                        <p class="text-slate-400">Última: {{ $equipo->fecha_ultima_validacion->format('d/m/Y') }}</p>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-slate-400">Sin datos</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium',
                                'bg-green-100 text-green-700'  => $equipo->estado === 'activo',
                                'bg-amber-100 text-amber-700'  => $equipo->estado === 'en_mantenimiento',
                                'bg-slate-100 text-slate-500'  => $equipo->estado === 'inactivo',
                            ])>
                                <span @class([
                                    'h-1.5 w-1.5 rounded-full',
                                    'bg-green-500' => $equipo->estado === 'activo',
                                    'bg-amber-500' => $equipo->estado === 'en_mantenimiento',
                                    'bg-slate-400' => $equipo->estado === 'inactivo',
                                ])></span>
                                {{ match($equipo->estado) {
                                    'activo'           => 'Activo',
                                    'inactivo'         => 'Inactivo',
                                    'en_mantenimiento' => 'Mantenimiento',
                                    default            => $equipo->estado,
                                } }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.equipos.edit', $equipo->id) }}"
                                   class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">Editar</a>
                                @if(!$equipo->esterilizaciones()->exists())
                                    <form method="POST" action="{{ route('admin.equipos.destroy', $equipo->id) }}"
                                          onsubmit="return confirm('¿Eliminar este equipo?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs font-medium text-red-400 hover:text-red-600 transition-colors">Eliminar</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <span class="text-4xl">⚙️</span>
                                <p class="text-sm font-medium text-slate-500">No hay equipos registrados</p>
                                <a href="{{ route('admin.equipos.create') }}" class="text-xs font-medium text-teal-600 hover:underline">Crear el primer equipo</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($equipos->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">{{ $equipos->links() }}</div>
    @endif
</div>

@endsection