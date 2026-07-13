<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialLote extends Model
{
    /**
     * La tabla asociada al modelo.
     * Por defecto Laravel buscaría "historial_lotes", lo cual coincide perfectamente,
     * pero dejarlo explícito evita cualquier problema de mapeo.
     *
     * @var string
     */
    protected $table = 'historial_lotes';

    /**
     * Los atributos que son asignables en masa (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lote_id',
        'user_id',
        'accion',
        'estado_origen',
        'estado_destino',
        'detalle',
    ];

    /**
     * Relación: Un registro de historial pertenece a un lote específico.
     * * @return BelongsTo
     */
    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class);
    }

    /**
     * Relación: Un registro de historial fue generado por un usuario.
     * * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
