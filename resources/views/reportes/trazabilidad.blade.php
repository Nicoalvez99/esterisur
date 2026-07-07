@extends('layouts.modulo')

@section('title', 'Trazabilidad')
@section('modulo-label', 'Reportes')
@section('page-title', 'Trazabilidad por lote')
@section('page-subtitle', 'Historial completo de recepción a entrega')

@section('sidebar-nav')
    <a href="{{ route('reportes.index') }}" class="nav-op">← Panel</a>
@endsection

@section('content')

{{-- Buscador --}}
<form method="GET" action="{{ route('reportes.trazabilidad') }}"
      class="mb-6 rounded-xl bg-white border border-slate-200 p-5">
    <label class="block text-xs font-semibold text-slate-600 mb-2">Número de lote o UUID</label>
    <div class="flex gap-3">
        <input type="text" name="numero_lote"
               value="{{ request('numero_lote') }}"
               placeholder="Ej: LOT-20250615-0001"
               autofocus
               class="flex-1 rounded-lg border border-slate-300 px-4 py-2.5 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500" />
        <button type="submit"
                class="rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-teal-700 transition-colors">
            Buscar
        </button>
    </div>
</form>

@if(request('numero_lote') && !$lote)
    <div class="rounded-xl border border-red-200 bg-red-50 p-5 text-center">
        <p class="text-sm font-semibold text-red-700">No se encontró el lote "{{ request('numero_lote') }}"</p>
        <p class="text-xs text-red-500 mt-1">Verificá el número e intentá de nuevo.</p>
    </div>
@endif

