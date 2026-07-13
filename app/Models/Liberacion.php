<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Liberacion extends Model
{
    protected $fillable = [
        'lote_id',
        'esterilizacion_id',
        'resultado',
        'controles_completos',
        'post_proceso_ok',
        'sin_incidencias_abiertas',
        'responsable_id',
        'observaciones',
        'fecha_liberacion',
    ];

    protected $casts = [
        'controles_completos'       => 'boolean',
        'post_proceso_ok'           => 'boolean',
        'sin_incidencias_abiertas'  => 'boolean',
        'fecha_liberacion'          => 'datetime',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function esterilizacion(): BelongsTo
    {
        return $this->belongsTo(Esterilizacion::class);
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function getResultadoLabelAttribute(): string
    {
        return match($this->resultado) {
            'liberado'  => 'Liberado',
            'retenido'  => 'Retenido',
            'rechazado' => 'Rechazado',
            default     => ucfirst($this->resultado),
        };
    }
}