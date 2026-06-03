<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos_cadena_frio', function (Blueprint $t) {
            $t->id();
            $t->string('codigo', 50)->unique();
            $t->string('nombre', 150);
            $t->enum('tipo', ['NEVERA','CONGELADOR','CUARTO_FRIO','TRANSPORTE'])->default('NEVERA');
            $t->string('ubicacion', 255)->nullable();
            $t->decimal('temperatura_min', 5, 2);
            $t->decimal('temperatura_max', 5, 2);
            $t->decimal('humedad_min', 5, 2)->nullable();
            $t->decimal('humedad_max', 5, 2)->nullable();
            $t->string('fabricante', 150)->nullable();
            $t->string('modelo', 150)->nullable();
            $t->string('serial', 150)->nullable();
            $t->date('fecha_calibracion')->nullable();
            $t->date('proxima_calibracion')->nullable();
            $t->boolean('estado')->default(true);
            $t->timestamps();
        });

        Schema::create('sensores_temperatura', function (Blueprint $t) {
            $t->id();
            $t->foreignId('equipo_id')->constrained('equipos_cadena_frio')->cascadeOnDelete();
            $t->string('codigo_sensor', 100);
            $t->string('marca', 100)->nullable();
            $t->string('modelo', 100)->nullable();
            $t->boolean('estado')->default(true);
            $t->timestamps();
        });

        Schema::create('monitoreo_temperatura', function (Blueprint $t) {
            $t->id();
            $t->foreignId('equipo_id')->constrained('equipos_cadena_frio')->cascadeOnDelete();
            $t->foreignId('sensor_id')->nullable()->constrained('sensores_temperatura')->nullOnDelete();
            $t->dateTime('fecha_hora');
            $t->decimal('temperatura', 5, 2);
            $t->decimal('humedad', 5, 2)->nullable();
            $t->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $t->enum('origen', ['MANUAL','AUTOMATICO'])->default('MANUAL');
            $t->boolean('fuera_rango')->default(false);
            $t->text('observaciones')->nullable();
            $t->timestamps();
            $t->index(['equipo_id', 'fecha_hora']);
        });

        Schema::create('alertas_cadena_frio', function (Blueprint $t) {
            $t->id();
            $t->foreignId('equipo_id')->constrained('equipos_cadena_frio')->cascadeOnDelete();
            $t->dateTime('fecha_inicio');
            $t->dateTime('fecha_fin')->nullable();
            $t->decimal('temperatura_registrada', 5, 2);
            $t->decimal('temperatura_permitida_min', 5, 2);
            $t->decimal('temperatura_permitida_max', 5, 2);
            $t->enum('severidad', ['BAJA','MEDIA','ALTA','CRITICA'])->default('MEDIA');
            $t->enum('estado', ['ABIERTA','INVESTIGACION','CERRADA'])->default('ABIERTA');
            $t->text('observaciones')->nullable();
            $t->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::create('lotes_cadena_frio', function (Blueprint $t) {
            $t->id();
            $t->foreignId('inventario_lote_id')->constrained('inventario_lotes')->cascadeOnDelete();
            $t->foreignId('equipo_id')->constrained('equipos_cadena_frio')->cascadeOnDelete();
            $t->dateTime('fecha_ingreso');
            $t->dateTime('fecha_salida')->nullable();
            $t->text('observaciones')->nullable();
            $t->timestamps();
            $t->index(['inventario_lote_id','equipo_id']);
        });

        Schema::create('afectacion_lotes', function (Blueprint $t) {
            $t->id();
            $t->foreignId('alerta_id')->constrained('alertas_cadena_frio')->cascadeOnDelete();
            $t->foreignId('inventario_lote_id')->constrained('inventario_lotes')->cascadeOnDelete();
            $t->enum('estado', ['PENDIENTE_EVALUACION','LIBERADO','BLOQUEADO','DESECHADO'])->default('PENDIENTE_EVALUACION');
            $t->text('observaciones')->nullable();
            $t->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $t->timestamps();
        });

        Schema::table('inventario_lotes', function (Blueprint $t) {
            if (!Schema::hasColumn('inventario_lotes', 'estado_calidad')) {
                $t->enum('estado_calidad', ['LIBERADO','CUARENTENA','BLOQUEADO','DESECHADO'])
                  ->default('LIBERADO')->after('estado');
            }
            if (!Schema::hasColumn('inventario_lotes', 'fecha_ultima_verificacion')) {
                $t->dateTime('fecha_ultima_verificacion')->nullable()->after('estado_calidad');
            }
            if (!Schema::hasColumn('inventario_lotes', 'equipo_cadena_frio_id')) {
                $t->foreignId('equipo_cadena_frio_id')->nullable()
                  ->after('fecha_ultima_verificacion')
                  ->constrained('equipos_cadena_frio')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventario_lotes', function (Blueprint $t) {
            if (Schema::hasColumn('inventario_lotes', 'equipo_cadena_frio_id')) {
                $t->dropConstrainedForeignId('equipo_cadena_frio_id');
            }
            if (Schema::hasColumn('inventario_lotes', 'fecha_ultima_verificacion')) {
                $t->dropColumn('fecha_ultima_verificacion');
            }
            if (Schema::hasColumn('inventario_lotes', 'estado_calidad')) {
                $t->dropColumn('estado_calidad');
            }
        });
        Schema::dropIfExists('afectacion_lotes');
        Schema::dropIfExists('lotes_cadena_frio');
        Schema::dropIfExists('alertas_cadena_frio');
        Schema::dropIfExists('monitoreo_temperatura');
        Schema::dropIfExists('sensores_temperatura');
        Schema::dropIfExists('equipos_cadena_frio');
    }
};
