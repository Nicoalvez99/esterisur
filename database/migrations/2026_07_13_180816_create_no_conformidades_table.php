<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('no_conformidades', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lote_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('tipo', [
                'material_sucio',
                'material_roto',
                'faltante',
                'sobrante',
                'sin_remito',
                'control_fallido',
                'empaque_danado',
                'aireacion_incompleta',
                'error_etiqueta',
                'otro',
            ]);

            $table->enum('estado', [
                'abierta',
                'en_proceso',
                'cerrada',
            ])->default('abierta');

            $table->enum('accion_tomada', [
                'devolver',
                'retener',
                'reprocesar',
                'rechazar',
                'observar',
            ])->nullable();

            $table->text('descripcion');
            $table->unsignedInteger('cantidad_afectada')->nullable();
            $table->text('accion_correctiva')->nullable();
            $table->text('observaciones_cierre')->nullable();

            // Quién la registró
            $table->foreignId('registrado_por')
                ->constrained('users')
                ->restrictOnDelete();

            // Quién la cerró
            $table->foreignId('cerrado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('fecha_cierre')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('no_conformidades');
    }
};