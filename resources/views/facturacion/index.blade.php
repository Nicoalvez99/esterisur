@extends('layouts.modulo')

@section('title', 'Facturación')
@section('modulo-label', 'Facturación')
@section('page-title', 'Facturación')
@section('page-subtitle', 'Consolidado de remitos para administración')

@section('sidebar-nav')
    <a href="{{ route('facturacion.index') }}"
       class="nav-op {{ request()->routeIs('facturacion.index') ? 'active' : '' }}">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
        </svg>
        Consolidado
    </a>
@endsection

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-3 gap-3 mb-5">
    <div class="rounded-xl bg-white border border-slate-200 p-4 text-center">
        <p class="text-xs font-medium text-slate-500">Total remitos</p>
        <p class="mt-1 text-2xl font-bold text-slate-700">{{ $stats['total_remitos'] }}</p>
    </div>
    <div class="rounded-xl bg-white border border-amber-200 bg-amber-50 p-4 text-center">
        <p class="text-xs font-medium text-amber-600">Sin facturar</p>
        <p class="mt-1 text-2xl font-bold text-amber-700">{{ $stats['sin_facturar'] }}</p>
    </div>
    <div class="rounded-xl bg-white border border-green-200 bg-green-50 p-4 text-center">
        <p class="text-xs font-medium text-green-600">Facturados</p>
        <p class="mt-1 text-2xl font-bold text-green-700">{{ $stats['facturados'] }}</p>
    </div>
</div>

{{-- Filtros --}}
<form method="GET" id="form-filtros"
      class="mb-5 rounded-xl bg-white border border-slate-200 p-4">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5 mb-3">
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Desde</label>
            <input type="date" name="desde" value="{{ $desde }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Hasta</label>
            <input type="date" name="hasta" value="{{ $hasta }}"
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
            <label class="block text-xs font-medium text-slate-500 mb-1">Método</label>
            <select name="metodo"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
                <option value="">Todos</option>
                <option value="vapor"  {{ request('metodo') === 'vapor' ? 'selected' : '' }}>♨️ Vapor</option>
                <option value="eto"    {{ request('metodo') === 'eto'   ? 'selected' : '' }}>🧪 ETO</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">Estado</label>
            <select name="facturado"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
                <option value="">Todos</option>
                <option value="0" {{ request('facturado') === '0' ? 'selected' : '' }}>Sin facturar</option>
                <option value="1" {{ request('facturado') === '1' ? 'selected' : '' }}>Facturados</option>
            </select>
        </div>
    </div>
    <div class="flex gap-2">
        <button type="submit"
                class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors">
            Filtrar
        </button>
        <a href="{{ route('facturacion.index') }}"
           class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
            Este mes
        </a>
        <button type="button" onclick="window.print()"
                class="ml-auto rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
            🖨️ Imprimir
        </button>
    </div>
</form>

