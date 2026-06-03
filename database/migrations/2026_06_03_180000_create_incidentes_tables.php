<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Tabla principal: cabecera del incidente
        Schema::create('incidentes', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 60)->unique();
            $t->dateTime('fecha_incidente');
            $t->enum('tipo_incidente', [
                'INVENTARIO',
                'CADENA_FRIO',
                'PRODUCCION',
                'PREPARACION',
                'REEMPAQUE',
                'DISPENSACION',
                'CALIDAD',
                'PACIENTE',
                'EQUIPO',
                'AUDITORIA',
            ]);
            $t->enum('clasificacion', [
                'DESVIACION',
                'NO_CONFORMIDAD',
                'EVENTO_ADVERSO',
                'RIESGO',
                'HALLAZGO',
            ])->default('DESVIACION');
            $t->enum('severidad', ['BAJA','MEDIA','ALTA','CRITICA'])->default('MEDIA');
            $t->text('descripcion');
            $t->foreignId('usuario_reporta_id')->nullable()->constrained('users')->nullOnDelete();
            $t->dateTime('fecha_reporte')->nullable();
            $t->enum('estado', ['ABIERTO','INVESTIGACION','ACCION_CORRECTIVA','CERRADO'])->default('ABIERTO');
            $t->timestamps();

            $t->index(['tipo_incidente','estado']);
            $t->index(['severidad','estado']);
            $t->index('fecha_incidente');
        });

        // Detalle: investigación / análisis causa raíz
        Schema::create('incidentes_detalle', function (Blueprint $t) {
            $t->id();
            $t->foreignId('incidente_id')->constrained('incidentes')->cascadeOnDelete();
            $t->text('causa_raiz')->nullable();
            $t->text('impacto')->nullable();
            $t->text('conclusion')->nullable();
            $t->foreignId('responsable_investigacion_id')->nullable()->constrained('users')->nullOnDelete();
            $t->dateTime('fecha_cierre')->nullable();
            $t->timestamps();
        });

        // Afectaciones: trazabilidad 360°
        Schema::create('incidentes_afectaciones', function (Blueprint $t) {
            $t->id();
            $t->foreignId('incidente_id')->constrained('incidentes')->cascadeOnDelete();
            $t->foreignId('inventario_lote_id')->nullable()->constrained('inventario_lotes')->nullOnDelete();
            $t->unsignedBigInteger('mezcla_id')->nullable();
            $t->unsignedBigInteger('preparacion_id')->nullable();
            $t->unsignedBigInteger('reempaque_id')->nullable();
            $t->unsignedBigInteger('entrega_id')->nullable();
            $t->unsignedBigInteger('paciente_id')->nullable();
            $t->unsignedBigInteger('equipo_cadena_frio_id')->nullable();
            $t->unsignedBigInteger('control_calidad_id')->nullable();
            $t->text('observaciones')->nullable();
            $t->timestamps();

            $t->index('inventario_lote_id');
            $t->index('mezcla_id');
            $t->index('preparacion_id');
            $t->index('reempaque_id');
            $t->index('entrega_id');
            $t->index('paciente_id');
            $t->index('equipo_cadena_frio_id');
            $t->index('control_calidad_id');
        });

        // Acciones correctivas y preventivas (CAPA)
        Schema::create('incidentes_acciones', function (Blueprint $t) {
            $t->id();
            $t->foreignId('incidente_id')->constrained('incidentes')->cascadeOnDelete();
            $t->enum('tipo_accion', ['CORRECTIVA','PREVENTIVA']);
            $t->text('descripcion');
            $t->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $t->date('fecha_compromiso')->nullable();
            $t->date('fecha_ejecucion')->nullable();
            $t->enum('estado', ['PENDIENTE','EN_PROCESO','CERRADA'])->default('PENDIENTE');
            $t->timestamps();
        });

        // Evidencias
        Schema::create('incidentes_evidencias', function (Blueprint $t) {
            $t->id();
            $t->foreignId('incidente_id')->constrained('incidentes')->cascadeOnDelete();
            $t->string('nombre_archivo', 255);
            $t->string('ruta_archivo', 500);
            $t->string('tipo_archivo', 50)->nullable();
            $t->text('observaciones')->nullable();
            $t->timestamps();
        });

        // Seguimiento / bitácora
        Schema::create('incidentes_seguimiento', function (Blueprint $t) {
            $t->id();
            $t->foreignId('incidente_id')->constrained('incidentes')->cascadeOnDelete();
            $t->dateTime('fecha');
            $t->text('comentario');
            $t->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        // Bloqueo automático en inventario_lotes
        Schema::table('inventario_lotes', function (Blueprint $t) {
            if (!Schema::hasColumn('inventario_lotes', 'bloqueado_incidente')) {
                $t->boolean('bloqueado_incidente')->default(false)->after('estado_calidad');
            }
            if (!Schema::hasColumn('inventario_lotes', 'incidente_id')) {
                $t->unsignedBigInteger('incidente_id')->nullable()->after('bloqueado_incidente');
                $t->index('incidente_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventario_lotes', function (Blueprint $t) {
            if (Schema::hasColumn('inventario_lotes', 'incidente_id')) {
                $t->dropIndex(['incidente_id']);
                $t->dropColumn('incidente_id');
            }
            if (Schema::hasColumn('inventario_lotes', 'bloqueado_incidente')) {
                $t->dropColumn('bloqueado_incidente');
            }
        });
        Schema::dropIfExists('incidentes_seguimiento');
        Schema::dropIfExists('incidentes_evidencias');
        Schema::dropIfExists('incidentes_acciones');
        Schema::dropIfExists('incidentes_afectaciones');
        Schema::dropIfExists('incidentes_detalle');
        Schema::dropIfExists('incidentes');
    }
};
