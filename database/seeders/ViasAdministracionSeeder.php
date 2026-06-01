<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ViasAdministracionSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('vias_administracion')->insert([
            [
                'codigo'=>'IV', 'nombre'=>'Intravenosa', 'nombre_corto'=>'IV',
                'descripcion'=>'Administración directa al torrente sanguíneo a través de una vena periférica o central.',
                'tipo'=>'PARENTERAL',
                'esteril_requerido'=>1,'requiere_bomba_infusion'=>1,'requiere_filtro'=>0,
                'permite_bolo'=>1,'permite_infusion_continua'=>1,
                'velocidad_min_ml_h'=>1.00,'velocidad_max_ml_h'=>999.00,'osmolaridad_max'=>900.00,
                'fotosensible'=>0,'requiere_monitorizacion'=>1,
                'riesgo_clinico'=>'ALTO','color_identificacion'=>'#ef4444','icono'=>'💉',
                'observaciones'=>'Verificar permeabilidad de la vía antes de administrar. Compatible con bombas volumétricas.',
                'estado'=>1,'created_at'=>$now,'updated_at'=>$now,
            ],
            [
                'codigo'=>'IM', 'nombre'=>'Intramuscular', 'nombre_corto'=>'IM',
                'descripcion'=>'Administración dentro del tejido muscular profundo.',
                'tipo'=>'PARENTERAL',
                'esteril_requerido'=>1,'requiere_bomba_infusion'=>0,'requiere_filtro'=>0,
                'permite_bolo'=>1,'permite_infusion_continua'=>0,
                'velocidad_min_ml_h'=>null,'velocidad_max_ml_h'=>null,'osmolaridad_max'=>null,
                'fotosensible'=>0,'requiere_monitorizacion'=>0,
                'riesgo_clinico'=>'MEDIO','color_identificacion'=>'#f59e0b','icono'=>'🩹',
                'observaciones'=>'Volumen máximo por sitio 5 ml (adultos). Rotar sitios de aplicación.',
                'estado'=>1,'created_at'=>$now,'updated_at'=>$now,
            ],
            [
                'codigo'=>'SC', 'nombre'=>'Subcutánea', 'nombre_corto'=>'SC',
                'descripcion'=>'Administración en el tejido adiposo subcutáneo, absorción lenta y sostenida.',
                'tipo'=>'PARENTERAL',
                'esteril_requerido'=>1,'requiere_bomba_infusion'=>0,'requiere_filtro'=>0,
                'permite_bolo'=>1,'permite_infusion_continua'=>1,
                'velocidad_min_ml_h'=>null,'velocidad_max_ml_h'=>null,'osmolaridad_max'=>null,
                'fotosensible'=>0,'requiere_monitorizacion'=>0,
                'riesgo_clinico'=>'BAJO','color_identificacion'=>'#3b82f6','icono'=>'🧴',
                'observaciones'=>'Rotar sitios de aplicación (abdomen, muslo, brazo).',
                'estado'=>1,'created_at'=>$now,'updated_at'=>$now,
            ],
            [
                'codigo'=>'VO', 'nombre'=>'Oral', 'nombre_corto'=>'VO',
                'descripcion'=>'Administración a través de la boca, absorción gastrointestinal.',
                'tipo'=>'ENTERAL',
                'esteril_requerido'=>0,'requiere_bomba_infusion'=>0,'requiere_filtro'=>0,
                'permite_bolo'=>1,'permite_infusion_continua'=>0,
                'velocidad_min_ml_h'=>null,'velocidad_max_ml_h'=>null,'osmolaridad_max'=>null,
                'fotosensible'=>0,'requiere_monitorizacion'=>0,
                'riesgo_clinico'=>'BAJO','color_identificacion'=>'#10b981','icono'=>'💊',
                'observaciones'=>'Verificar si debe administrarse con o sin alimentos.',
                'estado'=>1,'created_at'=>$now,'updated_at'=>$now,
            ],
            [
                'codigo'=>'IT', 'nombre'=>'Intratecal', 'nombre_corto'=>'IT',
                'descripcion'=>'Administración directa en el espacio subaracnoideo (líquido cefalorraquídeo).',
                'tipo'=>'PARENTERAL',
                'esteril_requerido'=>1,'requiere_bomba_infusion'=>0,'requiere_filtro'=>1,
                'permite_bolo'=>1,'permite_infusion_continua'=>0,
                'velocidad_min_ml_h'=>null,'velocidad_max_ml_h'=>null,'osmolaridad_max'=>null,
                'fotosensible'=>0,'requiere_monitorizacion'=>1,
                'riesgo_clinico'=>'CRITICO','color_identificacion'=>'#7c2d12','icono'=>'🧠',
                'observaciones'=>'Requiere DOBLE validación. Estrictamente sin conservantes. Volumen máximo 10 ml.',
                'estado'=>1,'created_at'=>$now,'updated_at'=>$now,
            ],
            [
                'codigo'=>'EP', 'nombre'=>'Epidural', 'nombre_corto'=>'EP',
                'descripcion'=>'Administración en el espacio epidural para analgesia o anestesia regional.',
                'tipo'=>'PARENTERAL',
                'esteril_requerido'=>1,'requiere_bomba_infusion'=>1,'requiere_filtro'=>1,
                'permite_bolo'=>1,'permite_infusion_continua'=>1,
                'velocidad_min_ml_h'=>2.00,'velocidad_max_ml_h'=>20.00,'osmolaridad_max'=>null,
                'fotosensible'=>0,'requiere_monitorizacion'=>1,
                'riesgo_clinico'=>'CRITICO','color_identificacion'=>'#991b1b','icono'=>'🔴',
                'observaciones'=>'Solo medicamentos sin conservantes. Monitorización continua de signos vitales.',
                'estado'=>1,'created_at'=>$now,'updated_at'=>$now,
            ],
            [
                'codigo'=>'INH', 'nombre'=>'Inhalatoria', 'nombre_corto'=>'INH',
                'descripcion'=>'Administración por vía aérea mediante nebulización o aerosol presurizado.',
                'tipo'=>'RESPIRATORIA',
                'esteril_requerido'=>0,'requiere_bomba_infusion'=>0,'requiere_filtro'=>0,
                'permite_bolo'=>0,'permite_infusion_continua'=>0,
                'velocidad_min_ml_h'=>null,'velocidad_max_ml_h'=>null,'osmolaridad_max'=>null,
                'fotosensible'=>0,'requiere_monitorizacion'=>0,
                'riesgo_clinico'=>'BAJO','color_identificacion'=>'#06b6d4','icono'=>'🌬',
                'observaciones'=>'Verificar técnica del paciente. Enjuagar boca tras corticoides inhalados.',
                'estado'=>1,'created_at'=>$now,'updated_at'=>$now,
            ],
            [
                'codigo'=>'OFT', 'nombre'=>'Oftálmica', 'nombre_corto'=>'OFT',
                'descripcion'=>'Aplicación tópica en el ojo en forma de gotas o ungüento.',
                'tipo'=>'OFTALMICA',
                'esteril_requerido'=>1,'requiere_bomba_infusion'=>0,'requiere_filtro'=>0,
                'permite_bolo'=>0,'permite_infusion_continua'=>0,
                'velocidad_min_ml_h'=>null,'velocidad_max_ml_h'=>null,'osmolaridad_max'=>null,
                'fotosensible'=>1,'requiere_monitorizacion'=>0,
                'riesgo_clinico'=>'MEDIO','color_identificacion'=>'#a78bfa','icono'=>'👁',
                'observaciones'=>'Lavarse las manos antes. No tocar el aplicador con el ojo.',
                'estado'=>1,'created_at'=>$now,'updated_at'=>$now,
            ],
            [
                'codigo'=>'TOP', 'nombre'=>'Tópica', 'nombre_corto'=>'TOP',
                'descripcion'=>'Aplicación sobre la piel para efecto local.',
                'tipo'=>'TOPICA',
                'esteril_requerido'=>0,'requiere_bomba_infusion'=>0,'requiere_filtro'=>0,
                'permite_bolo'=>0,'permite_infusion_continua'=>0,
                'velocidad_min_ml_h'=>null,'velocidad_max_ml_h'=>null,'osmolaridad_max'=>null,
                'fotosensible'=>0,'requiere_monitorizacion'=>0,
                'riesgo_clinico'=>'BAJO','color_identificacion'=>'#84cc16','icono'=>'🧴',
                'observaciones'=>'Aplicar capa delgada en piel limpia y seca.',
                'estado'=>1,'created_at'=>$now,'updated_at'=>$now,
            ],
            [
                'codigo'=>'REC', 'nombre'=>'Rectal', 'nombre_corto'=>'REC',
                'descripcion'=>'Administración por vía rectal (supositorios o enemas).',
                'tipo'=>'RECTAL',
                'esteril_requerido'=>0,'requiere_bomba_infusion'=>0,'requiere_filtro'=>0,
                'permite_bolo'=>1,'permite_infusion_continua'=>0,
                'velocidad_min_ml_h'=>null,'velocidad_max_ml_h'=>null,'osmolaridad_max'=>null,
                'fotosensible'=>0,'requiere_monitorizacion'=>0,
                'riesgo_clinico'=>'BAJO','color_identificacion'=>'#facc15','icono'=>'💊',
                'observaciones'=>'Útil cuando la vía oral no está disponible.',
                'estado'=>1,'created_at'=>$now,'updated_at'=>$now,
            ],
        ]);
    }
}
