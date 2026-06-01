<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formas_farmaceuticas', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 100);
            $table->string('nombre_corto', 50)->nullable();
            $table->text('descripcion')->nullable();

            $table->enum('tipo', [
                'SOLIDO',
                'LIQUIDO',
                'SEMISOLIDO',
                'GASEOSO',
                'ESTERIL',
            ])->nullable();

            $table->boolean('requiere_reconstitucion')->default(0);
            $table->boolean('requiere_dilucion')->default(0);
            $table->boolean('esteril')->default(0);
            $table->boolean('multidosis')->default(0);
            $table->boolean('reutilizable')->default(0);
            $table->boolean('requiere_cadena_frio')->default(0);

            $table->decimal('temperatura_min', 5, 2)->nullable();
            $table->decimal('temperatura_max', 5, 2)->nullable();
            $table->integer('tiempo_estabilidad_horas')->nullable();

            $table->boolean('permite_fraccionamiento')->default(0);

            $table->enum('riesgo_contaminacion', ['BAJO', 'MEDIO', 'ALTO'])->nullable();

            $table->string('color_identificacion', 20)->nullable();
            $table->string('icono', 255)->nullable();

            $table->text('observaciones')->nullable();

            $table->boolean('estado')->default(1);

            $table->timestamps();

            $table->index('tipo');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formas_farmaceuticas');
    }
};
