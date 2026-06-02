<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mezclas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->nullable();
            $table->foreignId('formula_id')->nullable()->constrained('formulas')->nullOnDelete();
            $table->enum('tipo_mezcla', [
                'NUTRICION_PARENTERAL','ANTIBIOTICO','ONCOLOGIA','PEDIATRICA','MAGISTRAL'
            ])->default('MAGISTRAL');
            $table->dateTime('fecha_programada');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->decimal('volumen_programado', 12, 2)->nullable();
            $table->unsignedBigInteger('unidad_volumen_id')->nullable();
            $table->integer('cantidad_preparaciones')->default(1);
            $table->text('observaciones')->nullable();
            $table->enum('estado', [
                'PROGRAMADA','EN_PROCESO','CONTROL_CALIDAD','LIBERADA','CANCELADA'
            ])->default('PROGRAMADA');
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->unsignedBigInteger('usuario_preparador_id')->nullable();
            $table->unsignedBigInteger('usuario_validador_id')->nullable();
            $table->timestamps();

            $table->foreign('unidad_volumen_id')->references('id')->on('unidades_medida')->nullOnDelete();
            $table->foreign('usuario_preparador_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('usuario_validador_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::create('mezclas_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mezcla_id')->constrained('mezclas')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->unsignedBigInteger('presentacion_id')->nullable();
            $table->decimal('dosis_requerida', 12, 4);
            $table->unsignedBigInteger('unidad_medida_id')->nullable();
            $table->integer('orden_preparacion')->default(1);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('presentacion_id')->references('id')->on('presentaciones')->nullOnDelete();
            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();
        });

        Schema::create('mezclas_consumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mezcla_id')->constrained('mezclas')->cascadeOnDelete();
            $table->foreignId('inventario_lote_id')->constrained('inventario_lotes');
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->unsignedBigInteger('presentacion_id')->nullable();
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad_consumida', 12, 4);
            $table->unsignedBigInteger('unidad_medida_id')->nullable();
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->timestamps();

            $table->foreign('presentacion_id')->references('id')->on('presentaciones')->nullOnDelete();
            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();
        });

        Schema::create('mezclas_producto_final', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mezcla_id')->constrained('mezclas')->cascadeOnDelete();
            $table->string('lote_produccion', 100);
            $table->dateTime('fecha_produccion');
            $table->dateTime('fecha_vencimiento')->nullable();
            $table->decimal('volumen_final', 12, 2)->nullable();
            $table->unsignedBigInteger('unidad_volumen_id')->nullable();
            $table->integer('cantidad_unidades')->default(1);
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('unidad_volumen_id')->references('id')->on('unidades_medida')->nullOnDelete();
        });

        Schema::create('mezclas_control_calidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mezcla_id')->constrained('mezclas')->cascadeOnDelete();
            $table->dateTime('fecha_control');
            $table->string('aspecto_visual', 255)->nullable();
            $table->decimal('volumen_verificado', 12, 2)->nullable();
            $table->decimal('ph', 5, 2)->nullable();
            $table->decimal('osmolaridad', 12, 2)->nullable();
            $table->decimal('temperatura', 5, 2)->nullable();
            $table->boolean('cumple')->default(true);
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('usuario_control_id')->nullable();
            $table->timestamps();

            $table->foreign('usuario_control_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mezclas_control_calidad');
        Schema::dropIfExists('mezclas_producto_final');
        Schema::dropIfExists('mezclas_consumo');
        Schema::dropIfExists('mezclas_detalle');
        Schema::dropIfExists('mezclas');
    }
};
