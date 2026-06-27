<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'numero_lote',
        'uuid',
        'instituciones_id',
        'estado_actual',
        'metodo_esterilizacion',
        'fecha_recepcion',
        'fecha_entrega_pactada',
        'fecha_finalizacion',
        'usuario_actual_id',
        'prioridad',
        'observaciones',
    ];

    protected $casts = [
        'fecha_recepcion'      => 'datetime',
        'fecha_entrega_pactada'=> 'date',
        'fecha_finalizacion'   => 'datetime',
    ];

    // -----------------------------------------------------------------------
    // Constantes
    // -----------------------------------------------------------------------

    const ESTADOS = [
        'recepcion'         => 'Recepción',
        'acondicionamiento' => 'Acondicionamiento',
        'vapor'             => 'Esterilización vapor',
        'eto'               => 'Esterilización ETO',
        'control_calidad'   => 'Control de calidad',
        'almacenamiento'    => 'Almacenamiento',
        'entrega'           => 'Preparación de entrega',
        'finalizado'        => 'Finalizado',
        'retenido'          => 'Retenido',
        'rechazado'         => 'Rechazado',
    ];

    const PRIORIDADES = [
        'normal'  => 'Normal',
        'urgente' => 'Urgente',
        'critica' => 'Crítica',
    ];

    const METODOS = [
        'vapor' => 'Vapor (Autoclave)',
        'eto'   => 'ETO (Óxido de etileno)',
    ];

    // -----------------------------------------------------------------------
    // Relaciones
    // -----------------------------------------------------------------------

    /** Institución cliente */
    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class, 'instituciones_id');
    }

    /** Usuario responsable actual del lote */
    public function usuarioActual(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_actual_id');
    }

    /** Datos del ingreso (recepción) */
    public function recepcion(): HasOne
    {
        return $this->hasOne(Recepcion::class);
    }

    /** Ítems/dispositivos del lote */
    public function items(): HasMany
    {
        return $this->hasMany(LoteItem::class);
    }

    /** Registro de acondicionamiento */
    public function acondicionamiento(): HasOne
    {
        return $this->hasOne(Acondicionamiento::class);
    }

    /** Historial de estados y acciones */
    public function historial(): HasMany
    {
        return $this->hasMany(HistorialLote::class);
    }

    /** Esterilizacion del lote (via pivot) */
    public function esterilizaciones()
    {
        return $this->belongsToMany(Esterilizacion::class, 'esterilizacion_lotes');
    }

    /** Ultimo ciclo de esterilizacion */
    public function esterilizacion(): HasOne
    {
        return $this->hasOne(\App\Models\Esterilizacion::class, 'id', 'id')
                    ->whereHas('lotes', fn($q) => $q->where('lote_id', $this->id))
                    ->latestOfMany();
    }

    /** Registro de liberacion */
    public function liberacion(): HasOne
    {
        return $this->hasOne(Liberacion::class);
    }

    // -----------------------------------------------------------------------
    // Helpers de estado
    // -----------------------------------------------------------------------

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado_actual] ?? ucfirst($this->estado_actual);
    }

    public function getPrioridadLabelAttribute(): string
    {
        return self::PRIORIDADES[$this->prioridad] ?? ucfirst($this->prioridad);
    }

    public function getMetodoLabelAttribute(): string
    {
        return self::METODOS[$this->metodo_esterilizacion] ?? strtoupper($this->metodo_esterilizacion);
    }

    public function estaActivo(): bool
    {
        return !in_array($this->estado_actual, ['finalizado', 'rechazado']);
    }

    public function estaRetenido(): bool
    {
        return $this->estado_actual === 'retenido';
    }

    public function puedeAvanzar(): bool
    {
        return !in_array($this->estado_actual, ['retenido', 'rechazado', 'finalizado']);
    }

    public function entregaVencida(): bool
    {
        return $this->fecha_entrega_pactada
            && $this->fecha_entrega_pactada->isPast()
            && !in_array($this->estado_actual, ['finalizado', 'despachado', 'entregado']);
    }

    // -----------------------------------------------------------------------
    // Scopes
    // -----------------------------------------------------------------------

    public function scopeActivos($query)
    {
        return $query->whereNotIn('estado_actual', ['finalizado', 'rechazado']);
    }

    public function scopeEnEstado($query, string|array $estado)
    {
        return $query->whereIn('estado_actual', (array) $estado);
    }

    public function scopeDeInstitucion($query, int $institucionId)
    {
        return $query->where('instituciones_id', $institucionId);
    }

    public function scopePrioritarios($query)
    {
        return $query->whereIn('prioridad', ['urgente', 'critica'])
                     ->orderByRaw("FIELD(prioridad, 'critica', 'urgente')");
    }

    public function scopeVencidos($query)
    {
        return $query->whereNotNull('fecha_entrega_pactada')
                     ->whereDate('fecha_entrega_pactada', '<', today())
                     ->whereNotIn('estado_actual', ['finalizado', 'rechazado']);
    }
}
