<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AcondicionamientoItem extends Model
{
    protected $fillable = [
        'acondicionamiento_id',
        'nombre',
        'cant_declarada',
        'cant_real',
        'estado_limpieza',
        'estado_integridad',
        'accion',
        'motivo_devolucion',
        'observaciones',
    ];

    public function acondicionamiento(): BelongsTo
    {
        return $this->belongsTo(Acondicionamiento::class);
    }
}