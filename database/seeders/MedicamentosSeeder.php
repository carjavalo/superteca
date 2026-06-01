<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Medicamento;

class MedicamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicamentos = [
            [
                'codigo' => 'MED-001',
                'nombre' => 'Acetaminofén 500mg',
                'nombre_generico' => 'Paracetamol',
                'concentracion' => '500',
                'unidad_medida' => 'mg',
                'forma_farmaceutica' => 'Tableta',
                'via_administracion' => 'Oral',
                'laboratorio' => 'Genfar',
                'registro_invima' => 'INV-123456',
                'requiere_refrigeracion' => false,
                'fotoproteccion' => false,
                'alto_riesgo' => false,
                'controlado' => false,
                'estabilidad_horas' => null,
                'estado' => true,
            ],
            [
                'codigo' => 'MED-002',
                'nombre' => 'Amoxicilina 250mg/5ml',
                'nombre_generico' => 'Amoxicilina',
                'concentracion' => '250',
                'unidad_medida' => 'mg/5ml',
                'forma_farmaceutica' => 'Suspensión Oral',
                'via_administracion' => 'Oral',
                'laboratorio' => 'Bayer',
                'registro_invima' => 'INV-654321',
                'requiere_refrigeracion' => true,
                'fotoproteccion' => false,
                'alto_riesgo' => false,
                'controlado' => false,
                'estabilidad_horas' => 336,
                'estado' => true,
            ],
            [
                'codigo' => 'MED-003',
                'nombre' => 'Insulina Glargina 100 UI',
                'nombre_generico' => 'Insulina',
                'concentracion' => '100',
                'unidad_medida' => 'UI/ml',
                'forma_farmaceutica' => 'Solución inyectable',
                'via_administracion' => 'Subcutánea',
                'laboratorio' => 'Sanofi',
                'registro_invima' => 'INV-987654',
                'requiere_refrigeracion' => true,
                'fotoproteccion' => true,
                'alto_riesgo' => true,
                'controlado' => false,
                'estabilidad_horas' => 672,
                'estado' => true,
            ],
            [
                'codigo' => 'MED-004',
                'nombre' => 'Diazepam 5mg',
                'nombre_generico' => 'Diazepam',
                'concentracion' => '5',
                'unidad_medida' => 'mg',
                'forma_farmaceutica' => 'Tableta',
                'via_administracion' => 'Oral',
                'laboratorio' => 'Roche',
                'registro_invima' => 'INV-112233',
                'requiere_refrigeracion' => false,
                'fotoproteccion' => true,
                'alto_riesgo' => false,
                'controlado' => true,
                'estabilidad_horas' => null,
                'estado' => true,
            ],
            [
                'codigo' => 'MED-005',
                'nombre' => 'Omeprazol 20mg',
                'nombre_generico' => 'Omeprazol',
                'concentracion' => '20',
                'unidad_medida' => 'mg',
                'forma_farmaceutica' => 'Cápsula',
                'via_administracion' => 'Oral',
                'laboratorio' => 'MK',
                'registro_invima' => 'INV-556677',
                'requiere_refrigeracion' => false,
                'fotoproteccion' => true,
                'alto_riesgo' => false,
                'controlado' => false,
                'estabilidad_horas' => null,
                'estado' => true,
            ],
        ];

        foreach ($medicamentos as $medicamento) {
            Medicamento::updateOrCreate(['codigo' => $medicamento['codigo']], $medicamento);
        }
    }
}
