<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventario_lotes', function (Blueprint $table) {
            $table->foreign('proveedor_id')
                  ->references('id')->on('proveedores')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('inventario_lotes', function (Blueprint $table) {
            $table->dropForeign(['proveedor_id']);
        });
    }
};
