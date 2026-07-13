@extends('layouts.modulo')

@section('title', 'Lote ' . $lote->numero_lote)
@section('modulo-label', 'Auditoría')
@section('page-title', $lote->numero_lote)
@section('page-subtitle', 'Detalle completo · solo lectura')

@section('sidebar-nav')
    <a href="{{ route('auditor.lotes') }}" class="nav-op">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver a lotes
    </a>
@endsection

@section('content')

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- COLUMNA PRINCIPAL --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Estado general --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <div class="flex flex-wrap items-center gap-3">
                @php
                    $estadoColors = ['recepcion' => 'bg-blue-100 text-blue-700', 'acondicionamiento' => 'bg-indigo-100 text-indigo-700', 'vapor' => 'bg-teal-100 text-teal-700', 'eto' => 'bg-purple-100 text-purple-700', 'control_calidad' => 'bg-yellow-100 text-yellow-700', 'almacenamiento' => 'bg-green-100 text-green-700', 'finalizado' => 'bg-slate-100 text-slate-600', 'retenido' => 'bg-amber-100 text-amber-700', 'rechazado' => 'bg-red-100 text-red-700'];
                @endphp
                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $estadoColors[$lote->estado_actual] ?? 'bg-slate-100 text-slate-600' }}">
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
                <span class="ml-auto text-xs text-slate-400">
                    Recibido {{ $lote->fecha_recepcion?->diffForHumans() }}
                </span>
            </div>
        </div>

        {{-- Recepción --}}
        @if($lote->recepcion)
            @php $rec = $lote->recepcion; @endphp
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-blue-500 text-xs font-bold text-white">1</span>
                    Recepción
                </h2>
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-3 text-xs">
                    <div><dt class="text-slate-400">Fecha</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rec->created_at->format('d/m/Y H:i') }}</dd></div>
                    <div><dt class="text-slate-400">Operario</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rec->operario?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Institución</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rec->institucion?->nombre ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Chofer</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rec->chofer_nombre ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Remito</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rec->tiene_remito ? $rec->remito_numero : 'Sin remito' }}</dd></div>
                    <div><dt class="text-slate-400">Empaque</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rec->estado_empaque === 'empaquetado' ? '✅ Empaquetado' : '🔄 Sin empaquetar' }}</dd></div>
                    <div><dt class="text-slate-400">Entrega pactada</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rec->fecha_entrega_pactada?->format('d/m/Y') ?? '—' }}</dd></div>
                </dl>
                <div class="mt-3 grid grid-cols-5 gap-2">
                    @foreach(['Cajas' => $rec->cant_cajas, 'Bultos' => $rec->cant_bultos, 'Unidades' => $rec->cant_unidades, 'Eq. ropa' => $rec->cant_equipos_ropa, 'Litros' => $rec->cant_litros] as $label => $val)
                        @if($val > 0)
                            <div class="rounded-lg bg-slate-50 border border-slate-100 p-2 text-center">
                                <p class="text-xs font-bold text-slate-700">{{ $val }}</p>
                                <p class="text-xs text-slate-400">{{ $label }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Acondicionamiento --}}
        @if($lote->acondicionamiento)
            @php $acond = $lote->acondicionamiento; @endphp
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-indigo-500 text-xs font-bold text-white">2</span>
                    Acondicionamiento
                </h2>
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4 text-xs mb-3">
                    <div><dt class="text-slate-400">Operario</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $acond->operario?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Declarado</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $acond->cant_declarada }}</dd></div>
                    <div><dt class="text-slate-400">Real</dt><dd class="font-semibold {{ $acond->diferencia != 0 ? 'text-amber-600' : 'text-slate-700' }} mt-0.5">{{ $acond->cant_real }}</dd></div>
                    <div><dt class="text-slate-400">Diferencia</dt><dd class="font-semibold {{ $acond->diferencia != 0 ? 'text-amber-600' : 'text-green-600' }} mt-0.5">{{ $acond->diferencia > 0 ? '+' : '' }}{{ $acond->diferencia }}</dd></div>
                    <div><dt class="text-slate-400">Empaque</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ ucfirst(str_replace('_',' ',$acond->tipo_empaque)) }} x{{ $acond->cant_empaque }}</dd></div>
                    <div><dt class="text-slate-400">Devueltos</dt><dd class="font-semibold {{ $acond->cant_devuelto > 0 ? 'text-amber-600' : 'text-slate-700' }} mt-0.5">{{ $acond->cant_devuelto }}</dd></div>
                    <div><dt class="text-slate-400">Resultado</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ ucfirst(str_replace('_',' ',$acond->resultado)) }}</dd></div>
                </dl>
                @if($acond->items->isNotEmpty())
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs border-t border-slate-100 pt-3">
                            <thead>
                                <tr class="text-slate-400">
                                    <th class="pb-1.5 text-left font-semibold">Ítem</th>
                                    <th class="pb-1.5 text-center font-semibold">Decl.</th>
                                    <th class="pb-1.5 text-center font-semibold">Real</th>
                                    <th class="pb-1.5 text-center font-semibold">Limpieza</th>
                                    <th class="pb-1.5 text-center font-semibold">Acción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50">
                                @foreach($acond->items as $item)
                                    <tr>
                                        <td class="py-1.5 font-medium text-slate-700">{{ $item->nombre }}</td>
                                        <td class="py-1.5 text-center text-slate-500">{{ $item->cant_declarada }}</td>
                                        <td class="py-1.5 text-center font-semibold text-slate-700">{{ $item->cant_real }}</td>
                                        <td class="py-1.5 text-center">{{ $item->estado_limpieza === 'limpio' ? '✅' : '🔴' }}</td>
                                        <td class="py-1.5 text-center">
                                            <span @class(['inline-flex rounded-full px-1.5 py-0.5 font-semibold', 'bg-teal-100 text-teal-700' => $item->accion === 'procesar', 'bg-amber-100 text-amber-700' => $item->accion === 'devolver', 'bg-red-100 text-red-700' => $item->accion === 'retener'])>
                                                {{ ucfirst($item->accion) }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endif

        {{-- Esterilización --}}
        @if($esterilizacion)
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">3</span>
                    Esterilización — {{ strtoupper($esterilizacion->metodo) }}
                </h2>
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4 text-xs mb-3">
                    <div><dt class="text-slate-400">Equipo</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $esterilizacion->equipo?->nombre ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Operario</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $esterilizacion->operario?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Temperatura</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $esterilizacion->temperatura }}°C</dd></div>
                    <div><dt class="text-slate-400">Tiempo</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $esterilizacion->tiempo_minutos }} min</dd></div>
                    <div><dt class="text-slate-400">Inicio</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $esterilizacion->fecha_inicio?->format('d/m H:i') }}</dd></div>
                    <div><dt class="text-slate-400">Fin</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $esterilizacion->fecha_fin?->format('H:i') ?? '—' }}</dd></div>
                    @if($esterilizacion->metodo === 'eto')
                        <div><dt class="text-slate-400">Aireación</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $esterilizacion->aireacionHoras() ?? '—' }}h</dd></div>
                    @endif
                    <div><dt class="text-slate-400">Resultado ciclo</dt>
                        <dd class="mt-0.5">
                            <span @class(['inline-flex rounded-full px-2 py-0.5 text-xs font-semibold', 'bg-green-100 text-green-700' => $esterilizacion->resultado === 'conforme', 'bg-red-100 text-red-700' => $esterilizacion->resultado === 'no_conforme', 'bg-amber-100 text-amber-700' => $esterilizacion->resultado === 'pendiente'])>
                                {{ ucfirst($esterilizacion->resultado) }}
                            </span>
                        </dd>
                    </div>
                </dl>
                <div class="flex flex-wrap gap-2 border-t border-slate-100 pt-3">
                    @foreach($esterilizacion->controles as $ctrl)
                        <div @class(['rounded-lg px-3 py-2 text-xs', 'bg-green-50 border border-green-200' => $ctrl->resultado === 'conforme', 'bg-red-50 border border-red-200' => $ctrl->resultado === 'no_conforme', 'bg-amber-50 border border-amber-200' => $ctrl->resultado === 'pendiente'])>
                            <p class="font-semibold text-slate-700">{{ $ctrl->tipo_label }}</p>
                            <p @class(['font-bold', 'text-green-600' => $ctrl->resultado === 'conforme', 'text-red-600' => $ctrl->resultado === 'no_conforme', 'text-amber-600' => $ctrl->resultado === 'pendiente'])>
                                {{ match($ctrl->resultado) { 'conforme' => '✅ Conforme', 'no_conforme' => '❌ No conforme', 'pendiente' => '⏳ Pendiente', default => $ctrl->resultado } }}
                            </p>
                            @if($ctrl->descripcion)
                                <p class="text-slate-400 mt-0.5">{{ $ctrl->descripcion }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Liberación --}}
        @if($lote->liberacion)
            @php $lib = $lote->liberacion; @endphp
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span @class(['flex h-5 w-5 items-center justify-center rounded-full text-xs font-bold text-white', 'bg-green-500' => $lib->resultado === 'liberado', 'bg-amber-500' => $lib->resultado === 'retenido', 'bg-red-500' => $lib->resultado === 'rechazado'])>4</span>
                    Liberación — {{ ucfirst($lib->resultado) }}
                </h2>
                <dl class="grid grid-cols-2 gap-3 text-xs">
                    <div><dt class="text-slate-400">Responsable</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $lib->responsable?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Fecha</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $lib->fecha_liberacion?->format('d/m/Y H:i') }}</dd></div>
                </dl>
                @if($lib->observaciones)
                    <p class="mt-2 text-xs text-slate-600 bg-slate-50 rounded-lg px-3 py-2">{{ $lib->observaciones }}</p>
                @endif
            </div>
        @endif

        {{-- Despacho --}}
        @if($lote->remito)
            @php $rem = $lote->remito; @endphp
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-800 text-xs font-bold text-white">5</span>
                    Despacho — <span class="font-mono">{{ $rem->numero }}</span>
                </h2>
                <dl class="grid grid-cols-2 gap-3 sm:grid-cols-4 text-xs">
                    <div><dt class="text-slate-400">Operario</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rem->operario?->name ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Chofer</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rem->chofer_nombre ?? '—' }}</dd></div>
                    <div><dt class="text-slate-400">Fecha despacho</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rem->fecha_despacho?->format('d/m/Y H:i') }}</dd></div>
                    <div><dt class="text-slate-400">Total bultos</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rem->totalBultos() }}</dd></div>
                    <div><dt class="text-slate-400">Facturado</dt><dd class="font-semibold text-slate-700 mt-0.5">{{ $rem->facturado ? '✅ Sí' : '⏳ Pendiente' }}</dd></div>
                </dl>
            </div>
        @endif

        {{-- Historial --}}
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
            <p class="font-mono text-xl font-bold">{{ $lote->numero_lote }}</p>
            <p class="mt-2 text-xs text-slate-400 font-mono break-all">{{ $lote->uuid }}</p>
        </div>

        <div class="rounded-xl bg-white border border-slate-200 p-4 space-y-2 text-xs">
            <p class="font-semibold text-slate-600">Datos generales</p>
            <div class="space-y-1.5 text-slate-500">
                <p>Institución: <span class="font-medium text-slate-700">{{ $lote->institucion?->nombre ?? '—' }}</span></p>
                <p>Método: <span class="font-medium text-slate-700 uppercase">{{ $lote->metodo_esterilizacion }}</span></p>
                <p>Recepción: <span class="font-medium text-slate-700">{{ $lote->fecha_recepcion?->format('d/m/Y H:i') ?? '—' }}</span></p>
                @if($lote->fecha_finalizacion)
                    <p>Finalización: <span class="font-medium text-slate-700">{{ $lote->fecha_finalizacion->format('d/m/Y H:i') }}</span></p>
                    <p>Tiempo total: <span class="font-medium text-teal-700">{{ $lote->fecha_recepcion->diffForHumans($lote->fecha_finalizacion, true) }}</span></p>
                @endif
            </div>
        </div>

        <button onclick="window.print()"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
            🖨️ Imprimir
        </button>

        <a href="{{ route('auditor.lotes') }}"
           class="block w-full rounded-xl border border-slate-200 px-4 py-2.5 text-center text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
            ← Volver a lotes
        </a>
    </div>
</div>

<style>
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
    @media print { button, a.nav-op { display:none!important; } }
</style>

@endsection