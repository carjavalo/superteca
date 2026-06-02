<?php

namespace Database\Seeders;

use App\Models\Formula;
use App\Models\FormulaDetalle;
use App\Models\Medicamento;
use Illuminate\Database\Seeder;

class FormulasDemoSeeder extends Seeder
{
    public function run(): void
    {
        $medicamentos = Medicamento::all();
        if ($medicamentos->isEmpty()) {
            $this->command->error("No hay medicamentos en la base.");
            return;
        }

        $formulasBase = [
            [
                'nombre' => 'Vancomicina IV 1g',
                'tipo' => 'ANTIBIOTICO',
                'descripcion' => 'Antibiótico glucopéptido para infecciones por Gram positivos resistentes',
                'volumen' => 250,
                'estabilidad' => 24,
                'temp_min' => 2, 'temp_max' => 8,
                'refri' => true,
                'detalles' => 2,
            ],
            [
                'nombre' => 'NPT Adulto 2000 kcal',
                'tipo' => 'NUTRICION_PARENTERAL',
                'descripcion' => 'Nutrición parenteral total para adulto con requerimiento calórico estándar',
                'volumen' => 2000,
                'estabilidad' => 24,
                'temp_min' => 2, 'temp_max' => 8,
                'refri' => true,
                'detalles' => 4,
            ],
            [
                'nombre' => 'Quimioterapia CHOP',
                'tipo' => 'ONCOLOGIA',
                'descripcion' => 'Esquema oncológico para linfoma no Hodgkin',
                'volumen' => 500,
                'estabilidad' => 12,
                'temp_min' => 15, 'temp_max' => 25,
                'refri' => false,
                'detalles' => 3,
            ],
            [
                'nombre' => 'Suspensión Pediátrica de Amoxicilina',
                'tipo' => 'PEDIATRIA',
                'descripcion' => 'Reconstitución magistral para uso pediátrico ambulatorio',
                'volumen' => 100,
                'estabilidad' => 168,
                'temp_min' => 2, 'temp_max' => 25,
                'refri' => true,
                'detalles' => 2,
            ],
            [
                'nombre' => 'Solución Magistral de Omeprazol',
                'tipo' => 'MAGISTRAL',
                'descripcion' => 'Preparación magistral oral para pacientes con sondas',
                'volumen' => 60,
                'estabilidad' => 72,
                'temp_min' => 2, 'temp_max' => 8,
                'refri' => true,
                'detalles' => 2,
            ],
        ];

        foreach ($formulasBase as $base) {
            $formula = Formula::create([
                'nombre' => $base['nombre'],
                'tipo_formula' => $base['tipo'],
                'descripcion' => $base['descripcion'],
                'volumen_final' => $base['volumen'],
                'tiempo_estabilidad_horas' => $base['estabilidad'],
                'temperatura_min' => $base['temp_min'],
                'temperatura_max' => $base['temp_max'],
                'requiere_refrigeracion' => $base['refri'],
                'observaciones' => 'Fórmula demostrativa generada automáticamente.',
                'estado' => true,
            ]);

            $componentes = $medicamentos->random(min($base['detalles'], $medicamentos->count()));
            $orden = 1;
            foreach ($componentes as $m) {
                FormulaDetalle::create([
                    'formula_id' => $formula->id,
                    'medicamento_id' => $m->id,
                    'dosis' => rand(50, 1000),
                    'orden_preparacion' => $orden++,
                    'obligatorio' => true,
                    'observaciones' => $orden === 2 ? 'Principio activo' : 'Diluyente o coadyuvante',
                ]);
            }
        }

        $this->command->info("¡Listo! Se crearon ".count($formulasBase)." fórmulas de demostración con sus componentes.");
    }
}
