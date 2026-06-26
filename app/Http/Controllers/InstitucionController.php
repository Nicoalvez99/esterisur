<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institucion;

class InstitucionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Institucion::latest();

 
        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where(function ($qb) use ($q) {
                $qb->where('nombre', 'like', "%{$q}%")
                   ->orWhere('cuit', 'like', "%{$q}%")
                   ->orWhere('codigo', 'like', "%{$q}%");
            });
        }
 
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }
 
        $instituciones = $query->paginate(15)->withQueryString();
 
        return view('admin.instituciones.index', compact('instituciones'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.instituciones.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'codigo'       => ['nullable', 'string', 'max:20', 'unique:institucions,codigo'],
            'cuit'         => ['nullable', 'string', 'max:20'],
            'telefono'     => ['nullable', 'string', 'max:50'],
            'email'        => ['nullable', 'email', 'max:100'],
            'direccion'    => ['nullable', 'string', 'max:200'],
            'ciudad'       => ['nullable', 'string', 'max:100'],
            'provincia'    => ['nullable', 'string', 'max:100'],
            'observaciones'=> ['nullable', 'string', 'max:1000'],
            'activo'       => ['boolean'],
        ], [
            'nombre.required' => 'El nombre de la institución es obligatorio.',
            'codigo.unique'   => 'Ya existe una institución con ese código.',
            'email.email'     => 'El email no tiene un formato válido.',
        ]);
 
        $validated['activo'] = $request->boolean('activo', true);
 
        Institucion::create($validated);
 
        return redirect()
            ->route('admin.instituciones.index')
            ->with('success', "Institución \"{$validated['nombre']}\" creada correctamente.");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Institucion $institucion)
    {
        return view('admin.instituciones.edit', compact('institucion'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Institucion $institucion)
    {
        $validated = $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'codigo'       => ['nullable', 'string', 'max:20', "unique:instituciones,codigo,{$institucion->id}"],
            'cuit'         => ['nullable', 'string', 'max:20'],
            'telefono'     => ['nullable', 'string', 'max:50'],
            'email'        => ['nullable', 'email', 'max:100'],
            'direccion'    => ['nullable', 'string', 'max:200'],
            'ciudad'       => ['nullable', 'string', 'max:100'],
            'provincia'    => ['nullable', 'string', 'max:100'],
            'observaciones'=> ['nullable', 'string', 'max:1000'],
            'activo'       => ['boolean'],
        ], [
            'nombre.required' => 'El nombre de la institución es obligatorio.',
            'codigo.unique'   => 'Ya existe una institución con ese código.',
            'email.email'     => 'El email no tiene un formato válido.',
        ]);
 
        $validated['activo'] = $request->boolean('activo');
 
        $institucion->update($validated);
 
        return redirect()
            ->route('admin.instituciones.index')
            ->with('success', "Institución \"{$institucion->nombre}\" actualizada correctamente.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Institucion $institucion)
    {
        // No eliminar si tiene lotes asociados
        if ($institucion->lotes()->exists()) {
            return back()->with('error', 'No se puede eliminar una institución con lotes asociados. Desactivala en su lugar.');
        }
 
        $nombre = $institucion->nombre;
        $institucion->delete();
 
        return redirect()
            ->route('admin.instituciones.index')
            ->with('success', "Institución \"{$nombre}\" eliminada.");
    }
}
