<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Traslado;
use App\Models\DetalleTraslado;
use App\Models\AjusteInventario;
use App\Models\DetalleAjusteInventario;
use App\Models\InventarioLote;
use App\Models\Bodega;
use App\Models\User;
use App\Models\MovimientoInventario;

class DemoLogisticaSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create();
        $bodegas = Bodega::take(3)->get();
        if ($bodegas->count() < 2) return;

        $bodegaCentral = $bodegas->where('tipo', 'CENTRAL')->first() ?? $bodegas->first();
        $bodegaFarmacia = $bodegas->where('tipo', 'FARMACIA')->first() ?? $bodegas->last();

        $lotes = InventarioLote::where('cantidad_actual', '>', 10)->where('estado', 1)->take(5)->get();
        if ($lotes->isEmpty()) return;

        // --- 5 TRASLADOS ---
        $estadosTraslado = ['BORRADOR', 'PENDIENTE', 'EN_TRANSITO', 'RECIBIDO', 'RECHAZADO'];
        
        foreach ($estadosTraslado as $index => $estado) {
            $lote = $lotes->random();
            $traslado = Traslado::create([
                'bodega_origen_id'    => $bodegaCentral->id,
                'bodega_destino_id'   => $bodegaFarmacia->id,
                'tipo_traslado'       => 'INTERNO',
                'fecha_solicitud'     => now()->subDays(5 - $index),
                'estado'              => $estado,
                'usuario_solicita_id' => $user->id,
                'observaciones'       => "Traslado de prueba $index",
            ]);

            $cantidad = rand(5, 10);
            $valorTotal = $cantidad * $lote->costo_unitario;

            DetalleTraslado::create([
                'traslado_id'        => $traslado->id,
                'inventario_lote_id' => $lote->id,
                'medicamento_id'     => $lote->medicamento_id,
                'presentacion_id'    => $lote->presentacion_id,
                'lote'               => $lote->lote,
                'fecha_vencimiento'  => $lote->fecha_vencimiento,
                'cantidad'           => $cantidad,
                'costo_unitario'     => $lote->costo_unitario,
            ]);

            $traslado->update(['valor_total' => $valorTotal]);
        }

        // --- 5 AJUSTES ---
        $estadosAjuste = ['BORRADOR', 'APROBADO', 'ANULADO', 'BORRADOR', 'APROBADO'];
        $motivo = DB::table('ajuste_motivos')->first();
        if (!$motivo) return;

        foreach ($estadosAjuste as $index => $estado) {
            $lote = $lotes->random();
            $tipoAjuste = $index % 2 == 0 ? 'NEGATIVO' : 'POSITIVO';
            
            $ajuste = AjusteInventario::create([
                'fecha_ajuste'        => now()->subDays(5 - $index),
                'motivo_ajuste_id'           => $motivo->id,
                'tipo_ajuste'         => $tipoAjuste,
                'estado'              => $estado,
                'usuario_solicita_id' => $user->id,
                'observaciones'       => "Ajuste de prueba $index",
            ]);

            $stockSis = $lote->cantidad_actual;
            $diff = $tipoAjuste == 'POSITIVO' ? rand(5, 20) : -rand(1, 4);
            $stockFisico = $stockSis + $diff;
            $valorAjuste = abs($diff) * $lote->costo_unitario;

            DetalleAjusteInventario::create([
                'ajuste_id'          => $ajuste->id,
                'inventario_lote_id' => $lote->id,
                'medicamento_id'     => $lote->medicamento_id,
                'lote'               => $lote->lote,
                'fecha_vencimiento'  => $lote->fecha_vencimiento,
                'stock_sistema'      => $stockSis,
                'stock_fisico'       => $stockFisico,
                'diferencia'         => $diff,
                'costo_unitario'     => $lote->costo_unitario,
                'valor_ajuste'       => $valorAjuste,
            ]);

            $ajuste->update(['valor_total' => $valorAjuste]);
        }
    }
}
