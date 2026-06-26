@extends('layouts.admin')

@section('title', 'Editar — ' . $usuario->name)
@section('page-title', $usuario->name)
@section('page-subtitle', 'Editar usuario')

@section('content')

{{-- Contraseña reseteada --}}
@if(session('password_generada'))
    <div class="mb-5 rounded-xl border border-amber-300 bg-amber-50 p-4">
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-amber-800">Nueva contraseña para <strong>{{ session('usuario_creado') }}</strong></p>
                <p class="mt-1 text-xs text-amber-700">No se volverá a mostrar.</p>
                <div class="mt-2 flex items-center gap-3">
                    <code id="pass-generada" class="rounded-lg bg-white border border-amber-300 px-4 py-2 font-mono text-base font-bold tracking-widest text-amber-900 select-all">
                        {{ session('password_generada') }}
                    </code>
                    <button onclick="copiarPassword()" class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-600 transition-colors">
                        Copiar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<form method="POST" action="{{ route('admin.usuarios.update', $usuario->id) }}" novalidate>
@csrf @method('PUT')

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    <div class="lg:col-span-2 space-y-5">

        {{-- Datos --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">1</span>
                Datos del usuario
            </h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nombre completo <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $usuario->name) }}"
                           required class="input-field @error('name') border-red-400 @enderror" />
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $usuario->email) }}"
                           required class="input-field @error('email') border-red-400 @enderror" />
                    @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Rol --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">2</span>
                Rol y permisos
            </h2>
            @php
                $rolesInfo = [
                    'administrador'     => ['color' => 'border-purple-300 bg-purple-50',  'dot' => 'bg-purple-500',  'desc' => 'Acceso total al sistema.'],
                    'recepcion'         => ['color' => 'border-blue-300 bg-blue-50',      'dot' => 'bg-blue-500',    'desc' => 'Registra ingresos y crea lotes.'],
                    'acondicionamiento' => ['color' => 'border-indigo-300 bg-indigo-50',  'dot' => 'bg-indigo-500',  'desc' => 'Controla y empaca el material.'],
                    'esterilizacion'    => ['color' => 'border-teal-300 bg-teal-50',      'dot' => 'bg-teal-500',    'desc' => 'Carga ciclos y registra parámetros.'],
                    'calidad'           => ['color' => 'border-green-300 bg-green-50',    'dot' => 'bg-green-500',   'desc' => 'Valida controles y libera lotes.'],
                    'despacho'          => ['color' => 'border-orange-300 bg-orange-50',  'dot' => 'bg-orange-500',  'desc' => 'Prepara entrega y emite remitos.'],
                    'facturacion'       => ['color' => 'border-yellow-300 bg-yellow-50',  'dot' => 'bg-yellow-500',  'desc' => 'Consulta y exporta facturación.'],
                    'auditor'           => ['color' => 'border-slate-300 bg-slate-50',    'dot' => 'bg-slate-400',   'desc' => 'Solo lectura de lotes e informes.'],
                ];
                $rolActual = old('role', $usuario->role);
            @endphp
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                @foreach($roles as $key => $label)
                    @php $info = $rolesInfo[$key]; $activo = $rolActual === $key; @endphp
                    <label class="rol-card {{ $activo ? 'rol-card--active ' . $info['color'] : '' }}"
                           id="rol-{{ $key }}"
                           onclick="selectRol('{{ $key }}', '{{ $info['color'] }}')">
                        <input type="radio" name="role" value="{{ $key }}"
                               {{ $activo ? 'checked' : '' }} class="sr-only" />
                        <div class="flex items-start gap-2.5">
                            <span class="mt-0.5 h-2.5 w-2.5 shrink-0 rounded-full {{ $info['dot'] }}"></span>
                            <div>
                                <p class="text-xs font-semibold text-slate-800">{{ $label }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $info['desc'] }}</p>
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
                <input type="checkbox" name="activo" value="1"
                       {{ old('activo', $usuario->activo) ? 'checked' : '' }}
                       class="h-4 w-4 rounded accent-teal-500" />
                <div>
                    <p class="text-sm font-medium text-slate-700">Cuenta activa</p>
                    <p class="text-xs text-slate-400">Si se desactiva, el usuario no podrá iniciar sesión.</p>
                </div>
            </label>
        </div>

        {{-- Reset contraseña manual --}}
        <div class="rounded-xl bg-white border border-slate-200 p-5">
            <h2 class="mb-3 text-sm font-semibold text-slate-800 flex items-center gap-2">
                <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-300 text-xs font-bold text-slate-600">4</span>
                Cambiar contraseña
                <span class="text-xs font-normal text-slate-400">(opcional)</span>
            </h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Nueva contraseña</label>
                    <input type="password" name="nueva_password" id="nueva_password"
                           placeholder="Mínimo 8 caracteres"
                           class="input-field @error('nueva_password') border-red-400 @enderror" />
                    @error('nueva_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">Confirmar contraseña</label>
                    <input type="password" name="nueva_password_confirmation"
                           placeholder="Repetir contraseña"
                           class="input-field" />
                </div>
            </div>
            <p class="mt-2 text-xs text-slate-400">Dejá en blanco para no modificar la contraseña actual.</p>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-3">
            <button type="submit"
                    class="w-full rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white hover:bg-teal-700 active:scale-95 transition-all">
                Guardar cambios
            </button>
            <a href="{{ route('admin.usuarios.index') }}"
               class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">
                Cancelar
            </a>
        </div>

        {{-- Reset rápido --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4">
            <p class="text-xs font-semibold text-slate-600 mb-2">Reset rápido de contraseña</p>
            <p class="text-xs text-slate-400 mb-3">Genera una contraseña nueva aleatoria y la muestra una sola vez.</p>
            <form method="POST" action="{{ route('admin.usuarios.reset-password', $usuario->id) }}"
                  onsubmit="return confirm('¿Resetear la contraseña de {{ $usuario->name }}?')">
                @csrf
                <button type="submit"
                        class="w-full rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50 transition-colors">
                    Generar nueva contraseña
                </button>
            </form>
        </div>

        {{-- Info --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-1.5 text-xs text-slate-500">
            <p>Creado: <span class="font-medium text-slate-700">{{ $usuario->created_at->format('d/m/Y H:i') }}</span></p>
            <p>Última modificación: <span class="font-medium text-slate-700">{{ $usuario->updated_at->format('d/m/Y H:i') }}</span></p>
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
    .rol-card:hover { border-color: rgb(94 234 212); background-color: rgb(240 253 250); }
    .rol-card--active { border-color: rgb(20 184 166) !important; }
</style>

<script>
    function selectRol(key, colorClass) {
        document.querySelectorAll('.rol-card').forEach(card => {
            card.classList.remove('rol-card--active');
            card.className = card.className.replace(/border-\w+-300|bg-\w+-50/g, '').trim();
        });
        const card = document.getElementById('rol-' + key);
        card.classList.add('rol-card--active');
        colorClass.split(' ').forEach(c => { if(c) card.classList.add(c); });
        card.querySelector('input[type=radio]').checked = true;
    }

    function copiarPassword() {
        const texto = document.getElementById('pass-generada').innerText.trim();
        navigator.clipboard.writeText(texto).then(() => {
            const btn = event.target;
            btn.textContent = '¡Copiado!';
            setTimeout(() => btn.textContent = 'Copiar', 2000);
        });
    }
</script>

@endsection