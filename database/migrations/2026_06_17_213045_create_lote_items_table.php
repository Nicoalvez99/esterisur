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
        Schema::create('lote_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('lote_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('nombre');

            $table->integer('cantidad')
                ->default(1);

            $table->string('codigo_interno')
                ->nullable();

            $table->text('observaciones')
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote_items');
    }
};
