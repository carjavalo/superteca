<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Presentacion;
use App\Models\ViaAdministracion;

class LinkPresentacionesViaSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'Oral'        => 'VO',
            'Subcutánea'  => 'SC',
            'Subcutanea'  => 'SC',
            'Intramuscular' => 'IM',
            'Intravenosa' => 'IV',
            'Inhalatoria' => 'INH',
            'Tópica'      => 'TOP',
            'Topica'      => 'TOP',
            'Rectal'      => 'REC',
            'Oftálmica'   => 'OFT',
        ];

        foreach (Presentacion::all() as $p) {
            if (! $p->via_administracion) continue;
            $codigo = $map[$p->via_administracion] ?? null;
            if (! $codigo) continue;
            $via = ViaAdministracion::where('codigo', $codigo)->first();
            if ($via) {
                $p->via_administracion_id = $via->id;
                $p->save();
                $this->command->info($p->nombre . ' -> ' . $via->nombre);
            }
        }
    }
}
