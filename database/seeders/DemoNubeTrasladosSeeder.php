<?php

namespace Database\Seeders;

use App\Models\Bodega;
use App\Models\DetalleTraslado;
use App\Models\InventarioLote;
use App\Models\MovimientoInventario;
use App\Models\Traslado;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DemoNubeTrasladosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        if (!$user) {
            $this->command->error("No hay usuarios en el sistema.");
            return;
        }

        // Obtener dos bodegas diferentes
        $bodegas = Bodega::inRandomOrder()->limit(2)->get();
        if ($bodegas->count() < 2) {
            $this->command->error("Se necesitan al menos 2 bodegas para un traslado.");
            return;
        }
        $origen = $bodegas[0];
        $destino = $bodegas[1];

        // Buscar un lote con saldo mayor o igual a 10 para no agotar el inventario
        $lote = InventarioLote::with('medicamento')->where('estado', 1)->where('cantidad_actual', '>=', 10)->inRandomOrder()->first();

        if (!$lote) {
            $this->command->error("Faltan lotes con stock >= 10 en producción.");
            return;
        }

        $medicamento = $lote->medicamento;
        $costoU = $lote->costo_unitario > 0 ? $lote->costo_unitario : 5000;
        
        $consecutivo = Traslado::max('id') + 100; // Para que el código sea único en la demo

        // ------------------------------------------------------------------
        // REGISTRO 1: EN TRÁNSITO (Salió de origen, va camino a destino, aún no recibido)
        // ------------------------------------------------------------------
        $traslado1 = Traslado::create([
            'codigo' => 'TR-NUBE-' . str_pad($consecutivo, 4, '0', STR_PAD_LEFT),
            'fecha_solicitud' => now()->subHours(5),
            'fecha_envio' => now()->subHours(2),
            'fecha_recepcion' => null,
            'bodega_origen_id' => $origen->id,
            'bodega_destino_id' => $destino->id,
            'tipo_traslado' => 'INTERNO',
            'estado' => 'EN_TRANSITO',
            'usuario_solicita_id' => $user->id,
            'usuario_envia_id' => $user->id,
            'usuario_recibe_id' => null,
            'observaciones' => 'Traslado de insumos urgentes (Demo Nube)',
            'valor_total' => $costoU * 2,
        ]);

        DetalleTraslado::create([
            'traslado_id' => $traslado1->id,
            'inventario_lote_id' => $lote->id,
            'medicamento_id' => $medicamento->id,
            'lote' => $lote->lote,
            'fecha_vencimiento' => $lote->fecha_vencimiento,
            'cantidad' => 2,
            'costo_unitario' => $costoU,
        ]);


        // ------------------------------------------------------------------
        // REGISTRO 2: RECIBIDO (Afecta el Kardex de salida y entrada)
        // ------------------------------------------------------------------
        $traslado2 = Traslado::create([
            'codigo' => 'TR-NUBE-' . str_pad($consecutivo + 1, 4, '0', STR_PAD_LEFT),
            'fecha_solicitud' => now()->subDays(2),
            'fecha_envio' => now()->subDays(1),
            'fecha_recepcion' => now(),
            'bodega_origen_id' => $origen->id,
            'bodega_destino_id' => $destino->id,
            'tipo_traslado' => 'INTERNO',
            'estado' => 'RECIBIDO',
            'usuario_solicita_id' => $user->id,
            'usuario_envia_id' => $user->id,
            'usuario_recibe_id' => $user->id,
            'observaciones' => 'Reposición de stock rutinaria completada (Demo Nube)',
            'valor_total' => $costoU * 5,
        ]);

        DetalleTraslado::create([
            'traslado_id' => $traslado2->id,
            'inventario_lote_id' => $lote->id,
            'medicamento_id' => $medicamento->id,
            'lote' => $lote->lote,
            'fecha_vencimiento' => $lote->fecha_vencimiento,
            'cantidad' => 5,
            'costo_unitario' => $costoU,
        ]);

        // Simular el Movimiento de Inventario del Traslado Recibido 
        // 1. Descuento del origen (simulado en el mismo lote para la demo)
        $stockAnterior = $lote->cantidad_actual;
        $lote->cantidad_actual -= 5;
        $lote->save();

        $movDataSalida = [
            'tipo_movimiento' => 'TRASLADO_SALIDA',
            'referencia_tipo' => 'Traslado',
            'referencia_id' => $traslado2->id,
            'inventario_lote_id' => $lote->id,
            'cantidad' => -5,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $lote->cantidad_actual,
            'fecha_movimiento' => now(),
            'usuario_id' => $user->id,
            'observacion' => "Salida por Traslado {$traslado2->codigo} hacia {$destino->nombre} (Demo Nube)",
        ];

        // Columnas extras
        if (Schema::hasColumn('movimientos_inventario', 'medicamento_id')) {
            $movDataSalida['medicamento_id'] = $medicamento->id;
        }
        if (Schema::hasColumn('movimientos_inventario', 'lote_codigo')) {
            $movDataSalida['lote_codigo'] = $lote->lote;
        }
        if (Schema::hasColumn('movimientos_inventario', 'fecha_vencimiento')) {
            $movDataSalida['fecha_vencimiento'] = $lote->fecha_vencimiento;
        }

        MovimientoInventario::create($movDataSalida);

        // Opcional: Generar la entrada en destino
        // En una app real crearíamos u obtendríamos el lote en el destino, pero para Kardex basta con este mov.
        $movDataEntrada = $movDataSalida;
        $movDataEntrada['tipo_movimiento'] = 'TRASLADO_ENTRADA';
        $movDataEntrada['cantidad'] = 5;
        $movDataEntrada['stock_anterior'] = 0; // Simplificación para demo
        $movDataEntrada['stock_nuevo'] = 5; 
        $movDataEntrada['observacion'] = "Entrada por Traslado {$traslado2->codigo} desde {$origen->nombre} (Demo Nube)";
        
        MovimientoInventario::create($movDataEntrada);

        $this->command->info("¡Exito en la Nube! Se crearon 2 traslados: Uno 'EN TRÁNSITO' y otro 'RECIBIDO' (con registro en Kardex).");
    }
}
