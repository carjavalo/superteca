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
        Schema::create('inventario_lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicamento_id')->constrained('medicamentos')->onDelete('cascade');
            $table->string('lote', 100);
            $table->date('fecha_vencimiento')->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->decimal('cantidad_inicial', 12, 2)->default(0);
            $table->decimal('cantidad_actual', 12, 2)->default(0);
            $table->string('unidad_medida', 50)->nullable();
            $table->decimal('costo_unitario', 12, 2)->nullable();
            $table->string('ubicacion', 100)->nullable();
            $table->decimal('temperatura_min', 5, 2)->nullable();
            $table->decimal('temperatura_max', 5, 2)->nullable();
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->boolean('estado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_lotes');
    }
};
