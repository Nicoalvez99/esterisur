<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recepcion extends Model
{
    protected $fillable = [
        'lote_id',
        'institucion_id',
        'chofer_nombre',
        'chofer_transporte',
        'tiene_remito',
        'remito_numero',
        'cant_cajas',
        'cant_bultos',
        'cant_unidades',
        'cant_equipos_ropa',
        'cant_litros',
        'metodo',
        'estado_empaque',
        'fecha_entrega_pactada',
        'prioridad',
        'operario_id',
        'observaciones',
    ];

    protected $casts = [
        'tiene_remito'         => 'boolean',
        'fecha_entrega_pactada' => 'date',
        'cant_litros'          => 'decimal:2',
    ];

    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class);
    }

    public function operario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operario_id');
    }
}
