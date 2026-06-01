<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laboratorios', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 255);
            $table->string('nit', 50)->nullable();
            $table->string('registro_invima', 100)->nullable();
            $table->string('pais_origen', 100)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('telefono', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('sitio_web', 255)->nullable();
            $table->string('contacto_comercial', 255)->nullable();
            $table->string('contacto_farmacovigilancia', 255)->nullable();
            $table->boolean('requiere_cadena_frio')->default(0);
            $table->boolean('estado')->default(1);
            $table->text('observaciones')->nullable();
            $table->string('logo', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laboratorios');
    }
};
