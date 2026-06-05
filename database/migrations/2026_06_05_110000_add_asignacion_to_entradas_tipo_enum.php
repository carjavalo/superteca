<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Agrega 'ASIGNACION' al ENUM de entradas.tipo_entrada para permitir
 * registrar entradas asignadas a un paciente.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE `entradas` MODIFY COLUMN `tipo_entrada` ENUM('COMPRA','DONACION','DEVOLUCION','TRASLADO','AJUSTE','PRODUCCION','ASIGNACION') NOT NULL DEFAULT 'COMPRA'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `entradas` MODIFY COLUMN `tipo_entrada` ENUM('COMPRA','DONACION','DEVOLUCION','TRASLADO','AJUSTE','PRODUCCION') NOT NULL DEFAULT 'COMPRA'");
    }
};
