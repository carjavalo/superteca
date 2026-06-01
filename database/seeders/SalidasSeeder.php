<?php

namespace Database\Seeders;

use App\Models\DetalleSalida;
use App\Models\InventarioLote;
use App\Models\MovimientoInventario;
use App\Models\Salida;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SalidasSeeder extends Seeder
{
    public function run(): void
    {
        $usuario       = User::orderBy('id')->first();
        $autorizadores = User::orderBy('id')->pluck('id')->all();
        if (! $usuario) {
            $this->command->warn('No hay usuarios para registrar las salidas.');
            return;
        }

        // Lotes ordenados por FEFO con stock disponible y datos completos
        $lotes = InventarioLote::where('cantidad_actual', '>', 0)
            ->where('estado', 1)
            ->whereNotNull('presentacion_id')
            ->whereNotNull('medicamento_id')
            ->orderByRaw('CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END, fecha_vencimiento ASC')
            ->get();

        if ($lotes->count() < 1) {
            $this->command->warn('No hay lotes con stock disponible.');
            return;
        }

        $hoy = Carbon::now();

        $plantillas = [
            [
                'tipo'        => 'DISPENSACION',
                'fecha'       => $hoy->copy()->subHours(2),
                'documento'   => 'DISP-2026-0001',
                'paciente_id' => 1042,
                'servicio_id' => 3,
                'observ'      => 'Entrega a paciente hospitalizado',
                'qty_pct'     => 0.20,
                'motivo'      => 'Dispensación ambulatoria',
            ],
            [
                'tipo'         => 'PRODUCCION',
                'fecha'        => $hoy->copy()->subHours(6),
                'documento'    => 'PROD-NPT-2026-014',
                'paciente_id'  => 1099,
                'servicio_id'  => 7,
                'observ'       => 'Preparación de mezcla NPT',
                'qty_pct'      => 0.15,
                'motivo'       => 'Preparación NPT',
                'preparacion'  => 'NPT-2026-0014',
            ],
            [
                'tipo'         => 'CONSUMO_INTERNO',
                'fecha'        => $hoy->copy()->subDay(),
                'documento'    => 'INT-2026-205',
                'servicio_id'  => 12,
                'observ'       => 'Consumo en sala de procedimientos',
                'qty_pct'      => 0.10,
                'motivo'       => 'Uso en sala',
            ],
            [
                'tipo'        => 'TRASLADO',
                'fecha'       => $hoy->copy()->subDays(2),
                'documento'   => 'TRAS-2026-077',
                'observ'      => 'Traslado a bodega satélite urgencias',
                'bodega_origen_id'  => 1,
                'bodega_destino_id' => 2,
                'qty_pct'     => 0.10,
                'motivo'      => 'Traslado entre bodegas',
            ],
            [
                'tipo'        => 'DANO',
                'fecha'       => $hoy->copy()->subDays(3),
                'documento'   => 'ACTA-DAN-2026-009',
                'observ'      => 'Ruptura de ampolla durante manipulación',
                'qty_pct'     => 0.05,
                'motivo'      => 'Daño físico - acta de baja',
            ],
        ];

        DB::transaction(function () use ($plantillas, $lotes, $usuario, $autorizadores) {
            foreach ($plantillas as $i => $p) {
                // Toma un lote distinto por cada salida si hay suficientes; si no, recicla
                $lote = $lotes[$i % $lotes->count()]->fresh();
                if ((float) $lote->cantidad_actual <= 0) continue;

                $cant = max(1, round((float) $lote->cantidad_actual * $p['qty_pct'], 2));
                if ($cant > (float) $lote->cantidad_actual) {
                    $cant = (float) $lote->cantidad_actual;
                }

                $codigo = 'SAL-' . $p['fecha']->format('Ymd') . '-' . str_pad((string)($i + 1), 4, '0', STR_PAD_LEFT);

                $salida = Salida::create([
                    'codigo'            => $codigo,
                    'tipo_salida'       => $p['tipo'],
                    'fecha_salida'      => $p['fecha'],
                    'paciente_id'       => $p['paciente_id']        ?? null,
                    'servicio_id'       => $p['servicio_id']        ?? null,
                    'bodega_origen_id'  => $p['bodega_origen_id']   ?? null,
                    'bodega_destino_id' => $p['bodega_destino_id']  ?? null,
                    'numero_documento'  => $p['documento']          ?? null,
                    'observaciones'     => $p['observ']             ?? null,
                    'subtotal'          => 0,
                    'impuestos'         => 0,
                    'total'             => 0,
                    'estado'            => 'BORRADOR',
                    'usuario_id'        => $usuario->id,
                    'autorizado_por'    => $autorizadores[array_rand($autorizadores)] ?? null,
                ]);

                $costoUnit = (float) $lote->costo_unitario;
                $costoTot  = $costoUnit * $cant;

                DetalleSalida::create([
                    'salida_id'          => $salida->id,
                    'inventario_lote_id' => $lote->id,
                    'medicamento_id'     => $lote->medicamento_id,
                    'presentacion_id'    => $lote->presentacion_id,
                    'lote'               => $lote->lote,
                    'fecha_vencimiento'  => $lote->fecha_vencimiento,
                    'cantidad'           => $cant,
                    'unidad_medida_id'   => $lote->unidad_medida_id,
                    'costo_unitario'     => $costoUnit,
                    'costo_total'        => $costoTot,
                    'motivo_salida'      => $p['motivo']      ?? null,
                    'paciente_id'        => $p['paciente_id'] ?? null,
                    'numero_preparacion' => $p['preparacion'] ?? null,
                ]);

                // Confirmar: descontar stock y registrar movimiento
                $anterior = (float) $lote->cantidad_actual;
                $nuevo    = $anterior - $cant;
                $lote->cantidad_actual = $nuevo;
                $lote->save();

                MovimientoInventario::create([
                    'tipo_movimiento'    => 'SALIDA',
                    'referencia_tipo'    => Salida::class,
                    'referencia_id'      => $salida->id,
                    'inventario_lote_id' => $lote->id,
                    'cantidad'           => -1 * $cant,
                    'stock_anterior'     => $anterior,
                    'stock_nuevo'        => $nuevo,
                    'fecha_movimiento'   => $p['fecha'],
                    'usuario_id'         => $usuario->id,
                    'observacion'        => 'Salida ' . $codigo . ' (' . Salida::TIPOS[$p['tipo']] . ')',
                    'created_at'         => $p['fecha'],
                ]);

                $salida->update([
                    'subtotal' => $costoTot,
                    'total'    => $costoTot,
                    'estado'   => 'CONFIRMADA',
                ]);
            }
        });

        $this->command->info('Salidas de ejemplo creadas correctamente.');
    }
}
