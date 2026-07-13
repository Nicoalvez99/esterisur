<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assist;
use App\Models\User;
use Illuminate\Http\Request;

class FicharController extends Controller
{
    /**
     * Pantalla de fichaje — pública, pensada para tablet fija.
     */
    public function index()
    {
        return view('fichar.index');
    }

    /**
     * Procesa el DNI y registra la asistencia.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dni' => ['required', 'string', 'max:20'],
        ], [
            'dni.required' => 'Ingresá tu DNI.',
        ]);

        $dni = preg_replace('/\D/', '', $request->dni); // solo números

        $user = User::where('dni', $dni)
            ->where('activo', true)
            ->first();

        if (!$user) {
            return back()->with('error_fichaje', 'DNI no encontrado o usuario inactivo.');
        }

        $tipo = Assist::proximoTipo($user);

        Assist::create([
            'user_id' => $user->id,
            'tipo'    => $tipo,
        ]);

        return redirect()
            ->route('fichar.confirmacion', [
                'nombre' => $user->name,
                'tipo'   => $tipo,
                'hora'   => now()->format('H:i'),
            ]);
    }

    /**
     * Pantalla de confirmación — se muestra 5 segundos y vuelve al inicio.
     */
    public function confirmacion(Request $request)
    {
        return view('fichar.confirmacion', [
            'nombre' => $request->query('nombre', ''),
            'tipo'   => $request->query('tipo', 'entrada'),
            'hora'   => $request->query('hora', now()->format('H:i')),
        ]);
    }
}
