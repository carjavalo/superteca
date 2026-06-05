<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vincula una entrada de inventario con un paciente cuando el tipo de entrada
 * es "ASIGNACION" (entradas asignadas a un paciente, para registro de inventario).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('entradas', 'paciente_id')) {
            Schema::table('entradas', function (Blueprint $table) {
                $table->unsignedBigInteger('paciente_id')->nullable()->after('proveedor_id');
                $table->foreign('paciente_id')->references('id')->on('pacientes')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('entradas', 'paciente_id')) {
            Schema::table('entradas', function (Blueprint $table) {
                $table->dropForeign(['paciente_id']);
                $table->dropColumn('paciente_id');
            });
        }
    }
};
