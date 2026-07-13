<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assist extends Model
{
    protected $fillable = [
        'user_id',
        'tipo',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -----------------------------------------------------------------------
    // Helpers estáticos
    // -----------------------------------------------------------------------

    /**
     * Determina qué tipo de fichaje corresponde al usuario ahora.
     * Si el último registro del día es 'entrada' → corresponde 'salida' y viceversa.
     */
    public static function proximoTipo(User $user): string
    {
        $ultimo = static::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->latest()
            ->value('tipo');

        return $ultimo === 'entrada' ? 'salida' : 'entrada';
    }

    /**
     * Calcula las horas trabajadas del día para un usuario.
     * Empareja entradas con salidas cronológicamente.
     */
    public static function horasTrabajadas(User $user, $fecha = null): float
    {
        $fecha ??= today();

        $registros = static::where('user_id', $user->id)
            ->whereDate('created_at', $fecha)
            ->orderBy('created_at')
            ->get();

        $total   = 0;
        $entrada = null;

        foreach ($registros as $reg) {
            if ($reg->tipo === 'entrada') {
                $entrada = $reg->created_at;
            } elseif ($reg->tipo === 'salida' && $entrada) {
                $total  += $entrada->diffInMinutes($reg->created_at);
                $entrada = null;
            }
        }

        return round($total / 60, 2);
    }

    /**
     * Verifica si el usuario está actualmente "dentro" (última marca del día es entrada).
     */
    public static function estaAdentro(User $user): bool
    {
        $ultimo = static::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->latest()
            ->value('tipo');

        return $ultimo === 'entrada';
    }
}