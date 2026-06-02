<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ampliar enum a string flexible
        DB::statement("ALTER TABLE movimientos_inventario MODIFY tipo_movimiento VARCHAR(30) NOT NULL");

        Schema::table('movimientos_inventario', function (Blueprint $table) {
            if (!Schema::hasColumn('movimientos_inventario', 'medicamento_id')) {
                $table->unsignedBigInteger('medicamento_id')->nullable()->after('inventario_lote_id');
                $table->foreign('medicamento_id')->references('id')->on('medicamentos')->nullOnDelete();
                $table->index('medicamento_id');
            }
            if (!Schema::hasColumn('movimientos_inventario', 'presentacion_id')) {
                $table->unsignedBigInteger('presentacion_id')->nullable()->after('medicamento_id');
                $table->foreign('presentacion_id')->references('id')->on('presentaciones')->nullOnDelete();
            }
            if (!Schema::hasColumn('movimientos_inventario', 'lote_codigo')) {
                $table->string('lote_codigo', 100)->nullable()->after('presentacion_id');
            }
            if (!Schema::hasColumn('movimientos_inventario', 'fecha_vencimiento')) {
                $table->date('fecha_vencimiento')->nullable()->after('lote_codigo');
            }
            if (!Schema::hasColumn('movimientos_inventario', 'bodega_origen_id')) {
                $table->unsignedBigInteger('bodega_origen_id')->nullable()->after('fecha_vencimiento');
            }
            if (!Schema::hasColumn('movimientos_inventario', 'bodega_destino_id')) {
                $table->unsignedBigInteger('bodega_destino_id')->nullable()->after('bodega_origen_id');
            }
            if (!Schema::hasColumn('movimientos_inventario', 'proveedor_id')) {
                $table->unsignedBigInteger('proveedor_id')->nullable()->after('bodega_destino_id');
                $table->foreign('proveedor_id')->references('id')->on('proveedores')->nullOnDelete();
            }
            if (!Schema::hasColumn('movimientos_inventario', 'costo_unitario')) {
                $table->decimal('costo_unitario', 14, 2)->nullable()->after('proveedor_id');
            }
            if (!Schema::hasColumn('movimientos_inventario', 'costo_total')) {
                $table->decimal('costo_total', 14, 2)->nullable()->after('costo_unitario');
            }
        });

        // Backfill de datos para registros existentes
        DB::statement("
            UPDATE movimientos_inventario m
            INNER JOIN inventario_lotes l ON m.inventario_lote_id = l.id
            SET 
                m.medicamento_id    = COALESCE(m.medicamento_id, l.medicamento_id),
                m.presentacion_id   = COALESCE(m.presentacion_id, l.presentacion_id),
                m.lote_codigo       = COALESCE(m.lote_codigo, l.lote),
                m.fecha_vencimiento = COALESCE(m.fecha_vencimiento, l.fecha_vencimiento),
                m.proveedor_id      = COALESCE(m.proveedor_id, l.proveedor_id),
                m.costo_unitario    = COALESCE(m.costo_unitario, l.costo_unitario),
                m.costo_total       = COALESCE(m.costo_total, l.costo_unitario * ABS(m.cantidad))
        ");
    }

    public function down(): void
    {
        Schema::table('movimientos_inventario', function (Blueprint $table) {
            $table->dropForeign(['medicamento_id']);
            $table->dropForeign(['presentacion_id']);
            $table->dropForeign(['proveedor_id']);
            $table->dropColumn([
                'medicamento_id','presentacion_id','lote_codigo','fecha_vencimiento',
                'bodega_origen_id','bodega_destino_id','proveedor_id','costo_unitario','costo_total'
            ]);
        });
        DB::statement("ALTER TABLE movimientos_inventario MODIFY tipo_movimiento ENUM('ENTRADA','SALIDA','AJUSTE','TRASLADO') NOT NULL");
    }
};