{{-- Resumen por institución --}}
@if($porInstitucion->isNotEmpty())
    <div class="rounded-xl bg-white border border-slate-200 overflow-hidden mb-5">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-800">Resumen por institución</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 bg-slate-50/60">
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Institución</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Remitos</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Cajas</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Bultos</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Unidades</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Litros</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Sin facturar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($porInstitucion as $row)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-3 text-xs font-semibold text-slate-800">
                                {{ $row->institucion?->nombre ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center text-xs font-bold text-slate-700">{{ $row->total_remitos }}</td>
                            <td class="px-4 py-3 text-center text-xs text-slate-600 hidden sm:table-cell">{{ $row->total_cajas }}</td>
                            <td class="px-4 py-3 text-center text-xs text-slate-600 hidden sm:table-cell">{{ $row->total_bultos }}</td>
                            <td class="px-4 py-3 text-center text-xs text-slate-600 hidden md:table-cell">{{ $row->total_unidades }}</td>
                            <td class="px-4 py-3 text-center text-xs text-slate-600 hidden md:table-cell">{{ number_format($row->total_litros, 2) }}</td>
                            <td class="px-4 py-3 text-center">
                                @if($row->sin_facturar > 0)
                                    <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-bold text-amber-700">
                                        {{ $row->sin_facturar }}
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                                        ✅ Al día
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                {{-- Fila de totales --}}
                <tfoot>
                    <tr class="border-t-2 border-slate-200 bg-slate-50 font-bold">
                        <td class="px-5 py-3 text-xs text-slate-700">TOTAL PERÍODO</td>
                        <td class="px-4 py-3 text-center text-xs text-slate-800">{{ $totales['total_remitos'] }}</td>
                        <td class="px-4 py-3 text-center text-xs text-slate-800 hidden sm:table-cell">
                            {{ ($totales['total_cajas_chicas'] ?? 0) + ($totales['total_cajas_medianas'] ?? 0) + ($totales['total_cajas_grandes'] ?? 0) }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-800 hidden sm:table-cell">{{ $totales['total_bultos'] ?? 0 }}</td>
                        <td class="px-4 py-3 text-center text-xs text-slate-800 hidden md:table-cell">{{ $totales['total_unidades'] ?? 0 }}</td>
                        <td class="px-4 py-3 text-center text-xs text-slate-800 hidden md:table-cell">{{ number_format($totales['total_litros'] ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-center text-xs text-slate-800">{{ $stats['sin_facturar'] }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endif

{{-- Tabla de remitos con acción de marcar facturado --}}
<form method="POST" action="{{ route('facturacion.marcar') }}" id="form-facturar">
@csrf

<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
        <h2 class="text-sm font-semibold text-slate-800">Detalle de remitos</h2>
        <div class="flex items-center gap-3">
            <label class="flex items-center gap-1.5 text-xs text-slate-500 cursor-pointer">
                <input type="checkbox" id="select-all" class="accent-teal-500"
                       onchange="toggleTodos(this)" />
                Seleccionar todos
            </label>
            <button type="submit"
                    class="rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-700 transition-colors">
                ✅ Marcar seleccionados como facturados
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-4 py-3 w-8"></th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Remito</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Institución</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Fecha despacho</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Método</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Cajas</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Bultos</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Unid.</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($remitos as $remito)
                    <tr @class([
                        'hover:bg-slate-50/60 transition-colors',
                        'bg-green-50/40' => $remito->facturado,
                    ])>
                        <td class="px-4 py-3 text-center">
                            @if(!$remito->facturado)
                                <input type="checkbox" name="remito_ids[]"
                                       value="{{ $remito->id }}"
                                       class="accent-teal-500 remito-check" />
                            @else
                                <span class="text-green-500 text-xs">✅</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-mono text-xs font-bold text-slate-800">{{ $remito->numero }}</p>
                            <p class="text-xs text-slate-400 font-mono">{{ $remito->lote?->numero_lote }}</p>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-xs text-slate-700">
                            {{ $remito->institucion?->nombre ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-slate-600">
                            {{ $remito->fecha_despacho?->format('d/m/Y H:i') ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span @class([
                                'inline-flex rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-blue-100 text-blue-700'     => $remito->lote?->metodo_esterilizacion === 'vapor',
                                'bg-purple-100 text-purple-700' => $remito->lote?->metodo_esterilizacion === 'eto',
                            ])>
                                {{ strtoupper($remito->lote?->metodo_esterilizacion ?? '—') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-600 hidden lg:table-cell">
                            {{ $remito->cant_cajas_chicas + $remito->cant_cajas_medianas + $remito->cant_cajas_grandes }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-600 hidden lg:table-cell">
                            {{ $remito->cant_bultos }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-slate-600 hidden lg:table-cell">
                            {{ $remito->cant_unidades }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($remito->facturado)
                                <div>
                                    <span class="inline-flex rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                                        Facturado
                                    </span>
                                    <form method="POST"
                                          action="{{ route('facturacion.desmarcar', $remito->id) }}"
                                          class="mt-1"
                                          onsubmit="return confirm('¿Desmarcar como facturado?')">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                                class="text-xs text-slate-400 hover:text-red-500 transition-colors">
                                            Desmarcar
                                        </button>
                                    </form>
                                </div>
                            @else
                                <span class="inline-flex rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                                    Pendiente
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-5 py-12 text-center">
                            <span class="text-3xl block mb-2">📄</span>
                            <p class="text-sm font-medium text-slate-500">Sin remitos para este período y filtros</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($remitos->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">
            {{ $remitos->links() }}
        </div>
    @endif
</div>

</form>

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    .nav-op.active { background:rgb(20 184 166/.1); color:rgb(15 118 110); }

    @media print {
        .nav-op, button, form#form-facturar input[type=checkbox], .pagination { display: none !important; }
        body { background: white !important; }
    }
</style>

<script>
    function toggleTodos(master) {
        document.querySelectorAll('.remito-check').forEach(function(cb) {
            cb.checked = master.checked;
        });
    }
</script>

@endsection