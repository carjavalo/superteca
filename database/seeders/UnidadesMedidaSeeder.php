<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\UnidadMedida;
use Carbon\Carbon;

class UnidadesMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Paso 1: insertar las bases (factor 1) para tener unidad_base_id
        $bases = [
            ['codigo'=>'G',  'nombre'=>'Gramo',     'abreviatura'=>'g',  'tipo'=>'PESO',        'simbolo'=>'g',  'precision_decimal'=>3, 'factor_conversion'=>1],
            ['codigo'=>'L',  'nombre'=>'Litro',     'abreviatura'=>'L',  'tipo'=>'VOLUMEN',     'simbolo'=>'L',  'precision_decimal'=>3, 'factor_conversion'=>1],
            ['codigo'=>'H',  'nombre'=>'Hora',      'abreviatura'=>'h',  'tipo'=>'TIEMPO',      'simbolo'=>'h',  'precision_decimal'=>2, 'factor_conversion'=>1],
            ['codigo'=>'C',  'nombre'=>'Celsius',   'abreviatura'=>'°C', 'tipo'=>'TEMPERATURA', 'simbolo'=>'°C', 'precision_decimal'=>1, 'factor_conversion'=>1],
            ['codigo'=>'UI', 'nombre'=>'Unidad internacional', 'abreviatura'=>'UI', 'tipo'=>'CANTIDAD', 'simbolo'=>'UI', 'precision_decimal'=>0, 'factor_conversion'=>1],
            ['codigo'=>'M2', 'nombre'=>'Metro cuadrado', 'abreviatura'=>'m²', 'tipo'=>'SUPERFICIE', 'simbolo'=>'m²', 'precision_decimal'=>2, 'factor_conversion'=>1],
            ['codigo'=>'MLH','nombre'=>'Mililitro por hora', 'abreviatura'=>'mL/h','tipo'=>'VELOCIDAD','simbolo'=>'mL/h','precision_decimal'=>2,'factor_conversion'=>1],
        ];

        foreach ($bases as $b) {
            DB::table('unidades_medida')->insert(array_merge($b, [
                'unidad_base_id'       => null,
                'permite_fracciones'   => 1,
                'activa_calculos'      => 1,
                'color_identificacion' => UnidadMedida::COLOR_TIPO[$b['tipo']] ?? null,
                'icono'                => UnidadMedida::ICONO_TIPO[$b['tipo']] ?? null,
                'estado'               => 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]));
        }

        $base = fn ($codigo) => UnidadMedida::where('codigo', $codigo)->value('id');
        $idG  = $base('G');
        $idL  = $base('L');
        $idH  = $base('H');
        $idMLH = $base('MLH');

        // Paso 2: derivadas
        $derivadas = [
            // Peso
            ['codigo'=>'MCG','nombre'=>'Microgramo','abreviatura'=>'mcg','tipo'=>'PESO','simbolo'=>'µg','precision_decimal'=>3,'factor_conversion'=>0.000001,'unidad_base_id'=>$idG,'observaciones'=>'1 g = 1.000.000 mcg'],
            ['codigo'=>'MG', 'nombre'=>'Miligramo','abreviatura'=>'mg','tipo'=>'PESO','simbolo'=>'mg','precision_decimal'=>3,'factor_conversion'=>0.001,'unidad_base_id'=>$idG,'observaciones'=>'1 g = 1000 mg'],
            ['codigo'=>'KG', 'nombre'=>'Kilogramo','abreviatura'=>'kg','tipo'=>'PESO','simbolo'=>'kg','precision_decimal'=>3,'factor_conversion'=>1000,'unidad_base_id'=>$idG,'observaciones'=>'1 kg = 1000 g'],

            // Volumen
            ['codigo'=>'ML', 'nombre'=>'Mililitro','abreviatura'=>'mL','tipo'=>'VOLUMEN','simbolo'=>'mL','precision_decimal'=>2,'factor_conversion'=>0.001,'unidad_base_id'=>$idL,'observaciones'=>'1 L = 1000 mL'],
            ['codigo'=>'GTT','nombre'=>'Gota','abreviatura'=>'gtt','tipo'=>'VOLUMEN','simbolo'=>'gtt','precision_decimal'=>0,'factor_conversion'=>0.00005,'unidad_base_id'=>$idL,'observaciones'=>'Aproximadamente 20 gtt = 1 mL'],

            // Tiempo
            ['codigo'=>'MIN','nombre'=>'Minuto','abreviatura'=>'min','tipo'=>'TIEMPO','simbolo'=>'min','precision_decimal'=>0,'factor_conversion'=>0.016666667,'unidad_base_id'=>$idH,'observaciones'=>'1 h = 60 min'],
            ['codigo'=>'SEG','nombre'=>'Segundo','abreviatura'=>'s','tipo'=>'TIEMPO','simbolo'=>'s','precision_decimal'=>0,'factor_conversion'=>0.000277778,'unidad_base_id'=>$idH,'observaciones'=>'1 h = 3600 s'],
            ['codigo'=>'D',  'nombre'=>'Día','abreviatura'=>'d','tipo'=>'TIEMPO','simbolo'=>'d','precision_decimal'=>0,'factor_conversion'=>24,'unidad_base_id'=>$idH,'observaciones'=>'1 d = 24 h'],

            // Velocidad
            ['codigo'=>'GTTMIN','nombre'=>'Gotas por minuto','abreviatura'=>'gtt/min','tipo'=>'VELOCIDAD','simbolo'=>'gtt/min','precision_decimal'=>0,'factor_conversion'=>0.05,'unidad_base_id'=>$idMLH,'observaciones'=>'Equivalencia aproximada: 1 gtt/min ≈ 3 mL/h'],
            ['codigo'=>'MCGKGMIN','nombre'=>'Microgramos/kg/min','abreviatura'=>'mcg/kg/min','tipo'=>'VELOCIDAD','simbolo'=>'µg/kg/min','precision_decimal'=>3,'factor_conversion'=>1,'unidad_base_id'=>null,'observaciones'=>'Dosificación de drogas vasoactivas'],

            // Concentración
            ['codigo'=>'MGML','nombre'=>'Miligramo por mililitro','abreviatura'=>'mg/mL','tipo'=>'CONCENTRACION','simbolo'=>'mg/mL','precision_decimal'=>3,'factor_conversion'=>1,'unidad_base_id'=>null,'observaciones'=>'Concentración másica estándar'],
            ['codigo'=>'PCT', 'nombre'=>'Porcentaje','abreviatura'=>'%','tipo'=>'CONCENTRACION','simbolo'=>'%','precision_decimal'=>2,'factor_conversion'=>1,'unidad_base_id'=>null,'observaciones'=>'1% = 10 mg/mL (peso/volumen)'],
            ['codigo'=>'MEQML','nombre'=>'Miliequivalente por mL','abreviatura'=>'mEq/mL','tipo'=>'CONCENTRACION','simbolo'=>'mEq/mL','precision_decimal'=>3,'factor_conversion'=>1,'unidad_base_id'=>null,'observaciones'=>'Concentración electrolítica'],

            // Cantidad
            ['codigo'=>'TAB','nombre'=>'Tableta','abreviatura'=>'tab','tipo'=>'CANTIDAD','simbolo'=>'tab','precision_decimal'=>0,'factor_conversion'=>1,'unidad_base_id'=>null,'observaciones'=>'Unidad discreta'],
            ['codigo'=>'CAP','nombre'=>'Cápsula','abreviatura'=>'cap','tipo'=>'CANTIDAD','simbolo'=>'cap','precision_decimal'=>0,'factor_conversion'=>1,'unidad_base_id'=>null,'observaciones'=>'Unidad discreta'],
            ['codigo'=>'AMP','nombre'=>'Ampolla','abreviatura'=>'amp','tipo'=>'CANTIDAD','simbolo'=>'amp','precision_decimal'=>0,'factor_conversion'=>1,'unidad_base_id'=>null,'observaciones'=>'Unidad discreta'],
            ['codigo'=>'VIAL','nombre'=>'Vial','abreviatura'=>'vial','tipo'=>'CANTIDAD','simbolo'=>'vial','precision_decimal'=>0,'factor_conversion'=>1,'unidad_base_id'=>null,'observaciones'=>'Frasco unidosis o multidosis'],
        ];

        foreach ($derivadas as $d) {
            DB::table('unidades_medida')->insert(array_merge($d, [
                'permite_fracciones'   => in_array($d['tipo'], ['CANTIDAD']) ? 0 : 1,
                'activa_calculos'      => 1,
                'color_identificacion' => UnidadMedida::COLOR_TIPO[$d['tipo']] ?? null,
                'icono'                => UnidadMedida::ICONO_TIPO[$d['tipo']] ?? null,
                'estado'               => 1,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]));
        }
    }
}
