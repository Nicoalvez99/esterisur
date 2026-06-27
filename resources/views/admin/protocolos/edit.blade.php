@extends('layouts.admin')
@section('title', 'Editar protocolo — ' . $protocolo->institucion->nombre)
@section('page-title', $protocolo->institucion->nombre)
@section('page-subtitle', 'Editar protocolo' . ($protocolo->nombre ? ' — ' . $protocolo->nombre : ''))

@section('content')
<form method="POST" action="{{ route('admin.protocolos.update', $protocolo->id) }}" novalidate>
@csrf @method('PUT')
<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-5">
        @include('admin.protocolos._form', ['protocolo' => $protocolo])
    </div>
    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-3">
            <button type="submit" class="w-full rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white hover:bg-teal-700 active:scale-95 transition-all">
                Guardar cambios
            </button>
            <a href="{{ route('admin.protocolos.index') }}" class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">Cancelar</a>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-1.5 text-xs text-slate-500">
            <p>Creado: <span class="font-medium text-slate-700">{{ $protocolo->created_at->format('d/m/Y H:i') }}</span></p>
            <p>Modificado: <span class="font-medium text-slate-700">{{ $protocolo->updated_at->format('d/m/Y H:i') }}</span></p>
        </div>
        <form method="POST" action="{{ route('admin.protocolos.destroy', $protocolo->id) }}"
              onsubmit="return confirm('¿Eliminar este protocolo?')">
            @csrf @method('DELETE')
            <button type="submit" class="w-full rounded-xl border border-red-200 px-6 py-2.5 text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors">
                Eliminar protocolo
            </button>
        </form>
    </div>
</div>
</form>
@endsection