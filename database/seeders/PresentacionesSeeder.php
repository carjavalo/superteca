<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Medicamento;
use Carbon\Carbon;

class PresentacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Medicamentos según DB: 1: Acetaminofén, 2: Amoxicilina, 3: Insulina, 4: Diazepam, 5: Omeprazol.

        DB::table('presentaciones')->insert([
            [
                'medicamento_id' => 1, // Acetaminofén
                'codigo' => 'PRE-001',
                'nombre' => 'Caja x 100 Tabletas Blíster',
                'concentracion' => 500.00,
                'unidad_concentracion' => 'mg',
                'volumen' => null,
                'unidad_volumen' => null,
                'forma_farmaceutica' => 'Tableta',
                'via_administracion' => 'Oral',
                'tipo_envase' => 'Caja/Blíster',
                'requiere_refrigeracion' => 0,
                'foto' => null,
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'medicamento_id' => 2, // Amoxicilina
                'codigo' => 'PRE-002',
                'nombre' => 'Multidosis Frasco x 100 ml (Polvo suspensión)',
                'concentracion' => 250.00,
                'unidad_concentracion' => 'mg',
                'volumen' => 100.00,
                'unidad_volumen' => 'ml',
                'forma_farmaceutica' => 'Polvo para suspensión',
                'via_administracion' => 'Oral',
                'tipo_envase' => 'Frasco PET',
                'requiere_refrigeracion' => 0,
                'foto' => null,
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'medicamento_id' => 3, // Insulina Glargina
                'codigo' => 'PRE-003',
                'nombre' => 'Lantus Solostar Pen Prellenado x 3ml',
                'concentracion' => 100.00,
                'unidad_concentracion' => 'UI/ml',
                'volumen' => 3.00,
                'unidad_volumen' => 'ml',
                'forma_farmaceutica' => 'Solución inyectable',
                'via_administracion' => 'Subcutánea',
                'tipo_envase' => 'Lapicero precargado',
                'requiere_refrigeracion' => 1,
                'foto' => null,
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'medicamento_id' => 4, // Diazepam
                'codigo' => 'PRE-004',
                'nombre' => 'Caja x 30 Tabletas (Envase clínico)',
                'concentracion' => 5.00,
                'unidad_concentracion' => 'mg',
                'volumen' => null,
                'unidad_volumen' => null,
                'forma_farmaceutica' => 'Tableta ranurada',
                'via_administracion' => 'Oral',
                'tipo_envase' => 'Caja/Blíster ALU-PVC',
                'requiere_refrigeracion' => 0,
                'foto' => null,
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'medicamento_id' => 5, // Omeprazol
                'codigo' => 'PRE-005',
                'nombre' => 'Frasco Gotero x 30 Cápsulas con microgránulos',
                'concentracion' => 20.00,
                'unidad_concentracion' => 'mg',
                'volumen' => null,
                'unidad_volumen' => null,
                'forma_farmaceutica' => 'Cápsula dura',
                'via_administracion' => 'Oral',
                'tipo_envase' => 'Frasco PAD',
                'requiere_refrigeracion' => 0,
                'foto' => null,
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}
