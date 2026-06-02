<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dispensacion_entregas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->enum('tipo_entrega', [
                'PACIENTE','SERVICIO','ENFERMERIA','QUIROFANO','UCI','URGENCIAS','HOSPITALIZACION','FARMACIA_SATELITE'
            ])->default('PACIENTE');
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes')->nullOnDelete();
            $table->foreignId('servicio_id')->nullable()->constrained('servicios_hospitalarios')->nullOnDelete();
            $table->dateTime('fecha_entrega');
            $table->foreignId('usuario_dispensador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_recibe_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recibe_nombre')->nullable();
            $table->string('recibe_documento', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['PENDIENTE','ENTREGADA','PARCIAL','DEVUELTA','ANULADA'])->default('PENDIENTE');
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('dispensacion_entregas_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->constrained('dispensacion_entregas')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->nullable()->constrained('medicamentos')->nullOnDelete();
            $table->foreignId('presentacion_id')->nullable()->constrained('presentaciones')->nullOnDelete();
            $table->foreignId('preparacion_id')->nullable()->constrained('preparaciones')->nullOnDelete();
            $table->foreignId('mezcla_id')->nullable()->constrained('mezclas')->nullOnDelete();
            $table->decimal('cantidad', 12, 2)->default(0);
            $table->foreignId('unidad_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->decimal('costo_unitario', 12, 4)->default(0);
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('dispensacion_entregas_lotes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_detalle_id')->constrained('dispensacion_entregas_detalle')->cascadeOnDelete();
            $table->foreignId('inventario_lote_id')->nullable()->constrained('inventario_lotes')->nullOnDelete();
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad_entregada', 12, 2)->default(0);
            $table->decimal('costo_unitario', 12, 4)->default(0);
            $table->timestamps();
        });

        Schema::create('dispensacion_entregas_recepcion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrega_id')->constrained('dispensacion_entregas')->cascadeOnDelete();
            $table->dateTime('fecha_recepcion');
            $table->foreignId('usuario_recibe_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('recibe_nombre')->nullable();
            $table->string('recibe_documento', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('recibido')->default(true);
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('dispensacion_devoluciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->foreignId('entrega_id')->constrained('dispensacion_entregas')->cascadeOnDelete();
            $table->dateTime('fecha_devolucion');
            $table->text('motivo')->nullable();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('estado', ['PENDIENTE','APROBADA','RECHAZADA'])->default('PENDIENTE');
            $table->decimal('cantidad_devuelta', 12, 2)->default(0);
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('dispensacion_devoluciones_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('devolucion_id')->constrained('dispensacion_devoluciones')->cascadeOnDelete();
            $table->foreignId('entrega_lote_id')->nullable()->constrained('dispensacion_entregas_lotes')->nullOnDelete();
            $table->foreignId('inventario_lote_id')->nullable()->constrained('inventario_lotes')->nullOnDelete();
            $table->decimal('cantidad', 12, 2)->default(0);
            $table->decimal('costo_unitario', 12, 4)->default(0);
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->boolean('reingresa_stock')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensacion_devoluciones_detalle');
        Schema::dropIfExists('dispensacion_devoluciones');
        Schema::dropIfExists('dispensacion_entregas_recepcion');
        Schema::dropIfExists('dispensacion_entregas_lotes');
        Schema::dropIfExists('dispensacion_entregas_detalle');
        Schema::dropIfExists('dispensacion_entregas');
    }
};
