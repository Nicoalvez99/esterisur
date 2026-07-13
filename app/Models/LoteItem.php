<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoteItem extends Model
{
    /**
     * La tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'lote_items';

    /**
     * Los atributos que son asignables en masa (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lote_id',
        'nombre',
        'cantidad',
        'codigo_interno',
        'observaciones',
    ];

    /**
     * Relación: Un ítem pertenece a un lote específico.
     *
     * @return BelongsTo
     */
    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }
}
