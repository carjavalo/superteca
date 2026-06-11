<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Crea el catálogo dinámico `TProveedor`, que alimenta el campo «Tipo de
 * proveedor» del modal Nuevo Proveedor (/admin/proveedores).
 *
 * Estructura solicitada:
 *   id          BIGINT  auto-increment  PRIMARY KEY
 *   Detalle     VARCHAR(120)  -> nombre del tipo de proveedor
 *   Observacion VARCHAR(300)  -> objetivo / descripción del tipo
 *
 * Se siembra con los tipos que hoy existen en el select (constante
 * Proveedor::TIPOS). El código interno (DISTRIBUIDOR, LABORATORIO, …) se deriva
 * del Detalle en el modelo, conservando la compatibilidad con los proveedores
 * ya registrados.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TProveedor', function (Blueprint $table) {
            $table->id();
            $table->string('Detalle', 120);
            $table->string('Observacion', 300)->nullable();
        });

        $tipos = [
            ['Detalle' => 'Distribuidor',       'Observacion' => 'Empresa que distribuye y comercializa medicamentos e insumos de distintos fabricantes.'],
            ['Detalle' => 'Laboratorio',        'Observacion' => 'Laboratorio o fabricante farmacéutico que produce los medicamentos.'],
            ['Detalle' => 'Operador Logístico', 'Observacion' => 'Empresa encargada del almacenamiento y transporte (logística) de los productos.'],
            ['Detalle' => 'Droguería',          'Observacion' => 'Establecimiento farmacéutico que dispensa o suministra medicamentos.'],
            ['Detalle' => 'Insumos',            'Observacion' => 'Proveedor de insumos, dispositivos médicos y material hospitalario.'],
            ['Detalle' => 'Servicios',          'Observacion' => 'Proveedor de servicios (mantenimiento, asesoría, etc.) para la operación.'],
        ];

        foreach ($tipos as $t) {
            if (! DB::table('TProveedor')->where('Detalle', $t['Detalle'])->exists()) {
                DB::table('TProveedor')->insert($t);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('TProveedor');
    }
};
