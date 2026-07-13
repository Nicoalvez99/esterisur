@extends('layouts.admin')

@section('title', 'Nuevo usuario')
@section('page-title', 'Nuevo usuario')
@section('page-subtitle', 'Crear cuenta para el personal')

@section('content')

<form method="POST" action="{{ route('admin.usuarios.store') }}" novalidate>
@csrf

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    {{-- Campos principales --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Datos personales --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">1</span>
                Datos del usuario
            </h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Nombre completo <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           placeholder="Ej: María González"
                           required autofocus
                           class="input-field @error('name') border-red-400 @enderror" />
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="Ej: mgonzalez@esterisur.com"
                           required
                           class="input-field @error('email') border-red-400 @enderror" />
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="mt-4 rounded-lg bg-blue-50 border border-blue-200 px-4 py-3">
                <p class="text-xs text-blue-700">
                    <strong>Contraseña:</strong> Se genera automáticamente y se mostrará una sola vez al crear el usuario. El operario deberá cambiarla al primer ingreso.
                </p>
            </div>
        </div>

        {{-- Rol --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">2</span>
                Rol y permisos <span class="text-red-500">*</span>
            </h2>

            @php
                $rolesInfo = [
                    'administrador'     => ['color' => 'border-purple-300 bg-purple-50',  'dot' => 'bg-purple-500',  'desc' => 'Acceso total al sistema. Gestiona usuarios, instituciones y configuración.'],
                    'recepcion'         => ['color' => 'border-blue-300 bg-blue-50',      'dot' => 'bg-blue-500',    'desc' => 'Registra ingresos de material, crea lotes y gestiona remitos de entrada.'],
                    'acondicionamiento' => ['color' => 'border-indigo-300 bg-indigo-50',  'dot' => 'bg-indigo-500',  'desc' => 'Controla, cuenta y empaca el material antes de esterilizar.'],
                    'esterilizacion'    => ['color' => 'border-teal-300 bg-teal-50',      'dot' => 'bg-teal-500',    'desc' => 'Carga ciclos de vapor o ETO, registra parámetros y actas.'],
                    'calidad'           => ['color' => 'border-green-300 bg-green-50',    'dot' => 'bg-green-500',   'desc' => 'Valida controles físicos, químicos y biológicos. Libera o rechaza lotes.'],
                    'despacho'          => ['color' => 'border-orange-300 bg-orange-50',  'dot' => 'bg-orange-500',  'desc' => 'Prepara la entrega, emite remitos y registra el despacho del material.'],
                    'facturacion'       => ['color' => 'border-yellow-300 bg-yellow-50',  'dot' => 'bg-yellow-500',  'desc' => 'Consulta y exporta datos de facturación. Sin acceso a operaciones.'],
                    'auditor'           => ['color' => 'border-slate-300 bg-slate-50',    'dot' => 'bg-slate-400',   'desc' => 'Solo lectura. Puede ver lotes, historial e informes sin modificar nada.'],
                ];
            @endphp

            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach($roles as $key => $label)
                    @php $info = $rolesInfo[$key]; @endphp
                    <label class="rol-card {{ old('role') === $key ? 'rol-card--active ' . $info['color'] : '' }}"
                           id="rol-{{ $key }}"
                           onclick="selectRol('{{ $key }}', '{{ $info['color'] }}')">
                        <input type="radio" name="role" value="{{ $key }}"
                               {{ old('role') === $key ? 'checked' : '' }}
                               class="sr-only" />
                        <div class="flex items-start gap-2.5">
                            <span class="mt-0.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $info['dot'] }}"></span>
                            <div>
                                <p class="text-xs font-semibold text-slate-800">{{ $label }}</p>
                                <p class="text-xs text-slate-500 leading-relaxed mt-0.5">{{ $info['desc'] }}</p>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('role') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        {{-- Estado --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-3 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-300 text-xs font-bold text-slate-600">3</span>
                Estado de la cuenta
            </h2>
            <label class="flex items-center gap-3 cursor-pointer rounded-lg border border-slate-200 p-3 hover:bg-slate-50 transition-colors">
                <input type="hidden" name="activo" value="0" />
                <input type="checkbox" name="activo" value="1" checked
                       class="h-4 w-4 rounded accent-teal-500" />
                <div>
                    <p class="text-sm font-medium text-slate-700">Cuenta activa</p>
                    <p class="text-xs text-slate-400">Si se desactiva, el usuario no podrá iniciar sesión.</p>
                </div>
            </label>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-3">
            <button type="submit"
                    class="w-full rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white hover:bg-teal-700 active:scale-95 transition-all">
                Crear usuario
            </button>
            <a href="{{ route('admin.usuarios.index') }}"
               class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">
                Cancelar
            </a>
        </div>

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
            <p class="text-xs font-semibold text-amber-700 mb-1">⚠️ Contraseña temporal</p>
            <p class="text-xs text-amber-600 leading-relaxed">
                Al crear el usuario se genera una contraseña aleatoria que aparecerá <strong>una sola vez</strong> en pantalla. Copiala y entregásela al operario.
            </p>
        </div>
    </div>

</div>
</form>

<style>
    .input-field {
        width: 100%;
        border-radius: 0.5rem;
        border: 1px solid rgb(203 213 225);
        background: white;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        color: rgb(30 41 59);
        transition: border-color .15s, box-shadow .15s;
    }
    .input-field:focus {
        outline: none;
        border-color: rgb(20 184 166);
        box-shadow: 0 0 0 3px rgb(20 184 166 / .15);
    }
    .rol-card {
        display: block;
        border-radius: 0.625rem;
        border: 2px solid rgb(226 232 240);
        padding: 0.75rem;
        cursor: pointer;
        transition: all 0.15s;
    }
    .rol-card:hover {
        border-color: rgb(94 234 212);
        background-color: rgb(240 253 250);
    }
    .rol-card--active {
        border-color: rgb(20 184 166) !important;
    }
</style>

<script>
    let rolActivo = null;

    function selectRol(key, colorClass) {
        // Limpiar todos
        document.querySelectorAll('.rol-card').forEach(card => {
            card.classList.remove('rol-card--active');
            card.className = card.className
                .split(' ')
                .filter(c => !c.includes('border-') || c === 'border-2')
                .join(' ');
        });

        // Activar el seleccionado
        const card = document.getElementById('rol-' + key);
        card.classList.add('rol-card--active');
        colorClass.split(' ').forEach(c => card.classList.add(c));

        // Marcar el radio
        card.querySelector('input[type=radio]').checked = true;
        rolActivo = key;
    }

    // Inicializar si hay old()
    @if(old('role'))
        selectRol('{{ old('role') }}', '{{ $rolesInfo[old('role')]['color'] ?? '' }}');
    @endif
</script>

@endsection