<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_ajustes_inventario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ajuste_id')->constrained('ajustes_inventario')->cascadeOnDelete();
            $table->foreignId('inventario_lote_id')->constrained('inventario_lotes');
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('stock_sistema', 12, 2)->default(0);
            $table->decimal('stock_fisico', 12, 2)->default(0);
            $table->decimal('diferencia', 12, 2)->default(0);
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('valor_ajuste', 14, 2)->default(0);
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index('ajuste_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_ajustes_inventario');
    }
};
