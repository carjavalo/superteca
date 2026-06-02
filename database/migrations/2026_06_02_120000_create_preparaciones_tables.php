<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo: tipos de preparación (NPT, IV, citostáticos, etc.)
        Schema::create('tipo_preparaciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120)->unique();
            $table->string('codigo', 30)->nullable();
            $table->text('descripcion')->nullable();
            $table->string('color', 20)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        // Catálogo: servicios hospitalarios (UCI, Urgencias, Hospitalización...)
        Schema::create('servicios_hospitalarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120)->unique();
            $table->string('codigo', 30)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        // Pacientes
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('documento', 30)->unique();
            $table->string('tipo_documento', 10)->default('CC');
            $table->string('nombres', 120);
            $table->string('apellidos', 120);
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('sexo', ['M','F','O'])->nullable();
            $table->decimal('peso', 6, 2)->nullable();
            $table->decimal('talla', 5, 2)->nullable();
            $table->string('cama', 30)->nullable();
            $table->unsignedBigInteger('servicio_id')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('servicio_id')->references('id')->on('servicios_hospitalarios')->nullOnDelete();
            $table->index(['nombres','apellidos']);
        });

        // Tabla principal: orden de producción / preparación
        Schema::create('preparaciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->unsignedBigInteger('paciente_id')->nullable();
            $table->unsignedBigInteger('tipo_preparacion_id');
            $table->unsignedBigInteger('servicio_id')->nullable();
            $table->dateTime('fecha_programada');
            $table->dateTime('fecha_inicio')->nullable();
            $table->dateTime('fecha_fin')->nullable();
            $table->decimal('volumen_final', 12, 2)->nullable();
            $table->unsignedBigInteger('unidad_volumen_id')->nullable();
            $table->text('observaciones')->nullable();
            $table->enum('estado', [
                'PROGRAMADA',
                'EN_PROCESO',
                'CONTROL_CALIDAD',
                'LIBERADA',
                'ENTREGADA',
                'ANULADA',
            ])->default('PROGRAMADA');
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->unsignedBigInteger('usuario_preparador_id')->nullable();
            $table->unsignedBigInteger('usuario_validador_id')->nullable();
            $table->timestamps();

            $table->foreign('paciente_id')->references('id')->on('pacientes')->nullOnDelete();
            $table->foreign('tipo_preparacion_id')->references('id')->on('tipo_preparaciones');
            $table->foreign('servicio_id')->references('id')->on('servicios_hospitalarios')->nullOnDelete();
            $table->foreign('unidad_volumen_id')->references('id')->on('unidades_medida')->nullOnDelete();
            $table->foreign('usuario_preparador_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('usuario_validador_id')->references('id')->on('users')->nullOnDelete();
            $table->index('estado');
            $table->index('fecha_programada');
        });

        // Detalle de fórmula (qué debe contener)
        Schema::create('preparaciones_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preparacion_id')->constrained('preparaciones')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->unsignedBigInteger('presentacion_id')->nullable();
            $table->decimal('dosis', 12, 4);
            $table->unsignedBigInteger('unidad_medida_id')->nullable();
            $table->decimal('concentracion', 12, 4)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('presentacion_id')->references('id')->on('presentaciones')->nullOnDelete();
            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();
            $table->index('preparacion_id');
        });

        // Consumo real de inventario (trazabilidad por lote)
        Schema::create('preparaciones_consumo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preparacion_id')->constrained('preparaciones')->cascadeOnDelete();
            $table->foreignId('inventario_lote_id')->constrained('inventario_lotes');
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad_consumida', 12, 4);
            $table->unsignedBigInteger('unidad_medida_id')->nullable();
            $table->decimal('costo_unitario', 12, 4)->default(0);
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->timestamps();

            $table->foreign('unidad_medida_id')->references('id')->on('unidades_medida')->nullOnDelete();
            $table->index('preparacion_id');
            $table->index('inventario_lote_id');
        });

        // Control de calidad
        Schema::create('preparaciones_control_calidad', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preparacion_id')->constrained('preparaciones')->cascadeOnDelete();
            $table->dateTime('fecha_control');
            $table->string('aspecto_visual', 255)->nullable();
            $table->decimal('volumen_verificado', 12, 2)->nullable();
            $table->decimal('ph', 5, 2)->nullable();
            $table->decimal('osmolaridad', 12, 2)->nullable();
            $table->boolean('cumple')->default(false);
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('usuario_control_id')->nullable();
            $table->timestamps();

            $table->foreign('usuario_control_id')->references('id')->on('users')->nullOnDelete();
            $table->index('preparacion_id');
        });

        // Entrega final
        Schema::create('preparaciones_entrega', function (Blueprint $table) {
            $table->id();
            $table->foreignId('preparacion_id')->constrained('preparaciones')->cascadeOnDelete();
            $table->unsignedBigInteger('paciente_id')->nullable();
            $table->dateTime('fecha_entrega');
            $table->unsignedBigInteger('usuario_entrega_id')->nullable();
            $table->unsignedBigInteger('servicio_destino_id')->nullable();
            $table->string('recibido_por', 150)->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->foreign('paciente_id')->references('id')->on('pacientes')->nullOnDelete();
            $table->foreign('usuario_entrega_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('servicio_destino_id')->references('id')->on('servicios_hospitalarios')->nullOnDelete();
            $table->index('preparacion_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preparaciones_entrega');
        Schema::dropIfExists('preparaciones_control_calidad');
        Schema::dropIfExists('preparaciones_consumo');
        Schema::dropIfExists('preparaciones_detalle');
        Schema::dropIfExists('preparaciones');
        Schema::dropIfExists('pacientes');
        Schema::dropIfExists('servicios_hospitalarios');
        Schema::dropIfExists('tipo_preparaciones');
    }
};
