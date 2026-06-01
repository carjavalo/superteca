<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entradas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->enum('tipo_entrada', ['COMPRA','DONACION','DEVOLUCION','TRASLADO','AJUSTE','PRODUCCION'])->default('COMPRA');
            $table->unsignedBigInteger('proveedor_id')->nullable();
            $table->string('numero_factura', 100)->nullable();
            $table->string('numero_remision', 100)->nullable();
            $table->dateTime('fecha_entrada');
            $table->date('fecha_documento')->nullable();
            $table->text('observaciones')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('impuestos', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->enum('estado', ['BORRADOR','CONFIRMADA','ANULADA'])->default('BORRADOR');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('bodega_destino_id')->nullable();
            $table->timestamps();

            $table->foreign('proveedor_id')->references('id')->on('proveedores')->nullOnDelete();
            $table->foreign('usuario_id')->references('id')->on('users')->nullOnDelete();
            $table->index('tipo_entrada');
            $table->index('estado');
            $table->index('fecha_entrada');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entradas');
    }
};
