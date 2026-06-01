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
        Schema::table('laboratorios', function (Blueprint $table) {
            $table->enum('semaforo_sanitario', ['verde', 'amarillo', 'rojo'])->default('verde')->after('estado');
        });
        
        Schema::table('medicamentos', function (Blueprint $table) {
            $table->enum('semaforo_sanitario', ['verde', 'amarillo', 'rojo'])->default('verde')->after('estado');
        });

        Schema::table('presentaciones', function (Blueprint $table) {
            $table->enum('semaforo_sanitario', ['verde', 'amarillo', 'rojo'])->default('verde')->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laboratorios', function (Blueprint $table) {
            $table->dropColumn('semaforo_sanitario');
        });
        
        Schema::table('medicamentos', function (Blueprint $table) {
            $table->dropColumn('semaforo_sanitario');
        });

        Schema::table('presentaciones', function (Blueprint $table) {
            $table->dropColumn('semaforo_sanitario');
        });
    }
};
