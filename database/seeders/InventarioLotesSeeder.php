<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InventarioLote;
use App\Models\Medicamento;
use Carbon\Carbon;

class InventarioLotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicamentos = Medicamento::take(2)->get();

        if ($medicamentos->count() > 0) {
            $med1 = $medicamentos->first();
            InventarioLote::create([
                'medicamento_id' => $med1->id,
                'lote' => 'LOTE-AX901',
                'fecha_vencimiento' => Carbon::now()->addMonths(12),
                'fecha_ingreso' => Carbon::now()->subDays(5),
                'cantidad_inicial' => 500,
                'cantidad_actual' => 450,
                'unidad_medida' => 'Cajas',
                'costo_unitario' => 12.50,
                'ubicacion' => 'Estante A-1',
                'temperatura_min' => 15.0,
                'temperatura_max' => 25.0,
                'estado' => 1,
            ]);
        }

        if ($medicamentos->count() > 1) {
            $med2 = $medicamentos->last();
            InventarioLote::create([
                'medicamento_id' => $med2->id,
                'lote' => 'LOTE-BX205',
                'fecha_vencimiento' => Carbon::now()->addDays(15), // a punto de vencer para ver el kardex chulo
                'fecha_ingreso' => Carbon::now()->subMonths(6),
                'cantidad_inicial' => 1000,
                'cantidad_actual' => 120,
                'unidad_medida' => 'Frascos',
                'costo_unitario' => 8.75,
                'ubicacion' => 'Estante B-3 (Refrigerado)',
                'temperatura_min' => 2.0,
                'temperatura_max' => 8.0,
                'estado' => 1,
            ]);
        }
    }
}
