<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remitos', function (Blueprint $table) {
            $table->id();

            // Número de remito (único, generado automáticamente)
            $table->string('numero')->unique();

            // Lote asociado (1:1 por ahora, se puede extender a muchos)
            $table->foreignId('lote_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('institucion_id')
                ->constrained('institucions')
                ->restrictOnDelete();

            // Chofer y transporte
            $table->string('chofer_nombre')->nullable();
            $table->string('chofer_transporte')->nullable();

            // Cantidades despachadas
            $table->unsignedInteger('cant_cajas_chicas')->default(0);
            $table->unsignedInteger('cant_cajas_medianas')->default(0);
            $table->unsignedInteger('cant_cajas_grandes')->default(0);
            $table->unsignedInteger('cant_bultos')->default(0);
            $table->unsignedInteger('cant_unidades')->default(0);
            $table->unsignedInteger('cant_equipos_ropa')->default(0);
            $table->decimal('cant_litros', 8, 2)->default(0);

            // Estado del remito
            $table->enum('estado', [
                'preparacion',  // armando las cajas
                'despachado',   // salió con el chofer
                'entregado',    // confirmación de entrega
            ])->default('preparacion');

            // Fechas
            $table->dateTime('fecha_despacho')->nullable();
            $table->dateTime('fecha_entrega_confirmada')->nullable();

            // Operario que generó el remito
            $table->foreignId('operario_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('observaciones')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remitos');
    }
};
