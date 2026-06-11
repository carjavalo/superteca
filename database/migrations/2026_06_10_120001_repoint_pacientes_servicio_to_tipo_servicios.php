<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Libera la llave foránea `pacientes.servicio_id -> servicios_hospitalarios`.
 *
 * El campo «Servicio» de pacientes pasa a alimentarse del catálogo dinámico
 * `TipoServicios` (gestionable desde Configuración → Gestor de Servicios). Como
 * los ids de ese nuevo catálogo no existen en `servicios_hospitalarios`, la FK
 * original impediría guardar pacientes con tipos nuevos; por eso se elimina. La
 * integridad se mantiene por validación (`exists:TipoServicios,id`) y por el
 * bloqueo de borrado de servicios en uso en el CRUD.
 *
 * No hay datos en riesgo: la tabla `pacientes` no tiene registros con servicio.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropForeign(['servicio_id']);
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->foreign('servicio_id')->references('id')->on('servicios_hospitalarios')->nullOnDelete();
        });
    }
};
