<?php

namespace Database\Seeders;

use App\Models\AjusteMotivo;
use Illuminate\Database\Seeder;

class AjusteMotivosSeeder extends Seeder
{
    public function run(): void
    {
        $motivos = [
            ['codigo' => 'AJM-001', 'nombre' => 'Conteo físico',         'tipo' => 'AMBOS',    'requiere_observacion' => true,  'requiere_aprobacion' => true],
            ['codigo' => 'AJM-002', 'nombre' => 'Error de digitación',   'tipo' => 'AMBOS',    'requiere_observacion' => true,  'requiere_aprobacion' => true],
            ['codigo' => 'AJM-003', 'nombre' => 'Pérdida',               'tipo' => 'NEGATIVO', 'requiere_observacion' => true,  'requiere_aprobacion' => true],
            ['codigo' => 'AJM-004', 'nombre' => 'Daño',                  'tipo' => 'NEGATIVO', 'requiere_observacion' => true,  'requiere_aprobacion' => true],
            ['codigo' => 'AJM-005', 'nombre' => 'Sobrante',              'tipo' => 'POSITIVO', 'requiere_observacion' => true,  'requiere_aprobacion' => true],
            ['codigo' => 'AJM-006', 'nombre' => 'Corrección auditoría',  'tipo' => 'AMBOS',    'requiere_observacion' => true,  'requiere_aprobacion' => true],
            ['codigo' => 'AJM-007', 'nombre' => 'Error de lote',         'tipo' => 'AMBOS',    'requiere_observacion' => true,  'requiere_aprobacion' => true],
        ];

        foreach ($motivos as $m) {
            AjusteMotivo::updateOrCreate(['codigo' => $m['codigo']], $m + ['estado' => true]);
        }
    }
}
