<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50)->nullable();

            $table->enum('tipo_proveedor', [
                'DISTRIBUIDOR',
                'LABORATORIO',
                'OPERADOR_LOGISTICO',
                'DROGUERIA',
                'INSUMOS',
                'SERVICIOS',
            ])->nullable();

            $table->string('razon_social', 255);
            $table->string('nombre_comercial', 255)->nullable();
            $table->string('nit', 50);
            $table->string('digito_verificacion', 5)->nullable();

            $table->string('registro_invima', 100)->nullable();
            $table->string('habilitacion_salud', 100)->nullable();

            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('pais', 100)->nullable();

            $table->string('telefono', 100)->nullable();
            $table->string('celular', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('sitio_web', 255)->nullable();

            $table->string('contacto_comercial', 255)->nullable();
            $table->string('telefono_contacto', 100)->nullable();
            $table->string('email_contacto', 150)->nullable();
            $table->string('contacto_farmacovigilancia', 255)->nullable();
            $table->string('contacto_logistica', 255)->nullable();

            $table->string('condiciones_pago', 255)->nullable();
            $table->integer('dias_credito')->nullable();

            $table->boolean('maneja_cadena_frio')->default(0);
            $table->decimal('temperatura_min', 5, 2)->nullable();
            $table->decimal('temperatura_max', 5, 2)->nullable();

            $table->integer('tiempo_entrega_horas')->nullable();
            $table->string('horario_entrega', 255)->nullable();

            $table->text('certificaciones')->nullable();
            $table->text('observaciones')->nullable();
            $table->string('logo', 255)->nullable();

            $table->boolean('estado')->default(1);

            $table->timestamps();

            $table->index('tipo_proveedor');
            $table->index('estado');
            $table->index('ciudad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
