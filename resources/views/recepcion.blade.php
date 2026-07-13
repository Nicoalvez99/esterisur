@extends('layouts.modulo')

@section('title', 'Nueva recepción')
@section('page-title', 'Nueva recepción')
@section('page-subtitle', 'Registrá el ingreso de material')

@section('content')
<style>
    .input-field {
        width: 100%;
        border-radius: 0.5rem;
        border: 1px solid rgb(203 213 225);
        background-color: white;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: rgb(30 41 59);
        transition: border-color 0.15s, box-shadow 0.15s;
    }

    .input-field:focus {
        outline: none;
        border-color: rgb(20 184 166);
        box-shadow: 0 0 0 3px rgb(20 184 166 / 0.15);
    }

    .metodo-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        border: 2px solid rgb(226 232 240);
        padding: 0.875rem;
        cursor: pointer;
        transition: all 0.15s;
        text-align: center;
    }

    .metodo-card:hover {
        border-color: rgb(94 234 212);
        background-color: rgb(240 253 250);
    }

    .metodo-card--active {
        border-color: rgb(20 184 166) !important;
        background-color: rgb(240 253 250) !important;
    }
</style>
<form method="POST" action="{{ route('recepcion.store') }}" id="form-recepcion" novalidate>
    @csrf

    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- ============================================================
         COLUMNA IZQUIERDA — datos del ingreso (2/3)
    ============================================================ --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- BLOQUE 1: Institución --}}
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">1</span>
                    Institución
                </h2>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Institución <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="institucion_id"
                        id="institucion_id"
                        required
                        class="input-field @error('institucion_id') border-red-400 @enderror">
                        <option value="">— Seleccioná una institución —</option>
                        @foreach($instituciones as $inst)
                        <option value="{{ $inst->id }}" {{ old('institucion_id') == $inst->id ? 'selected' : '' }}>
                            {{ $inst->nombre }}
                            @if($inst->codigo) ({{ $inst->codigo }}) @endif
                        </option>
                        @endforeach
                    </select>
                    @error('institucion_id')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- BLOQUE 2: Chofer y remito --}}
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">2</span>
                    Transporte y documentación
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Nombre del chofer</label>
                        <input
                            type="text"
                            name="chofer_nombre"
                            value="{{ old('chofer_nombre') }}"
                            placeholder="Ej: Juan García"
                            class="input-field" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Empresa / transporte</label>
                        <input
                            type="text"
                            name="chofer_transporte"
                            value="{{ old('chofer_transporte') }}"
                            placeholder="Ej: Transporte Rápido SA"
                            class="input-field" />
                    </div>
                </div>

                {{-- Remito --}}
                <div class="mt-4 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-semibold text-slate-700 mb-3">¿Viene con remito? <span class="text-red-500">*</span></p>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tiene_remito" value="1"
                                {{ old('tiene_remito') == '1' ? 'checked' : '' }}
                                class="accent-teal-500"
                                onchange="toggleRemito(true)" />
                            <span class="text-sm text-slate-700">Sí, tiene remito</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="tiene_remito" value="0"
                                {{ old('tiene_remito') == '0' ? 'checked' : '' }}
                                class="accent-teal-500"
                                onchange="toggleRemito(false)" />
                            <span class="text-sm text-slate-700">No tiene remito</span>
                        </label>
                    </div>
                    @error('tiene_remito')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror

                    <div id="campo-remito" class="{{ old('tiene_remito') == '1' ? '' : 'hidden' }} mt-3">
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">
                            Número de remito <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="remito_numero"
                            id="remito_numero"
                            value="{{ old('remito_numero') }}"
                            placeholder="Ej: R-00123"
                            class="input-field @error('remito_numero') border-red-400 @enderror" />
                        @error('remito_numero')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Alerta si no hay remito --}}
                    <div id="alerta-sin-remito" class="{{ old('tiene_remito') == '0' ? '' : 'hidden' }} mt-3 flex items-center gap-2 rounded-lg bg-amber-50 border border-amber-200 px-3 py-2">
                        <svg class="h-4 w-4 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <p class="text-xs text-amber-700">El lote quedará en estado <strong>OBSERVADO</strong> hasta recibir documentación.</p>
                    </div>
                </div>
            </div>

            {{-- BLOQUE 3: Cantidades --}}
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">3</span>
                    Cantidades recibidas
                </h2>

                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                    @php
                    $cantidades = [
                    ['name' => 'cant_cajas', 'label' => 'Cajas', 'icon' => '📦'],
                    ['name' => 'cant_bultos', 'label' => 'Bultos', 'icon' => '🗃️'],
                    ['name' => 'cant_unidades', 'label' => 'Unidades', 'icon' => '🔧'],
                    ['name' => 'cant_equipos_ropa', 'label' => 'Equipos ropa', 'icon' => '👕'],
                    ['name' => 'cant_litros', 'label' => 'Litros', 'icon' => '💧'],
                    ];
                    @endphp

                    @foreach($cantidades as $c)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-center">
                        <p class="text-lg mb-1">{{ $c['icon'] }}</p>
                        <label class="block text-xs font-medium text-slate-500 mb-1.5">{{ $c['label'] }}</label>
                        <input
                            type="number"
                            name="{{ $c['name'] }}"
                            value="{{ old($c['name'], 0) }}"
                            min="0"
                            step="{{ $c['name'] === 'cant_litros' ? '0.01' : '1' }}"
                            class="w-full rounded-md border border-slate-300 bg-white px-2 py-1.5 text-center text-sm font-semibold text-slate-800 focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500" />
                        @error($c['name'])
                        <p class="mt-0.5 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- BLOQUE 4: Método y empaque --}}
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">4</span>
                    Clasificación del material
                </h2>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                    {{-- Método --}}
                    <div>
                        <p class="text-xs font-semibold text-slate-700 mb-3">
                            Método de esterilización <span class="text-red-500">*</span>
                        </p>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="metodo-card {{ old('metodo') === 'vapor' ? 'metodo-card--active' : '' }}" id="card-vapor">
                                <input type="radio" name="metodo" value="vapor"
                                    {{ old('metodo') === 'vapor' ? 'checked' : '' }}
                                    class="sr-only" onchange="selectMetodo('vapor')" />
                                <div class="text-2xl mb-1">♨️</div>
                                <p class="text-sm font-semibold text-slate-800">Vapor</p>
                                <p class="text-xs text-slate-500">Autoclave</p>
                            </label>
                            <label class="metodo-card {{ old('metodo') === 'eto' ? 'metodo-card--active' : '' }}" id="card-eto">
                                <input type="radio" name="metodo" value="eto"
                                    {{ old('metodo') === 'eto' ? 'checked' : '' }}
                                    class="sr-only" onchange="selectMetodo('eto')" />
                                <div class="text-2xl mb-1">🧪</div>
                                <p class="text-sm font-semibold text-slate-800">ETO</p>
                                <p class="text-xs text-slate-500">Óxido de etileno</p>
                            </label>
                        </div>
                        @error('metodo')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Estado del empaque --}}
                    <div>
                        <p class="text-xs font-semibold text-slate-700 mb-3">
                            Estado del empaque <span class="text-red-500">*</span>
                        </p>
                        <div class="grid grid-cols-1 gap-3">
                            <label class="metodo-card {{ old('estado_empaque') === 'empaquetado' ? 'metodo-card--active' : '' }}" id="card-empaquetado">
                                <input type="radio" name="estado_empaque" value="empaquetado"
                                    {{ old('estado_empaque') === 'empaquetado' ? 'checked' : '' }}
                                    class="sr-only" onchange="selectEmpaque('empaquetado')" />
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">✅</span>
                                    <div class="text-left">
                                        <p class="text-sm font-semibold text-slate-800">Empaquetado</p>
                                        <p class="text-xs text-slate-500">Va directo a esterilización</p>
                                    </div>
                                </div>
                            </label>
                            <label class="metodo-card {{ old('estado_empaque') === 'sin_empaquetar' ? 'metodo-card--active' : '' }}" id="card-sin-empaquetar">
                                <input type="radio" name="estado_empaque" value="sin_empaquetar"
                                    {{ old('estado_empaque') === 'sin_empaquetar' ? 'checked' : '' }}
                                    class="sr-only" onchange="selectEmpaque('sin_empaquetar')" />
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">🔄</span>
                                    <div class="text-left">
                                        <p class="text-sm font-semibold text-slate-800">Sin empaquetar</p>
                                        <p class="text-xs text-slate-500">Pasa por acondicionamiento</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('estado_empaque')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Banner derivación automática --}}
                <div id="banner-derivacion" class="hidden mt-4 rounded-lg border px-4 py-3 text-xs font-medium"></div>
            </div>

            {{-- BLOQUE 5: Observaciones --}}
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-300 text-xs font-bold text-slate-600">5</span>
                    Observaciones
                    <span class="text-xs font-normal text-slate-400">(opcional)</span>
                </h2>
                <textarea
                    name="observaciones"
                    rows="3"
                    placeholder="Anotá cualquier observación relevante sobre el material recibido..."
                    class="input-field resize-none">{{ old('observaciones') }}</textarea>
            </div>

        </div>

        {{-- ============================================================
         COLUMNA DERECHA — resumen + fecha + prioridad + submit (1/3)
    ============================================================ --}}
        <div class="space-y-5">

            {{-- Fecha de entrega --}}
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-3 text-sm font-semibold text-slate-800">Fecha de entrega</h2>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">
                    Fecha comprometida <span class="text-red-500">*</span>
                </label>
                <input
                    type="date"
                    name="fecha_entrega_pactada"
                    value="{{ old('fecha_entrega_pactada') }}"
                    min="{{ now()->toDateString() }}"
                    required
                    class="input-field @error('fecha_entrega_pactada') border-red-400 @enderror" />
                @error('fecha_entrega_pactada')
                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Prioridad --}}
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-3 text-sm font-semibold text-slate-800">Prioridad</h2>
                <div class="space-y-2">
                    @foreach([
                    ['value' => 'normal', 'label' => 'Normal', 'color' => 'text-slate-700', 'bg' => 'bg-slate-100', 'dot' => 'bg-slate-400'],
                    ['value' => 'urgente', 'label' => 'Urgente', 'color' => 'text-amber-700', 'bg' => 'bg-amber-50', 'dot' => 'bg-amber-400'],
                    ['value' => 'critica', 'label' => 'Crítica', 'color' => 'text-red-700', 'bg' => 'bg-red-50', 'dot' => 'bg-red-500'],
                    ] as $p)
                    <label class="flex items-center gap-3 rounded-lg border border-transparent px-3 py-2.5 cursor-pointer
                                  hover:border-slate-200 hover:{{ $p['bg'] }} transition-all
                                  {{ old('prioridad', 'normal') === $p['value'] ? 'border-slate-200 ' . $p['bg'] : '' }}">
                        <input type="radio" name="prioridad" value="{{ $p['value'] }}"
                            {{ old('prioridad', 'normal') === $p['value'] ? 'checked' : '' }}
                            class="sr-only" />
                        <span class="h-2.5 w-2.5 rounded-full {{ $p['dot'] }} shrink-0"></span>
                        <span class="text-sm font-medium {{ $p['color'] }}">{{ $p['label'] }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Resumen visual --}}
            <div class="rounded-xl border border-teal-200 bg-teal-50 p-5">
                <h2 class="mb-3 text-sm font-semibold text-teal-800">Resumen</h2>
                <dl class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <dt class="text-teal-600">Institución</dt>
                        <dd class="font-semibold text-teal-900 text-right max-w-32 truncate" id="resumen-institucion">—</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-teal-600">Método</dt>
                        <dd class="font-semibold text-teal-900" id="resumen-metodo">—</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-teal-600">Empaque</dt>
                        <dd class="font-semibold text-teal-900" id="resumen-empaque">—</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-teal-600">Próximo paso</dt>
                        <dd class="font-semibold text-teal-900" id="resumen-siguiente">—</dd>
                    </div>
                </dl>
            </div>

            {{-- Botón submit --}}
            <button
                type="submit"
                id="btn-submit"
                class="w-full rounded-xl bg-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-sm
                   hover:bg-teal-700 active:scale-95 transition-all duration-150
                   disabled:opacity-50 disabled:cursor-not-allowed">
                Registrar ingreso
            </button>

            <a href="{{ route('recepcion.index') }}"
                class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors mt-1">
                Cancelar y volver al listado
            </a>
        </div>

    </div>
