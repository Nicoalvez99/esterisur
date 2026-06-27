@extends('layouts.modulo')

@section('title', 'Revisar — ' . $lote->numero_lote)
@section('modulo-label', 'Control de calidad')
@section('page-title', 'Revisión del lote')
@section('page-subtitle', $lote->numero_lote . ' · ' . ($lote->recepcion?->institucion?->nombre ?? ''))

@section('sidebar-nav')
    <a href="{{ route('calidad.index') }}" class="nav-op">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver al panel
    </a>
@endsection

@section('content')

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- ============================================================ COLUMNA PRINCIPAL ============================================================ --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Info del lote --}}
        <div class="rounded-xl border border-teal-200 bg-teal-50 p-4">
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-xs">
                <div><span class="text-teal-600">Lote</span> <span class="ml-1.5 font-mono font-bold text-teal-900">{{ $lote->numero_lote }}</span></div>
                <div><span class="text-teal-600">Institución</span> <span class="ml-1.5 font-semibold text-teal-900">{{ $lote->recepcion?->institucion?->nombre ?? '—' }}</span></div>
                <div><span class="text-teal-600">Método</span> <span class="ml-1.5 font-semibold text-teal-900 uppercase">{{ $lote->metodo_esterilizacion }}</span></div>
                <div><span class="text-teal-600">Equipo</span> <span class="ml-1.5 font-semibold text-teal-900">{{ $esterilizacion->equipo?->nombre ?? '—' }}</span></div>
                @if($lote->prioridad !== 'normal')
                    <span @class(['rounded-full px-2.5 py-0.5 text-xs font-bold', 'bg-amber-500 text-white' => $lote->prioridad === 'urgente', 'bg-red-600 text-white' => $lote->prioridad === 'critica'])>
                        ⚡ {{ ucfirst($lote->prioridad) }}
                    </span>
                @endif
            </div>
        </div>

        {{-- SECCIÓN 1: Parámetros del ciclo (solo lectura) --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="text-lg">{{ $lote->metodo_esterilizacion === 'vapor' ? '♨️' : '🧪' }}</span>
                Parámetros del ciclo #{{ $esterilizacion->id }}
            </h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach([
                    ['label' => 'Temperatura',   'value' => ($esterilizacion->temperatura ?? '—') . '°C'],
                    ['label' => 'Tiempo',         'value' => ($esterilizacion->tiempo_minutos ?? '—') . ' min'],
                    ['label' => $lote->metodo_esterilizacion === 'vapor' ? 'Presión' : 'Concentración',
                     'value' => $lote->metodo_esterilizacion === 'vapor'
                         ? ($esterilizacion->presion ?? '—') . ' bar'
                         : ($esterilizacion->concentracion ?? '—') . ' mg/L'],
                    ['label' => 'Duración real',  'value' => ($esterilizacion->duracionMinutos() ?? '—') . ' min'],
                ] as $p)
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <p class="text-xs text-slate-500">{{ $p['label'] }}</p>
                        <p class="mt-1 text-sm font-bold text-slate-800">{{ $p['value'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- ETO: aireación --}}
            @if($lote->metodo_esterilizacion === 'eto')
                <div @class([
                    'mt-4 rounded-lg px-4 py-3 flex items-center gap-2 text-xs font-semibold',
                    'bg-green-50 border border-green-200 text-green-700' => $postProcesoOk,
                    'bg-red-50 border border-red-200 text-red-700'       => !$postProcesoOk,
                ])>
                    {{ $postProcesoOk ? '✅' : '❌' }}
                    @if($postProcesoOk)
                        Aireación completada — {{ $esterilizacion->aireacionHoras() }}h
                        ({{ $esterilizacion->aireacion_inicio?->format('d/m H:i') }} → {{ $esterilizacion->aireacion_fin?->format('H:i') }})
                    @else
                        Aireación ETO <strong>no registrada</strong>. No se puede liberar hasta completarla en el módulo de esterilización.
                    @endif
                </div>
            @endif
        </div>

        {{-- SECCIÓN 2: Controles --}}
        <div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800">Controles del ciclo</h2>
                @if($hayPendiente)
                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-700">
                        ⏳ Hay controles pendientes
                    </span>
                @elseif($todosConformes)
                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-700">
                        ✅ Todos conformes
                    </span>
                @elseif($hayNoConforme)
                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-semibold text-red-700">
                        ❌ Hay no conformes
                    </span>
                @endif
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($esterilizacion->controles as $control)
                    <div @class([
                        'px-5 py-4',
                        'bg-red-50/50'   => $control->resultado === 'no_conforme',
                        'bg-amber-50/50' => $control->resultado === 'pendiente',
                    ])>
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3 flex-1">
                                <span class="text-xl mt-0.5">
                                    {{ match($control->tipo) { 'fisico' => '📊', 'quimico' => '🧫', 'biologico' => '🦠', default => '🔬' } }}
                                </span>
                                <div class="flex-1">
                                    <p class="text-xs font-bold text-slate-800">Control {{ $control->tipo_label }}</p>
                                    @if($control->descripcion)
                                        <p class="text-xs text-slate-500">{{ $control->descripcion }}</p>
                                    @endif
                                    @if($control->observaciones)
                                        <p class="text-xs text-slate-400 italic">{{ $control->observaciones }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400 mt-1">
                                        Registrado por {{ $control->operario?->name ?? '—' }}
                                        @if($control->fecha_lectura)
                                            · {{ $control->fecha_lectura->format('d/m H:i') }}
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="shrink-0">
                                <span @class([
                                    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
                                    'bg-green-100 text-green-700' => $control->resultado === 'conforme',
                                    'bg-red-100 text-red-700'     => $control->resultado === 'no_conforme',
                                    'bg-amber-100 text-amber-700' => $control->resultado === 'pendiente',
                                ])>
                                    {{ match($control->resultado) { 'conforme' => '✅ Conforme', 'no_conforme' => '❌ No conforme', 'pendiente' => '⏳ Pendiente', default => $control->resultado } }}
                                </span>
                            </div>
                        </div>

                        {{-- Formulario para actualizar controles pendientes o no conformes --}}
                        @if($control->resultado !== 'conforme' && !$lote->liberacion)
                            <form method="POST" action="{{ route('calidad.control.update', $control->id) }}"
                                  class="mt-3 rounded-lg border border-slate-200 bg-white p-3">
                                @csrf @method('PATCH')
                                <p class="text-xs font-semibold text-slate-600 mb-2">Actualizar resultado</p>
                                <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                    <div>
                                        <select name="resultado" class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-teal-500 focus:outline-none">
                                            <option value="conforme"    {{ $control->resultado === 'conforme'    ? 'selected' : '' }}>✅ Conforme</option>
                                            <option value="no_conforme" {{ $control->resultado === 'no_conforme' ? 'selected' : '' }}>❌ No conforme</option>
                                        </select>
                                    </div>
                                    <div>
                                        <input type="text" name="descripcion" value="{{ $control->descripcion }}"
                                               placeholder="Descripción / referencia"
                                               class="w-full rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-teal-500 focus:outline-none" />
                                    </div>
                                    <div class="flex gap-2">
                                        <input type="text" name="observaciones" value="{{ $control->observaciones }}"
                                               placeholder="Observaciones..."
                                               class="flex-1 rounded-lg border border-slate-300 px-2 py-1.5 text-xs focus:border-teal-500 focus:outline-none" />
                                        <button type="submit"
                                                class="rounded-lg bg-teal-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-teal-700 transition-colors whitespace-nowrap">
                                            Guardar
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="px-5 py-6 text-center text-sm text-slate-400">Sin controles registrados para este ciclo.</div>
                @endforelse
            </div>
        </div>

        {{-- SECCIÓN 3: Decisión de liberación --}}
        @if(!$lote->liberacion)
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800">Decisión de liberación</h2>

                {{-- Checklist de requisitos --}}
                <div class="mb-5 space-y-2">
                    @php
                        $checks = [
                            ['ok' => $controlesCompletos && !$hayNoConforme, 'label' => 'Controles completos y conformes',
                             'fail' => $hayNoConforme ? 'Hay controles no conformes.' : ($hayPendiente ? 'Hay controles pendientes de lectura.' : '')],
                            ['ok' => $postProcesoOk, 'label' => $lote->metodo_esterilizacion === 'eto' ? 'Aireación ETO completada' : 'Post-proceso OK',
                             'fail' => 'La aireación ETO no está registrada.'],
                        ];
                    @endphp
                    @foreach($checks as $check)
                        <div @class([
                            'flex items-start gap-2.5 rounded-lg px-3 py-2.5 text-xs',
                            'bg-green-50 border border-green-200' => $check['ok'],
                            'bg-red-50 border border-red-200'     => !$check['ok'],
                        ])>
                            <span class="text-base shrink-0">{{ $check['ok'] ? '✅' : '❌' }}</span>
                            <div>
                                <p class="font-semibold {{ $check['ok'] ? 'text-green-800' : 'text-red-800' }}">
                                    {{ $check['label'] }}
                                </p>
                                @if(!$check['ok'] && $check['fail'])
                                    <p class="text-red-600 mt-0.5">{{ $check['fail'] }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <form method="POST" action="{{ route('calidad.liberar', $lote->id) }}" novalidate>
                @csrf
                <input type="hidden" name="esterilizacion_id" value="{{ $esterilizacion->id }}" />
                <input type="hidden" name="controles_completos" value="{{ ($controlesCompletos && !$hayNoConforme) ? '1' : '0' }}" />
                <input type="hidden" name="post_proceso_ok" value="{{ $postProcesoOk ? '1' : '0' }}" />
                <input type="hidden" name="sin_incidencias_abiertas" value="1" />

                {{-- Decisión --}}
                <div class="mb-4">
                    <p class="text-xs font-semibold text-slate-700 mb-2">Decisión <span class="text-red-500">*</span></p>
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                        @foreach([
                            ['val' => 'liberado',  'label' => '✅ Liberar',  'sub' => 'Pasa a almacenamiento',
                             'disabled' => !($controlesCompletos && !$hayNoConforme && $postProcesoOk)],
                            ['val' => 'retenido',  'label' => '⚠️ Retener',  'sub' => 'Queda en espera',
                             'disabled' => false],
                            ['val' => 'rechazado', 'label' => '❌ Rechazar', 'sub' => 'No apto para entrega',
                             'disabled' => false],
                        ] as $dec)
                            <label @class([
                                'decision-card rounded-xl border-2 p-3 transition-all',
                                'border-green-300 bg-green-50'    => $dec['val'] === 'liberado',
                                'border-amber-300 bg-amber-50'    => $dec['val'] === 'retenido',
                                'border-red-300 bg-red-50'        => $dec['val'] === 'rechazado',
                                'cursor-pointer hover:opacity-90' => !$dec['disabled'],
                                'opacity-40 cursor-not-allowed'   => $dec['disabled'],
                            ])
                                id="dec-{{ $dec['val'] }}"
                                data-val="{{ $dec['val'] }}"
                                data-disabled="{{ $dec['disabled'] ? 'true' : 'false' }}">
                                <input type="radio" name="resultado" value="{{ $dec['val'] }}"
                                       class="sr-only" {{ $dec['disabled'] ? 'disabled' : '' }} />
                                <p class="text-sm font-bold text-slate-800">{{ $dec['label'] }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $dec['sub'] }}</p>
                            </label>
                        @endforeach
                    </div>
                    @error('resultado') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Observaciones --}}
                <div class="mb-4">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Observaciones
                        <span class="text-slate-400 font-normal">(requeridas si se retiene o rechaza)</span>
                    </label>
                    <textarea name="observaciones" rows="3"
                              placeholder="Motivo de la decisión, acción correctiva, notas para el próximo paso..."
                              class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 resize-none">{{ old('observaciones') }}</textarea>
                </div>

                <button type="submit" id="btn-liberar"
                        disabled
                        class="w-full rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white hover:bg-teal-700 active:scale-95 transition-all disabled:opacity-40 disabled:cursor-not-allowed">
                    Confirmar decisión
                </button>
                </form>
            </div>

        @else
            {{-- Ya tiene liberación registrada --}}
            @php $lib = $lote->liberacion; @endphp
            <div @class([
                'rounded-xl p-5 border',
                'bg-green-50 border-green-200' => $lib->resultado === 'liberado',
                'bg-amber-50 border-amber-200' => $lib->resultado === 'retenido',
                'bg-red-50 border-red-200'     => $lib->resultado === 'rechazado',
            ])>
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-2xl">
                        {{ match($lib->resultado) { 'liberado' => '✅', 'retenido' => '⚠️', 'rechazado' => '❌', default => '🔵' } }}
                    </span>
                    <div>
                        <p @class(['text-sm font-bold', 'text-green-800' => $lib->resultado === 'liberado', 'text-amber-800' => $lib->resultado === 'retenido', 'text-red-800' => $lib->resultado === 'rechazado'])>
                            Lote {{ strtoupper($lib->resultado) }}
                        </p>
                        <p class="text-xs text-slate-500">
                            por {{ $lib->responsable?->name ?? '—' }}
                            · {{ $lib->fecha_liberacion?->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
                @if($lib->observaciones)
                    <p class="text-xs text-slate-600 mt-2 border-t border-slate-200 pt-2">{{ $lib->observaciones }}</p>
                @endif
            </div>
        @endif

        {{-- Historial --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800">Historial del lote</h2>
            @if($lote->historial->count())
                <ol class="relative border-l border-slate-200 space-y-4 ml-2">
                    @foreach($lote->historial->sortByDesc('created_at') as $item)
                        <li class="pl-5">
                            <div class="absolute -left-1.5 mt-1 h-3 w-3 rounded-full border-2 border-white bg-teal-400"></div>
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <p class="text-xs font-semibold text-slate-800">{{ $item->accion }}</p>
                                    @if($item->estado_origen && $item->estado_destino)
                                        <p class="text-xs text-slate-400">{{ ucfirst(str_replace('_', ' ', $item->estado_origen)) }} → {{ ucfirst(str_replace('_', ' ', $item->estado_destino)) }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400">por {{ $item->user?->name ?? '—' }}</p>
                                </div>
                                <span class="shrink-0 text-xs text-slate-400">{{ $item->created_at->format('d/m H:i') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ol>
            @else
                <p class="text-sm text-slate-400">Sin historial.</p>
            @endif
        </div>

    </div>

    {{-- ============================================================ SIDEBAR ============================================================ --}}
    <div class="space-y-4">

        {{-- Lote --}}
        <div class="rounded-xl bg-slate-900 text-white p-5 text-center">
            <p class="text-xs text-slate-400 mb-1">Lote</p>
            <p class="font-mono text-xl font-bold">{{ $lote->numero_lote }}</p>
            <p class="mt-2">
                <span @class([
                    'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
                    'bg-yellow-500/20 text-yellow-300' => $lote->estado_actual === 'control_calidad',
                    'bg-green-500/20 text-green-300'   => $lote->estado_actual === 'almacenamiento',
                    'bg-red-500/20 text-red-300'       => in_array($lote->estado_actual, ['retenido', 'rechazado']),
                ])>
                    {{ ucfirst(str_replace('_', ' ', $lote->estado_actual)) }}
                </span>
            </p>
        </div>

        {{-- Resumen verificación --}}
        <div class="rounded-xl bg-white border border-slate-200 p-4">
            <p class="text-xs font-semibold text-slate-600 mb-3">Verificación previa</p>
            <ul class="space-y-2 text-xs">
                <li class="flex items-center gap-2 {{ ($controlesCompletos && !$hayNoConforme) ? 'text-green-700' : 'text-red-600' }}">
                    <span>{{ ($controlesCompletos && !$hayNoConforme) ? '✅' : '❌' }}</span>
                    Controles completos y conformes
                </li>
                <li class="flex items-center gap-2 {{ $postProcesoOk ? 'text-green-700' : 'text-red-600' }}">
                    <span>{{ $postProcesoOk ? '✅' : '❌' }}</span>
                    Post-proceso {{ $lote->metodo_esterilizacion === 'eto' ? '(aireación)' : '(enfriamiento)' }} OK
                </li>
            </ul>
        </div>

        {{-- Datos recepción --}}
        <div class="rounded-xl bg-white border border-slate-200 p-4 space-y-1.5 text-xs">
            <p class="font-semibold text-slate-600 mb-2">Datos del lote</p>
            <p class="text-slate-500">Entrega pactada: <span class="font-medium text-slate-700">{{ $lote->recepcion?->fecha_entrega_pactada?->format('d/m/Y') ?? '—' }}</span></p>
            <p class="text-slate-500">Recibido: <span class="font-medium text-slate-700">{{ $lote->fecha_recepcion?->format('d/m/Y H:i') ?? '—' }}</span></p>
            @if($lote->recepcion?->tiene_remito)
                <p class="text-slate-500">Remito: <span class="font-mono font-medium text-slate-700">{{ $lote->recepcion->remito_numero }}</span></p>
            @endif
        </div>

        <a href="{{ route('calidad.index') }}"
           class="block w-full rounded-xl border border-slate-200 px-6 py-2.5 text-center text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
            ← Volver al panel
        </a>
    </div>
</div>

<style>
    .decision-card { display:block; }
    .decision-card--active { box-shadow: 0 0 0 2px rgb(20 184 166); }
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
</style>

<script>
    // Usar event delegation en lugar de onclick inline
    document.querySelectorAll('.decision-card').forEach(function(card) {
        if (card.dataset.disabled === 'true') return;

        card.addEventListener('click', function() {
            const val = this.dataset.val;

            // Limpiar estado visual de todas las cards
            document.querySelectorAll('.decision-card').forEach(function(c) {
                c.classList.remove('ring-2', 'ring-green-500', 'ring-amber-500', 'ring-red-500');
            });

            // Marcar el radio
            this.querySelector('input[type=radio]').checked = true;

            // Aplicar ring según decisión
            const ringMap = { liberado: 'ring-green-500', retenido: 'ring-amber-500', rechazado: 'ring-red-500' };
            this.classList.add('ring-2', ringMap[val] || 'ring-teal-500');

            // Habilitar y actualizar texto del botón
            const btn = document.getElementById('btn-liberar');
            btn.disabled = false;
            const textMap = {
                liberado:  '✅ Confirmar liberación',
                retenido:  '⚠️ Confirmar retención',
                rechazado: '❌ Confirmar rechazo',
            };
            btn.textContent = textMap[val] || 'Confirmar';
        });
    });
</script>

@endsection