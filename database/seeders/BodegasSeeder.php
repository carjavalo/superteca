<?php

namespace Database\Seeders;

use App\Models\Bodega;
use Illuminate\Database\Seeder;

class BodegasSeeder extends Seeder
{
    public function run(): void
    {
        $bodegas = [
            ['codigo' => 'BOD-001', 'nombre' => 'Bodega Principal',       'tipo' => 'CENTRAL',  'ubicacion' => 'Piso 1 — Almacén General'],
            ['codigo' => 'BOD-002', 'nombre' => 'Farmacia Central',       'tipo' => 'FARMACIA', 'ubicacion' => 'Piso 1 — Área de Farmacia'],
            ['codigo' => 'BOD-003', 'nombre' => 'Central de Mezclas',     'tipo' => 'MEZCLAS',  'ubicacion' => 'Piso 2 — Área Estéril'],
            ['codigo' => 'BOD-004', 'nombre' => 'Urgencias',              'tipo' => 'SERVICIO', 'ubicacion' => 'Piso 1 — Urgencias'],
            ['codigo' => 'BOD-005', 'nombre' => 'UCI Adultos',            'tipo' => 'SERVICIO', 'ubicacion' => 'Piso 3 — Cuidado Intensivo'],
            ['codigo' => 'BOD-006', 'nombre' => 'Hospitalización',        'tipo' => 'SERVICIO', 'ubicacion' => 'Piso 2 y 3 — Hospitalización'],
            ['codigo' => 'BOD-007', 'nombre' => 'Quirófanos',             'tipo' => 'SERVICIO', 'ubicacion' => 'Piso 2 — Cirugía'],
            ['codigo' => 'BOD-008', 'nombre' => 'Nevera de Biológicos',   'tipo' => 'NEVERA',   'ubicacion' => 'Farmacia Central — Refrigeración'],
        ];

        foreach ($bodegas as $b) {
            Bodega::firstOrCreate(['codigo' => $b['codigo']], $b);
        }
    }
}
