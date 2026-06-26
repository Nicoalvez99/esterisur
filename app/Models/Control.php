<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Control extends Model
{
    protected $fillable = [
        'esterilizacion_id', 'tipo', 'resultado',
        'descripcion', 'observaciones', 'operario_id', 'fecha_lectura',
    ];

    protected $casts = [
        'fecha_lectura' => 'datetime',
    ];

    public function esterilizacion(): BelongsTo
    {
        return $this->belongsTo(Esterilizacion::class);
    }

    public function operario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operario_id');
    }

    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            'fisico'    => 'Físico',
            'quimico'   => 'Químico',
            'biologico' => 'Biológico',
            default     => ucfirst($this->tipo),
        };
    }
}