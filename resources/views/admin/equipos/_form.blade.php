@php $eq = $equipo ?? null; @endphp

{{-- Identificación --}}
<div class="rounded-xl bg-white border border-slate-200 p-5">
    <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">1</span>
        Identificación
    </h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Nombre del equipo <span class="text-red-500">*</span></label>
            <input type="text" name="nombre" value="{{ old('nombre', $eq?->nombre) }}"
                   placeholder="Ej: Autoclave 1, Equipo ETO principal"
                   required class="input-field @error('nombre') border-red-400 @enderror" />
            @error('nombre') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Número interno / código</label>
            <input type="text" name="numero_interno" value="{{ old('numero_interno', $eq?->numero_interno) }}"
                   placeholder="Ej: AC-001"
                   class="input-field @error('numero_interno') border-red-400 @enderror" />
            @error('numero_interno') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Capacidad (unidades)</label>
            <input type="number" name="capacidad" value="{{ old('capacidad', $eq?->capacidad) }}"
                   min="1" placeholder="Ej: 20"
                   class="input-field" />
        </div>
    </div>
</div>

{{-- Método --}}
<div class="rounded-xl bg-white border border-slate-200 p-5">
    <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">2</span>
        Método <span class="text-red-500">*</span>
    </h2>
    <div class="grid grid-cols-2 gap-3">
        @foreach([
            ['val' => 'vapor', 'label' => 'Vapor',           'sub' => 'Autoclave',         'icon' => '♨️', 'color' => 'border-blue-300 bg-blue-50'],
            ['val' => 'eto',   'label' => 'ETO',             'sub' => 'Óxido de etileno',  'icon' => '🧪', 'color' => 'border-purple-300 bg-purple-50'],
        ] as $m)
            <label class="metodo-card {{ old('metodo', $eq?->metodo) === $m['val'] ? 'metodo-card--active ' . $m['color'] : '' }}"
                   id="met-{{ $m['val'] }}"
                   onclick="selectMetodo('{{ $m['val'] }}', '{{ $m['color'] }}')">
                <input type="radio" name="metodo" value="{{ $m['val'] }}"
                       {{ old('metodo', $eq?->metodo) === $m['val'] ? 'checked' : '' }}
                       class="sr-only" />
                <span class="text-3xl">{{ $m['icon'] }}</span>
                <div class="text-center">
                    <p class="text-sm font-bold text-slate-800">{{ $m['label'] }}</p>
                    <p class="text-xs text-slate-500">{{ $m['sub'] }}</p>
                </div>
            </label>
        @endforeach
    </div>
    @error('metodo') <p class="mt-2 text-xs text-red-500">{{ $message }}</p> @enderror
</div>

{{-- Marca y modelo --}}
<div class="rounded-xl bg-white border border-slate-200 p-5">
    <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">3</span>
        Datos técnicos
    </h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Marca</label>
            <input type="text" name="marca" value="{{ old('marca', $eq?->marca) }}"
                   placeholder="Ej: Tuttnauer, Getinge, 3M"
                   class="input-field" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Modelo</label>
            <input type="text" name="modelo" value="{{ old('modelo', $eq?->modelo) }}"
                   placeholder="Ej: 3870 EHS"
                   class="input-field" />
        </div>
    </div>
</div>

{{-- Validación --}}
<div class="rounded-xl bg-white border border-slate-200 p-5">
    <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-teal-500 text-xs font-bold text-white">4</span>
        Validación técnica
    </h2>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Última validación</label>
            <input type="date" name="fecha_ultima_validacion"
                   value="{{ old('fecha_ultima_validacion', $eq?->fecha_ultima_validacion?->format('Y-m-d')) }}"
                   class="input-field" />
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Próxima validación</label>
            <input type="date" name="fecha_proxima_validacion"
                   value="{{ old('fecha_proxima_validacion', $eq?->fecha_proxima_validacion?->format('Y-m-d')) }}"
                   class="input-field @error('fecha_proxima_validacion') border-red-400 @enderror" />
            @error('fecha_proxima_validacion') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
        </div>
    </div>
