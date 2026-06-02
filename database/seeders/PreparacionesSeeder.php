<?php

namespace Database\Seeders;

use App\Models\Paciente;
use App\Models\ServicioHospitalario;
use App\Models\TipoPreparacion;
use Illuminate\Database\Seeder;

class PreparacionesSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Nutrición Parenteral',  'codigo' => 'NPT',  'color' => '#0ea5e9'],
            ['nombre' => 'Mezcla Intravenosa',    'codigo' => 'IV',   'color' => '#22c55e'],
            ['nombre' => 'Citostáticos',          'codigo' => 'CITO', 'color' => '#ef4444'],
            ['nombre' => 'Antibióticos',          'codigo' => 'ATB',  'color' => '#a855f7'],
            ['nombre' => 'Mezclas Pediátricas',   'codigo' => 'PED',  'color' => '#f59e0b'],
            ['nombre' => 'Magistrales',           'codigo' => 'MAG',  'color' => '#64748b'],
        ];
        foreach ($tipos as $t) {
            TipoPreparacion::firstOrCreate(['nombre' => $t['nombre']], $t + ['estado' => true]);
        }

        $servicios = [
            ['nombre' => 'UCI Adultos',     'codigo' => 'UCI-A'],
            ['nombre' => 'UCI Pediátrica',  'codigo' => 'UCI-P'],
            ['nombre' => 'UCI Neonatal',    'codigo' => 'UCIN'],
            ['nombre' => 'Urgencias',       'codigo' => 'URG'],
            ['nombre' => 'Hospitalización', 'codigo' => 'HOSP'],
            ['nombre' => 'Oncología',       'codigo' => 'ONCO'],
        ];
        foreach ($servicios as $s) {
            ServicioHospitalario::firstOrCreate(['nombre' => $s['nombre']], $s + ['estado' => true]);
        }

        $servicio = ServicioHospitalario::first();
        $pacientes = [
            ['documento' => '1001001001', 'nombres' => 'Juan',   'apellidos' => 'Pérez Gómez',  'sexo' => 'M', 'cama' => 'A-101'],
            ['documento' => '1002002002', 'nombres' => 'María',  'apellidos' => 'López Ruiz',   'sexo' => 'F', 'cama' => 'B-203'],
            ['documento' => '1003003003', 'nombres' => 'Carlos', 'apellidos' => 'Martínez Ríos','sexo' => 'M', 'cama' => 'C-305'],
        ];
        foreach ($pacientes as $p) {
            Paciente::firstOrCreate(['documento' => $p['documento']], $p + [
                'tipo_documento' => 'CC',
                'servicio_id'    => $servicio?->id,
                'estado'         => true,
            ]);
        }
    }
}
