<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presentaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('unidad_medida_id')->nullable()->after('unidad_concentracion');
            $table->unsignedBigInteger('unidad_volumen_id')->nullable()->after('unidad_volumen');

            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();
            $table->foreign('unidad_volumen_id')->references('id')->on('unidades_medida')->nullOnDelete();
        });

        Schema::table('inventario_lotes', function (Blueprint $table) {
            $table->unsignedBigInteger('unidad_medida_id')->nullable()->after('unidad_medida');
            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('presentaciones', function (Blueprint $table) {
            $table->dropForeign(['unidad_medida_id']);
            $table->dropForeign(['unidad_volumen_id']);
            $table->dropColumn(['unidad_medida_id', 'unidad_volumen_id']);
        });

        Schema::table('inventario_lotes', function (Blueprint $table) {
            $table->dropForeign(['unidad_medida_id']);
            $table->dropColumn('unidad_medida_id');
        });
    }
};
