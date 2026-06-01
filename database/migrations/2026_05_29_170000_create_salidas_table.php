<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salidas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->enum('tipo_salida', [
                'PRODUCCION',
                'DISPENSACION',
                'VENCIMIENTO',
                'DANO',
                'DEVOLUCION_PROVEEDOR',
                'TRASLADO',
                'CONSUMO_INTERNO',
                'AJUSTE_NEGATIVO',
            ])->default('DISPENSACION');
            $table->dateTime('fecha_salida');
            $table->unsignedBigInteger('paciente_id')->nullable();
            $table->unsignedBigInteger('servicio_id')->nullable();
            $table->unsignedBigInteger('bodega_origen_id')->nullable();
            $table->unsignedBigInteger('bodega_destino_id')->nullable();
            $table->string('numero_documento', 100)->nullable();
            $table->text('observaciones')->nullable();
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('impuestos', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->enum('estado', ['BORRADOR','CONFIRMADA','ANULADA'])->default('BORRADOR');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->unsignedBigInteger('autorizado_por')->nullable();
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('autorizado_por')->references('id')->on('users')->nullOnDelete();
            $table->index('tipo_salida');
            $table->index('estado');
            $table->index('fecha_salida');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salidas');
    }
};
