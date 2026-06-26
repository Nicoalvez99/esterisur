<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    protected $fillable = [
        'nombre', 'metodo', 'marca', 'modelo', 'numero_interno',
        'capacidad', 'estado', 'fecha_ultima_validacion',
        'fecha_proxima_validacion', 'observaciones',
    ];

    protected $casts = [
        'fecha_ultima_validacion'  => 'date',
        'fecha_proxima_validacion' => 'date',
    ];

    public function esterilizaciones(): HasMany
    {
        return $this->hasMany(Esterilizacion::class);
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'activo';
    }

    public function validacionVencida(): bool
    {
        return $this->fecha_proxima_validacion
            && $this->fecha_proxima_validacion->isPast();
    }
}