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
        Schema::create('recepciones', function (Blueprint $table) {
            $table->id();
 
            // Relación 1:1 con lote
            $table->foreignId('lote_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();
 
            // Institución (redundante con lotes pero útil para consultas directas)
            $table->foreignId('institucion_id')
                ->constrained('instituciones')
                ->restrictOnDelete();
 
            // Chofer y transporte (texto libre por ahora, tabla después)
            $table->string('chofer_nombre')->nullable();
            $table->string('chofer_transporte')->nullable();
 
            // Remito
            $table->boolean('tiene_remito')->default(false);
            $table->string('remito_numero')->nullable();
 
            // Cantidades recibidas
            $table->unsignedInteger('cant_cajas')->default(0);
            $table->unsignedInteger('cant_bultos')->default(0);
            $table->unsignedInteger('cant_unidades')->default(0);
            $table->unsignedInteger('cant_equipos_ropa')->default(0);
            $table->decimal('cant_litros', 8, 2)->default(0);
 
            // Clasificación
            $table->enum('metodo', ['vapor', 'eto']);
            $table->enum('estado_empaque', ['empaquetado', 'sin_empaquetar']);
 
            // Fecha de entrega comprometida
            $table->date('fecha_entrega_pactada')->nullable();
 
            // Prioridad
            $table->enum('prioridad', ['normal', 'urgente', 'critica'])->default('normal');
 
            // Operario que registró
            $table->foreignId('operario_id')
                ->constrained('users')
                ->restrictOnDelete();
 
            // Observaciones
            $table->text('observaciones')->nullable();
 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recepciones');
    }
};
