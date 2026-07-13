<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Protocolo extends Model
{
    protected $fillable = [
        'institucion_id',
        'nombre',
        'metodo_permitido',
        'tipo_empaque',
        'empaque_detalle',
        'tipo_traslado',
        'vencimiento_dias',
        'unidades_por_caja',
        'formato_remito',
        'requisitos_especiales',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class);
    }

    public function getMetodoLabelAttribute(): string
    {
        return match($this->metodo_permitido) {
            'vapor'  => 'Vapor',
            'eto'    => 'ETO',
            'ambos'  => 'Vapor y ETO',
            default  => $this->metodo_permitido,
        };
    }

    public function getEmpaqueLabel(): string
    {
        return match($this->tipo_empaque) {
            'bolsa_simple' => 'Bolsa simple',
            'doble_bolsa'  => 'Doble bolsa',
            'caja'         => 'Caja',
            'bulto'        => 'Bulto',
            'otro'         => 'Otro',
            default        => $this->tipo_empaque,
        };
    }

    public function getTrasladoLabel(): string
    {
        return match($this->tipo_traslado) {
            'retira_cliente'  => 'Retira el cliente',
            'envio_domicilio' => 'Envío a domicilio',
            'courier'         => 'Courier',
            'otro'            => 'Otro',
            default           => $this->tipo_traslado,
        };
    }
}
