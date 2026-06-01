<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presentacion;
use App\Models\FormaFarmaceutica;

class LinkPresentacionesFormaSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Tableta'               => 'Tableta',
            'Tableta ranurada'      => 'Tableta',
            'Cápsula'               => 'Cápsula',
            'Cápsula dura'          => 'Cápsula',
            'Solución inyectable'   => 'Jeringa Prellenada',
            'Polvo para suspensión' => 'Suspensión Oral',
            'Suspensión Oral'       => 'Suspensión Oral',
            'Solución Oral'         => 'Solución Oral',
        ];

        foreach (Presentacion::all() as $p) {
            if (! $p->forma_farmaceutica) {
                continue;
            }
            $name = $map[$p->forma_farmaceutica] ?? $p->forma_farmaceutica;
            $ff = FormaFarmaceutica::where('nombre', $name)->first();
            if ($ff) {
                $p->forma_farmaceutica_id = $ff->id;
                $p->save();
                $this->command->info($p->nombre . ' -> ' . $ff->nombre);
            }
        }
    }
}
