<?php

namespace Database\Seeders;

use App\Models\InventarioLote;
use App\Models\MovimientoInventario;
use App\Models\Reempaque;
use App\Models\ReempaqueConsumo;
use App\Models\ReempaqueControlCalidad;
use App\Models\ReempaqueDetalle;
use App\Models\ReempaqueProductoFinal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ReempaquesDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $lotes = InventarioLote::with('medicamento')->where('estado', 1)->where('cantidad_actual', '>=', 1)->get();

        if (!$user || $lotes->isEmpty()) {
            $this->command->error("Faltan usuarios o lotes con stock para correr el demo de reempaques.");
            return;
        }

        $estados = ['PROGRAMADO','EN_PROCESO','CONTROL_CALIDAD','LIBERADO'];

        foreach ($estados as $i => $estado) {
            $lote = $lotes->random();
            $factor = [10, 50, 100][array_rand([10,50,100])];

            $reempaque = Reempaque::create([
                'medicamento_origen_id' => $lote->medicamento_id,
                'presentacion_origen_id' => $lote->presentacion_id,
                'presentacion_destino_id' => $lote->presentacion_id,
                'unidad_medida_destino_id' => $lote->unidad_medida_id,
                'factor_conversion' => $factor,
                'cantidad_esperada' => $factor,
                'fecha_programada' => now()->subHours(8 - $i*2),
                'fecha_inicio' => $i >= 1 ? now()->subHours(7 - $i*2) : null,
                'fecha_fin' => $i >= 3 ? now()->subHours(1) : null,
                'estado' => $estado,
                'observaciones' => 'Reempaque demo - ' . $estado,
                'usuario_responsable_id' => $user->id,
                'usuario_aprobador_id' => $i >= 3 ? $user->id : null,
            ]);

            // Insumo de ejemplo
            ReempaqueDetalle::create([
                'reempaque_id' => $reempaque->id,
                'insumo' => 'Etiquetas autoadhesivas',
                'cantidad' => $factor,
                'unidad_medida_id' => null,
                'costo_unitario' => 50,
                'costo_total' => 50 * $factor,
            ]);

            if ($i >= 1) {
                $costoU = $lote->costo_unitario ?: 5000;
                $cant = 1;
                ReempaqueConsumo::create([
                    'reempaque_id' => $reempaque->id,
                    'inventario_lote_id' => $lote->id,
                    'medicamento_id' => $lote->medicamento_id,
                    'lote_origen' => $lote->lote,
                    'fecha_vencimiento' => $lote->fecha_vencimiento,
                    'cantidad_consumida' => $cant,
                    'unidad_medida_id' => $lote->unidad_medida_id,
                    'costo_unitario' => $costoU,
                    'costo_total' => $costoU * $cant,
                ]);

                if ($i >= 2) {
                    ReempaqueControlCalidad::create([
                        'reempaque_id' => $reempaque->id,
                        'fecha_control' => now()->subHours(2),
                        'cantidad_verificada' => $factor,
                        'etiquetado_correcto' => true,
                        'lote_visible' => true,
                        'fecha_vencimiento_visible' => true,
                        'cumple' => true,
                        'observaciones' => 'Reempaque verificado y aprobado',
                        'usuario_control_id' => $user->id,
                    ]);
                }

                if ($estado === 'LIBERADO') {
                    $stockAnterior = $lote->cantidad_actual;
                    $lote->cantidad_actual = max(0, $lote->cantidad_actual - $cant);
                    $lote->save();

                    $cantidadGenerada = $cant * $factor;
                    $costoUGen = $costoU / $factor;

                    // SALIDA
                    $movS = [
                        'tipo_movimiento' => 'REEMPAQUE_SALIDA',
                        'referencia_tipo' => 'Reempaque',
                        'referencia_id' => $reempaque->id,
                        'inventario_lote_id' => $lote->id,
                        'cantidad' => -$cant,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $lote->cantidad_actual,
                        'fecha_movimiento' => now(),
                        'usuario_id' => $user->id,
                        'observacion' => "Salida demo reempaque {$reempaque->codigo}",
                    ];
                    if (Schema::hasColumn('movimientos_inventario','medicamento_id')) $movS['medicamento_id'] = $lote->medicamento_id;
                    if (Schema::hasColumn('movimientos_inventario','lote_codigo')) $movS['lote_codigo'] = $lote->lote;
                    if (Schema::hasColumn('movimientos_inventario','fecha_vencimiento')) $movS['fecha_vencimiento'] = $lote->fecha_vencimiento;
                    MovimientoInventario::create($movS);

                    // Nuevo lote
                    $nuevoLote = InventarioLote::create([
                        'medicamento_id' => $lote->medicamento_id,
                        'presentacion_id' => $lote->presentacion_id,
                        'lote' => $reempaque->codigo,
                        'fecha_vencimiento' => $lote->fecha_vencimiento,
                        'fecha_ingreso' => now(),
                        'cantidad_inicial' => $cantidadGenerada,
                        'cantidad_actual' => $cantidadGenerada,
                        'unidad_medida_id' => $lote->unidad_medida_id,
                        'costo_unitario' => round($costoUGen, 2),
                        'estado' => 1,
                    ]);

                    // ENTRADA
                    $movE = [
                        'tipo_movimiento' => 'REEMPAQUE_ENTRADA',
                        'referencia_tipo' => 'Reempaque',
                        'referencia_id' => $reempaque->id,
                        'inventario_lote_id' => $nuevoLote->id,
                        'cantidad' => $cantidadGenerada,
                        'stock_anterior' => 0,
                        'stock_nuevo' => $cantidadGenerada,
                        'fecha_movimiento' => now(),
                        'usuario_id' => $user->id,
                        'observacion' => "Entrada demo reempaque {$reempaque->codigo} (origen {$lote->lote})",
                    ];
                    if (Schema::hasColumn('movimientos_inventario','medicamento_id')) $movE['medicamento_id'] = $lote->medicamento_id;
                    if (Schema::hasColumn('movimientos_inventario','lote_codigo')) $movE['lote_codigo'] = $reempaque->codigo;
                    if (Schema::hasColumn('movimientos_inventario','fecha_vencimiento')) $movE['fecha_vencimiento'] = $lote->fecha_vencimiento;
                    MovimientoInventario::create($movE);

                    ReempaqueProductoFinal::create([
                        'reempaque_id' => $reempaque->id,
                        'medicamento_id' => $lote->medicamento_id,
                        'presentacion_id' => $lote->presentacion_id,
                        'lote_reempaque' => $reempaque->codigo,
                        'lote_origen' => $lote->lote,
                        'fecha_reempaque' => now(),
                        'fecha_vencimiento' => $lote->fecha_vencimiento,
                        'cantidad_generada' => $cantidadGenerada,
                        'unidad_medida_id' => $lote->unidad_medida_id,
                        'costo_unitario' => round($costoUGen, 4),
                        'inventario_lote_generado_id' => $nuevoLote->id,
                    ]);

                    $reempaque->update(['costo_total' => ($costoU * $cant) + (50 * $factor)]);
                }
            }
        }

        $this->command->info("¡Listo! Se crearon 4 reempaques de demostración (uno por cada estado del flujo).");
    }
}
