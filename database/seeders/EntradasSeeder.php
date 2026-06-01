<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Entrada;
use App\Models\DetalleEntrada;
use App\Models\InventarioLote;
use App\Models\MovimientoInventario;
use App\Models\Proveedor;
use App\Models\Medicamento;
use App\Models\Presentacion;
use App\Models\Laboratorio;
use App\Models\UnidadMedida;
use App\Models\User;
use Carbon\Carbon;

class EntradasSeeder extends Seeder
{
    public function run(): void
    {
        $proveedores = Proveedor::all();
        $medicamentos = Medicamento::with('presentaciones')->get();
        $presentaciones = Presentacion::all();
        $laboratorios = Laboratorio::all();
        $userId = User::value('id') ?? 1;

        if ($proveedores->isEmpty() || $medicamentos->isEmpty() || $presentaciones->isEmpty()) {
            $this->command->warn('Faltan maestros (proveedores/medicamentos/presentaciones). Aborta seeder.');
            return;
        }

        // ---- Plantillas de las 5 entradas ----
        $plantillas = [
            [
                'tipo'      => 'COMPRA',
                'estado'    => 'CONFIRMADA',
                'dias_atras'=> 14,
                'factura'   => 'FV-2026-001045',
                'remision'  => 'REM-7781',
                'obs'       => 'Compra mensual programada',
                'items_n'   => 3,
                'venc_meses'=> 24,
            ],
            [
                'tipo'      => 'COMPRA',
                'estado'    => 'CONFIRMADA',
                'dias_atras'=> 7,
                'factura'   => 'FV-2026-001112',
                'remision'  => 'REM-7820',
                'obs'       => 'Reposición urgente UCI',
                'items_n'   => 2,
                'venc_meses'=> 18,
            ],
            [
                'tipo'      => 'DONACION',
                'estado'    => 'CONFIRMADA',
                'dias_atras'=> 3,
                'factura'   => null,
                'remision'  => 'DON-2026-04',
                'obs'       => 'Donación cruz roja - lote vigilado',
                'items_n'   => 2,
                'venc_meses'=> 12,
            ],
            [
                'tipo'      => 'DEVOLUCION',
                'estado'    => 'CONFIRMADA',
                'dias_atras'=> 1,
                'factura'   => 'DEV-2026-009',
                'remision'  => null,
                'obs'       => 'Devolución desde piso 3 - empaque sin abrir',
                'items_n'   => 1,
                'venc_meses'=> 9,
            ],
            [
                'tipo'      => 'COMPRA',
                'estado'    => 'BORRADOR',
                'dias_atras'=> 0,
                'factura'   => 'FV-2026-001188',
                'remision'  => 'REM-7901',
                'obs'       => 'Pendiente de validación QF',
                'items_n'   => 3,
                'venc_meses'=> 24,
            ],
        ];

        foreach ($plantillas as $idx => $tpl) {
            DB::transaction(function () use ($tpl, $idx, $proveedores, $medicamentos, $presentaciones, $laboratorios, $userId) {
                $fecha = Carbon::now()->subDays($tpl['dias_atras'])->setTime(8 + $idx, 30, 0);
                $proveedor = $proveedores->random();

                // Generar código
                $base = 'ENT-'.$fecha->format('Ymd');
                $n = Entrada::where('codigo', 'like', $base.'%')->count() + 1;
                $codigo = $base.'-'.str_pad($n, 4, '0', STR_PAD_LEFT);

                $entrada = Entrada::create([
                    'codigo'           => $codigo,
                    'tipo_entrada'     => $tpl['tipo'],
                    'proveedor_id'     => $tpl['tipo']==='DONACION' ? null : $proveedor->id,
                    'numero_factura'   => $tpl['factura'],
                    'numero_remision'  => $tpl['remision'],
                    'fecha_entrada'    => $fecha,
                    'fecha_documento'  => $fecha->copy()->subDay()->toDateString(),
                    'observaciones'    => $tpl['obs'],
                    'subtotal'         => 0,
                    'impuestos'        => 0,
                    'total'            => 0,
                    'estado'           => 'BORRADOR',
                    'usuario_id'       => $userId,
                ]);

                // Tomar N presentaciones distintas
                $picks = $presentaciones->shuffle()->take($tpl['items_n']);

                $subtotal = 0;
                foreach ($picks as $j => $pres) {
                    $cantidad = [50, 100, 200, 30, 60, 24][array_rand([50, 100, 200, 30, 60, 24])];
                    $costoUnit = round([1500, 2800, 4500, 950, 12000, 8500][array_rand([1500, 2800, 4500, 950, 12000, 8500])], 2);
                    $costoTotal = round($cantidad * $costoUnit, 2);
                    $subtotal += $costoTotal;

                    $lab = $laboratorios->isNotEmpty() ? $laboratorios->random() : null;
                    $lote = strtoupper(substr(md5($entrada->id.'-'.$pres->id.'-'.$j), 0, 8));
                    $venc = $fecha->copy()->addMonths($tpl['venc_meses'])->addDays(rand(-30, 60))->toDateString();

                    DetalleEntrada::create([
                        'entrada_id'       => $entrada->id,
                        'medicamento_id'   => $pres->medicamento_id,
                        'presentacion_id'  => $pres->id,
                        'laboratorio_id'   => $lab?->id,
                        'proveedor_id'     => $entrada->proveedor_id,
                        'lote'             => $lote,
                        'fecha_vencimiento'=> $venc,
                        'fecha_fabricacion'=> $fecha->copy()->subMonths(2)->toDateString(),
                        'cantidad'         => $cantidad,
                        'unidad_medida_id' => $pres->unidad_medida_id,
                        'costo_unitario'   => $costoUnit,
                        'costo_total'      => $costoTotal,
                        'temperatura_min'  => 2,
                        'temperatura_max'  => 25,
                        'ubicacion'        => 'BOD-A-EST'.rand(1,8),
                        'registro_invima'  => optional($pres->medicamento)->registro_invima,
                        'observaciones'    => null,
                    ]);
                }

                $impuestos = round($subtotal * 0.19, 2);
                $entrada->update([
                    'subtotal'  => $subtotal,
                    'impuestos' => $impuestos,
                    'total'     => $subtotal + $impuestos,
                ]);

                // Si la plantilla pide CONFIRMADA, ejecutar el flujo completo
                if ($tpl['estado'] === 'CONFIRMADA') {
                    $this->confirmar($entrada, $userId);
                }
            });
        }

        $this->command->info('Entradas creadas correctamente.');
    }

