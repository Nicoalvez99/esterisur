@extends('layouts.admin')

@section('title', 'Nueva institución')
@section('page-title', 'Nueva institución')
@section('page-subtitle', 'Completá los datos del cliente')

@section('content')

<form method="POST" action="{{ route('admin.instituciones.store') }}" novalidate>
@csrf

<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">

    <div class="lg:col-span-2 space-y-5">
        @include('layouts._form')
    </div>

    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-3">
            <button type="submit"
                    class="w-full rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white hover:bg-teal-700 active:scale-95 transition-all">
                Guardar institución
            </button>
            <a href="{{ route('admin.instituciones.index') }}"
               class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">
                Cancelar
            </a>
        </div>

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
            <p class="text-xs font-semibold text-slate-600 mb-2">¿Para qué sirve este registro?</p>
            <p class="text-xs text-slate-500 leading-relaxed">
                Cada institución representa un cliente. Al recibir material en recepción, se asocia a una institución para trazar todo el proceso hasta la entrega.
            </p>
        </div>
    </div>
</div>

</form>
@endsection