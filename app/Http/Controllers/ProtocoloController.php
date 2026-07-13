<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use App\Models\Protocolo;
use Illuminate\Http\Request;

class ProtocoloController extends Controller
{
    public function index(Request $request)
    {
        $query = Protocolo::with('institucion')->latest();

        if ($request->filled('institucion_id')) {
            $query->where('institucion_id', $request->institucion_id);
        }
        if ($request->filled('metodo')) {
            $query->where('metodo_permitido', $request->metodo);
        }

        $protocolos    = $query->paginate(15)->withQueryString();
        $instituciones = Institucion::where('activo', true)->orderBy('nombre')->get();

        return view('admin.protocolos.index', compact('protocolos', 'instituciones'));
    }

    public function create(Request $request)
    {
        $instituciones    = Institucion::where('activo', true)->orderBy('nombre')->get();
        $institucionPreseleccionada = $request->get('institucion_id');
        return view('admin.protocolos.create', compact('instituciones', 'institucionPreseleccionada'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'institucion_id'        => ['required', 'exists:instituciones,id'],
            'nombre'                => ['nullable', 'string', 'max:100'],
            'metodo_permitido'      => ['required', 'in:vapor,eto,ambos'],
            'tipo_empaque'          => ['required', 'in:bolsa_simple,doble_bolsa,caja,bulto,otro'],
            'empaque_detalle'       => ['nullable', 'string', 'max:150'],
            'tipo_traslado'         => ['required', 'in:retira_cliente,envio_domicilio,courier,otro'],
            'vencimiento_dias'      => ['required', 'integer', 'min:1', 'max:3650'],
            'unidades_por_caja'     => ['nullable', 'integer', 'min:1'],
            'formato_remito'        => ['nullable', 'string', 'max:200'],
            'requisitos_especiales' => ['nullable', 'string', 'max:2000'],
            'activo'                => ['boolean'],
        ], [
            'institucion_id.required'   => 'Seleccioná una institución.',
            'metodo_permitido.required' => 'Seleccioná el método permitido.',
            'tipo_empaque.required'     => 'Seleccioná el tipo de empaque.',
            'tipo_traslado.required'    => 'Seleccioná el tipo de traslado.',
            'vencimiento_dias.required' => 'Ingresá los días de vencimiento.',
        ]);

        $validated['activo'] = $request->boolean('activo', true);

        $protocolo = Protocolo::create($validated);
        $inst      = $protocolo->institucion->nombre;

        return redirect()
            ->route('admin.protocolos.index')
            ->with('success', "Protocolo creado para \"{$inst}\".");
    }

    public function edit(Protocolo $protocolo)
    {
        $instituciones = Institucion::where('activo', true)->orderBy('nombre')->get();
        $protocolo->load('institucion');
        return view('admin.protocolos.edit', compact('protocolo', 'instituciones'));
    }

    public function update(Request $request, Protocolo $protocolo)
    {
        $validated = $request->validate([
            'institucion_id'        => ['required', 'exists:instituciones,id'],
            'nombre'                => ['nullable', 'string', 'max:100'],
            'metodo_permitido'      => ['required', 'in:vapor,eto,ambos'],
            'tipo_empaque'          => ['required', 'in:bolsa_simple,doble_bolsa,caja,bulto,otro'],
            'empaque_detalle'       => ['nullable', 'string', 'max:150'],
            'tipo_traslado'         => ['required', 'in:retira_cliente,envio_domicilio,courier,otro'],
            'vencimiento_dias'      => ['required', 'integer', 'min:1', 'max:3650'],
            'unidades_por_caja'     => ['nullable', 'integer', 'min:1'],
            'formato_remito'        => ['nullable', 'string', 'max:200'],
            'requisitos_especiales' => ['nullable', 'string', 'max:2000'],
            'activo'                => ['boolean'],
        ]);

        $validated['activo'] = $request->boolean('activo');
        $protocolo->update($validated);

        return redirect()
            ->route('admin.protocolos.index')
            ->with('success', "Protocolo actualizado correctamente.");
    }

    public function destroy(Protocolo $protocolo)
    {
        $protocolo->delete();
        return redirect()
            ->route('admin.protocolos.index')
            ->with('success', 'Protocolo eliminado.');
    }
}
