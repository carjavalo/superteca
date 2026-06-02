<?php

namespace Database\Seeders;

use App\Models\InventarioLote;
use App\Models\MovimientoInventario;
use App\Models\Paciente;
use App\Models\Preparacion;
use App\Models\PreparacionConsumo;
use App\Models\PreparacionControlCalidad;
use App\Models\PreparacionDetalle;
use App\Models\PreparacionEntrega;
use App\Models\ServicioHospitalario;
use App\Models\TipoPreparacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DummyPreparacionesSeeder extends Seeder
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

        $pacientes = Paciente::all();
        $tipos = TipoPreparacion::all();
        $servicios = ServicioHospitalario::all();
        // Buscar lotes con saldo para que la demostración funcione
        $lotes = InventarioLote::with('medicamento')->where('estado', 1)->where('cantidad_actual', '>=', 1)->get();

        if ($lotes->count() < 1 || $pacientes->isEmpty() || $tipos->isEmpty() || $servicios->isEmpty()) {
            $this->command->error("Faltan datos base (lotes con saldo, pacientes, tipos o servicios) para correr la demostración.");
            return;
        }

        // Definimos los 5 estados para tener uno de cada uno en el Kanban
        $estados = ['PROGRAMADA', 'EN_PROCESO', 'CONTROL_CALIDAD', 'LIBERADA', 'ENTREGADA'];

        foreach ($estados as $i => $estado) {
            $paciente = $pacientes->random();
            $tipo = $tipos->random();
            $servicio = $servicios->random();
            $lote = $lotes->random();
            $medicamento = $lote->medicamento;

            // 1. Cabecera (Preparación)
            $prep = Preparacion::create([
                'paciente_id' => $paciente->id,
                'tipo_preparacion_id' => $tipo->id,
                'servicio_id' => $servicio->id,
                'fecha_programada' => now()->subHours(10 - $i),
                'fecha_inicio' => in_array($estado, ['EN_PROCESO', 'CONTROL_CALIDAD', 'LIBERADA', 'ENTREGADA']) ? now()->subHours(9 - $i) : null,
                'fecha_fin' => in_array($estado, ['LIBERADA', 'ENTREGADA']) ? now()->subHours(2 - $i) : null,
                'volumen_final' => rand(50, 250),
                'estado' => $estado,
                'usuario_preparador_id' => $user->id,
                'costo_total' => in_array($estado, ['LIBERADA', 'ENTREGADA']) ? rand(15000, 50000) : 0,
                'usuario_validador_id' => in_array($estado, ['LIBERADA', 'ENTREGADA']) ? $user->id : null,
            ]);

            // 2. Detalle (Dosis formulada por el médico)
            PreparacionDetalle::create([
                'preparacion_id' => $prep->id,
                'medicamento_id' => $medicamento->id,
                'dosis' => 1,
                'observaciones' => 'Dosis de prueba automatizada para estado: ' . $estado
            ]);

            // Si está EN_PROCESO o en un estado posterior, se le han registrado insumos (consumos de inventario)
            if (in_array($estado, ['EN_PROCESO', 'CONTROL_CALIDAD', 'LIBERADA', 'ENTREGADA'])) {
                $qty = 1;
                $costoU = collect([$lote->costo_unitario, 15000])->max(); // Tomar un costo base si el lote no tiene
                $costoT = $qty * $costoU;

                PreparacionConsumo::create([
                    'preparacion_id' => $prep->id,
                    'inventario_lote_id' => $lote->id,
                    'medicamento_id' => $medicamento->id,
                    'lote' => $lote->lote,
                    'fecha_vencimiento' => $lote->fecha_vencimiento,
                    'cantidad_consumida' => $qty,
                    'costo_unitario' => $costoU,
                    'costo_total' => $costoT,
                ]);

                // Si está en CONTROL_CALIDAD o adelante, pasó por evaluación técnica y visual
                if (in_array($estado, ['CONTROL_CALIDAD', 'LIBERADA', 'ENTREGADA'])) {
                    PreparacionControlCalidad::create([
                        'preparacion_id' => $prep->id,
                        'fecha_control' => now()->subHours(1),
                        'aspecto_visual' => 'Aspecto normal, sin partículas',
                        'volumen_verificado' => rand(48, 252),
                        'ph' => rand(6, 8),
                        'cumple' => true,
                        'usuario_control_id' => $user->id,
                    ]);
                }

                // *** IMPORTANTE: KARDEX ***
                // Si la mezcla fue LIBERADA (o ENTREGADA), debemos restar efectivamente el inventario
                // tal cual lo hace nuestro código real de la farmacia para seguir la dinámica real
                if (in_array($estado, ['LIBERADA', 'ENTREGADA'])) {
                    $stockAnterior = $lote->cantidad_actual;
                    $lote->cantidad_actual -= $qty;
                    $lote->save();

                    $movData = [
                        'tipo_movimiento' => 'PRODUCCION',
                        'referencia_tipo' => 'Preparacion',
                        'referencia_id' => $prep->id,
                        'inventario_lote_id' => $lote->id,
                        'cantidad' => -$qty,
                        'stock_anterior' => $stockAnterior,
                        'stock_nuevo' => $lote->cantidad_actual,
                        'fecha_movimiento' => now(),
                        'usuario_id' => $user->id,
                        'observacion' => "Liberación de mezcla clínica {$prep->codigo} (Dummy)",
                    ];

                    // Verifica qué columnas existen para producción
                    if (Schema::hasColumn('movimientos_inventario', 'medicamento_id')) {
                        $movData['medicamento_id'] = $medicamento->id;
                    }
                    if (Schema::hasColumn('movimientos_inventario', 'lote_codigo')) {
                        $movData['lote_codigo'] = $lote->lote;
                    }
                    if (Schema::hasColumn('movimientos_inventario', 'fecha_vencimiento')) {
                        $movData['fecha_vencimiento'] = $lote->fecha_vencimiento;
                    }

                    MovimientoInventario::create($movData);
                }

                // Entrega al piso / paciente
                if ($estado === 'ENTREGADA') {
                    PreparacionEntrega::create([
                        'preparacion_id' => $prep->id,
                        'paciente_id' => $paciente->id,
                        'fecha_entrega' => now(),
                        'usuario_entrega_id' => $user->id,
                        'servicio_destino_id' => $servicio->id,
                        'recibido_por' => 'Jefe Enfermería Demo',
                    ]);
                }
            }
        }

        $this->command->info("¡Exitoso! Se crearon 5 preparaciones emulando todo el ciclo clínico (1 por cada estado en el Kanban).");
    }
}
