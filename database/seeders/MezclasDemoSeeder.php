<?php

namespace Database\Seeders;

use App\Models\Formula;
use App\Models\InventarioLote;
use App\Models\Mezcla;
use App\Models\MezclaConsumo;
use App\Models\MezclaControlCalidad;
use App\Models\MezclaDetalle;
use App\Models\MezclaProductoFinal;
use App\Models\MovimientoInventario;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MezclasDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $formulas = Formula::with('detalles.medicamento')->get();
        $lotes = InventarioLote::with('medicamento')->where('estado',1)->where('cantidad_actual','>=',1)->get();

        if (!$user || $formulas->isEmpty() || $lotes->isEmpty()) {
            $this->command->error("Faltan usuarios, fórmulas o lotes con stock para correr el demo.");
            return;
        }

        $estados = ['PROGRAMADA','EN_PROCESO','CONTROL_CALIDAD','LIBERADA'];
        $tipoMap = [
            'NUTRICION_PARENTERAL' => 'NUTRICION_PARENTERAL',
            'ANTIBIOTICO' => 'ANTIBIOTICO',
            'ONCOLOGIA' => 'ONCOLOGIA',
            'PEDIATRIA' => 'PEDIATRICA',
            'MAGISTRAL' => 'MAGISTRAL',
            'ESTANDAR' => 'MAGISTRAL',
        ];

        foreach ($estados as $i => $estado) {
            $formula = $formulas->random();
            $tipo = $tipoMap[$formula->tipo_formula] ?? 'MAGISTRAL';

            $mezcla = Mezcla::create([
                'formula_id' => $formula->id,
                'tipo_mezcla' => $tipo,
                'fecha_programada' => now()->subHours(8 - $i*2),
                'fecha_inicio' => $i >= 1 ? now()->subHours(7 - $i*2) : null,
                'fecha_fin' => $i >= 3 ? now()->subHours(1) : null,
                'volumen_programado' => $formula->volumen_final ?? 100,
                'cantidad_preparaciones' => rand(1, 5),
                'estado' => $estado,
                'usuario_preparador_id' => $user->id,
                'usuario_validador_id' => $i >= 3 ? $user->id : null,
                'observaciones' => 'Mezcla demo automatizada - ' . $estado,
            ]);

            // Componentes desde la fórmula
            $orden = 1;
            foreach ($formula->detalles as $d) {
                MezclaDetalle::create([
                    'mezcla_id' => $mezcla->id,
                    'medicamento_id' => $d->medicamento_id,
                    'dosis_requerida' => $d->dosis,
                    'unidad_medida_id' => $d->unidad_medida_id,
                    'orden_preparacion' => $orden++,
                ]);
            }

            // Consumos en EN_PROCESO o adelante
            if ($i >= 1) {
                $costoTotal = 0;
                $lote = $lotes->random();
                $costoU = $lote->costo_unitario ?: 5000;
                $cant = 1;
                MezclaConsumo::create([
                    'mezcla_id' => $mezcla->id,
                    'inventario_lote_id' => $lote->id,
                    'medicamento_id' => $lote->medicamento_id,
                    'lote' => $lote->lote,
                    'fecha_vencimiento' => $lote->fecha_vencimiento,
                    'cantidad_consumida' => $cant,
                    'costo_unitario' => $costoU,
                    'costo_total' => $costoU * $cant,
                ]);
                $costoTotal = $costoU * $cant;

                // Control en CONTROL_CALIDAD o adelante
                if ($i >= 2) {
                    MezclaControlCalidad::create([
                        'mezcla_id' => $mezcla->id,
                        'fecha_control' => now()->subHours(2),
                        'aspecto_visual' => 'Solución traslúcida sin partículas',
                        'volumen_verificado' => $formula->volumen_final ?? 100,
                        'ph' => 7.1,
                        'osmolaridad' => 290,
                        'temperatura' => 22,
                        'cumple' => true,
                        'observaciones' => 'Aprobado para liberación',
                        'usuario_control_id' => $user->id,
                    ]);
                }

                // Si está LIBERADA, mover Kardex y crear producto final
                if ($estado === 'LIBERADA') {
                    $stockAnterior = $lote->cantidad_actual;
                    $lote->cantidad_actual = max(0, $lote->cantidad_actual - $cant);
                    $lote->save();

                    $movData = [
                        'tipo_movimiento' => 'PRODUCCION',
                        'referencia_tipo' => 'Mezcla',
                        'referencia_id' => $mezcla->id,
                        'inventario_lote_id' => $lote->id,
                        'cantidad' => -$cant,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $lote->cantidad_actual,
                        'fecha_movimiento' => now(),
                        'usuario_id' => $user->id,
                        'observacion' => "Liberación mezcla {$mezcla->codigo} (Demo)",
                    ];
                    if (Schema::hasColumn('movimientos_inventario','medicamento_id')) {
                        $movData['medicamento_id'] = $lote->medicamento_id;
                    }
                    if (Schema::hasColumn('movimientos_inventario','lote_codigo')) {
                        $movData['lote_codigo'] = $lote->lote;
                    }
                    if (Schema::hasColumn('movimientos_inventario','fecha_vencimiento')) {
                        $movData['fecha_vencimiento'] = $lote->fecha_vencimiento;
                    }
                    MovimientoInventario::create($movData);

                    MezclaProductoFinal::create([
                        'mezcla_id' => $mezcla->id,
                        'lote_produccion' => 'PROD-' . $mezcla->codigo,
                        'fecha_produccion' => now(),
                        'fecha_vencimiento' => now()->addHours($formula->tiempo_estabilidad_horas ?: 24),
                        'volumen_final' => $formula->volumen_final ?? 100,
                        'cantidad_unidades' => $mezcla->cantidad_preparaciones,
                        'observaciones' => 'Producto liberado en demo',
                    ]);

                    $mezcla->update(['costo_total' => $costoTotal]);
                }
            }
        }

        $this->command->info("¡Listo! Se crearon 4 mezclas de demostración (una por cada estado del flujo).");
    }
}
