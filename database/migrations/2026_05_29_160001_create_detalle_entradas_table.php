<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_entradas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entrada_id');
            $table->unsignedBigInteger('medicamento_id');
            $table->unsignedBigInteger('presentacion_id');
            $table->unsignedBigInteger('laboratorio_id')->nullable();
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->string('lote', 100);
            $table->date('fecha_vencimiento')->nullable();
            $table->date('fecha_fabricacion')->nullable();
            $table->decimal('cantidad', 12, 2);
            $table->unsignedBigInteger('unidad_medida_id')->nullable();
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('costo_total', 12, 2)->default(0);
            $table->decimal('temperatura_min', 5, 2)->nullable();
            $table->decimal('temperatura_max', 5, 2)->nullable();
            $table->string('ubicacion', 100)->nullable();
            $table->string('registro_invima', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('entrada_id')->references('id')->on('entradas')->cascadeOnDelete();
            $table->foreign('medicamento_id')->references('id')->on('medicamentos')->restrictOnDelete();
            $table->foreign('presentacion_id')->references('id')->on('presentaciones')->restrictOnDelete();
            $table->foreign('laboratorio_id')->references('id')->on('laboratorios')->nullOnDelete();
            $table->foreign('proveedor_id')->references('id')->on('proveedores')->nullOnDelete();
            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();

            $table->index(['medicamento_id','lote']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_entradas');
    }
};
