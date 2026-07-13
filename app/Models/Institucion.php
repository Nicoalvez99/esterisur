<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Institucion extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'cuit',
        'telefono',
        'email',
        'direccion',
        'ciudad',
        'provincia',
        'activo',
        'observaciones',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function lotes(): HasMany
    {
        return $this->hasMany(Lote::class, 'instituciones_id');
    }

    public function recepciones(): HasMany
    {
        return $this->hasMany(Recepcion::class);
    }

    public function protocolos(): HasMany
    {
        return $this->hasMany(Protocolo::class);
    }

    /** Protocolo activo principal (el primero activo) */
    public function protocolo(): HasOne
    {
        return $this->hasOne(Protocolo::class)->where('activo', true)->latestOfMany();
    }
}