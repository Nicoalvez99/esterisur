<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('buscar')) {
            $q = $request->buscar;
            $query->where(function ($qb) use ($q) {
                $qb->where('name', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo === '1');
        }

        $usuarios = $query->paginate(15)->withQueryString();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = User::ROLES;
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role'  => ['required', 'in:' . implode(',', array_keys(User::ROLES))],
            'activo'=> ['boolean'],
        ], [
            'name.required'  => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.unique'   => 'Ya existe un usuario con ese email.',
            'role.required'  => 'Asigná un rol al usuario.',
            'role.in'        => 'El rol seleccionado no es válido.',
        ]);

        // Generar contraseña aleatoria segura
        $passwordPlano = Str::random(10);

        $usuario = User::create([
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'role'   => $validated['role'],
            'activo' => $request->boolean('activo', true),
            'password' => Hash::make($passwordPlano),
        ]);

        // Guardamos la contraseña en sesión para mostrarla UNA SOLA VEZ
        session()->flash('password_generada', $passwordPlano);
        session()->flash('usuario_creado', $usuario->name);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', "Usuario \"{$usuario->name}\" creado correctamente.");
    }

    public function edit(User $usuario)
    {
        $roles = User::ROLES;
        return view('admin.usuarios.edit', compact('usuario', 'roles'));
    }

    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', "unique:users,email,{$usuario->id}"],
            'role'  => ['required', 'in:' . implode(',', array_keys(User::ROLES))],
            'activo'=> ['boolean'],
        ], [
            'name.required'  => 'El nombre es obligatorio.',
            'email.required' => 'El email es obligatorio.',
            'email.unique'   => 'Ya existe un usuario con ese email.',
            'role.required'  => 'Asigná un rol al usuario.',
        ]);

        $data = [
            'name'   => $validated['name'],
            'email'  => $validated['email'],
            'role'   => $validated['role'],
            'activo' => $request->boolean('activo'),
        ];

        // Si se quiere resetear la contraseña
        if ($request->filled('nueva_password')) {
            $request->validate([
                'nueva_password' => ['min:8', 'confirmed'],
            ], [
                'nueva_password.min'       => 'La contraseña debe tener al menos 8 caracteres.',
                'nueva_password.confirmed' => 'Las contraseñas no coinciden.',
            ]);
            $data['password'] = Hash::make($request->nueva_password);
        }

        $usuario->update($data);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', "Usuario \"{$usuario->name}\" actualizado correctamente.");
    }

    /**
     * Genera y muestra una nueva contraseña para el usuario (reset rápido).
     */
    public function resetPassword(User $usuario)
    {
        $passwordPlano = Str::random(10);
        $usuario->update(['password' => Hash::make($passwordPlano)]);

        session()->flash('password_generada', $passwordPlano);
        session()->flash('usuario_creado', $usuario->name);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('success', "Contraseña de \"{$usuario->name}\" reseteada.");
    }
}