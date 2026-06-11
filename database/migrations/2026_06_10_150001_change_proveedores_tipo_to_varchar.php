<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Convierte `proveedores.tipo_proveedor` de ENUM (lista fija) a VARCHAR, para
 * que el catálogo dinámico `TProveedor` pueda aportar nuevos tipos sin alterar
 * la estructura de la tabla cada vez.
 *
 * Los valores existentes (DISTRIBUIDOR, LABORATORIO, …) se conservan: la
 * conversión ENUM -> VARCHAR preserva el texto de cada fila. La columna sigue
 * siendo NULL-able, como en el diseño original.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `proveedores` MODIFY COLUMN `tipo_proveedor` VARCHAR(50) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `proveedores` MODIFY COLUMN `tipo_proveedor` ENUM('DISTRIBUIDOR','LABORATORIO','OPERADOR_LOGISTICO','DROGUERIA','INSUMOS','SERVICIOS') NULL");
    }
};
