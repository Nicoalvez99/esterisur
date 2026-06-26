<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Acondicionamiento extends Model
{
    protected $fillable = [
        'lote_id',
        'tiene_planilla',
        'cant_declarada',
        'cant_real',
        'diferencia',
        'cant_limpio',
        'cant_sucio',
        'cant_integro',
        'cant_roto',
        'cant_devuelto',
        'tipo_empaque',
        'cant_empaque',
        'empaque_detalle',
        'resultado',
        'operario_id',
        'fecha_inicio',
        'fecha_fin',
        'observaciones',
    ];

    protected $casts = [
        'tiene_planilla' => 'boolean',
        'fecha_inicio'   => 'datetime',
        'fecha_fin'      => 'datetime',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function operario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operario_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AcondicionamientoItem::class);
    }
}