    private function confirmar(Entrada $entrada, int $userId): void
    {
        $entrada->load('detalles');

        foreach ($entrada->detalles as $det) {
            $lote = InventarioLote::where('medicamento_id', $det->medicamento_id)
                ->where('presentacion_id', $det->presentacion_id)
                ->where('lote', $det->lote)
                ->first();

            $stockAnterior = $lote?->cantidad_actual ?? 0;

            if ($lote) {
                $lote->cantidad_actual = $stockAnterior + $det->cantidad;
                $lote->save();
            } else {
                $lote = InventarioLote::create([
                    'medicamento_id'   => $det->medicamento_id,
                    'presentacion_id'  => $det->presentacion_id,
                    'proveedor_id'     => $det->proveedor_id ?: $entrada->proveedor_id,
                    'unidad_medida_id' => $det->unidad_medida_id,
                    'lote'             => $det->lote,
                    'fecha_vencimiento'=> $det->fecha_vencimiento,
                    'fecha_ingreso'    => $entrada->fecha_entrada,
                    'cantidad_inicial' => $det->cantidad,
                    'cantidad_actual'  => $det->cantidad,
                    'costo_unitario'   => $det->costo_unitario,
                    'ubicacion'        => $det->ubicacion,
                    'temperatura_min'  => $det->temperatura_min,
                    'temperatura_max'  => $det->temperatura_max,
                    'estado'           => 1,
                ]);
            }

            MovimientoInventario::create([
                'tipo_movimiento'    => 'ENTRADA',
                'referencia_tipo'    => Entrada::class,
                'referencia_id'      => $entrada->id,
                'inventario_lote_id' => $lote->id,
                'cantidad'           => $det->cantidad,
                'stock_anterior'     => $stockAnterior,
                'stock_nuevo'        => $stockAnterior + $det->cantidad,
                'fecha_movimiento'   => $entrada->fecha_entrada,
                'usuario_id'         => $userId,
                'observacion'        => 'Confirmación entrada '.$entrada->codigo,
            ]);
        }

        $entrada->update(['estado' => 'CONFIRMADA']);
    }
}
