@extends('layouts.admin')

@section('title', 'Editar — ' . $institucion->nombre)
@section('page-title', $institucion->nombre)
@section('page-subtitle', 'Editar institución')

@section('content')

<form method="POST" action="{{ route('admin.instituciones.update', $institucion->id) }}" novalidate>
@csrf
@method('PUT')

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    <div class="lg:col-span-2 space-y-5">
        @include('admin.instituciones._form', ['institucion' => $institucion])
    </div>

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-3">
            <button type="submit"
                    class="w-full rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white hover:bg-teal-700 active:scale-95 transition-all">
                Guardar cambios
            </button>
            <a href="{{ route('admin.instituciones.index') }}"
               class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">
                Cancelar
            </a>
        </div>

        {{-- Info del registro --}}
        <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-2">
            <p class="text-xs font-semibold text-slate-600">Info del registro</p>
            <div class="text-xs text-slate-500 space-y-1">
                <p>Creada: <span class="font-medium text-slate-700">{{ $institucion->created_at->format('d/m/Y H:i') }}</span></p>
                <p>Última modificación: <span class="font-medium text-slate-700">{{ $institucion->updated_at->format('d/m/Y H:i') }}</span></p>
            </div>
        </div>

        {{-- Eliminar (solo si no tiene lotes) --}}
        <form method="POST" action="{{ route('admin.instituciones.destroy', $institucion->id) }}"
              onsubmit="return confirm('¿Seguro que querés eliminar esta institución? Esta acción no se puede deshacer.')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="w-full rounded-xl border border-red-200 px-6 py-2.5 text-xs font-semibold text-red-500 hover:bg-red-50 transition-colors">
                Eliminar institución
            </button>
        </form>
    </div>
</div>

</form>
@endsection