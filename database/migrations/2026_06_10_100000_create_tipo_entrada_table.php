<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Crea el catálogo dinámico `tipoEntrada`, que alimenta el campo
 * «Tipo de entrada» del registro de entradas de inventario.
 *
 * Estructura solicitada:
 *   id          BIGINT  auto-increment  PRIMARY KEY
 *   Detalle     VARCHAR(120)  -> nombre del tipo de entrada
 *   Observacion VARCHAR(300)  -> objetivo / para qué se crea el tipo
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipoEntrada', function (Blueprint $table) {
            $table->id();
            $table->string('Detalle', 120);
            $table->string('Observacion', 300)->nullable();
        });

        // Semilla: los tipos de entrada que hoy existen en el select
        // (constante Entrada::TIPOS). El Detalle guarda la etiqueta legible;
        // el código interno (COMPRA, ASIGNACION, …) se deriva automáticamente
        // de él en el modelo TipoEntrada, conservando la compatibilidad con
        // las entradas ya registradas y con la lógica de Asignación a paciente.
        $tipos = [
            ['Detalle' => 'Compra',     'Observacion' => 'Ingreso de medicamentos e insumos adquiridos a proveedores mediante orden de compra o factura.'],
            ['Detalle' => 'Donación',   'Observacion' => 'Ingreso de productos recibidos como donación de entidades externas, campañas o terceros.'],
            ['Detalle' => 'Devolución', 'Observacion' => 'Reingreso al inventario de productos previamente despachados que son devueltos al servicio farmacéutico.'],
            ['Detalle' => 'Traslado',   'Observacion' => 'Ingreso de mercancía proveniente del traslado desde otra bodega o sede.'],
            ['Detalle' => 'Ajuste',     'Observacion' => 'Ingreso generado por ajustes de inventario: conteos físicos, sobrantes o correcciones de stock.'],
            ['Detalle' => 'Producción', 'Observacion' => 'Ingreso de productos terminados provenientes de procesos de producción o elaboración propia.'],
            ['Detalle' => 'Asignación', 'Observacion' => 'Ingreso de medicamentos asignados de forma nominal a un paciente específico.'],
        ];

        foreach ($tipos as $t) {
            if (! DB::table('tipoEntrada')->where('Detalle', $t['Detalle'])->exists()) {
                DB::table('tipoEntrada')->insert($t);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tipoEntrada');
    }
};