</div>

{{-- Estado y observaciones --}}
<div class="rounded-xl bg-white border border-slate-200 p-5">
    <h2 class="mb-4 text-sm font-semibold text-slate-800 flex items-center gap-2">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-slate-300 text-xs font-bold text-slate-600">5</span>
        Estado y notas
    </h2>
    <div class="space-y-4">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-2">Estado del equipo <span class="text-red-500">*</span></label>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                @foreach([
                    ['val' => 'activo',           'label' => 'Activo',          'dot' => 'bg-green-500', 'bg' => 'hover:bg-green-50 hover:border-green-300'],
                    ['val' => 'en_mantenimiento', 'label' => 'En mantenimiento','dot' => 'bg-amber-500', 'bg' => 'hover:bg-amber-50 hover:border-amber-300'],
                    ['val' => 'inactivo',         'label' => 'Inactivo',        'dot' => 'bg-slate-400', 'bg' => 'hover:bg-slate-50 hover:border-slate-300'],
                ] as $est)
                    <label class="estado-card {{ old('estado', $eq?->estado ?? 'activo') === $est['val'] ? 'estado-card--active' : '' }}"
                           id="est-{{ $est['val'] }}"
                           onclick="selectEstado('{{ $est['val'] }}')">
                        <input type="radio" name="estado" value="{{ $est['val'] }}"
                               {{ old('estado', $eq?->estado ?? 'activo') === $est['val'] ? 'checked' : '' }}
                               class="sr-only" />
                        <span class="h-2.5 w-2.5 rounded-full {{ $est['dot'] }} shrink-0"></span>
                        <span class="text-sm font-medium text-slate-700">{{ $est['label'] }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1.5">Observaciones</label>
            <textarea name="observaciones" rows="3"
                      placeholder="Ciclos habituales, restricciones, notas de mantenimiento..."
                      class="input-field resize-none">{{ old('observaciones', $eq?->observaciones) }}</textarea>
        </div>
    </div>
</div>

<style>
    .input-field { width:100%; border-radius:.5rem; border:1px solid rgb(203 213 225); background:white; padding:.5rem .75rem; font-size:.875rem; color:rgb(30 41 59); transition:border-color .15s,box-shadow .15s; }
    .input-field:focus { outline:none; border-color:rgb(20 184 166); box-shadow:0 0 0 3px rgb(20 184 166/.15); }
    .metodo-card { display:flex; flex-direction:column; align-items:center; gap:.5rem; padding:1.25rem; border-radius:.75rem; border:2px solid rgb(226 232 240); cursor:pointer; transition:all .15s; }
    .metodo-card:hover { border-color:rgb(94 234 212); background:rgb(240 253 250); }
    .metodo-card--active { border-color:rgb(20 184 166)!important; }
    .estado-card { display:flex; align-items:center; gap:.625rem; padding:.625rem .875rem; border-radius:.625rem; border:2px solid rgb(226 232 240); cursor:pointer; transition:all .15s; }
    .estado-card:hover { border-color:rgb(203 213 225); }
    .estado-card--active { border-color:rgb(20 184 166)!important; background:rgb(240 253 250)!important; }
</style>

<script>
    function selectMetodo(val, colorClass) {
        document.querySelectorAll('.metodo-card').forEach(c => {
            c.classList.remove('metodo-card--active');
            c.className = c.className.replace(/border-\w+-300|bg-\w+-50/g, '').trim();
        });
        const card = document.getElementById('met-' + val);
        card.classList.add('metodo-card--active');
        colorClass.split(' ').forEach(c => { if(c) card.classList.add(c); });
        card.querySelector('input').checked = true;
    }

    function selectEstado(val) {
        document.querySelectorAll('.estado-card').forEach(c => c.classList.remove('estado-card--active'));
        document.getElementById('est-' + val).classList.add('estado-card--active');
        document.querySelector(`input[name="estado"][value="${val}"]`).checked = true;
    }
</script>