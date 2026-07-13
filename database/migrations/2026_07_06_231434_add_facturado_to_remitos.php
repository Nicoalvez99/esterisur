<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('remitos', function (Blueprint $table) {
            $table->boolean('facturado')->default(false)->after('observaciones');
            $table->dateTime('fecha_facturacion')->nullable()->after('facturado');
            $table->foreignId('facturado_por')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->after('fecha_facturacion');
        });
    }

    public function down(): void
    {
        Schema::table('remitos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('facturado_por');
            $table->dropColumn(['facturado', 'fecha_facturacion']);
        });
    }
};
