<?php

namespace Database\Seeders;

use App\Models\DispensacionEntrega;
use App\Models\DispensacionEntregaDetalle;
use App\Models\DispensacionEntregaLote;
use App\Models\InventarioLote;
use App\Models\Paciente;
use App\Models\ServicioHospitalario;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DispensacionEntregasDemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) { $this->command->warn('No hay usuarios. Saltando seeder.'); return; }

        $pacientes = Paciente::limit(10)->get();
        $servicios = ServicioHospitalario::where('estado', 1)->get();
        $lotes = InventarioLote::where('estado', 1)->where('cantidad_actual', '>', 5)->limit(20)->get();

        if ($pacientes->isEmpty() && $servicios->isEmpty()) {
            $this->command->warn('No hay pacientes ni servicios. Saltando.');
            return;
        }
        if ($lotes->isEmpty()) {
            $this->command->warn('No hay lotes con stock. Saltando.');
            return;
        }

        $tipos = ['PACIENTE','SERVICIO','UCI','URGENCIAS','HOSPITALIZACION'];

        for ($i = 0; $i < 8; $i++) {
            DB::transaction(function () use ($i, $user, $pacientes, $servicios, $lotes, $tipos) {
                $tipo = $tipos[$i % count($tipos)];
                $paciente = $tipo === 'PACIENTE' && $pacientes->count() ? $pacientes->random() : null;
                $servicio = $tipo !== 'PACIENTE' && $servicios->count() ? $servicios->random() : null;

                $entrega = DispensacionEntrega::create([
                    'tipo_entrega'           => $tipo,
                    'paciente_id'            => $paciente?->id,
                    'servicio_id'            => $servicio?->id,
                    'fecha_entrega'          => now()->subDays(rand(0, 5))->subHours(rand(0, 8)),
                    'usuario_dispensador_id' => $user->id,
                    'recibe_nombre'          => $paciente ? trim($paciente->nombres.' '.$paciente->apellidos) : ('Enfermería '.($servicio->nombre ?? '')),
                    'observaciones'          => 'Entrega demo #'.($i+1),
                    'estado'                 => 'PENDIENTE',
                ]);

                $usados = [];
                $costoEntrega = 0;
                $itemsCount = rand(1, 3);

                for ($j = 0; $j < $itemsCount; $j++) {
                    $lote = $lotes->whereNotIn('id', $usados)->random();
                    $usados[] = $lote->id;

                    $cant = min((float) $lote->cantidad_actual, rand(1, 3));
                    $cu = (float) ($lote->costo_unitario ?: 0);

                    $det = DispensacionEntregaDetalle::create([
                        'entrega_id'       => $entrega->id,
                        'medicamento_id'   => $lote->medicamento_id,
                        'presentacion_id'  => $lote->presentacion_id,
                        'cantidad'         => $cant,
                        'unidad_medida_id' => $lote->unidad_medida_id ?? null,
                        'costo_unitario'   => $cu,
                        'costo_total'      => $cu * $cant,
                    ]);

                    DispensacionEntregaLote::create([
                        'entrega_detalle_id' => $det->id,
                        'inventario_lote_id' => $lote->id,
                        'lote'               => $lote->lote,
                        'fecha_vencimiento'  => $lote->fecha_vencimiento,
                        'cantidad_entregada' => $cant,
                        'costo_unitario'     => $cu,
                    ]);

                    $costoEntrega += $cu * $cant;
                }

                $entrega->update(['costo_total' => $costoEntrega]);
            });
        }

        $this->command->info('Seeder de Dispensación · Entregas creado: 8 entregas demo en estado Pendiente.');
    }
}
