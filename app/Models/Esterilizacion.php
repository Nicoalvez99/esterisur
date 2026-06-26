<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Esterilizacion extends Model
{
    protected $fillable = [
        'equipo_id', 'metodo', 'operario_id',
        'fecha_inicio', 'fecha_fin',
        'temperatura', 'presion', 'tiempo_minutos',
        'concentracion', 'aireacion_inicio', 'aireacion_fin',
        'resultado', 'observaciones',
    ];

    protected $casts = [
        'fecha_inicio'     => 'datetime',
        'fecha_fin'        => 'datetime',
        'aireacion_inicio' => 'datetime',
        'aireacion_fin'    => 'datetime',
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function operario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operario_id');
    }

    public function lotes(): BelongsToMany
    {
        return $this->belongsToMany(Lote::class, 'esterilizacion_lotes')
                    ->withPivot('observaciones')
                    ->withTimestamps();
    }

    public function controles(): HasMany
    {
        return $this->hasMany(Control::class);
    }

    // Helpers
    public function duracionMinutos(): ?int
    {
        if (!$this->fecha_inicio || !$this->fecha_fin) return null;
        return (int) $this->fecha_inicio->diffInMinutes($this->fecha_fin);
    }

    public function aireacionHoras(): ?float
    {
        if (!$this->aireacion_inicio || !$this->aireacion_fin) return null;
        return round($this->aireacion_inicio->diffInMinutes($this->aireacion_fin) / 60, 1);
    }

    public function todosControlesConformes(): bool
    {
        return $this->controles->isNotEmpty()
            && $this->controles->every(fn($c) => $c->resultado === 'conforme');
    }
}