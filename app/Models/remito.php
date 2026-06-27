<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Remito extends Model
{
    protected $fillable = [
        'numero',
        'lote_id',
        'institucion_id',
        'chofer_nombre',
        'chofer_transporte',
        'cant_cajas_chicas',
        'cant_cajas_medianas',
        'cant_cajas_grandes',
        'cant_bultos',
        'cant_unidades',
        'cant_equipos_ropa',
        'cant_litros',
        'estado',
        'fecha_despacho',
        'fecha_entrega_confirmada',
        'operario_id',
        'observaciones',
    ];

    protected $casts = [
        'fecha_despacho'           => 'datetime',
        'fecha_entrega_confirmada' => 'datetime',
        'cant_litros'              => 'decimal:2',
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

    public function totalBultos(): int
    {
        return $this->cant_cajas_chicas
            + $this->cant_cajas_medianas
            + $this->cant_cajas_grandes
            + $this->cant_bultos;
    }
}