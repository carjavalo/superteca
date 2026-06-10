<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Convierte `vias_administracion.tipo` de ENUM (lista fija) a VARCHAR, para que
 * el catálogo dinámico `TipoAdministracion` pueda aportar nuevos tipos sin tener
 * que alterar la estructura de la tabla cada vez.
 *
 * Los valores existentes (PARENTERAL, ENTERAL, …, VAGINAL) se conservan: la
 * conversión ENUM -> VARCHAR preserva el texto de cada fila. La columna sigue
 * siendo NULL-able, como en el diseño original.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `vias_administracion` MODIFY COLUMN `tipo` VARCHAR(50) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `vias_administracion` MODIFY COLUMN `tipo` ENUM('PARENTERAL','ENTERAL','TOPICA','RESPIRATORIA','OFTALMICA','OTICA','NASAL','RECTAL','VAGINAL') NULL");
    }
};
