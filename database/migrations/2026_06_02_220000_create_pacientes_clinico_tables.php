<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Extender pacientes con datos clínicos
        Schema::table('pacientes', function (Blueprint $table) {
            if (!Schema::hasColumn('pacientes', 'telefono'))         $table->string('telefono', 50)->nullable()->after('talla');
            if (!Schema::hasColumn('pacientes', 'correo'))           $table->string('correo', 150)->nullable()->after('telefono');
            if (!Schema::hasColumn('pacientes', 'direccion'))        $table->string('direccion', 255)->nullable()->after('correo');
            if (!Schema::hasColumn('pacientes', 'eps'))              $table->string('eps', 120)->nullable()->after('direccion');
            if (!Schema::hasColumn('pacientes', 'fecha_ingreso'))    $table->dateTime('fecha_ingreso')->nullable()->after('cama');
            if (!Schema::hasColumn('pacientes', 'fecha_egreso'))     $table->dateTime('fecha_egreso')->nullable()->after('fecha_ingreso');
            if (!Schema::hasColumn('pacientes', 'estado_clinico'))   $table->enum('estado_clinico', ['ACTIVO','EGRESADO','FALLECIDO'])->default('ACTIVO')->after('observaciones');
        });

        Schema::create('pacientes_alergias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->nullable()->constrained('medicamentos')->nullOnDelete();
            $table->string('descripcion', 255);
            $table->enum('severidad', ['LEVE','MODERADA','SEVERA'])->default('LEVE');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('pacientes_diagnosticos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->string('codigo_cie10', 20)->nullable();
            $table->string('descripcion', 255);
            $table->boolean('principal')->default(false);
            $table->date('fecha_diagnostico')->nullable();
            $table->timestamps();
        });

        Schema::create('pacientes_prescripciones', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('medico_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_prescripcion');
            $table->enum('estado', ['ACTIVA','SUSPENDIDA','FINALIZADA'])->default('ACTIVA');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('pacientes_prescripciones_detalle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescripcion_id')->constrained('pacientes_prescripciones')->cascadeOnDelete();
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->decimal('dosis', 12, 4);
            $table->foreignId('unidad_medida_id')->nullable()->constrained('unidades_medida')->nullOnDelete();
            $table->string('frecuencia', 100)->nullable();
            $table->integer('duracion_dias')->nullable();
            $table->foreignId('via_administracion_id')->nullable()->constrained('vias_administracion')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::create('pacientes_tratamientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('preparacion_id')->nullable()->constrained('preparaciones')->nullOnDelete();
            $table->foreignId('mezcla_id')->nullable()->constrained('mezclas')->nullOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->enum('estado', ['ACTIVO','SUSPENDIDO','FINALIZADO'])->default('ACTIVO');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        // Trazabilidad: qué lote recibió cada paciente
        Schema::create('pacientes_dispensaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained('pacientes')->cascadeOnDelete();
            $table->foreignId('entrega_id')->nullable()->constrained('dispensacion_entregas')->nullOnDelete();
            $table->foreignId('entrega_detalle_id')->nullable()->constrained('dispensacion_entregas_detalle')->nullOnDelete();
            $table->foreignId('medicamento_id')->nullable()->constrained('medicamentos')->nullOnDelete();
            $table->foreignId('inventario_lote_id')->nullable()->constrained('inventario_lotes')->nullOnDelete();
            $table->string('lote', 100)->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('cantidad', 12, 2)->default(0);
            $table->dateTime('fecha_entrega');
            $table->decimal('costo_total', 14, 2)->default(0);
            $table->timestamps();

            $table->index(['paciente_id','fecha_entrega']);
            $table->index('lote');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes_dispensaciones');
        Schema::dropIfExists('pacientes_tratamientos');
        Schema::dropIfExists('pacientes_prescripciones_detalle');
        Schema::dropIfExists('pacientes_prescripciones');
        Schema::dropIfExists('pacientes_diagnosticos');
        Schema::dropIfExists('pacientes_alergias');
        Schema::table('pacientes', function (Blueprint $table) {
            foreach (['telefono','correo','direccion','eps','fecha_ingreso','fecha_egreso','estado_clinico'] as $c) {
                if (Schema::hasColumn('pacientes', $c)) $table->dropColumn($c);
            }
        });
    }
};
