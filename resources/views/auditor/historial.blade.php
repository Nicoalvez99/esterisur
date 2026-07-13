@extends('layouts.modulo')

@section('title', 'Historial')
@section('modulo-label', 'Auditoría')
@section('page-title', 'Historial de acciones')
@section('page-subtitle', 'Todas las acciones registradas en el sistema')

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
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-3">
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
            <label class="block text-xs font-medium text-slate-500 mb-1">Usuario</label>
            <select name="user_id"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none">
                <option value="">Todos</option>
                @foreach($usuarios as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1">N° de lote</label>
            <input type="text" name="numero_lote" value="{{ request('numero_lote') }}"
                   placeholder="LOT-..."
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none" />
        </div>
    </div>
    <div class="flex gap-2">
        <button type="submit"
                class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors">
            Filtrar
        </button>
        <a href="{{ route('auditor.historial') }}"
           class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
            Hoy
        </a>
        <span class="ml-auto text-xs text-slate-400 flex items-center">
            {{ $historial->total() }} registro{{ $historial->total() !== 1 ? 's' : '' }}
        </span>
    </div>
</form>

{{-- Tabla --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Acción</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Lote</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden md:table-cell">Institución</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Transición</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Usuario</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Fecha</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($historial as $item)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3">
                            <p class="text-xs font-semibold text-slate-800">{{ $item->accion }}</p>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @if($item->lote)
                                <a href="{{ route('auditor.show', $item->lote->id) }}"
                                   class="font-mono text-xs font-bold text-teal-600 hover:text-teal-800 transition-colors">
                                    {{ $item->lote->numero_lote }}
                                </a>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-xs text-slate-600">
                            {{ $item->lote?->institucion?->nombre ?? '—' }}
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @if($item->estado_origen && $item->estado_destino)
                                <div class="flex items-center gap-1 text-xs">
                                    <span class="text-slate-400">{{ ucfirst(str_replace('_', ' ', $item->estado_origen)) }}</span>
                                    <span class="text-slate-300">→</span>
                                    <span class="font-medium text-slate-700">{{ ucfirst(str_replace('_', ' ', $item->estado_destino)) }}</span>
                                </div>
                            @else
                                <span class="text-xs text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-slate-200 text-xs font-bold text-slate-600">
                                    {{ strtoupper(substr($item->user?->name ?? 'S', 0, 1)) }}
                                </div>
                                <span class="text-xs text-slate-600">{{ $item->user?->name ?? 'Sistema' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <p class="font-mono text-xs text-slate-600">{{ $item->created_at->format('d/m/Y') }}</p>
                            <p class="font-mono text-xs text-slate-400">{{ $item->created_at->format('H:i:s') }}</p>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <span class="text-3xl block mb-2">📋</span>
                            <p class="text-sm text-slate-400">Sin registros para este filtro</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($historial->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">{{ $historial->links() }}</div>
    @endif
</div>

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    .nav-op.active { background:rgb(20 184 166/.1); color:rgb(15 118 110); }
</style>

@endsection