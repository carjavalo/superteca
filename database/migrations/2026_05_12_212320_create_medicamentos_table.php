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
        Schema::create('medicamentos', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 255);
            $table->string('nombre_generico', 255)->nullable();
            $table->string('concentracion', 100)->nullable();
            $table->string('unidad_medida', 50)->nullable();
            $table->string('forma_farmaceutica', 100)->nullable();
            $table->string('via_administracion', 100)->nullable();
            $table->string('laboratorio', 255)->nullable();
            $table->string('registro_invima', 100)->nullable();
            $table->boolean('requiere_refrigeracion')->default(0);
            $table->boolean('fotoproteccion')->default(0);
            $table->boolean('alto_riesgo')->default(0);
            $table->boolean('controlado')->default(0);
            $table->integer('estabilidad_horas')->nullable();
            $table->boolean('estado')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicamentos');
    }
};
