@extends('layouts.modulo')

@section('title', 'Nuevo ciclo — ' . strtoupper($metodo))
@section('modulo-label', 'Esterilización')
@section('page-title', 'Nuevo ciclo de ' . ($metodo === 'vapor' ? 'vapor' : 'ETO'))
@section('page-subtitle', $metodo === 'vapor' ? 'Autoclave' : 'Óxido de etileno')

@section('sidebar-nav')
    <a href="{{ route('esterilizacion.index') }}" class="nav-op">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver al panel
    </a>
@endsection

@section('content')

<form method="POST" action="{{ route('esterilizacion.store') }}" novalidate>
@csrf
<input type="hidden" name="metodo" value="{{ $metodo }}" />

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- ============================================================ COLUMNA PRINCIPAL ============================================================ --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Banner método --}}
        <div @class([
            'rounded-xl p-4 flex items-center gap-3',
            'bg-blue-50 border border-blue-200'   => $metodo === 'vapor',
            'bg-purple-50 border border-purple-200'=> $metodo === 'eto',
        ])>
            <span class="text-3xl">{{ $metodo === 'vapor' ? '♨️' : '🧪' }}</span>
            <div>
                <p @class(['text-sm font-bold', 'text-blue-800' => $metodo === 'vapor', 'text-purple-800' => $metodo === 'eto'])>
                    Ciclo por {{ $metodo === 'vapor' ? 'Vapor (Autoclave)' : 'Óxido de Etileno (ETO)' }}
                </p>
                <p @class(['text-xs', 'text-blue-600' => $metodo === 'vapor', 'text-purple-600' => $metodo === 'eto'])>
                    {{ $metodo === 'vapor' ? 'Completá temperatura, tiempo y presión del ciclo.' : 'Completá temperatura, concentración y registrá el inicio de aireación.' }}
                </p>
            </div>
            <a href="{{ route('esterilizacion.create', ['metodo' => $metodo === 'vapor' ? 'eto' : 'vapor']) }}"
               @class(['ml-auto text-xs font-medium underline', 'text-blue-600' => $metodo === 'vapor', 'text-purple-600' => $metodo === 'eto'])>
                Cambiar a {{ $metodo === 'vapor' ? 'ETO' : 'Vapor' }}
            </a>
        </div>

        {{-- PASO 1: Equipo --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">1</span>
                Equipo
            </h2>
            @if($equipos->isEmpty())
                <div class="rounded-lg bg-amber-50 border border-amber-200 px-4 py-3 text-xs text-amber-700">
                    ⚠️ No hay equipos activos de tipo <strong>{{ strtoupper($metodo) }}</strong> disponibles. El administrador debe cargar equipos primero.
                </div>
            @else
                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach($equipos as $equipo)
                        <label class="equipo-card {{ old('equipo_id') == $equipo->id ? 'equipo-card--active' : '' }}"
                               id="eq-{{ $equipo->id }}"
                               onclick="selectEquipo({{ $equipo->id }})">
                            <input type="radio" name="equipo_id" value="{{ $equipo->id }}"
                                   {{ old('equipo_id') == $equipo->id ? 'checked' : '' }}
                                   class="sr-only" />
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-xs font-bold text-slate-800">{{ $equipo->nombre }}</p>
                                    <p class="text-xs text-slate-500">{{ $equipo->marca }} {{ $equipo->modelo }}</p>
                                    @if($equipo->numero_interno)
                                        <p class="text-xs font-mono text-slate-400">{{ $equipo->numero_interno }}</p>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    @if($equipo->capacidad)
                                        <p class="text-xs text-slate-500">Cap. {{ $equipo->capacidad }}</p>
                                    @endif
                                    @if($equipo->validacionVencida())
                                        <span class="text-xs text-red-500 font-semibold">⚠️ Val. vencida</span>
                                    @elseif($equipo->fecha_proxima_validacion)
                                        <span class="text-xs text-slate-400">Val. {{ $equipo->fecha_proxima_validacion->format('m/Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('equipo_id') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            @endif
        </div>

        {{-- PASO 2: Lotes --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">2</span>
                Lotes a incluir en este ciclo
                <span class="text-xs font-normal text-slate-400">(podés incluir varios)</span>
            </h2>
            @if($lotes->isEmpty())
                <div class="rounded-lg bg-slate-50 border border-slate-200 px-4 py-6 text-center text-sm text-slate-400">
                    No hay lotes disponibles para {{ strtoupper($metodo) }} en este momento.
                </div>
            @else
                <div class="space-y-2" id="lotes-container">
                    @foreach($lotes as $lote)
                        <label class="lote-check flex items-center gap-3 rounded-lg border-2 border-slate-200 p-3 cursor-pointer hover:border-teal-300 hover:bg-teal-50/50 transition-all {{ in_array($lote->id, old('lote_ids', [])) ? 'border-teal-400 bg-teal-50' : '' }}"
                               onclick="toggleLote(this)">
                            <input type="checkbox" name="lote_ids[]" value="{{ $lote->id }}"
                                   {{ in_array($lote->id, old('lote_ids', [])) ? 'checked' : '' }}
                                   class="h-4 w-4 accent-teal-500 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono text-xs font-bold text-slate-800">{{ $lote->numero_lote }}</span>
                                    <span class="text-xs text-slate-500">{{ $lote->recepcion?->institucion?->nombre ?? '—' }}</span>
                                    @if($lote->prioridad !== 'normal')
                                        <span @class([
                                            'rounded-full px-2 py-0.5 text-xs font-bold',
                                            'bg-amber-500 text-white' => $lote->prioridad === 'urgente',
                                            'bg-red-600 text-white'   => $lote->prioridad === 'critica',
                                        ])>{{ ucfirst($lote->prioridad) }}</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    Recibido {{ $lote->fecha_recepcion?->diffForHumans() }}
                                    @if($lote->recepcion?->fecha_entrega_pactada)
                                        · Entrega {{ $lote->recepcion->fecha_entrega_pactada->format('d/m/Y') }}
                                    @endif
                                </p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('lote_ids') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
            @endif
        </div>

        {{-- PASO 3: Parámetros del ciclo --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">3</span>
                Parámetros del ciclo
            </h2>

            {{-- Horario --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mb-4">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Inicio del ciclo <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="fecha_inicio"
                           value="{{ old('fecha_inicio', now()->format('Y-m-d\TH:i')) }}"
                           class="input-field @error('fecha_inicio') border-red-400 @enderror" />
                    @error('fecha_inicio') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Fin del ciclo <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="fecha_fin"
                           value="{{ old('fecha_fin') }}"
                           class="input-field @error('fecha_fin') border-red-400 @enderror" />
                    @error('fecha_fin') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Parámetros según método --}}
            @if($metodo === 'vapor')
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">
                            Temperatura (°C) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="temperatura" step="0.1"
                               value="{{ old('temperatura', 134) }}"
                               placeholder="134"
                               class="input-field text-center font-semibold @error('temperatura') border-red-400 @enderror" />
                        @error('temperatura') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">
                            Tiempo (minutos) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="tiempo_minutos"
                               value="{{ old('tiempo_minutos', 18) }}"
                               placeholder="18"
                               class="input-field text-center font-semibold @error('tiempo_minutos') border-red-400 @enderror" />
                        @error('tiempo_minutos') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">Presión (bar)</label>
                        <input type="number" name="presion" step="0.01"
                               value="{{ old('presion', 2.1) }}"
                               placeholder="2.1"
                               class="input-field text-center font-semibold" />
                    </div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">
                            Temperatura (°C) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="temperatura" step="0.1"
                               value="{{ old('temperatura', 55) }}"
                               class="input-field text-center font-semibold @error('temperatura') border-red-400 @enderror" />
                        @error('temperatura') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">
                            Tiempo (minutos) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="tiempo_minutos"
                               value="{{ old('tiempo_minutos', 180) }}"
                               class="input-field text-center font-semibold @error('tiempo_minutos') border-red-400 @enderror" />
                        @error('tiempo_minutos') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">
                            Concentración (mg/L) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" name="concentracion" step="0.01"
                               value="{{ old('concentracion') }}"
                               class="input-field text-center font-semibold @error('concentracion') border-red-400 @enderror" />
                        @error('concentracion') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Aireación ETO --}}
                <div class="mt-4 rounded-lg bg-purple-50 border border-purple-200 p-4">
                    <p class="text-xs font-semibold text-purple-800 mb-3">💨 Inicio de aireación <span class="text-red-500">*</span></p>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1.5">Inicio aireación</label>
                            <input type="datetime-local" name="aireacion_inicio"
                                   value="{{ old('aireacion_inicio') }}"
                                   class="input-field @error('aireacion_inicio') border-red-400 @enderror" />
                            @error('aireacion_inicio') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-end">
                            <div class="rounded-lg bg-white border border-purple-200 px-3 py-2 text-xs text-purple-700 w-full">
                                ℹ️ El fin de aireación se registra después, cuando se cumple el tiempo mínimo requerido.
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- PASO 4: Controles --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">4</span>
                Controles del ciclo
            </h2>

            @php
                $controlesBase = $metodo === 'vapor'
                    ? [
                        ['tipo' => 'fisico',    'label' => 'Físico',    'desc' => 'Impresora / gráfico del equipo',  'icon' => '📊', 'required' => true],
                        ['tipo' => 'quimico',   'label' => 'Químico',   'desc' => 'Indicador químico (cinta testigo)', 'icon' => '🧫', 'required' => true],
                        ['tipo' => 'biologico', 'label' => 'Biológico', 'desc' => 'Integradores biológicos',          'icon' => '🦠', 'required' => false],
                    ]
                    : [
                        ['tipo' => 'quimico',   'label' => 'Químico',   'desc' => 'Indicador químico ETO',    'icon' => '🧫', 'required' => true],
                        ['tipo' => 'biologico', 'label' => 'Biológico', 'desc' => 'Integrador biológico ETO', 'icon' => '🦠', 'required' => true],
                    ];
            @endphp

            <div class="space-y-3">
                @foreach($controlesBase as $idx => $ctrl)
                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="text-xl">{{ $ctrl['icon'] }}</span>
                            <div>
                                <p class="text-xs font-bold text-slate-800">
                                    Control {{ $ctrl['label'] }}
                                    @if($ctrl['required']) <span class="text-red-500">*</span> @else <span class="text-slate-400">(cuando aplique)</span> @endif
                                </p>
                                <p class="text-xs text-slate-500">{{ $ctrl['desc'] }}</p>
                            </div>
                        </div>
                        <input type="hidden" name="controles[{{ $idx }}][tipo]" value="{{ $ctrl['tipo'] }}" />

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Resultado</label>
                                <select name="controles[{{ $idx }}][resultado]"
                                        class="input-field text-xs"
                                        onchange="onControlResultado(this)">
                                    <option value="conforme" {{ old("controles.{$idx}.resultado") === 'conforme' ? 'selected' : '' }}>✅ Conforme</option>
                                    <option value="no_conforme" {{ old("controles.{$idx}.resultado") === 'no_conforme' ? 'selected' : '' }}>❌ No conforme</option>
                                    @if($ctrl['tipo'] === 'biologico')
                                        <option value="pendiente" {{ old("controles.{$idx}.resultado", 'pendiente') === 'pendiente' ? 'selected' : '' }}>⏳ Pendiente lectura</option>
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Descripción / referencia</label>
                                <input type="text" name="controles[{{ $idx }}][descripcion]"
                                       value="{{ old("controles.{$idx}.descripcion") }}"
                                       placeholder="{{ $ctrl['tipo'] === 'fisico' ? 'Ej: Hoja impresora OK' : ($ctrl['tipo'] === 'quimico' ? 'Ej: Cinta viraje completo' : 'Ej: Spore test lote 123') }}"
                                       class="input-field text-xs" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">Observaciones</label>
                                <input type="text" name="controles[{{ $idx }}][observaciones]"
                                       value="{{ old("controles.{$idx}.observaciones") }}"
                                       placeholder="Opcional..."
                                       class="input-field text-xs" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Alerta control no conforme --}}
            <div id="alerta-no-conforme" class="hidden mt-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs font-semibold text-red-700">
                ⛔ Hay un control no conforme. Los lotes de este ciclo quedarán en estado <strong>RETENIDO</strong> automáticamente.
            </div>
        </div>

        {{-- Observaciones --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-3 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-300 text-xs font-bold text-slate-600">5</span>
                Observaciones generales
                <span class="text-xs font-normal text-slate-400">(opcional)</span>
            </h2>
            <textarea name="observaciones" rows="3"
                      placeholder="Anotá cualquier observación sobre el ciclo, el material o el equipo..."
                      class="input-field resize-none">{{ old('observaciones') }}</textarea>
        </div>
    </div>

    {{-- ============================================================ SIDEBAR ============================================================ --}}
    <div class="space-y-4">

        {{-- Resumen --}}
        <div class="rounded-xl bg-slate-900 text-white p-5">
            <p class="text-xs text-slate-400 mb-3">Resumen del ciclo</p>
            <dl class="space-y-2 text-xs">
                <div class="flex justify-between">
                    <dt class="text-slate-400">Método</dt>
                    <dd class="font-bold">{{ $metodo === 'vapor' ? '♨️ Vapor' : '🧪 ETO' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Equipo</dt>
                    <dd class="font-semibold text-white" id="resumen-equipo">—</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-400">Lotes</dt>
                    <dd class="font-bold text-teal-400" id="resumen-lotes">0</dd>
                </div>
            </dl>
        </div>

        {{-- Referencia parámetros --}}
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs font-semibold text-slate-600 mb-2">
                {{ $metodo === 'vapor' ? '♨️ Referencia vapor' : '🧪 Referencia ETO' }}
            </p>
            @if($metodo === 'vapor')
                <ul class="space-y-1 text-xs text-slate-500">
                    <li>• Ciclo estándar: <strong>134°C / 18 min</strong></li>
                    <li>• Ciclo flash: <strong>134°C / 4 min</strong></li>
                    <li>• Ciclo 121°C: <strong>121°C / 30 min</strong></li>
                    <li>• Presión típica: <strong>2.1 bar</strong></li>
                </ul>
            @else
                <ul class="space-y-1 text-xs text-slate-500">
                    <li>• Temperatura: <strong>55°C ± 5°C</strong></li>
                    <li>• Aireación mínima: <strong>12h a 50°C</strong></li>
                    <li>• O <strong>8h a 60°C</strong></li>
                </ul>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full rounded-xl bg-teal-600 px-6 py-3.5 text-sm font-bold text-white shadow-sm hover:bg-teal-700 active:scale-95 transition-all">
            Registrar ciclo
        </button>
        <a href="{{ route('esterilizacion.index') }}"
           class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">
            Cancelar
        </a>
    </div>
</div>
</form>

<style>
    .input-field { width:100%; border-radius:.5rem; border:1px solid rgb(203 213 225); background:white; padding:.5rem .75rem; font-size:.875rem; color:rgb(30 41 59); transition:border-color .15s,box-shadow .15s; }
    .input-field:focus { outline:none; border-color:rgb(20 184 166); box-shadow:0 0 0 3px rgb(20 184 166/.15); }
    .equipo-card { display:block; border-radius:.75rem; border:2px solid rgb(226 232 240); padding:.875rem; cursor:pointer; transition:all .15s; }
    .equipo-card:hover { border-color:rgb(94 234 212); background:rgb(240 253 250); }
    .equipo-card--active { border-color:rgb(20 184 166)!important; background:rgb(240 253 250)!important; }
    .lote-check.checked { border-color:rgb(20 184 166); background:rgb(240 253 250); }
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
</style>

<script>
    // Equipos
    function selectEquipo(id) {
        document.querySelectorAll('.equipo-card').forEach(c => c.classList.remove('equipo-card--active'));
        document.getElementById('eq-' + id).classList.add('equipo-card--active');
        document.querySelector(`input[name="equipo_id"][value="${id}"]`).checked = true;
        const nombre = document.getElementById('eq-' + id).querySelector('p').textContent.trim();
        document.getElementById('resumen-equipo').textContent = nombre;
    }

    // Lotes checkbox
    function toggleLote(label) {
        const cb = label.querySelector('input[type=checkbox]');
        cb.checked = !cb.checked;
        label.classList.toggle('border-teal-400', cb.checked);
        label.classList.toggle('bg-teal-50', cb.checked);
        label.classList.toggle('border-slate-200', !cb.checked);
        actualizarResumenLotes();
    }

    function actualizarResumenLotes() {
        const total = document.querySelectorAll('input[name="lote_ids[]"]:checked').length;
        document.getElementById('resumen-lotes').textContent = total;
    }

    // Control no conforme — alerta
    function onControlResultado(select) {
        const hayNoConforme = [...document.querySelectorAll('select[name*="[resultado]"]')]
            .some(s => s.value === 'no_conforme');
        document.getElementById('alerta-no-conforme').classList.toggle('hidden', !hayNoConforme);
    }

    // Inicializar
    document.querySelectorAll('input[name="lote_ids[]"]').forEach(cb => {
        cb.addEventListener('change', actualizarResumenLotes);
    });
    actualizarResumenLotes();

    @if(old('equipo_id'))
        selectEquipo({{ old('equipo_id') }});
    @endif
</script>

@endsection