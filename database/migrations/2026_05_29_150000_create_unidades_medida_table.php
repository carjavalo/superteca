<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unidades_medida', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 100);
            $table->string('abreviatura', 20);
            $table->enum('tipo', [
                'PESO',
                'VOLUMEN',
                'CONCENTRACION',
                'TEMPERATURA',
                'TIEMPO',
                'VELOCIDAD',
                'CANTIDAD',
                'SUPERFICIE',
                'OTRO',
            ])->nullable();
            $table->unsignedBigInteger('unidad_base_id')->nullable();
            $table->decimal('factor_conversion', 18, 8)->default(1);
            $table->string('simbolo', 20)->nullable();
            $table->unsignedInteger('precision_decimal')->default(2);
            $table->boolean('permite_fracciones')->default(true);
            $table->boolean('activa_calculos')->default(true);
            $table->string('color_identificacion', 20)->nullable();
            $table->string('icono', 255)->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('unidad_base_id')->references('id')->on('unidades_medida')->nullOnDelete();
            $table->index('tipo');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unidades_medida');
    }
};
