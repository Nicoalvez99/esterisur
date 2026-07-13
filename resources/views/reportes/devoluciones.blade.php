@extends('layouts.modulo')
@section('title', 'Devoluciones')
@section('modulo-label', 'Reportes')
@section('page-title', 'Informe de devoluciones')
@section('page-subtitle', 'Ítems devueltos en acondicionamiento')
@section('sidebar-nav')
    <a href="{{ route('reportes.index') }}" class="nav-op">← Panel</a>
@endsection

@section('content')

{{-- Filtros --}}
<form method="GET" class="mb-5 rounded-xl bg-white border border-slate-200 p-4 flex flex-wrap gap-3">
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

{{-- Total --}}
<div class="mb-5 rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 flex items-center gap-3">
    <span class="text-3xl">🔄</span>
    <div>
        <p class="text-sm font-bold text-amber-800">{{ $totalDevoluciones }} devolución{{ $totalDevoluciones !== 1 ? 'es' : '' }} en el período</p>
        <p class="text-xs text-amber-600">Del {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($hasta)->format('d/m/Y') }}</p>
    </div>
</div>

{{-- Tabla --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Ítem devuelto</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Institución</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Limpieza</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Integridad</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Motivo</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Operario</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($items as $item)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3">
                            <p class="text-xs font-semibold text-slate-800">{{ $item->nombre }}</p>
                            <p class="text-xs text-slate-400">Cant: {{ $item->cant_real }}</p>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            <p class="font-mono text-xs font-bold text-slate-700">
                                {{ $item->acondicionamiento?->lote?->numero_lote ?? '—' }}
                            </p>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-slate-600">
                            {{ $item->acondicionamiento?->lote?->institucion?->nombre ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-center hidden sm:table-cell">
                            <span @class([
                                'inline-flex rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $item->estado_limpieza === 'limpio',
                                'bg-red-100 text-red-700'     => $item->estado_limpieza === 'sucio',
                            ])>
                                {{ $item->estado_limpieza === 'limpio' ? '✅ Limpio' : '🔴 Sucio' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center hidden sm:table-cell">
                            <span @class([
                                'inline-flex rounded-full px-2 py-0.5 text-xs font-semibold',
                                'bg-green-100 text-green-700' => $item->estado_integridad === 'integro',
                                'bg-red-100 text-red-700'     => $item->estado_integridad === 'roto',
                            ])>
                                {{ $item->estado_integridad === 'integro' ? '✅ Íntegro' : '🔴 Roto' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600">
                            {{ $item->motivo_devolucion ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-600">
                            {{ $item->acondicionamiento?->operario?->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-600">
                            {{ $item->created_at?->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-5 py-12 text-center">
                            <span class="text-3xl block mb-2">✅</span>
                            <p class="text-sm text-slate-400">Sin devoluciones en este período</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">{{ $items->links() }}</div>
    @endif
</div>

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    @media print { form, button { display:none!important; } }
</style>

@endsection