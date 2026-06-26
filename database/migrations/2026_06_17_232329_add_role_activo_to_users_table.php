<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', [
                'administrador',
                'recepcion',
                'acondicionamiento',
                'esterilizacion',
                'calidad',
                'despacho',
                'facturacion',
                'auditor',
                'sin-asignar'
            ])->default('sin-asignar')->after('email');

            $table->boolean('activo')->default(true)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'activo']);
        });
    }
};
