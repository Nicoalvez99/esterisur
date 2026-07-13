<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liberaciones', function (Blueprint $table) {
            $table->id();

            // 1:1 con lote
            $table->foreignId('lote_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            // Esterilización de referencia
            $table->foreignId('esterilizacion_id')
                ->constrained()
                ->restrictOnDelete();

            // Decisión
            $table->enum('resultado', [
                'liberado',
                'retenido',
                'rechazado',
            ]);

            // Checks que el responsable verificó antes de liberar
            $table->boolean('controles_completos')->default(false);
            $table->boolean('post_proceso_ok')->default(false);     // enfriamiento o aireación
            $table->boolean('sin_incidencias_abiertas')->default(false);

            // Responsable de calidad
            $table->foreignId('responsable_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('observaciones')->nullable();
            $table->dateTime('fecha_liberacion');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liberaciones');
    }
};
