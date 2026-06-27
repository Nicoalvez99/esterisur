<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'activo'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    const ROLES = [
        'administrador'     => 'Administrador',
        'recepcion'         => 'Recepción',
        'acondicionamiento' => 'Acondicionamiento',
        'esterilizacion'    => 'Esterilización',
        'calidad'           => 'Control de calidad',
        'despacho'          => 'Despacho',
        'facturacion'       => 'Facturación',
        'auditor'           => 'Auditor',
    ];

    public function isAdmin(): bool
    {
        return $this->role === 'administrador';
    }
 
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }
 
    public function getRoleLabel(): string
    {
        return self::ROLES[$this->role] ?? ucfirst($this->role);
    }
 
    /**
     * Retorna la ruta nombrada de inicio según el rol del usuario.
     * Se usa al hacer login y como home del usuario autenticado.
     */
    public function homeRoute(): string
    {
        return match($this->role) {
            'administrador'     => 'admin.dashboard',
            'recepcion'         => 'recepcion',
            'acondicionamiento' => 'acondicionamiento.index',
            'esterilizacion'    => 'esterilizacion.index',
            'calidad'           => 'calidad.index',
            'despacho'          => 'despacho',
            'facturacion'       => 'facturacion',
            'auditor'           => 'auditor',
            default             => 'recepcion',
        };
    }
}
