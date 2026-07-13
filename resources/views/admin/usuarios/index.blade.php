@extends('layouts.admin')

@section('title', 'Usuarios')
@section('page-title', 'Usuarios')
@section('page-subtitle', 'Personal del sistema')

@section('content')

{{-- Alerta contraseña generada — aparece UNA sola vez --}}
@if(session('password_generada'))
    <div class="mb-5 rounded-xl border border-amber-300 bg-amber-50 p-4">
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 text-amber-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" />
            </svg>
            <div class="flex-1">
                <p class="text-sm font-semibold text-amber-800">
                    Contraseña generada para <strong>{{ session('usuario_creado') }}</strong>
                </p>
                <p class="mt-1 text-xs text-amber-700">
                    Copiá esta contraseña ahora. <strong>No se volverá a mostrar.</strong>
                </p>
                <div class="mt-2 flex items-center gap-3">
                    <code id="pass-generada"
                          class="rounded-lg bg-white border border-amber-300 px-4 py-2 font-mono text-base font-bold tracking-widest text-amber-900 select-all">
                        {{ session('password_generada') }}
                    </code>
                    <button onclick="copiarPassword()"
                            class="rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white hover:bg-amber-600 transition-colors">
                        Copiar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Header --}}
<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between mb-5">
    <p class="text-xs text-slate-500">{{ $usuarios->total() }} usuario{{ $usuarios->total() !== 1 ? 's' : '' }} registrado{{ $usuarios->total() !== 1 ? 's' : '' }}</p>
    <a href="{{ route('admin.usuarios.create') }}"
       class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2 text-sm font-semibold text-white hover:bg-teal-700 transition-colors">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        Nuevo usuario
    </a>
</div>

{{-- Filtros --}}
<form method="GET" class="mb-5 flex flex-col gap-3 sm:flex-row rounded-xl bg-white border border-slate-200 p-4">
    <div class="flex-1">
        <input type="text" name="buscar" value="{{ request('buscar') }}"
               placeholder="Buscar por nombre o email..."
               class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500" />
    </div>
    <div>
        <select name="role" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
            <option value="">Todos los roles</option>
            @foreach(\App\Models\User::ROLES as $key => $label)
                <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <select name="activo" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500">
            <option value="">Todos</option>
            <option value="1" {{ request('activo') === '1' ? 'selected' : '' }}>Activos</option>
            <option value="0" {{ request('activo') === '0' ? 'selected' : '' }}>Inactivos</option>
        </select>
    </div>
    <div class="flex gap-2">
        <button type="submit" class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700 transition-colors">
            Filtrar
        </button>
        <a href="{{ route('admin.usuarios.index') }}"
           class="rounded-lg border border-slate-300 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 transition-colors">
            Limpiar
        </a>
    </div>
</form>

{{-- Tabla --}}
<div class="rounded-xl bg-white border border-slate-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100 bg-slate-50/60">
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">Usuario</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden sm:table-cell">Rol</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">Estado</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 hidden lg:table-cell">Creado</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($usuarios as $usuario)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-teal-100 text-xs font-bold text-teal-700">
                                    {{ strtoupper(substr($usuario->name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="text-xs font-semibold text-slate-800">{{ $usuario->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $usuario->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell">
                            @php
                                $rolColors = [
                                    'administrador'     => 'bg-purple-100 text-purple-700',
                                    'recepcion'         => 'bg-blue-100 text-blue-700',
                                    'acondicionamiento' => 'bg-indigo-100 text-indigo-700',
                                    'esterilizacion'    => 'bg-teal-100 text-teal-700',
                                    'calidad'           => 'bg-green-100 text-green-700',
                                    'despacho'          => 'bg-orange-100 text-orange-700',
                                    'facturacion'       => 'bg-yellow-100 text-yellow-700',
                                    'auditor'           => 'bg-slate-100 text-slate-600',
                                ];
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $rolColors[$usuario->role] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $usuario->getRoleLabel() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span @class([
                                'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium',
                                'bg-green-100 text-green-700' => $usuario->activo,
                                'bg-slate-100 text-slate-500' => !$usuario->activo,
                            ])>
                                <span @class([
                                    'h-1.5 w-1.5 rounded-full',
                                    'bg-green-500' => $usuario->activo,
                                    'bg-slate-400' => !$usuario->activo,
                                ])></span>
                                {{ $usuario->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-400">
                            {{ $usuario->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.usuarios.edit', $usuario->id) }}"
                               class="text-xs font-medium text-teal-600 hover:text-teal-800 transition-colors">
                                Editar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <p class="text-sm font-medium text-slate-500">No hay usuarios registrados</p>
                                <a href="{{ route('admin.usuarios.create') }}" class="text-xs font-medium text-teal-600 hover:underline">
                                    Crear el primer usuario
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($usuarios->hasPages())
        <div class="border-t border-slate-100 px-5 py-3">
            {{ $usuarios->links() }}
        </div>
    @endif
</div>

<script>
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