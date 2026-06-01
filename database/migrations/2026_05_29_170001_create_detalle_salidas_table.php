<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_salidas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salida_id');
            $table->unsignedBigInteger('inventario_lote_id');
            $table->unsignedBigInteger('medicamento_id');
            $table->unsignedBigInteger('presentacion_id');
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad', 12, 2);
            $table->unsignedBigInteger('unidad_medida_id')->nullable();
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('costo_total', 12, 2)->default(0);
            $table->string('motivo_salida', 255)->nullable();
            $table->unsignedBigInteger('paciente_id')->nullable();
            $table->string('numero_preparacion', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('salida_id')->references('id')->on('salidas')->cascadeOnDelete();
            $table->foreign('inventario_lote_id')->references('id')->on('inventario_lotes')->restrictOnDelete();
            $table->foreign('medicamento_id')->references('id')->on('medicamentos')->restrictOnDelete();
            $table->foreign('presentacion_id')->references('id')->on('presentaciones')->restrictOnDelete();
            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();

            $table->index(['medicamento_id','lote']);
            $table->index('inventario_lote_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_salidas');
    }
};
