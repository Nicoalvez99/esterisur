@extends('layouts.admin')
@section('title', 'Nuevo protocolo')
@section('page-title', 'Nuevo protocolo')
@section('page-subtitle', 'Configurar protocolo de entrega para una institución')

@section('content')
<form method="POST" action="{{ route('admin.protocolos.store') }}" novalidate>
@csrf
<div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
    <div class="lg:col-span-2 space-y-5">
        @include('admin.protocolos._form')
    </div>
    <div class="space-y-4">
        <div class="rounded-xl border border-slate-200 bg-white p-5 space-y-3">
            <button type="submit" class="w-full rounded-xl bg-teal-600 px-6 py-3 text-sm font-bold text-white hover:bg-teal-700 active:scale-95 transition-all">
                Guardar protocolo
            </button>
            <a href="{{ route('admin.protocolos.index') }}" class="block text-center text-xs text-slate-400 hover:text-slate-600 transition-colors">Cancelar</a>
        </div>
        <div class="rounded-xl border border-slate-200 bg-amber-50 border-amber-200 p-4">
            <p class="text-xs font-semibold text-amber-700 mb-1">⚠️ Importante</p>
            <p class="text-xs text-amber-600 leading-relaxed">Los requisitos especiales se muestran como alerta visible a todos los operarios que trabajen con esta institución.</p>
        </div>
    </div>
</div>
</form>
@endsection