@extends('layouts.admin')
@section('title', 'Editar — ' . $equipo->nombre)
@section('page-title', $equipo->nombre)
@section('page-subtitle', 'Editar equipo')

@section('content')
<form method="POST" action="{{ route('admin.equipos.update', $equipo->id) }}" novalidate>
@csrf @method('PUT')
<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-5">
        @include('admin.equipos._form', ['equipo' => $equipo])
    </div>
    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-3">
            <button type="submit" class="w-full rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white hover:bg-teal-700 active:scale-95 transition-all">
                Guardar cambios
            </button>
            <a href="{{ route('admin.equipos.index') }}" class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">Cancelar</a>
        </div>
        <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-1.5 text-xs text-slate-500">
            <p>Creado: <span class="font-medium text-slate-700">{{ $equipo->created_at->format('d/m/Y H:i') }}</span></p>
            <p>Modificado: <span class="font-medium text-slate-700">{{ $equipo->updated_at->format('d/m/Y H:i') }}</span></p>
            @if($equipo->esterilizaciones()->exists())
                <p class="text-amber-600 font-medium mt-2">⚠️ Tiene ciclos registrados. No se puede eliminar.</p>
            @else
                <form method="POST" action="{{ route('admin.equipos.destroy', $equipo->id) }}"
                      onsubmit="return confirm('¿Eliminar este equipo?')" class="mt-2">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full rounded-lg border border-red-200 px-4 py-2 text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors">
                        Eliminar equipo
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
</form>
@endsection