@if($lote)
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Header del lote --}}
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="font-mono text-lg font-bold text-slate-800">{{ $lote->numero_lote }}</span>
                    @php
                        $estadoColors = ['recepcion' => 'bg-blue-100 text-blue-700', 'acondicionamiento' => 'bg-indigo-100 text-indigo-700', 'vapor' => 'bg-teal-100 text-teal-700', 'eto' => 'bg-purple-100 text-purple-700', 'control_calidad' => 'bg-yellow-100 text-yellow-700', 'almacenamiento' => 'bg-green-100 text-green-700', 'finalizado' => 'bg-slate-100 text-slate-600', 'retenido' => 'bg-amber-100 text-amber-700', 'rechazado' => 'bg-red-100 text-red-700'];
                    @endphp
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $estadoColors[$lote->estado_actual] ?? 'bg-slate-100 text-slate-600' }}">
                        {{ ucfirst(str_replace('_', ' ', $lote->estado_actual)) }}
                    </span>
                    <span @class(['inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold', 'bg-blue-100 text-blue-700' => $lote->metodo_esterilizacion === 'vapor', 'bg-purple-100 text-purple-700' => $lote->metodo_esterilizacion === 'eto'])>
                        {{ strtoupper($lote->metodo_esterilizacion) }}
                    </span>
                    @if($lote->prioridad !== 'normal')
                        <span @class(['rounded-full px-2.5 py-0.5 text-xs font-bold', 'bg-amber-500 text-white' => $lote->prioridad === 'urgente', 'bg-red-600 text-white' => $lote->prioridad === 'critica'])>
                            ⚡ {{ ucfirst($lote->prioridad) }}
                        </span>
                    @endif
                </div>
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-3 text-xs">
                    <div><dt class="text-slate-400">Institución</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $lote->institucion?->nombre ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Recepción</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $lote->fecha_recepcion?->format('d/m/Y H:i') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Entrega pactada</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $lote->fecha_entrega_pactada?->format('d/m/Y') ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Finalización</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $lote->fecha_finalizacion?->format('d/m/Y H:i') ?? '—' }}</dd></div>
                    @if($lote->fecha_finalizacion && $lote->fecha_recepcion)
                        <div><dt class="text-slate-400">Tiempo total</dt><dd class="font-semibold text-teal-700 mt-0.5">{{ $lote->fecha_recepcion->diffForHumans($lote->fecha_finalizacion, true) }}</dd></div>
                    @endif
                </dl>
            </div>

            {{-- Timeline del proceso --}}
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-5 text-sm font-semibold text-slate-800">Recorrido del lote</h2>
                <ol class="relative border-l-2 border-slate-200 space-y-6 ml-3">

                    {{-- 1. Recepción --}}
                    @if($lote->recepcion)
                        @php $rec = $lote->recepcion; @endphp
                        <li class="pl-6">
                            <div class="absolute -left-2.5 mt-1 h-5 w-5 rounded-full border-2 border-white bg-blue-500 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">1</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800">Recepción</p>
                            <p class="text-xs text-slate-400 mb-2">{{ $rec->created_at->format('d/m/Y H:i') }} · {{ $rec->operario?->name }}</p>
                            <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2 text-xs space-y-1">
                                <p><span class="text-slate-400">Chofer:</span> <span class="font-medium">{{ $rec->chofer_nombre ?? '—' }}</span></p>
                                <p><span class="text-slate-400">Remito:</span> <span class="font-medium">{{ $rec->tiene_remito ? $rec->remito_numero : 'Sin remito' }}</span></p>
                                <p>
                                    <span class="text-slate-400">Cantidades:</span>
                                    <span class="font-medium">
                                        {{ collect(['Cajas: '.$rec->cant_cajas, 'Bultos: '.$rec->cant_bultos, 'Unidades: '.$rec->cant_unidades])->filter(fn($v) => !str_ends_with($v, ': 0'))->implode(' · ') }}
                                    </span>
                                </p>
                                <p><span class="text-slate-400">Empaque:</span> <span class="font-medium">{{ $rec->estado_empaque === 'empaquetado' ? '✅ Empaquetado' : '🔄 Sin empaquetar' }}</span></p>
                            </div>
                        </li>
                    @endif

                    {{-- 2. Acondicionamiento --}}
                    @if($lote->acondicionamiento)
                        @php $acond = $lote->acondicionamiento; @endphp
                        <li class="pl-6">
                            <div class="absolute -left-2.5 mt-1 h-5 w-5 rounded-full border-2 border-white bg-indigo-500 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">2</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800">Acondicionamiento</p>
                            <p class="text-xs text-slate-400 mb-2">{{ $acond->created_at->format('d/m/Y H:i') }} · {{ $acond->operario?->name }}</p>
                            <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2 text-xs space-y-1">
                                <p><span class="text-slate-400">Declarado:</span> <span class="font-medium">{{ $acond->cant_declarada }}</span> · <span class="text-slate-400">Real:</span> <span class="font-medium {{ $acond->diferencia != 0 ? 'text-amber-600' : '' }}">{{ $acond->cant_real }}</span> @if($acond->diferencia != 0)<span class="text-amber-600">({{ $acond->diferencia > 0 ? '+' : '' }}{{ $acond->diferencia }})</span>@endif</p>
                                <p><span class="text-slate-400">Empaque:</span> <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $acond->tipo_empaque)) }} x{{ $acond->cant_empaque }}</span></p>
                                @if($acond->cant_devuelto > 0)
                                    <p class="text-amber-600 font-semibold">⚠️ {{ $acond->cant_devuelto }} ítem(s) devuelto(s)</p>
                                @endif
                                <p><span class="text-slate-400">Resultado:</span> <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $acond->resultado)) }}</span></p>
                            </div>
                        </li>
                    @endif

                    {{-- 3. Esterilización --}}
                    @if($esterilizacion)
                        <li class="pl-6">
                            <div class="absolute -left-2.5 mt-1 h-5 w-5 rounded-full border-2 border-white bg-teal-500 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">3</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800">Esterilización — {{ strtoupper($esterilizacion->metodo) }}</p>
                            <p class="text-xs text-slate-400 mb-2">{{ $esterilizacion->fecha_inicio?->format('d/m/Y H:i') }} · {{ $esterilizacion->operario?->name }}</p>
                            <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2 text-xs space-y-1">
                                <p><span class="text-slate-400">Equipo:</span> <span class="font-medium">{{ $esterilizacion->equipo?->nombre }}</span></p>
                                <p><span class="text-slate-400">Temperatura:</span> <span class="font-medium">{{ $esterilizacion->temperatura }}°C</span> · <span class="text-slate-400">Tiempo:</span> <span class="font-medium">{{ $esterilizacion->tiempo_minutos }} min</span></p>
                                @if($esterilizacion->metodo === 'eto' && $esterilizacion->aireacion_fin)
                                    <p><span class="text-slate-400">Aireación:</span> <span class="font-medium">{{ $esterilizacion->aireacionHoras() }}h</span></p>
                                @endif
                                <div class="flex gap-2 mt-1.5 flex-wrap">
                                    @foreach($esterilizacion->controles as $ctrl)
                                        <span @class(['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', 'bg-green-100 text-green-700' => $ctrl->resultado === 'conforme', 'bg-red-100 text-red-700' => $ctrl->resultado === 'no_conforme', 'bg-amber-100 text-amber-700' => $ctrl->resultado === 'pendiente'])>
                                            {{ $ctrl->tipo_label }}: {{ $ctrl->resultado === 'conforme' ? '✅' : ($ctrl->resultado === 'no_conforme' ? '❌' : '⏳') }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </li>
                    @endif

                    {{-- 4. Liberación --}}
                    @if($lote->liberacion)
                        @php $lib = $lote->liberacion; @endphp
                        <li class="pl-6">
                            <div @class(['absolute -left-2.5 mt-1 h-5 w-5 rounded-full border-2 border-white flex items-center justify-center', 'bg-green-500' => $lib->resultado === 'liberado', 'bg-amber-500' => $lib->resultado === 'retenido', 'bg-red-500' => $lib->resultado === 'rechazado'])>
                                <span class="text-white text-xs font-bold">4</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800">Liberación — {{ ucfirst($lib->resultado) }}</p>
                            <p class="text-xs text-slate-400 mb-2">{{ $lib->fecha_liberacion?->format('d/m/Y H:i') }} · {{ $lib->responsable?->name }}</p>
                            @if($lib->observaciones)
                                <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2 text-xs">
                                    <p class="text-slate-600">{{ $lib->observaciones }}</p>
                                </div>
                            @endif
                        </li>
                    @endif

                    {{-- 5. Despacho --}}
                    @if($lote->remito)
                        @php $rem = $lote->remito; @endphp
                        <li class="pl-6">
                            <div class="absolute -left-2.5 mt-1 h-5 w-5 rounded-full border-2 border-white bg-slate-800 flex items-center justify-center">
                                <span class="text-white text-xs font-bold">5</span>
                            </div>
                            <p class="text-xs font-bold text-slate-800">Despacho · <span class="font-mono">{{ $rem->numero }}</span></p>
                            <p class="text-xs text-slate-400 mb-2">{{ $rem->fecha_despacho?->format('d/m/Y H:i') }} · {{ $rem->operario?->name }}</p>
                            <div class="rounded-lg bg-slate-50 border border-slate-100 px-3 py-2 text-xs space-y-1">
                                <p><span class="text-slate-400">Chofer:</span> <span class="font-medium">{{ $rem->chofer_nombre ?? '—' }}</span></p>
                                <p><span class="text-slate-400">Total bultos:</span> <span class="font-medium">{{ $rem->totalBultos() }}</span></p>
                                <p><span class="text-slate-400">Facturado:</span> <span class="font-medium">{{ $rem->facturado ? '✅ Sí' : '⏳ Pendiente' }}</span></p>
                            </div>
                        </li>
                    @endif
                </ol>
            </div>

            {{-- Historial completo --}}
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800">Historial de acciones</h2>
                <div class="space-y-2">
                    @foreach($lote->historial->sortByDesc('created_at') as $item)
                        <div class="flex items-start justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2.5 text-xs">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $item->accion }}</p>
                                @if($item->estado_origen && $item->estado_destino)
                                    <p class="text-slate-400">{{ ucfirst(str_replace('_',' ',$item->estado_origen)) }} → {{ ucfirst(str_replace('_',' ',$item->estado_destino)) }}</p>
                                @endif
                                <p class="text-slate-400">{{ $item->user?->name ?? '—' }}</p>
                            </div>
                            <span class="shrink-0 font-mono text-slate-400">{{ $item->created_at->format('d/m H:i') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="space-y-4">
            <div class="rounded-xl bg-slate-900 text-white p-5 text-center">
                <p class="text-xs text-slate-400 mb-1">Lote</p>
                <p class="font-mono text-lg font-bold">{{ $lote->numero_lote }}</p>
                <p class="text-xs text-slate-400 mt-2 font-mono break-all">{{ $lote->uuid }}</p>
            </div>

            <button onclick="window.print()"
                    class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                🖨️ Imprimir trazabilidad
            </button>
        </div>
    </div>
@endif

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    @media print { button { display:none!important; } }
</style>

@endsection