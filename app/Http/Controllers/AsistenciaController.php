<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Assist;
use App\Models\User;
use Illuminate\Http\Request;

class AsistenciaController extends Controller
{
    public function index(Request $request)
    {
        $fecha  = $request->get('fecha', today()->toDateString());
        $userId = $request->get('user_id');

        $query = Assist::with('user')
            ->whereDate('created_at', $fecha)
            ->orderBy('created_at');

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $registros = $query->get();

        // Agrupar por usuario para calcular horas
        $porUsuario = $registros->groupBy('user_id')->map(function ($regs) {
            $user    = $regs->first()->user;
            $horas   = Assist::horasTrabajadas($user, $regs->first()->created_at->toDateString());
            $adentro = $regs->last()->tipo === 'entrada';

            return [
                'user'     => $user,
                'registros'=> $regs,
                'horas'    => $horas,
                'adentro'  => $adentro,
            ];
        });

        $usuarios  = User::where('activo', true)->orderBy('name')->get();

        // Stats del día
        $stats = [
            'presentes'  => Assist::whereDate('created_at', $fecha)
                ->where('tipo', 'entrada')
                ->distinct('user_id')->count('user_id'),
            'con_salida' => Assist::whereDate('created_at', $fecha)
                ->where('tipo', 'salida')
                ->distinct('user_id')->count('user_id'),
            'total_personal' => User::where('activo', true)->count(),
        ];

        return view('admin.asistencias.index', compact(
            'registros', 'porUsuario', 'usuarios', 'fecha', 'stats'
        ));
    }
}