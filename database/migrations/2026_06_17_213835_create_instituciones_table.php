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
        Schema::create('instituciones', function (Blueprint $table) {

            $table->id();

            $table->string('nombre');

            $table->string('codigo')
                ->nullable();

            $table->string('cuit')
                ->nullable();

            $table->string('telefono')
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->string('direccion')
                ->nullable();

            $table->string('ciudad')
                ->nullable();

            $table->string('provincia')
                ->nullable();

            $table->boolean('activo')
                ->default(true);

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
        Schema::dropIfExists('instituciones');
    }
};
