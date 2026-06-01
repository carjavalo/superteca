<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presentacion;
use App\Models\InventarioLote;
use App\Models\UnidadMedida;

class LinkUnidadesMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'mg'   => 'MG',
            'MG'   => 'MG',
            'mcg'  => 'MCG',
            'g'    => 'G',
            'kg'   => 'KG',
            'ml'   => 'ML',
            'mL'   => 'ML',
            'ML'   => 'ML',
            'L'    => 'L',
            'UI'   => 'UI',
            'ui'   => 'UI',
            '%'    => 'PCT',
            'mg/mL'=> 'MGML',
            'mEq'  => 'MEQML',
            'gtt'  => 'GTT',
            'tab'  => 'TAB',
            'cap'  => 'CAP',
        ];

        $resolve = function (?string $text) use ($map) {
            if (! $text) return null;
            $key = trim($text);
            if (isset($map[$key])) return UnidadMedida::where('codigo', $map[$key])->value('id');
            $u = UnidadMedida::where('abreviatura', $key)->orWhere('codigo', strtoupper($key))->first();
            return $u?->id;
        };

        foreach (Presentacion::all() as $p) {
            $changed = false;
            if (! $p->unidad_medida_id && $p->unidad_concentracion) {
                if ($id = $resolve($p->unidad_concentracion)) {
                    $p->unidad_medida_id = $id;
                    $changed = true;
                }
            }
            if (property_exists($p, 'unidad_volumen_id') || in_array('unidad_volumen_id', $p->getFillable())) {
                if (! $p->unidad_volumen_id && $p->unidad_volumen) {
                    if ($id = $resolve($p->unidad_volumen)) {
                        $p->unidad_volumen_id = $id;
                        $changed = true;
                    }
                }
            }
            if ($changed) {
                $p->save();
                $this->command->info("Presentacion #{$p->id} {$p->nombre} → unidad vinculada");
            }
        }

        foreach (InventarioLote::all() as $l) {
            if (! $l->unidad_medida_id && $l->unidad_medida) {
                if ($id = $resolve($l->unidad_medida)) {
                    $l->unidad_medida_id = $id;
                    $l->save();
                    $this->command->info("Lote #{$l->id} → unidad vinculada");
                }
            }
        }
    }
}
