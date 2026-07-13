<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <title>Fichaje — Esterisur</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="h-full bg-slate-900 font-sans antialiased flex items-center justify-center p-6">

<div class="w-full max-w-md">

    {{-- Logo --}}
    <div class="text-center mb-10">
        <div class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-teal-500 mb-4">
            <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6
                         1.5 1.5 0 013 7.5v9A1.5 1.5 0 006 18h12a1.5 1.5 0 001.5-1.5v-9
                         a1.5 1.5 0 00-.598-1.5 11.959 11.959 0 01-8.402-2.286z" />
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-white">Esterisur</h1>
        <p class="text-slate-400 text-sm mt-1">Sistema de asistencias</p>
    </div>

    {{-- Hora actual --}}
    <div class="text-center mb-8">
        <p id="reloj" class="text-5xl font-bold text-white tabular-nums tracking-tight"></p>
        <p id="fecha" class="text-slate-400 text-sm mt-1"></p>
    </div>

    {{-- Error --}}
    @if(session('error_fichaje'))
        <div class="mb-5 rounded-xl border border-red-500/30 bg-red-500/10 px-4 py-3 text-center text-sm font-semibold text-red-400">
            ❌ {{ session('error_fichaje') }}
        </div>
    @endif

    {{-- Formulario --}}
    <form method="POST" action="{{ route('fichar.store') }}" autocomplete="off">
        @csrf

        <div class="mb-4">
            <label class="block text-xs font-semibold uppercase tracking-widest text-slate-400 mb-3 text-center">
                Ingresá tu DNI
            </label>
            <input
                type="text"
                name="dni"
                id="dni"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="10"
                autofocus
                placeholder="Ej: 32456789"
                class="w-full rounded-2xl bg-slate-800 border-2 border-slate-700 px-6 py-5 text-center text-3xl font-bold text-white tracking-widest placeholder-slate-600
                       focus:border-teal-500 focus:outline-none focus:ring-0 transition-colors"
            />
            @error('dni')
                <p class="mt-2 text-center text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <button
            type="submit"
            class="w-full rounded-2xl bg-teal-500 py-5 text-lg font-bold text-white
                   hover:bg-teal-400 active:scale-95 transition-all duration-150 shadow-lg shadow-teal-500/20"
        >
            Registrar asistencia →
        </button>
    </form>

    {{-- Teclado numérico visual --}}
    <div class="mt-6 grid grid-cols-3 gap-3" id="teclado">
        @foreach([1,2,3,4,5,6,7,8,9,'←',0,'OK'] as $tecla)
            <button
                type="button"
                onclick="presionarTecla('{{ $tecla }}')"
                @class([
                    'rounded-xl py-4 text-xl font-bold transition-all active:scale-95',
                    'bg-slate-700 text-white hover:bg-slate-600' => is_numeric($tecla),
                    'bg-slate-700 text-slate-300 hover:bg-slate-600' => $tecla === '←',
                    'bg-teal-500 text-white hover:bg-teal-400 shadow-lg shadow-teal-500/20' => $tecla === 'OK',
                ])
            >
                {{ $tecla }}
            </button>
        @endforeach
    </div>

    <p class="mt-8 text-center text-xs text-slate-600">
        Esterisur · {{ now()->format('d/m/Y') }}
    </p>
</div>

<script>
    // Reloj en tiempo real
    function actualizarReloj() {
        const ahora = new Date();
        const hora  = ahora.toLocaleTimeString('es-AR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const fecha = ahora.toLocaleDateString('es-AR', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        document.getElementById('reloj').textContent = hora;
        document.getElementById('fecha').textContent = fecha.charAt(0).toUpperCase() + fecha.slice(1);
    }
    actualizarReloj();
    setInterval(actualizarReloj, 1000);

    // Teclado numérico virtual
    function presionarTecla(tecla) {
        const input = document.getElementById('dni');
        if (tecla === '←') {
            input.value = input.value.slice(0, -1);
        } else if (tecla === 'OK') {
            input.closest('form').submit();
        } else {
            if (input.value.length < 10) {
                input.value += tecla;
            }
        }
        input.focus();
    }

    // Limpiar el campo al hacer foco (para nueva entrada)
    document.getElementById('dni').addEventListener('focus', function() {
        this.select();
    });
</script>

</body>
</html>