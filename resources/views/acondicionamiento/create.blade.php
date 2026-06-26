@extends('layouts.modulo')

@section('title', 'Acondicionar — ' . $lote->numero_lote)
@section('modulo-label', 'Acondicionamiento')
@section('page-title', 'Acondicionar lote')
@section('page-subtitle', $lote->numero_lote . ' · ' . ($lote->recepcion?->institucion?->nombre ?? ''))

@section('sidebar-nav')
    <a href="{{ route('acondicionamiento.index') }}" class="nav-op">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver al listado
    </a>
@endsection

@section('content')

<form method="POST" action="{{ route('acondicionamiento.store', $lote->id) }}" id="form-acond" novalidate>
@csrf

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- ============================================================
         COLUMNA PRINCIPAL
    ============================================================ --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Info del lote (solo lectura) --}}
        <div class="rounded-xl border border-teal-200 bg-teal-50 p-4">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs">
                <div>
                    <span class="text-teal-600">Lote</span>
                    <span class="ml-1.5 font-mono font-bold text-teal-900">{{ $lote->numero_lote }}</span>
                </div>
                <div>
                    <span class="text-teal-600">Institución</span>
                    <span class="ml-1.5 font-semibold text-teal-900">{{ $lote->recepcion?->institucion?->nombre ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-teal-600">Método</span>
                    <span class="ml-1.5 font-semibold text-teal-900 uppercase">{{ $lote->metodo_esterilizacion }}</span>
                </div>
                <div>
                    <span class="text-teal-600">Recibido</span>
                    <span class="ml-1.5 font-semibold text-teal-900">{{ $lote->fecha_recepcion?->format('d/m/Y H:i') ?? '—' }}</span>
                </div>
                @if($lote->prioridad !== 'normal')
                    <span @class([
                        'rounded-full px-2.5 py-0.5 text-xs font-bold',
                        'bg-amber-500 text-white' => $lote->prioridad === 'urgente',
                        'bg-red-600 text-white'   => $lote->prioridad === 'critica',
                    ])>⚡ {{ ucfirst($lote->prioridad) }}</span>
                @endif
            </div>
        </div>

        {{-- PASO 1: Planilla y cantidades --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">1</span>
                Planilla y conteo general
            </h2>

            {{-- ¿Tiene planilla? --}}
            <div class="mb-4">
                <p class="text-xs font-semibold text-slate-700 mb-2">¿La institución envió planilla de dispositivos?</p>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tiene_planilla" value="1"
                               {{ old('tiene_planilla') == '1' ? 'checked' : '' }}
                               class="accent-teal-500" onchange="togglePlanilla(true)" />
                        <span class="text-sm text-slate-700">Sí, tiene planilla</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="tiene_planilla" value="0"
                               {{ old('tiene_planilla', '0') == '0' ? 'checked' : '' }}
                               class="accent-teal-500" onchange="togglePlanilla(false)" />
                        <span class="text-sm text-slate-700">No tiene planilla</span>
                    </label>
                </div>
            </div>

            {{-- Cantidad declarada --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Cantidad declarada (planilla/recepción)
                        <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="cant_declarada" id="cant-declarada"
                           value="{{ old('cant_declarada', $lote->recepcion?->cant_unidades ?? 0) }}"
                           min="0"
                           class="input-field"
                           oninput="calcularDiferencia()" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Cantidad real contada
                    </label>
                    <input type="number" id="cant-real-display"
                           value="0" min="0" readonly
                           class="input-field bg-slate-50 cursor-not-allowed"
                           title="Se calcula automáticamente desde los ítems" />
                    <p class="mt-1 text-xs text-slate-400">Calculado desde los ítems cargados abajo</p>
                </div>
            </div>

            {{-- Diferencia --}}
            <div id="banner-diferencia" class="hidden mt-3 rounded-lg px-4 py-2.5 text-xs font-semibold"></div>
        </div>

        {{-- PASO 2: Detalle de ítems --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">2</span>
                    Detalle de dispositivos / ítems
                </h2>
                <button type="button" onclick="agregarItem()"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-teal-300 bg-teal-50 px-3 py-1.5 text-xs font-semibold text-teal-700 hover:bg-teal-100 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Agregar ítem
                </button>
            </div>

            {{-- Encabezado de la tabla de ítems --}}
            <div class="hidden sm:grid grid-cols-12 gap-2 mb-2 px-1">
                <div class="col-span-3 text-xs font-semibold text-slate-500">Dispositivo</div>
                <div class="col-span-1 text-xs font-semibold text-slate-500 text-center">Decl.</div>
                <div class="col-span-1 text-xs font-semibold text-slate-500 text-center">Real</div>
                <div class="col-span-2 text-xs font-semibold text-slate-500 text-center">Limpieza</div>
                <div class="col-span-2 text-xs font-semibold text-slate-500 text-center">Integridad</div>
                <div class="col-span-2 text-xs font-semibold text-slate-500 text-center">Acción</div>
                <div class="col-span-1"></div>
            </div>

            <div id="items-container" class="space-y-2">
                {{-- Los ítems se agregan dinámicamente con JS --}}
            </div>

            {{-- Empty state --}}
            <div id="items-empty" class="rounded-lg border-2 border-dashed border-slate-200 py-8 text-center">
                <p class="text-sm text-slate-400">No hay ítems cargados.</p>
                <button type="button" onclick="agregarItem()"
                        class="mt-2 text-xs font-medium text-teal-600 hover:underline">
                    + Agregar primer ítem
                </button>
            </div>

            {{-- Resumen conteo (se actualiza con JS) --}}
            <div id="resumen-items" class="hidden mt-4 grid grid-cols-2 gap-2 sm:grid-cols-4 border-t border-slate-100 pt-4">
                <div class="rounded-lg bg-green-50 p-2.5 text-center">
                    <p class="text-xs text-green-600 font-medium">Limpios</p>
                    <p class="text-lg font-bold text-green-700" id="count-limpio">0</p>
                </div>
                <div class="rounded-lg bg-red-50 p-2.5 text-center">
                    <p class="text-xs text-red-600 font-medium">Sucios</p>
                    <p class="text-lg font-bold text-red-700" id="count-sucio">0</p>
                </div>
                <div class="rounded-lg bg-blue-50 p-2.5 text-center">
                    <p class="text-xs text-blue-600 font-medium">Íntegros</p>
                    <p class="text-lg font-bold text-blue-700" id="count-integro">0</p>
                </div>
                <div class="rounded-lg bg-amber-50 p-2.5 text-center">
                    <p class="text-xs text-amber-600 font-medium">A devolver</p>
                    <p class="text-lg font-bold text-amber-700" id="count-devolver">0</p>
                </div>
            </div>
        </div>

        {{-- PASO 3: Empaque --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">3</span>
                Empaque aplicado
            </h2>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Tipo de empaque <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach([
                            ['val' => 'bolsa_simple', 'label' => 'Bolsa simple',  'icon' => '🛍️'],
                            ['val' => 'doble_bolsa',  'label' => 'Doble bolsa',   'icon' => '🛍️🛍️'],
                            ['val' => 'caja',         'label' => 'Caja',          'icon' => '📦'],
                            ['val' => 'bulto',        'label' => 'Bulto',         'icon' => '🗃️'],
                            ['val' => 'otro',         'label' => 'Otro',          'icon' => '📋'],
                        ] as $emp)
                            <label class="empaque-card {{ old('tipo_empaque') === $emp['val'] ? 'empaque-card--active' : '' }}"
                                   id="emp-{{ $emp['val'] }}"
                                   onclick="selectEmpaque('{{ $emp['val'] }}')">
                                <input type="radio" name="tipo_empaque" value="{{ $emp['val'] }}"
                                       {{ old('tipo_empaque') === $emp['val'] ? 'checked' : '' }}
                                       class="sr-only" />
                                <span class="text-lg">{{ $emp['icon'] }}</span>
                                <span class="text-xs font-medium text-slate-700 text-center leading-tight">{{ $emp['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('tipo_empaque') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Cantidad <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="cant_empaque"
                           value="{{ old('cant_empaque', 1) }}"
                           min="1"
                           class="input-field text-center text-lg font-bold" />
                    @error('cant_empaque') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-3">
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Detalle del empaque (opcional)</label>
                <input type="text" name="empaque_detalle"
                       value="{{ old('empaque_detalle') }}"
                       placeholder="Ej: caja chica x12 unidades, doble bolsa con cinta testigo..."
                       class="input-field" />
            </div>
        </div>

        {{-- PASO 4: Resultado y observaciones --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">4</span>
                Resultado del acondicionamiento
            </h2>

            <div class="space-y-2 mb-4">
                @foreach([
                    ['val' => 'acondicionado',    'label' => 'Acondicionado — listo para esterilización',     'dot' => 'bg-green-500',  'bg' => 'hover:bg-green-50 hover:border-green-200'],
                    ['val' => 'con_devoluciones', 'label' => 'Con devoluciones — parte del material se devuelve, el resto sigue', 'dot' => 'bg-amber-500', 'bg' => 'hover:bg-amber-50 hover:border-amber-200'],
                    ['val' => 'retenido',         'label' => 'Retenido — no puede avanzar, requiere revisión', 'dot' => 'bg-red-500',   'bg' => 'hover:bg-red-50 hover:border-red-200'],
                ] as $r)
                    <label class="resultado-card {{ old('resultado') === $r['val'] ? 'resultado-card--active' : '' }}"
                           id="res-{{ $r['val'] }}"
                           onclick="selectResultado('{{ $r['val'] }}')">
                        <input type="radio" name="resultado" value="{{ $r['val'] }}"
                               {{ old('resultado') === $r['val'] ? 'checked' : '' }}
                               class="sr-only" />
                        <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $r['dot'] }} mt-0.5"></span>
                        <span class="text-sm text-slate-700">{{ $r['label'] }}</span>
                    </label>
                @endforeach
            </div>
            @error('resultado') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror

            <div class="mt-4">
                <label class="block text-xs font-medium text-slate-600 mb-1.5">Observaciones generales</label>
                <textarea name="observaciones" rows="3"
                          placeholder="Anotá cualquier observación sobre el estado del material, diferencias encontradas, etc."
                          class="input-field resize-none">{{ old('observaciones') }}</textarea>
            </div>
        </div>
    </div>

    {{-- ============================================================
         SIDEBAR
    ============================================================ --}}
    <div class="space-y-4">

        {{-- Resumen en tiempo real --}}
        <div class="rounded-xl bg-slate-900 text-white p-5">
            <p class="text-xs text-slate-400 mb-3">Resumen</p>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <dt class="text-slate-400">Total ítems</dt>
                    <dd class="font-bold text-white" id="resumen-total">0</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">A procesar</dt>
                    <dd class="font-bold text-green-400" id="resumen-procesar">0</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">A devolver</dt>
                    <dd class="font-bold text-amber-400" id="resumen-devolver">0</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Diferencia</dt>
                    <dd class="font-bold" id="resumen-diferencia">—</dd>
                </div>
            </dl>
        </div>

        {{-- Protocolo de la institución --}}
        @php $inst = $lote->recepcion?->institucion; @endphp
        @if($inst?->observaciones)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                <p class="text-xs font-semibold text-amber-800 mb-1.5">⚠️ Protocolo de {{ $inst->nombre }}</p>
                <p class="text-xs text-amber-700 leading-relaxed">{{ $inst->observaciones }}</p>
            </div>
        @endif

        {{-- Datos recepción --}}
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs font-semibold text-slate-600 mb-3">Datos de recepción</p>
            <dl class="space-y-1.5 text-xs">
                @php $rec = $lote->recepcion; @endphp
                <div class="flex justify-between">
                    <dt class="text-slate-400">Cajas</dt>
                    <dd class="font-medium text-slate-700">{{ $rec?->cant_cajas ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Bultos</dt>
                    <dd class="font-medium text-slate-700">{{ $rec?->cant_bultos ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Unidades</dt>
                    <dd class="font-medium text-slate-700">{{ $rec?->cant_unidades ?? 0 }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Entrega pactada</dt>
                    <dd class="font-medium text-slate-700">{{ $rec?->fecha_entrega_pactada?->format('d/m/Y') ?? '—' }}</dd>
                </div>
                @if($rec?->tiene_remito)
                    <div class="flex justify-between">
                        <dt class="text-slate-400">Remito</dt>
                        <dd class="font-mono font-medium text-slate-700">{{ $rec->remito_numero }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full rounded-xl bg-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-sm hover:bg-teal-700 active:scale-95 transition-all">
            Finalizar acondicionamiento
        </button>
        <a href="{{ route('acondicionamiento.index') }}"
           class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">
            Cancelar
        </a>
    </div>

</div>
</form>

{{-- Template invisible para clonar ítems --}}
<template id="item-template">
    <div class="item-row rounded-lg border border-slate-200 bg-slate-50 p-3" data-index="__IDX__">
        <div class="grid grid-cols-12 gap-2 items-start">

            {{-- Nombre --}}
            <div class="col-span-12 sm:col-span-3">
                <label class="block text-xs text-slate-500 mb-1 sm:hidden">Dispositivo</label>
                <input type="text" name="items[__IDX__][nombre]"
                       placeholder="Nombre del dispositivo"
                       class="input-field text-xs"
                       oninput="actualizarResumen()" />
            </div>

            {{-- Cant declarada --}}
            <div class="col-span-6 sm:col-span-1">
                <label class="block text-xs text-slate-500 mb-1 sm:hidden">Declarada</label>
                <input type="number" name="items[__IDX__][cant_declarada]"
                       value="1" min="0"
                       class="input-field text-xs text-center"
                       oninput="actualizarResumen()" />
            </div>

            {{-- Cant real --}}
            <div class="col-span-6 sm:col-span-1">
                <label class="block text-xs text-slate-500 mb-1 sm:hidden">Real</label>
                <input type="number" name="items[__IDX__][cant_real]"
                       value="1" min="0"
                       class="input-field text-xs text-center"
                       oninput="actualizarResumen()" />
            </div>

            {{-- Limpieza --}}
            <div class="col-span-6 sm:col-span-2">
                <label class="block text-xs text-slate-500 mb-1 sm:hidden">Limpieza</label>
                <select name="items[__IDX__][estado_limpieza]"
                        class="input-field text-xs"
                        onchange="actualizarResumen()">
                    <option value="limpio">✅ Limpio</option>
                    <option value="sucio">🔴 Sucio</option>
                </select>
            </div>

            {{-- Integridad --}}
            <div class="col-span-6 sm:col-span-2">
                <label class="block text-xs text-slate-500 mb-1 sm:hidden">Integridad</label>
                <select name="items[__IDX__][estado_integridad]"
                        class="input-field text-xs"
                        onchange="actualizarResumen()">
                    <option value="integro">✅ Íntegro</option>
                    <option value="roto">🔴 Roto</option>
                </select>
            </div>

            {{-- Acción --}}
            <div class="col-span-10 sm:col-span-2">
                <label class="block text-xs text-slate-500 mb-1 sm:hidden">Acción</label>
                <select name="items[__IDX__][accion]"
                        class="input-field text-xs accion-select"
                        onchange="onAccionChange(this)">
                    <option value="procesar">Procesar</option>
                    <option value="devolver">Devolver</option>
                    <option value="retener">Retener</option>
                </select>
            </div>

            {{-- Eliminar --}}
            <div class="col-span-2 sm:col-span-1 flex items-center justify-center pt-1">
                <button type="button" onclick="eliminarItem(this)"
                        class="rounded-md p-1.5 text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Motivo devolución (oculto por defecto) --}}
            <div class="col-span-12 motivo-devolucion hidden">
                <input type="text" name="items[__IDX__][motivo_devolucion]"
                       placeholder="Motivo de devolución (obligatorio)"
                       class="input-field text-xs border-amber-300 bg-amber-50" />
            </div>

        </div>
    </div>
</template>

<style>
    .input-field {
        width: 100%; border-radius: 0.5rem;
        border: 1px solid rgb(203 213 225); background: white;
        padding: 0.5rem 0.75rem; font-size: 0.875rem; color: rgb(30 41 59);
        transition: border-color .15s, box-shadow .15s;
    }
    .input-field:focus {
        outline: none; border-color: rgb(20 184 166);
        box-shadow: 0 0 0 3px rgb(20 184 166 / .15);
    }
    .empaque-card {
        display: flex; flex-direction: column; align-items: center;
        gap: 0.25rem; padding: 0.625rem; border-radius: 0.625rem;
        border: 2px solid rgb(226 232 240); cursor: pointer; transition: all .15s;
    }
    .empaque-card:hover { border-color: rgb(94 234 212); background: rgb(240 253 250); }
    .empaque-card--active { border-color: rgb(20 184 166) !important; background: rgb(240 253 250) !important; }
    .resultado-card {
        display: flex; align-items: flex-start; gap: 0.75rem;
        padding: 0.75rem 1rem; border-radius: 0.625rem;
        border: 2px solid rgb(226 232 240); cursor: pointer; transition: all .15s;
    }
    .resultado-card:hover { border-color: rgb(203 213 225); background: rgb(248 250 252); }
    .resultado-card--active { border-color: rgb(20 184 166) !important; background: rgb(240 253 250) !important; }
    .nav-op {
        display: flex; align-items: center; gap: .625rem; padding: .5rem .75rem;
        border-radius: .5rem; font-size: .875rem; font-weight: 500;
        color: rgb(100 116 139); transition: all .15s; text-decoration: none;
    }
    .nav-op:hover { background: rgb(241 245 249); color: rgb(30 41 59); }
</style>

<script>
    let itemIndex = 0;

    function agregarItem() {
        const template = document.getElementById('item-template');
        const container = document.getElementById('items-container');
        const empty = document.getElementById('items-empty');
        const resumen = document.getElementById('resumen-items');

        const html = template.innerHTML.replaceAll('__IDX__', itemIndex++);
        const div = document.createElement('div');
        div.innerHTML = html;
        container.appendChild(div.firstElementChild);

        empty.classList.add('hidden');
        resumen.classList.remove('hidden');

        actualizarResumen();
    }

    function eliminarItem(btn) {
        btn.closest('.item-row').remove();
        const container = document.getElementById('items-container');
        const empty = document.getElementById('items-empty');
        const resumen = document.getElementById('resumen-items');

        if (!container.children.length) {
            empty.classList.remove('hidden');
            resumen.classList.add('hidden');
        }
        actualizarResumen();
    }

    function onAccionChange(select) {
        const row = select.closest('.item-row');
        const motivoDiv = row.querySelector('.motivo-devolucion');
        motivoDiv.classList.toggle('hidden', select.value !== 'devolver');
        actualizarResumen();
    }

    function actualizarResumen() {
        const rows = document.querySelectorAll('.item-row');
        let total = rows.length, procesar = 0, devolver = 0, limpio = 0, sucio = 0, integro = 0, cantReal = 0;

        rows.forEach(row => {
            const accion     = row.querySelector('select[name*="[accion]"]')?.value;
            const limpieza   = row.querySelector('select[name*="[estado_limpieza]"]')?.value;
            const integridad = row.querySelector('select[name*="[estado_integridad]"]')?.value;
            const real       = parseInt(row.querySelector('input[name*="[cant_real]"]')?.value) || 0;

            if (accion === 'procesar') procesar++;
            if (accion === 'devolver') devolver++;
            if (limpieza === 'limpio') limpio++;
            if (limpieza === 'sucio')  sucio++;
            if (integridad === 'integro') integro++;
            cantReal += real;
        });

        // Actualizar contadores
        document.getElementById('count-limpio').textContent   = limpio;
        document.getElementById('count-sucio').textContent    = rows.length - limpio;
        document.getElementById('count-integro').textContent  = integro;
        document.getElementById('count-devolver').textContent = devolver;

        // Sidebar
        document.getElementById('resumen-total').textContent    = total;
        document.getElementById('resumen-procesar').textContent = procesar;
        document.getElementById('resumen-devolver').textContent = devolver;

        // Campo oculto de cant real
        document.getElementById('cant-real-display').value = cantReal;
        calcularDiferencia(cantReal);
    }

    function calcularDiferencia(cantReal) {
        const declarada = parseInt(document.getElementById('cant-declarada').value) || 0;
        const real      = cantReal !== undefined ? cantReal : parseInt(document.getElementById('cant-real-display').value) || 0;
        const diff      = real - declarada;

        const banner = document.getElementById('banner-diferencia');
        const resDiv  = document.getElementById('resumen-diferencia');

        if (real === 0 && declarada === 0) {
            banner.classList.add('hidden');
            resDiv.textContent = '—';
            resDiv.className = 'font-bold text-slate-400';
            return;
        }

        banner.classList.remove('hidden');

        if (diff === 0) {
            banner.className = 'mt-3 rounded-lg px-4 py-2.5 text-xs font-semibold bg-green-50 border border-green-200 text-green-700';
            banner.textContent = '✅ Las cantidades coinciden exactamente.';
            resDiv.textContent = '±0';
            resDiv.className = 'font-bold text-green-400';
        } else if (diff < 0) {
            banner.className = 'mt-3 rounded-lg px-4 py-2.5 text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
            banner.textContent = `⚠️ Faltante: se declararon ${declarada} y se contaron ${real}. Diferencia: ${diff}.`;
            resDiv.textContent = diff;
            resDiv.className = 'font-bold text-red-400';
        } else {
            banner.className = 'mt-3 rounded-lg px-4 py-2.5 text-xs font-semibold bg-amber-50 border border-amber-200 text-amber-700';
            banner.textContent = `⚠️ Sobrante: se declararon ${declarada} y se contaron ${real}. Diferencia: +${diff}.`;
            resDiv.textContent = '+' + diff;
            resDiv.className = 'font-bold text-amber-400';
        }
    }

    function selectEmpaque(val) {
        document.querySelectorAll('.empaque-card').forEach(c => c.classList.remove('empaque-card--active'));
        document.getElementById('emp-' + val).classList.add('empaque-card--active');
        document.querySelector(`input[name="tipo_empaque"][value="${val}"]`).checked = true;
    }

    function selectResultado(val) {
        document.querySelectorAll('.resultado-card').forEach(c => c.classList.remove('resultado-card--active'));
        document.getElementById('res-' + val).classList.add('resultado-card--active');
        document.querySelector(`input[name="resultado"][value="${val}"]`).checked = true;
    }

    function togglePlanilla(tiene) { /* por ahora solo visual */ }

    // Agregar un ítem inicial al cargar
    document.addEventListener('DOMContentLoaded', () => {
        agregarItem();
    });
</script>

@endsection