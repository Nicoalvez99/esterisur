<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('controles', function (Blueprint $table) {
            $table->id();
 
            $table->foreignId('esterilizacion_id')
                ->constrained()
                ->cascadeOnDelete();
 
            $table->enum('tipo', ['fisico', 'quimico', 'biologico']);
 
            $table->enum('resultado', [
                'conforme',
                'no_conforme',
                'pendiente',   // biológico puede tardar días
            ])->default('pendiente');
 
            $table->string('descripcion')->nullable(); // ej: "Bowie-Dick", "Spore test"
            $table->text('observaciones')->nullable();
 
            // Quién registró y cuándo
            $table->foreignId('operario_id')
                ->constrained('users')
                ->restrictOnDelete();
 
            $table->dateTime('fecha_lectura')->nullable(); // cuándo se leyó el resultado
            $table->timestamps();
        });
    }
 
    public function down(): void
    {
        Schema::dropIfExists('controles');
        Schema::dropIfExists('esterilizacion_lotes');
        Schema::dropIfExists('esterilizaciones');
        Schema::dropIfExists('equipos');
    }
};
