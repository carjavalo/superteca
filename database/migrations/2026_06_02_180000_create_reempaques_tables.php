<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reempaques', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->dateTime('fecha_programada');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->foreignId('medicamento_origen_id')->constrained('medicamentos');
            $table->foreignId('presentacion_origen_id')->nullable()->constrained('presentaciones')->nullOnDelete();
            $table->foreignId('presentacion_destino_id')->nullable()->constrained('presentaciones')->nullOnDelete();
            $table->foreignId('unidad_medida_destino_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->decimal('factor_conversion', 12, 4)->default(1)->comment('Unidades generadas por cada unidad consumida');
            $table->decimal('cantidad_esperada', 12, 2)->default(0);
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['PROGRAMADO','EN_PROCESO','CONTROL_CALIDAD','LIBERADO','ANULADO'])->default('PROGRAMADO');
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->foreignId('usuario_responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('usuario_aprobador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('reempaques_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reempaque_id')->constrained('reempaques')->cascadeOnDelete();
            $table->string('insumo')->comment('Bolsa, etiqueta, frasco, jeringa, etc.');
            $table->decimal('cantidad', 12, 2)->default(0);
            $table->foreignId('unidad_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->decimal('costo_unitario', 12, 2)->default(0);
            $table->decimal('costo_total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('reempaques_consumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reempaque_id')->constrained('reempaques')->cascadeOnDelete();
            $table->foreignId('inventario_lote_id')->constrained('inventario_lotes');
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->string('lote_origen', 100);
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad_consumida', 12, 2);
            $table->foreignId('unidad_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->decimal('costo_unitario', 12, 4)->default(0);
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('reempaques_producto_final', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reempaque_id')->constrained('reempaques')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->foreignId('presentacion_id')->nullable()->constrained('presentaciones')->nullOnDelete();
            $table->string('lote_reempaque', 100);
            $table->string('lote_origen', 100)->nullable();
            $table->dateTime('fecha_reempaque');
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad_generada', 12, 2);
            $table->foreignId('unidad_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->decimal('costo_unitario', 12, 4)->default(0);
            $table->string('codigo_barras', 100)->nullable();
            $table->foreignId('inventario_lote_generado_id')->nullable()->constrained('inventario_lotes')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('reempaques_control_calidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reempaque_id')->constrained('reempaques')->cascadeOnDelete();
            $table->dateTime('fecha_control');
            $table->decimal('cantidad_verificada', 12, 2)->nullable();
            $table->boolean('etiquetado_correcto')->default(true);
            $table->boolean('lote_visible')->default(true);
            $table->boolean('fecha_vencimiento_visible')->default(true);
            $table->boolean('cumple')->default(true);
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_control_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reempaques_control_calidad');
        Schema::dropIfExists('reempaques_producto_final');
        Schema::dropIfExists('reempaques_consumo');
        Schema::dropIfExists('reempaques_detalle');
        Schema::dropIfExists('reempaques');
    }
};
