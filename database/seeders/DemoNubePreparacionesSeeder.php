<?php

namespace Database\Seeders;

use App\Models\InventarioLote;
use App\Models\MovimientoInventario;
use App\Models\Paciente;
use App\Models\Preparacion;
use App\Models\PreparacionConsumo;
use App\Models\PreparacionControlCalidad;
use App\Models\PreparacionDetalle;
use App\Models\ServicioHospitalario;
use App\Models\TipoPreparacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DemoNubePreparacionesSeeder extends Seeder
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

        $paciente = Paciente::inRandomOrder()->first();
        $tipo = TipoPreparacion::inRandomOrder()->first();
        $servicio = ServicioHospitalario::inRandomOrder()->first();
        
        // Buscar un lote con saldo mayor o igual a 2 para no romper el stock
        $lote = InventarioLote::with('medicamento')->where('estado', 1)->where('cantidad_actual', '>=', 2)->inRandomOrder()->first();

        if (!$lote || !$paciente || !$tipo || !$servicio) {
            $this->command->error("Faltan datos base en producción (pacientes, tipos, servicios o lotes con stock >= 2).");
            return;
        }

        $medicamento = $lote->medicamento;
        $costoU = collect([$lote->costo_unitario, 5000])->max();

        // ------------------------------------------------------------------
        // REGISTRO 1: EN PROCESO (Se está preparando, consume pero aún no mueve Kardex)
        // ------------------------------------------------------------------
        $prep1 = Preparacion::create([
            'paciente_id' => $paciente->id,
            'tipo_preparacion_id' => $tipo->id,
            'servicio_id' => $servicio->id,
            'fecha_programada' => now(),
            'fecha_inicio' => now(),
            'estado' => 'EN_PROCESO',
            'usuario_preparador_id' => $user->id,
            'volumen_final' => 100,
        ]);

        PreparacionDetalle::create([
            'preparacion_id' => $prep1->id,
            'medicamento_id' => $medicamento->id,
            'dosis' => 1,
            'observaciones' => 'Dosis de demostración Nube - En Proceso'
        ]);

        PreparacionConsumo::create([
            'preparacion_id' => $prep1->id,
            'inventario_lote_id' => $lote->id,
            'medicamento_id' => $medicamento->id,
            'lote' => $lote->lote,
            'fecha_vencimiento' => $lote->fecha_vencimiento,
            'cantidad_consumida' => 1,
            'costo_unitario' => $costoU,
            'costo_total' => $costoU,
        ]);

        // ------------------------------------------------------------------
        // REGISTRO 2: LIBERADA (Terminada, control de calidad OK, descuenta Kardex)
        // ------------------------------------------------------------------
        $prep2 = Preparacion::create([
            'paciente_id' => $paciente->id,
            'tipo_preparacion_id' => $tipo->id,
            'servicio_id' => $servicio->id,
            'fecha_programada' => now()->subDay(),
            'fecha_inicio' => now()->subDay(),
            'fecha_fin' => now(),
            'estado' => 'LIBERADA',
            'usuario_preparador_id' => $user->id,
            'usuario_validador_id' => $user->id,
            'costo_total' => $costoU * 1.5,
            'volumen_final' => 100,
        ]);

        PreparacionDetalle::create([
            'preparacion_id' => $prep2->id,
            'medicamento_id' => $medicamento->id,
            'dosis' => 1,
            'observaciones' => 'Dosis de demostración Nube - Liberada'
        ]);

        PreparacionConsumo::create([
            'preparacion_id' => $prep2->id,
            'inventario_lote_id' => $lote->id,
            'medicamento_id' => $medicamento->id,
            'lote' => $lote->lote,
            'fecha_vencimiento' => $lote->fecha_vencimiento,
            'cantidad_consumida' => 1,
            'costo_unitario' => $costoU,
            'costo_total' => $costoU,
        ]);

        PreparacionControlCalidad::create([
            'preparacion_id' => $prep2->id,
            'fecha_control' => now(),
            'aspecto_visual' => 'Mezcla sin partículas',
            'volumen_verificado' => 100,
            'ph' => 7.2,
            'cumple' => true,
            'usuario_control_id' => $user->id,
        ]);

        // Mover Kardex e inventario de producción real por liberación
        $stockAnterior = $lote->cantidad_actual;
        $lote->cantidad_actual -= 1;
        $lote->save();

        $movData = [
            'tipo_movimiento' => 'PRODUCCION',
            'referencia_tipo' => 'Preparacion',
            'referencia_id' => $prep2->id,
            'inventario_lote_id' => $lote->id,
            'cantidad' => -1,
            'stock_anterior' => $stockAnterior,
            'stock_nuevo' => $lote->cantidad_actual,
            'fecha_movimiento' => now(),
            'usuario_id' => $user->id,
            'observacion' => "Liberación de mezcla clínica {$prep2->codigo} (Demo Nube)",
        ];

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

        $this->command->info("¡Exito en la Nube! Se crearon 2 preparaciones: Una 'En Proceso' y otra 'Liberada' (con descuento en Kardex).");
    }
}
