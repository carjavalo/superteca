<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo_movimiento', ['ENTRADA','SALIDA','AJUSTE','TRASLADO']);
            $table->string('referencia_tipo', 50)->nullable();
            $table->unsignedBigInteger('referencia_id')->nullable();
            $table->unsignedBigInteger('inventario_lote_id');
            $table->decimal('cantidad', 12, 2);
            $table->decimal('stock_anterior', 12, 2)->default(0);
            $table->decimal('stock_nuevo', 12, 2)->default(0);
            $table->dateTime('fecha_movimiento');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->foreign('inventario_lote_id')->references('id')->on('inventario_lotes')->cascadeOnDelete();
            $table->foreign('usuario_id')->references('id')->on('users')->nullOnDelete();
            $table->index('tipo_movimiento');
            $table->index(['referencia_tipo','referencia_id']);
            $table->index('fecha_movimiento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
