<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabla principal: cabecera del control
        Schema::create('controles_calidad', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 60)->unique();
            $t->enum('tipo_control', [
                'RECEPCION',
                'ALMACENAMIENTO',
                'CADENA_FRIO',
                'PRODUCCION',
                'PREPARACION',
                'MEZCLA',
                'REEMPAQUE',
                'PRODUCTO_TERMINADO',
                'DISPENSACION',
            ]);
            $t->dateTime('fecha_control');
            $t->foreignId('usuario_control_id')->nullable()->constrained('users')->nullOnDelete();
            $t->enum('resultado', ['PENDIENTE','APROBADO','CONDICIONAL','RECHAZADO'])->default('PENDIENTE');
            $t->text('observaciones')->nullable();
            $t->timestamps();

            $t->index(['tipo_control', 'resultado']);
            $t->index('fecha_control');
        });

        // Detalle: vincula con cualquier entidad del sistema
        Schema::create('controles_calidad_detalle', function (Blueprint $t) {
            $t->id();
            $t->foreignId('control_id')->constrained('controles_calidad')->cascadeOnDelete();
            $t->foreignId('inventario_lote_id')->nullable()->constrained('inventario_lotes')->nullOnDelete();
            $t->unsignedBigInteger('mezcla_id')->nullable();
            $t->unsignedBigInteger('preparacion_id')->nullable();
            $t->unsignedBigInteger('reempaque_id')->nullable();
            $t->unsignedBigInteger('entrega_id')->nullable();
            $t->unsignedBigInteger('entrada_id')->nullable();
            $t->timestamps();

            $t->index('inventario_lote_id');
            $t->index('mezcla_id');
            $t->index('preparacion_id');
            $t->index('reempaque_id');
            $t->index('entrega_id');
            $t->index('entrada_id');
        });

        // Catálogo de parámetros parametrizables
        Schema::create('controles_calidad_parametros', function (Blueprint $t) {
            $t->id();
            $t->string('nombre', 150);
            $t->string('tipo_control', 100);
            $t->string('unidad_medida', 50)->nullable();
            $t->decimal('valor_minimo', 12, 4)->nullable();
            $t->decimal('valor_maximo', 12, 4)->nullable();
            $t->boolean('obligatorio')->default(true);
            $t->boolean('estado')->default(true);
            $t->timestamps();

            $t->index('tipo_control');
        });

        // Resultados / mediciones por parámetro
        Schema::create('controles_calidad_resultados', function (Blueprint $t) {
            $t->id();
            $t->foreignId('control_id')->constrained('controles_calidad')->cascadeOnDelete();
            $t->foreignId('parametro_id')->constrained('controles_calidad_parametros')->cascadeOnDelete();
            $t->string('valor_obtenido', 255)->nullable();
            $t->boolean('cumple')->default(true);
            $t->text('observaciones')->nullable();
            $t->timestamps();
        });

        // Acciones correctivas
        Schema::create('controles_calidad_acciones', function (Blueprint $t) {
            $t->id();
            $t->foreignId('control_id')->constrained('controles_calidad')->cascadeOnDelete();
            $t->text('descripcion');
            $t->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $t->date('fecha_compromiso')->nullable();
            $t->date('fecha_cierre')->nullable();
            $t->enum('estado', ['ABIERTA','EN_PROCESO','CERRADA'])->default('ABIERTA');
            $t->timestamps();
        });

        // Evidencias adjuntas
        Schema::create('controles_calidad_evidencias', function (Blueprint $t) {
            $t->id();
            $t->foreignId('control_id')->constrained('controles_calidad')->cascadeOnDelete();
            $t->string('nombre_archivo', 255);
            $t->string('ruta_archivo', 500);
            $t->string('tipo_archivo', 50)->nullable();
            $t->text('observaciones')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controles_calidad_evidencias');
        Schema::dropIfExists('controles_calidad_acciones');
        Schema::dropIfExists('controles_calidad_resultados');
        Schema::dropIfExists('controles_calidad_parametros');
        Schema::dropIfExists('controles_calidad_detalle');
        Schema::dropIfExists('controles_calidad');
    }
};
