<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ---------------------------------------------------------------
        // EQUIPOS (maestro)
        // ---------------------------------------------------------------
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');                          // Ej: "Autoclave 1"
            $table->enum('metodo', ['vapor', 'eto']);
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_interno')->nullable();      // código interno
            $table->unsignedInteger('capacidad')->nullable();  // unidades/bolsas
            $table->enum('estado', ['activo', 'inactivo', 'en_mantenimiento'])->default('activo');
            $table->date('fecha_ultima_validacion')->nullable();
            $table->date('fecha_proxima_validacion')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // ---------------------------------------------------------------
        // ESTERILIZACIONES (ciclos)
        // ---------------------------------------------------------------
        Schema::create('esterilizaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipo_id')
                ->constrained('equipos')
                ->restrictOnDelete();

            $table->enum('metodo', ['vapor', 'eto']);

            // Operario que cargó el ciclo
            $table->foreignId('operario_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Tiempos del ciclo
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();

            // Parámetros VAPOR
            $table->decimal('temperatura', 5, 2)->nullable();  // °C
            $table->decimal('presion', 5, 2)->nullable();      // bar/kPa
            $table->unsignedInteger('tiempo_minutos')->nullable();

            // Parámetros ETO
            $table->decimal('concentracion', 6, 2)->nullable(); // mg/L
            $table->dateTime('aireacion_inicio')->nullable();
            $table->dateTime('aireacion_fin')->nullable();

            // Resultado general del ciclo
            $table->enum('resultado', [
                'conforme',
                'no_conforme',
                'pendiente',
            ])->default('pendiente');

            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // ---------------------------------------------------------------
        // PIVOT: esterilizacion ↔ lotes
        // ---------------------------------------------------------------
        Schema::create('esterilizacion_lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('esterilizacion_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('lote_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->unique(['esterilizacion_id', 'lote_id']);
        });

        // ---------------------------------------------------------------
        // CONTROLES (físico, químico, biológico)
        // ---------------------------------------------------------------
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
