<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Convierte `entradas.tipo_entrada` de ENUM (lista fija) a VARCHAR, para que el
 * catálogo dinámico `tipoEntrada` pueda aportar nuevos tipos sin tener que
 * alterar la estructura de la tabla cada vez que se agregue uno.
 *
 * Los valores existentes (COMPRA, DONACION, …, ASIGNACION) se conservan
 * intactos: la conversión ENUM -> VARCHAR preserva el texto de cada fila.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `entradas` MODIFY COLUMN `tipo_entrada` VARCHAR(50) NOT NULL DEFAULT 'COMPRA'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `entradas` MODIFY COLUMN `tipo_entrada` ENUM('COMPRA','DONACION','DEVOLUCION','TRASLADO','AJUSTE','PRODUCCION','ASIGNACION') NOT NULL DEFAULT 'COMPRA'");
    }
};
