<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acondicionamientos', function (Blueprint $table) {
            $table->id();

            // Relación 1:1 con lote
            $table->foreignId('lote_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            // ¿Vino con planilla del cliente?
            $table->boolean('tiene_planilla')->default(false);

            // Cantidades declaradas (de recepción / planilla)
            $table->unsignedInteger('cant_declarada')->default(0);

            // Cantidades reales contadas
            $table->unsignedInteger('cant_real')->default(0);

            // Diferencia calculada (puede ser negativa)
            $table->integer('diferencia')->default(0);

            // Resumen de estado del material
            $table->unsignedInteger('cant_limpio')->default(0);
            $table->unsignedInteger('cant_sucio')->default(0);
            $table->unsignedInteger('cant_integro')->default(0);
            $table->unsignedInteger('cant_roto')->default(0);
            $table->unsignedInteger('cant_devuelto')->default(0);

            // Empaque aplicado
            $table->enum('tipo_empaque', [
                'bolsa_simple',
                'doble_bolsa',
                'caja',
                'bulto',
                'otro',
            ])->nullable();
            $table->unsignedInteger('cant_empaque')->default(0);
            $table->string('empaque_detalle')->nullable(); // ej: "caja chica x12"

            // Resultado de la etapa
            $table->enum('resultado', [
                'acondicionado',   // todo ok, listo para esterilizar
                'con_devoluciones',// hubo devoluciones pero el resto sigue
                'retenido',        // no puede avanzar
            ])->default('acondicionado');

            // Operario
            $table->foreignId('operario_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });

        // Detalle ítem por ítem del acondicionamiento
        Schema::create('acondicionamiento_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('acondicionamiento_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nombre'); // nombre del dispositivo/ítem

            $table->unsignedInteger('cant_declarada')->default(0);
            $table->unsignedInteger('cant_real')->default(0);

            $table->enum('estado_limpieza', ['limpio', 'sucio'])->default('limpio');
            $table->enum('estado_integridad', ['integro', 'roto'])->default('integro');

            $table->enum('accion', [
                'procesar',   // continúa al ciclo
                'devolver',   // se devuelve a la institución
                'retener',    // queda retenido para revisión
            ])->default('procesar');

            $table->string('motivo_devolucion')->nullable();
            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acondicionamiento_items');
        Schema::dropIfExists('acondicionamientos');
    }
};