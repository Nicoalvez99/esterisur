@extends('layouts.modulo')

@section('title', 'Despachar — ' . $lote->numero_lote)
@section('modulo-label', 'Despacho')
@section('page-title', 'Preparar despacho')
@section('page-subtitle', $lote->numero_lote . ' · ' . ($lote->recepcion?->institucion?->nombre ?? ''))

@section('sidebar-nav')
    <a href="{{ route('despacho.index') }}" class="nav-op">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver al panel
    </a>
@endsection

@section('content')

@php
    $inst     = $lote->recepcion?->institucion;
    $protocolo = $inst?->protocolo;
    $rec      = $lote->recepcion;
@endphp

<form method="POST" action="{{ route('despacho.store', $lote->id) }}" novalidate>
@csrf

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- ============================================================ COLUMNA PRINCIPAL ============================================================ --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Info del lote liberado --}}
        <div class="rounded-xl border border-green-200 bg-green-50 p-4">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="h-2 w-2 rounded-full bg-green-500"></span>
                    <span class="font-semibold text-green-800">LIBERADO</span>
                </div>
                <div><span class="text-green-600">Lote</span> <span class="ml-1.5 font-mono font-bold text-green-900">{{ $lote->numero_lote }}</span></div>
                <div><span class="text-green-600">Institución</span> <span class="ml-1.5 font-semibold text-green-900">{{ $inst?->nombre ?? '—' }}</span></div>
                <div><span class="text-green-600">Método</span> <span class="ml-1.5 font-semibold text-green-900 uppercase">{{ $lote->metodo_esterilizacion }}</span></div>
                <div><span class="text-green-600">Liberado por</span> <span class="ml-1.5 font-semibold text-green-900">{{ $lote->liberacion?->responsable?->name ?? '—' }}</span></div>
            </div>
        </div>

        {{-- Protocolo de la institución --}}
        @if($protocolo)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-bold text-amber-800 mb-2">⚠️ Protocolo de {{ $inst->nombre }}</p>
                <div class="grid grid-cols-2 gap-x-6 gap-y-1 text-xs sm:grid-cols-3">
                    <div><span class="text-amber-600">Empaque:</span> <span class="font-semibold text-amber-900">{{ $protocolo->getEmpaqueLabel() }}</span></div>
                    <div><span class="text-amber-600">Traslado:</span> <span class="font-semibold text-amber-900">{{ $protocolo->getTrasladoLabel() }}</span></div>
                    <div><span class="text-amber-600">Vencimiento:</span> <span class="font-semibold text-amber-900">{{ $protocolo->vencimiento_dias }} días</span></div>
                    @if($protocolo->unidades_por_caja)
                        <div><span class="text-amber-600">Unid. por caja:</span> <span class="font-semibold text-amber-900">{{ $protocolo->unidades_por_caja }}</span></div>
                    @endif
                    @if($protocolo->empaque_detalle)
                        <div class="col-span-2 sm:col-span-3"><span class="text-amber-600">Detalle:</span> <span class="font-semibold text-amber-900">{{ $protocolo->empaque_detalle }}</span></div>
                    @endif
                </div>
                @if($protocolo->requisitos_especiales)
                    <div class="mt-3 border-t border-amber-200 pt-2 text-xs text-amber-800">
                        <p class="font-semibold mb-0.5">Requisitos especiales:</p>
                        <p class="leading-relaxed">{{ $protocolo->requisitos_especiales }}</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- PASO 1: Chofer --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">1</span>
                Transporte
            </h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nombre del chofer</label>
                    <input type="text" name="chofer_nombre"
                           value="{{ old('chofer_nombre', $rec?->chofer_nombre) }}"
                           placeholder="Ej: Juan García"
                           class="input-field" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Empresa / transporte</label>
                    <input type="text" name="chofer_transporte"
                           value="{{ old('chofer_transporte', $rec?->chofer_transporte) }}"
                           placeholder="Ej: Transporte Rápido SA"
                           class="input-field" />
                </div>
            </div>
        </div>

        {{-- PASO 2: Cantidades --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">2</span>
                Cantidades a despachar
            </h2>

            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 mb-3">
                @foreach([
                    ['name' => 'cant_cajas_chicas',   'label' => 'Cajas chicas',   'icon' => '📦'],
                    ['name' => 'cant_cajas_medianas',  'label' => 'Cajas medianas', 'icon' => '📦'],
                    ['name' => 'cant_cajas_grandes',   'label' => 'Cajas grandes',  'icon' => '📦'],
                    ['name' => 'cant_bultos',          'label' => 'Bultos',         'icon' => '🗃️'],
                    ['name' => 'cant_unidades',        'label' => 'Unidades',       'icon' => '🔧'],
                    ['name' => 'cant_equipos_ropa',    'label' => 'Equipos ropa',   'icon' => '👕'],
                    ['name' => 'cant_litros',          'label' => 'Litros',         'icon' => '💧'],
                ] as $c)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-center">
                        <p class="text-lg mb-1">{{ $c['icon'] }}</p>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ $c['label'] }}</label>
                        <input type="number"
                               name="{{ $c['name'] }}"
                               value="{{ old($c['name'], 0) }}"
                               min="0"
                               step="{{ $c['name'] === 'cant_litros' ? '0.01' : '1' }}"
                               class="w-full rounded-md border border-slate-300 bg-white px-2 py-1.5 text-center text-sm font-semibold text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500"
                               oninput="calcularTotal()" />
                    </div>
                @endforeach
            </div>

            @error('cant_cajas_chicas')
                <p class="text-xs text-red-500">{{ $message }}</p>
            @enderror

            {{-- Total calculado --}}
            <div class="mt-3 flex items-center justify-between rounded-lg bg-teal-50 border border-teal-200 px-4 py-2.5">
                <span class="text-xs font-semibold text-teal-700">Total bultos / piezas</span>
                <span id="total-bultos" class="text-lg font-bold text-teal-800">0</span>
            </div>
        </div>

        {{-- PASO 3: Observaciones --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-3 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-300 text-xs font-bold text-slate-600">3</span>
                Observaciones
                <span class="text-xs font-normal text-slate-400">(opcional)</span>
            </h2>
            <textarea name="observaciones" rows="3"
                      placeholder="Anotá cualquier observación sobre el despacho, condiciones de entrega, etc."
                      class="input-field resize-none">{{ old('observaciones') }}</textarea>
        </div>
    </div>

    {{-- ============================================================ SIDEBAR ============================================================ --}}
    <div class="space-y-4">

        {{-- Resumen del remito --}}
        <div class="rounded-xl bg-slate-900 text-white p-5">
            <p class="text-xs text-slate-400 mb-3">Resumen del remito</p>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <dt class="text-slate-400">Lote</dt>
                    <dd class="font-mono font-bold">{{ $lote->numero_lote }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Institución</dt>
                    <dd class="font-semibold text-right max-w-28 truncate">{{ $inst?->nombre ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Entrega pactada</dt>
                    <dd class="font-semibold {{ ($rec?->fecha_entrega_pactada && $rec->fecha_entrega_pactada->isPast()) ? 'text-red-400' : 'text-white' }}">
                        {{ $rec?->fecha_entrega_pactada?->format('d/m/Y') ?? '—' }}
                    </dd>
                </div>
                <div class="flex justify-between border-t border-slate-700 pt-2 mt-2">
                    <dt class="text-slate-400">Total a despachar</dt>
                    <dd class="font-bold text-teal-400 text-base" id="resumen-total">0</dd>
                </div>
            </dl>
        </div>

        {{-- Datos de recepción de referencia --}}
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs font-semibold text-slate-600 mb-3">Lo que ingresó</p>
            <dl class="space-y-1.5 text-xs">
                @foreach([
                    ['label' => 'Cajas',        'value' => $rec?->cant_cajas ?? 0],
                    ['label' => 'Bultos',        'value' => $rec?->cant_bultos ?? 0],
                    ['label' => 'Unidades',      'value' => $rec?->cant_unidades ?? 0],
                    ['label' => 'Equipos ropa',  'value' => $rec?->cant_equipos_ropa ?? 0],
                    ['label' => 'Litros',        'value' => $rec?->cant_litros ?? 0],
                ] as $d)
                    <div class="flex justify-between">
                        <dt class="text-slate-400">{{ $d['label'] }}</dt>
                        <dd class="font-medium text-slate-700">{{ $d['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full rounded-xl bg-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-sm hover:bg-teal-700 active:scale-95 transition-all">
            🚚 Generar remito y despachar
        </button>
        <a href="{{ route('despacho.index') }}"
           class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">
            Cancelar
        </a>
    </div>
</div>
</form>

<style>
    .input-field { width:100%; border-radius:.5rem; border:1px solid rgb(203 213 225); background:white; padding:.5rem .75rem; font-size:.875rem; color:rgb(30 41 59); transition:border-color .15s,box-shadow .15s; }
    .input-field:focus { outline:none; border-color:rgb(20 184 166); box-shadow:0 0 0 3px rgb(20 184 166/.15); }
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
</style>

<script>
    function calcularTotal() {
        const campos = [
            'cant_cajas_chicas', 'cant_cajas_medianas', 'cant_cajas_grandes',
            'cant_bultos', 'cant_unidades', 'cant_equipos_ropa'
        ];
        let total = 0;
        campos.forEach(function(name) {
            total += parseInt(document.querySelector('[name="' + name + '"]').value) || 0;
        });
        document.getElementById('total-bultos').textContent = total;
        document.getElementById('resumen-total').textContent = total;
    }

    document.addEventListener('DOMContentLoaded', calcularTotal);
</script>

@endsection