</form>



<script>
    // --- Remito toggle ---
    function toggleRemito(tiene) {
        document.getElementById('campo-remito').classList.toggle('hidden', !tiene);
        document.getElementById('alerta-sin-remito').classList.toggle('hidden', tiene);
        document.getElementById('remito_numero').required = tiene;
    }

    // --- Selección visual método ---
    function selectMetodo(val) {
        ['vapor', 'eto'].forEach(m => {
            document.getElementById('card-' + m).classList.toggle('metodo-card--active', m === val);
        });
        actualizarResumen();
    }

    // --- Selección visual empaque ---
    function selectEmpaque(val) {
        ['empaquetado', 'sin-empaquetar'].forEach(e => {
            document.getElementById('card-' + e).classList.toggle('metodo-card--active', e === val);
        });
        actualizarBannerDerivacion(val);
        actualizarResumen();
    }

    function actualizarBannerDerivacion(empaque) {
        const banner = document.getElementById('banner-derivacion');
        if (empaque === 'empaquetado') {
            banner.className = 'mt-4 rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-xs font-medium text-teal-700';
            banner.innerHTML = '✅ El material irá directo a <strong>Esterilización</strong> sin pasar por acondicionamiento.';
            banner.classList.remove('hidden');
        } else if (empaque === 'sin_empaquetar') {
            banner.className = 'mt-4 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-xs font-medium text-blue-700';
            banner.innerHTML = '🔄 El material pasará primero por <strong>Acondicionamiento</strong> antes de esterilizar.';
            banner.classList.remove('hidden');
        } else {
            banner.classList.add('hidden');
        }
    }

    // --- Resumen dinámico ---
    function actualizarResumen() {
        const instSelect = document.getElementById('institucion_id');
        const instTexto = instSelect.options[instSelect.selectedIndex]?.text ?? '—';

        const metodo = document.querySelector('input[name="metodo"]:checked')?.value;
        const empaque = document.querySelector('input[name="estado_empaque"]:checked')?.value;

        document.getElementById('resumen-institucion').textContent =
            instSelect.value ? instTexto.split('(')[0].trim() : '—';

        document.getElementById('resumen-metodo').textContent =
            metodo ? (metodo === 'vapor' ? 'Vapor (autoclave)' : 'ETO (óxido etileno)') : '—';

        document.getElementById('resumen-empaque').textContent =
            empaque ? (empaque === 'empaquetado' ? 'Empaquetado' : 'Sin empaquetar') : '—';

        let siguiente = '—';
        if (empaque === 'empaquetado' && metodo) {
            siguiente = metodo === 'vapor' ? 'Esterilización vapor' : 'Esterilización ETO';
        } else if (empaque === 'sin_empaquetar') {
            siguiente = 'Acondicionamiento';
        }
        document.getElementById('resumen-siguiente').textContent = siguiente;
    }

    // Inicializar listeners del resumen
    document.getElementById('institucion_id').addEventListener('change', actualizarResumen);
    document.querySelectorAll('input[name="metodo"]').forEach(r => r.addEventListener('change', actualizarResumen));
    document.querySelectorAll('input[name="estado_empaque"]').forEach(r => r.addEventListener('change', actualizarResumen));

    // Inicializar estado al cargar (por si hay old() de Laravel)
    (function init() {
        const tieneRemito = document.querySelector('input[name="tiene_remito"]:checked')?.value;
        if (tieneRemito === '1') toggleRemito(true);
        if (tieneRemito === '0') toggleRemito(false);

        const empaque = document.querySelector('input[name="estado_empaque"]:checked')?.value;
        if (empaque) actualizarBannerDerivacion(empaque);

        actualizarResumen();
    })();
</script>

@endsection