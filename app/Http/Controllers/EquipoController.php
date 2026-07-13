<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use Illuminate\Http\Request;

class EquipoController extends Controller
{
    public function index(Request $request)
    {
        $query = Equipo::latest();

        if ($request->filled('metodo')) {
            $query->where('metodo', $request->metodo);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $equipos = $query->paginate(15)->withQueryString();

        $stats = [
            'total'           => Equipo::count(),
            'activos'         => Equipo::where('estado', 'activo')->count(),
            'mantenimiento'   => Equipo::where('estado', 'en_mantenimiento')->count(),
            'val_vencida'     => Equipo::where('estado', 'activo')
                ->whereNotNull('fecha_proxima_validacion')
                ->whereDate('fecha_proxima_validacion', '<', today())
                ->count(),
        ];

        return view('admin.equipos.index', compact('equipos', 'stats'));
    }

    public function create()
    {
        return view('admin.equipos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'                   => ['required', 'string', 'max:100'],
            'metodo'                   => ['required', 'in:vapor,eto'],
            'marca'                    => ['nullable', 'string', 'max:80'],
            'modelo'                   => ['nullable', 'string', 'max:80'],
            'numero_interno'           => ['nullable', 'string', 'max:30', 'unique:equipos,numero_interno'],
            'capacidad'                => ['nullable', 'integer', 'min:1'],
            'estado'                   => ['required', 'in:activo,inactivo,en_mantenimiento'],
            'fecha_ultima_validacion'  => ['nullable', 'date'],
            'fecha_proxima_validacion' => ['nullable', 'date', 'after_or_equal:fecha_ultima_validacion'],
            'observaciones'            => ['nullable', 'string', 'max:1000'],
        ], [
            'nombre.required'          => 'El nombre del equipo es obligatorio.',
            'metodo.required'          => 'Seleccioná el método del equipo.',
            'numero_interno.unique'    => 'Ya existe un equipo con ese número interno.',
            'fecha_proxima_validacion.after_or_equal' => 'La próxima validación debe ser igual o posterior a la última.',
        ]);

        $equipo = Equipo::create($validated);

        return redirect()
            ->route('admin.equipos.index')
            ->with('success', "Equipo \"{$equipo->nombre}\" creado correctamente.");
    }

    public function edit(Equipo $equipo)
    {
        return view('admin.equipos.edit', compact('equipo'));
    }

    public function update(Request $request, Equipo $equipo)
    {
        $validated = $request->validate([
            'nombre'                   => ['required', 'string', 'max:100'],
            'metodo'                   => ['required', 'in:vapor,eto'],
            'marca'                    => ['nullable', 'string', 'max:80'],
            'modelo'                   => ['nullable', 'string', 'max:80'],
            'numero_interno'           => ['nullable', 'string', 'max:30', "unique:equipos,numero_interno,{$equipo->id}"],
            'capacidad'                => ['nullable', 'integer', 'min:1'],
            'estado'                   => ['required', 'in:activo,inactivo,en_mantenimiento'],
            'fecha_ultima_validacion'  => ['nullable', 'date'],
            'fecha_proxima_validacion' => ['nullable', 'date', 'after_or_equal:fecha_ultima_validacion'],
            'observaciones'            => ['nullable', 'string', 'max:1000'],
        ], [
            'nombre.required'       => 'El nombre del equipo es obligatorio.',
            'metodo.required'       => 'Seleccioná el método del equipo.',
            'numero_interno.unique' => 'Ya existe un equipo con ese número interno.',
        ]);

        $equipo->update($validated);

        return redirect()
            ->route('admin.equipos.index')
            ->with('success', "Equipo \"{$equipo->nombre}\" actualizado correctamente.");
    }

    public function destroy(Equipo $equipo)
    {
        if ($equipo->esterilizaciones()->exists()) {
            return back()->with('error', 'No se puede eliminar un equipo con ciclos registrados. Marcalo como inactivo.');
        }

        $nombre = $equipo->nombre;
        $equipo->delete();

        return redirect()
            ->route('admin.equipos.index')
            ->with('success', "Equipo \"{$nombre}\" eliminado.");
    }
}