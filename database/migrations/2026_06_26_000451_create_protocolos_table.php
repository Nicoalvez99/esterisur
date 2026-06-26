<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protocolos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('institucion_id')
                ->constrained('instituciones')
                ->cascadeOnDelete();

            $table->string('nombre')->nullable(); // ej: "Protocolo estándar", "Urgencias"

            // Método permitido para esta institución
            $table->enum('metodo_permitido', ['vapor', 'eto', 'ambos'])->default('ambos');

            // Empaque
            $table->enum('tipo_empaque', [
                'bolsa_simple',
                'doble_bolsa',
                'caja',
                'bulto',
                'otro',
            ])->default('bolsa_simple');
            $table->string('empaque_detalle')->nullable();

            // Tipo de traslado
            $table->enum('tipo_traslado', [
                'retira_cliente',
                'envio_domicilio',
                'courier',
                'otro',
            ])->default('retira_cliente');

            // Vencimiento del material esterilizado
            $table->unsignedInteger('vencimiento_dias')->default(180); // 6 meses por defecto

            // Cantidades por caja/bulto
            $table->unsignedInteger('unidades_por_caja')->nullable();
            $table->string('formato_remito')->nullable(); // descripción del formato de remito

            // Requisitos especiales visibles para operarios
            $table->text('requisitos_especiales')->nullable();

            $table->boolean('activo')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('protocolos');
    }
};
