@extends('layouts.modulo')

@section('title', 'Ciclo #' . $esterilizacion->id)
@section('modulo-label', 'Esterilización')
@section('page-title', 'Ciclo #' . $esterilizacion->id . ' — ' . strtoupper($esterilizacion->metodo))
@section('page-subtitle', $esterilizacion->equipo?->nombre . ' · ' . $esterilizacion->fecha_inicio?->format('d/m/Y'))

@section('sidebar-nav')
    <a href="{{ route('esterilizacion.index') }}" class="nav-op">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
        </svg>
        Volver al panel
    </a>
@endsection

@section('content')

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- COLUMNA PRINCIPAL --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Resultado general --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-2xl">{{ $esterilizacion->metodo === 'vapor' ? '♨️' : '🧪' }}</span>
                <span @class([
                    'inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-semibold',
                    'bg-green-100 text-green-700'  => $esterilizacion->resultado === 'conforme',
                    'bg-red-100 text-red-700'      => $esterilizacion->resultado === 'no_conforme',
                    'bg-amber-100 text-amber-700'  => $esterilizacion->resultado === 'pendiente',
                ])>
                    {{ match($esterilizacion->resultado) {
                        'conforme'    => '✅ Ciclo conforme',
                        'no_conforme' => '❌ Ciclo no conforme',
                        'pendiente'   => '⏳ Resultado pendiente',
                        default       => $esterilizacion->resultado,
                    } }}
                </span>
                <span class="text-xs text-slate-400 ml-auto">
                    Registrado por {{ $esterilizacion->operario?->name ?? '—' }}
                    · {{ $esterilizacion->created_at?->format('d/m/Y H:i') }}
                </span>
            </div>
        </div>

        {{-- Parámetros --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800">Parámetros del ciclo</h2>
            <dl class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                <div class="rounded-lg bg-slate-50 p-3 text-center">
                    <dt class="text-xs text-slate-500">Equipo</dt>
                    <dd class="mt-1 text-sm font-bold text-slate-800">{{ $esterilizacion->equipo?->nombre ?? '—' }}</dd>
                </div>
                <div class="rounded-lg bg-slate-50 p-3 text-center">
                    <dt class="text-xs text-slate-500">Temperatura</dt>
                    <dd class="mt-1 text-sm font-bold text-slate-800">{{ $esterilizacion->temperatura ?? '—' }}°C</dd>
                </div>
                <div class="rounded-lg bg-slate-50 p-3 text-center">
                    <dt class="text-xs text-slate-500">Tiempo</dt>
                    <dd class="mt-1 text-sm font-bold text-slate-800">{{ $esterilizacion->tiempo_minutos ?? '—' }} min</dd>
                </div>
                @if($esterilizacion->metodo === 'vapor')
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <dt class="text-xs text-slate-500">Presión</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-800">{{ $esterilizacion->presion ?? '—' }} bar</dd>
                    </div>
                @else
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <dt class="text-xs text-slate-500">Concentración</dt>
                        <dd class="mt-1 text-sm font-bold text-slate-800">{{ $esterilizacion->concentracion ?? '—' }} mg/L</dd>
                    </div>
                @endif
            </dl>

            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 text-xs text-slate-600">
                <div>
                    <p class="text-slate-400">Inicio ciclo</p>
                    <p class="font-semibold">{{ $esterilizacion->fecha_inicio?->format('d/m/Y H:i') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-slate-400">Fin ciclo</p>
                    <p class="font-semibold">{{ $esterilizacion->fecha_fin?->format('H:i') ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-slate-400">Duración real</p>
                    <p class="font-semibold">{{ $esterilizacion->duracionMinutos() ?? '—' }} min</p>
                </div>
            </div>
        </div>

        {{-- Aireación ETO --}}
        @if($esterilizacion->metodo === 'eto')
            <div class="rounded-xl bg-white border border-slate-200 p-5">
                <h2 class="mb-4 text-sm font-semibold text-slate-800">💨 Aireación</h2>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 text-xs mb-4">
                    <div class="rounded-lg bg-purple-50 p-3 text-center">
                        <p class="text-purple-600">Inicio aireación</p>
                        <p class="mt-1 font-bold text-purple-800">{{ $esterilizacion->aireacion_inicio?->format('d/m/Y H:i') ?? '—' }}</p>
                    </div>
                    <div class="rounded-lg {{ $esterilizacion->aireacion_fin ? 'bg-green-50' : 'bg-amber-50' }} p-3 text-center">
                        <p class="{{ $esterilizacion->aireacion_fin ? 'text-green-600' : 'text-amber-600' }}">Fin aireación</p>
                        <p class="mt-1 font-bold {{ $esterilizacion->aireacion_fin ? 'text-green-800' : 'text-amber-700' }}">
                            {{ $esterilizacion->aireacion_fin?->format('d/m/Y H:i') ?? '⏳ Pendiente' }}
                        </p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3 text-center">
                        <p class="text-slate-500">Total aireación</p>
                        <p class="mt-1 font-bold text-slate-700">
                            {{ $esterilizacion->aireacionHoras() ? $esterilizacion->aireacionHoras() . 'h' : '—' }}
                        </p>
                    </div>
                </div>

                @if(!$esterilizacion->aireacion_fin)
                    <form method="POST" action="{{ route('esterilizacion.fin-aireacion', $esterilizacion->id) }}"
                          class="flex flex-col sm:flex-row gap-3 rounded-lg bg-amber-50 border border-amber-200 p-4">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-amber-800 mb-1.5">
                                Registrar fin de aireación <span class="text-red-500">*</span>
                            </label>
                            <input type="datetime-local" name="aireacion_fin"
                                   value="{{ now()->format('Y-m-d\TH:i') }}"
                                   class="input-field" />
                            @error('aireacion_fin') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                    class="w-full sm:w-auto rounded-lg bg-amber-600 px-4 py-2 text-xs font-bold text-white hover:bg-amber-700 transition-colors">
                                Confirmar fin aireación
                            </button>
                        </div>
                    </form>
                @else
                    <div class="flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-xs text-green-700 font-semibold">
                        ✅ Aireación completada — {{ $esterilizacion->aireacionHoras() }}h totales
                    </div>
                @endif
            </div>
        @endif

        {{-- Controles --}}
        <div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800">Controles del ciclo</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($esterilizacion->controles as $control)
                    <div class="flex items-center justify-between px-5 py-4">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">
                                {{ match($control->tipo) { 'fisico' => '📊', 'quimico' => '🧫', 'biologico' => '🦠', default => '🔬' } }}
                            </span>
                            <div>
                                <p class="text-xs font-semibold text-slate-800">Control {{ $control->tipo_label }}</p>
                                @if($control->descripcion)
                                    <p class="text-xs text-slate-500">{{ $control->descripcion }}</p>
                                @endif
                                @if($control->observaciones)
                                    <p class="text-xs text-slate-400 italic">{{ $control->observaciones }}</p>
                                @endif
                            </div>
                        </div>
                        <span @class([
                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
                            'bg-green-100 text-green-700' => $control->resultado === 'conforme',
                            'bg-red-100 text-red-700'     => $control->resultado === 'no_conforme',
                            'bg-amber-100 text-amber-700' => $control->resultado === 'pendiente',
                        ])>
                            {{ match($control->resultado) {
                                'conforme'    => '✅ Conforme',
                                'no_conforme' => '❌ No conforme',
                                'pendiente'   => '⏳ Pendiente',
                                default       => $control->resultado,
                            } }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-6 text-center text-sm text-slate-400">Sin controles registrados.</div>
                @endforelse
            </div>
        </div>

        {{-- Lotes del ciclo --}}
        <div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-800">Lotes procesados en este ciclo</h2>
            </div>
            <div class="divide-y divide-slate-100">
                @foreach($esterilizacion->lotes as $lote)
                    <div class="flex items-center justify-between px-5 py-3">
                        <div>
                            <p class="font-mono text-xs font-bold text-slate-800">{{ $lote->numero_lote }}</p>
                            <p class="text-xs text-slate-500">{{ $lote->recepcion?->institucion?->nombre ?? '—' }}</p>
                        </div>
                        <span @class([
                            'inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold',
                            'bg-yellow-100 text-yellow-700' => $lote->estado_actual === 'control_calidad',
                            'bg-red-100 text-red-700'       => $lote->estado_actual === 'retenido',
                            'bg-green-100 text-green-700'   => $lote->estado_actual === 'almacenamiento',
                        ])>
                            {{ ucfirst(str_replace('_', ' ', $lote->estado_actual)) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- SIDEBAR --}}
    <div class="space-y-4">
        <div class="rounded-xl bg-slate-900 text-white p-5 text-center">
            <p class="text-xs text-slate-400 mb-1">Ciclo</p>
            <p class="text-3xl font-bold">#{{ $esterilizacion->id }}</p>
            <p class="mt-1 text-xs text-slate-400 uppercase">{{ $esterilizacion->metodo }}</p>
        </div>

        @if($esterilizacion->observaciones)
            <div class="rounded-xl bg-white border border-slate-200 p-4">
                <p class="text-xs font-semibold text-slate-600 mb-1">Observaciones</p>
                <p class="text-xs text-slate-600 leading-relaxed">{{ $esterilizacion->observaciones }}</p>
            </div>
        @endif

        <a href="{{ route('esterilizacion.index') }}"
           class="block w-full rounded-xl border border-slate-200 px-6 py-2.5 text-center text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">
            ← Volver al panel
        </a>
    </div>
</div>

<style>
    .input-field { width:100%; border-radius:.5rem; border:1px solid rgb(203 213 225); background:white; padding:.5rem .75rem; font-size:.875rem; color:rgb(30 41 59); transition:border-color .15s; }
    .input-field:focus { outline:none; border-color:rgb(20 184 166); box-shadow:0 0 0 3px rgb(20 184 166/.15); }
    .nav-op { display:flex; align-items:center; gap:.625rem; padding:.5rem .75rem; border-radius:.5rem; font-size:.875rem; font-weight:500; color:rgb(100 116 139); transition:all .15s; text-decoration:none; }
    .nav-op:hover { background:rgb(241 245 249); color:rgb(30 41 59); }
</style>

@endsection