{{--
    Partial: _form.blade.php
    Variables esperadas: $institucion (opcional, para edición)
--}}

@php $inst = $institucion ?? null; @endphp

{{-- Datos principales --}}
<div class="rounded-xl bg-white border border-slate-200 p-5">
    <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">1</span>
        Datos principales
    </h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-600 mb-1.5">
                Nombre de la institución <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nombre"
                   value="{{ old('nombre', $inst?->nombre) }}"
                   placeholder="Ej: Hospital Municipal San Martín"
                   required
                   class="input-field @error('nombre') border-red-400 @enderror" />
            @error('nombre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Código interno</label>
            <input type="text" name="codigo"
                   value="{{ old('codigo', $inst?->codigo) }}"
                   placeholder="Ej: HOSP-001"
                   class="input-field @error('codigo') border-red-400 @enderror" />
            @error('codigo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">CUIT</label>
            <input type="text" name="cuit"
                   value="{{ old('cuit', $inst?->cuit) }}"
                   placeholder="Ej: 30-12345678-9"
                   class="input-field" />
        </div>
    </div>
</div>

{{-- Contacto --}}
<div class="rounded-xl bg-white border border-slate-200 p-5">
    <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">2</span>
        Contacto
    </h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Teléfono</label>
            <input type="text" name="telefono"
                   value="{{ old('telefono', $inst?->telefono) }}"
                   placeholder="Ej: (011) 4444-5555"
                   class="input-field" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Email</label>
            <input type="email" name="email"
                   value="{{ old('email', $inst?->email) }}"
                   placeholder="Ej: contacto@hospital.com"
                   class="input-field @error('email') border-red-400 @enderror" />
            @error('email') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Domicilio --}}
<div class="rounded-xl bg-white border border-slate-200 p-5">
    <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">3</span>
        Domicilio
    </h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Dirección</label>
            <input type="text" name="direccion"
                   value="{{ old('direccion', $inst?->direccion) }}"
                   placeholder="Ej: Av. Corrientes 1234"
                   class="input-field" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Ciudad</label>
            <input type="text" name="ciudad"
                   value="{{ old('ciudad', $inst?->ciudad) }}"
                   placeholder="Ej: Buenos Aires"
                   class="input-field" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Provincia</label>
            <input type="text" name="provincia"
                   value="{{ old('provincia', $inst?->provincia) }}"
                   placeholder="Ej: Buenos Aires"
                   class="input-field" />
        </div>
    </div>
</div>

{{-- Observaciones + Estado --}}
<div class="rounded-xl bg-white border border-slate-200 p-5">
    <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-300 text-xs font-bold text-slate-600">4</span>
        Notas y estado
    </h2>
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Observaciones internas</label>
            <textarea name="observaciones" rows="3"
                      placeholder="Requisitos especiales, protocolos, notas de entrega..."
                      class="input-field resize-none">{{ old('observaciones', $inst?->observaciones) }}</textarea>
        </div>

        <label class="flex items-center gap-3 cursor-pointer rounded-lg border border-slate-200 p-3 hover:bg-slate-50 transition-colors">
            <input type="hidden" name="activo" value="0" />
            <input type="checkbox" name="activo" value="1"
                   {{ old('activo', $inst?->activo ?? true) ? 'checked' : '' }}
                   class="h-4 w-4 rounded accent-teal-500" />
            <div>
                <p class="text-sm font-medium text-slate-700">Institución activa</p>
                <p class="text-xs text-slate-400">Solo las instituciones activas aparecen en recepción y otros módulos.</p>
            </div>
        </label>
    </div>
</div>

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
</style>