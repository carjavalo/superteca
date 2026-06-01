<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vias_administracion', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 20)->unique()->nullable();
            $table->string('nombre', 100);
            $table->string('nombre_corto', 50)->nullable();
            $table->text('descripcion')->nullable();

            $table->enum('tipo', [
                'PARENTERAL',
                'ENTERAL',
                'TOPICA',
                'RESPIRATORIA',
                'OFTALMICA',
                'OTICA',
                'NASAL',
                'RECTAL',
                'VAGINAL',
            ])->nullable();

            $table->boolean('esteril_requerido')->default(0);
            $table->boolean('requiere_bomba_infusion')->default(0);
            $table->boolean('requiere_filtro')->default(0);
            $table->boolean('permite_bolo')->default(0);
            $table->boolean('permite_infusion_continua')->default(0);

            $table->decimal('velocidad_min_ml_h', 10, 2)->nullable();
            $table->decimal('velocidad_max_ml_h', 10, 2)->nullable();
            $table->decimal('osmolaridad_max', 10, 2)->nullable();

            $table->boolean('fotosensible')->default(0);
            $table->boolean('requiere_monitorizacion')->default(0);

            $table->enum('riesgo_clinico', ['BAJO', 'MEDIO', 'ALTO', 'CRITICO'])->nullable();

            $table->string('color_identificacion', 20)->nullable();
            $table->string('icono', 255)->nullable();

            $table->text('observaciones')->nullable();

            $table->boolean('estado')->default(1);

            $table->timestamps();

            $table->index('tipo');
            $table->index('riesgo_clinico');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vias_administracion');
    }
};
