<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->nullable();
            $table->string('nombre');
            $table->enum('tipo_formula', [
                'NUTRICION_PARENTERAL',
                'ANTIBIOTICO',
                'ONCOLOGIA',
                'PEDIATRIA',
                'MAGISTRAL',
                'ESTANDAR'
            ])->default('ESTANDAR');
            $table->text('descripcion')->nullable();
            $table->decimal('volumen_final', 12, 2)->nullable();
            $table->unsignedBigInteger('unidad_volumen_id')->nullable();
            $table->integer('tiempo_estabilidad_horas')->nullable();
            $table->decimal('temperatura_min', 5, 2)->nullable();
            $table->decimal('temperatura_max', 5, 2)->nullable();
            $table->boolean('requiere_refrigeracion')->default(false);
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('unidad_volumen_id')->references('id')->on('unidades_medida')->nullOnDelete();
        });

        Schema::create('formulas_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained('formulas')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->unsignedBigInteger('presentacion_id')->nullable();
            $table->decimal('dosis', 12, 4);
            $table->unsignedBigInteger('unidad_medida_id')->nullable();
            $table->integer('orden_preparacion')->default(1);
            $table->boolean('obligatorio')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('presentacion_id')->references('id')->on('presentaciones')->nullOnDelete();
            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();
        });

        Schema::create('formulas_compatibilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained('formulas')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->boolean('compatible')->default(true);
            $table->text('observacion')->nullable();
            $table->timestamps();
        });

        Schema::create('formulas_estabilidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained('formulas')->cascadeOnDelete();
            $table->decimal('temperatura_min', 5, 2)->nullable();
            $table->decimal('temperatura_max', 5, 2)->nullable();
            $table->integer('horas_estabilidad')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('formulas_diluyentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained('formulas')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->decimal('volumen', 12, 2);
            $table->unsignedBigInteger('unidad_medida_id')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();
        });

        Schema::create('formulas_requerimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained('formulas')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->decimal('cantidad_requerida', 12, 4);
            $table->unsignedBigInteger('unidad_medida_id')->nullable();
            $table->timestamps();

            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formulas_requerimientos');
        Schema::dropIfExists('formulas_diluyentes');
        Schema::dropIfExists('formulas_estabilidad');
        Schema::dropIfExists('formulas_compatibilidades');
        Schema::dropIfExists('formulas_detalle');
        Schema::dropIfExists('formulas');
    }
};
