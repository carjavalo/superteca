<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('traslados', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_envio')->nullable();
            $table->dateTime('fecha_recepcion')->nullable();
            $table->foreignId('bodega_origen_id')->constrained('bodegas');
            $table->foreignId('bodega_destino_id')->constrained('bodegas');
            $table->enum('tipo_traslado', ['INTERNO', 'ENTRE_SEDES', 'DEVOLUCION', 'REUBICACION'])->default('INTERNO');
            $table->enum('estado', ['BORRADOR', 'PENDIENTE', 'EN_TRANSITO', 'RECIBIDO', 'RECHAZADO', 'ANULADO'])->default('BORRADOR');
            $table->unsignedBigInteger('usuario_solicita_id')->nullable();
            $table->unsignedBigInteger('usuario_envia_id')->nullable();
            $table->unsignedBigInteger('usuario_recibe_id')->nullable();
            $table->text('observaciones')->nullable();
            $table->decimal('valor_total', 14, 2)->default(0);
            $table->timestamps();

            $table->foreign('usuario_solicita_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('usuario_envia_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('usuario_recibe_id')->references('id')->on('users')->nullOnDelete();
            $table->index('estado');
            $table->index('fecha_solicitud');
        });

        Schema::create('detalle_traslados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traslado_id')->constrained('traslados')->cascadeOnDelete();
            $table->foreignId('inventario_lote_id')->constrained('inventario_lotes');
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->unsignedBigInteger('presentacion_id')->nullable();
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad', 12, 2);
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->text('observacion')->nullable();
            $table->timestamps();

            $table->index('traslado_id');
        });

        Schema::create('recepcion_traslados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('traslado_id')->constrained('traslados')->cascadeOnDelete();
            $table->dateTime('fecha_recepcion');
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('recibido_completo')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('usuario_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepcion_traslados');
        Schema::dropIfExists('detalle_traslados');
        Schema::dropIfExists('traslados');
    }
};
