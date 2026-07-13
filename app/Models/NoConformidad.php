<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NoConformidad extends Model
{
    protected $table = 'no_conformidades';

    protected $fillable = [
        'lote_id',
        'tipo',
        'estado',
        'accion_tomada',
        'descripcion',
        'cantidad_afectada',
        'accion_correctiva',
        'observaciones_cierre',
        'registrado_por',
        'cerrado_por',
        'fecha_cierre',
    ];

    protected $casts = [
        'fecha_cierre' => 'datetime',
    ];

    const TIPOS = [
        'material_sucio'       => 'Material sucio',
        'material_roto'        => 'Material roto',
        'faltante'             => 'Faltante',
        'sobrante'             => 'Sobrante',
        'sin_remito'           => 'Sin remito',
        'control_fallido'      => 'Control fallido',
        'empaque_danado'       => 'Empaque dañado',
        'aireacion_incompleta' => 'Aireación incompleta',
        'error_etiqueta'       => 'Error de etiqueta',
        'otro'                 => 'Otro',
    ];

    const ACCIONES = [
        'devolver'   => 'Devolver a institución',
        'retener'    => 'Retener para revisión',
        'reprocesar' => 'Reprocesar',
        'rechazar'   => 'Rechazar',
        'observar'   => 'Observar y continuar',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function cerradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? ucfirst(str_replace('_', ' ', $this->tipo));
    }

    public function getAccionLabelAttribute(): string
    {
        return self::ACCIONES[$this->accion_tomada] ?? ucfirst($this->accion_tomada ?? '—');
    }
}