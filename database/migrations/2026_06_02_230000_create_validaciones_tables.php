<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('validaciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->foreignId('paciente_id')->nullable()->constrained('pacientes')->nullOnDelete();
            $table->foreignId('prescripcion_id')->nullable()->constrained('pacientes_prescripciones')->nullOnDelete();
            $table->foreignId('preparacion_id')->nullable()->constrained('preparaciones')->nullOnDelete();
            $table->foreignId('mezcla_id')->nullable()->constrained('mezclas')->nullOnDelete();
            $table->dateTime('fecha_validacion');
            $table->enum('tipo_validacion', ['PRESCRIPCION','PREPARACION','MEZCLA','DISPENSACION'])->default('PRESCRIPCION');
            $table->enum('resultado', ['PENDIENTE','APROBADA','OBSERVADA','RECHAZADA'])->default('PENDIENTE');
            $table->enum('prioridad', ['BAJA','NORMAL','ALTA','CRITICA'])->default('NORMAL');
            $table->text('observaciones')->nullable();
            $table->foreignId('farmaceutico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['resultado','tipo_validacion']);
            $table->index('paciente_id');
        });

        Schema::create('validaciones_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('validacion_id')->constrained('validaciones')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->nullable()->constrained('medicamentos')->nullOnDelete();
            $table->decimal('dosis_prescrita', 12, 4)->nullable();
            $table->decimal('dosis_recomendada', 12, 4)->nullable();
            $table->foreignId('unidad_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->foreignId('via_administracion_id')->nullable()->constrained('vias_administracion')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['VALIDO','OBSERVADO','RECHAZADO'])->default('VALIDO');
            $table->timestamps();
        });

        Schema::create('validaciones_alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('validacion_id')->constrained('validaciones')->cascadeOnDelete();
            $table->enum('tipo_alerta', ['ALERGIA','INTERACCION','DOSIS','VENCIMIENTO','STOCK','COMPATIBILIDAD','DUPLICIDAD']);
            $table->enum('severidad', ['BAJA','MEDIA','ALTA','CRITICA'])->default('MEDIA');
            $table->text('descripcion');
            $table->boolean('resuelta')->default(false);
            $table->timestamps();
            $table->index(['validacion_id','severidad']);
        });

        Schema::create('validaciones_aprobaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('validacion_id')->constrained('validaciones')->cascadeOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_aprobacion');
            $table->enum('accion', ['APROBADA','RECHAZADA','DEVUELTA','OBSERVADA']);
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('catalogo_interacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicamento_1_id')->constrained('medicamentos')->cascadeOnDelete();
            $table->foreignId('medicamento_2_id')->constrained('medicamentos')->cascadeOnDelete();
            $table->enum('severidad', ['LEVE','MODERADA','GRAVE'])->default('MODERADA');
            $table->text('descripcion')->nullable();
            $table->timestamps();
            $table->unique(['medicamento_1_id','medicamento_2_id'], 'cat_inter_par_unq');
        });

        Schema::create('catalogo_compatibilidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicamento_1_id')->constrained('medicamentos')->cascadeOnDelete();
            $table->foreignId('medicamento_2_id')->constrained('medicamentos')->cascadeOnDelete();
            $table->boolean('compatible')->default(true);
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->unique(['medicamento_1_id','medicamento_2_id'], 'cat_comp_par_unq');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_compatibilidades');
        Schema::dropIfExists('catalogo_interacciones');
        Schema::dropIfExists('validaciones_aprobaciones');
        Schema::dropIfExists('validaciones_alertas');
        Schema::dropIfExists('validaciones_detalle');
        Schema::dropIfExists('validaciones');
    }
};
