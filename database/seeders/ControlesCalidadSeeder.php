<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ControlCalidad;
use App\Models\ControlCalidadAccion;
use App\Models\ControlCalidadDetalle;
use App\Models\ControlCalidadParametro;
use App\Models\ControlCalidadResultado;
use App\Models\InventarioLote;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ControlesCalidadSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        $userId = $user?->id;

        // 1) PARÁMETROS por tipo
        $parametros = [
            // RECEPCION
            ['nombre' => 'Aspecto visual',     'tipo_control' => 'RECEPCION', 'unidad_medida' => null,  'valor_minimo' => null, 'valor_maximo' => null],
            ['nombre' => 'Etiquetado',         'tipo_control' => 'RECEPCION', 'unidad_medida' => null,  'valor_minimo' => null, 'valor_maximo' => null],
            ['nombre' => 'Fecha vencimiento',  'tipo_control' => 'RECEPCION', 'unidad_medida' => 'días','valor_minimo' => 180,  'valor_maximo' => null],
            ['nombre' => 'Empaque íntegro',    'tipo_control' => 'RECEPCION', 'unidad_medida' => null,  'valor_minimo' => null, 'valor_maximo' => null],

            // CADENA_FRIO
            ['nombre' => 'Temperatura',        'tipo_control' => 'CADENA_FRIO', 'unidad_medida' => '°C', 'valor_minimo' => 2,  'valor_maximo' => 8],
            ['nombre' => 'Humedad relativa',   'tipo_control' => 'CADENA_FRIO', 'unidad_medida' => '%',  'valor_minimo' => 30, 'valor_maximo' => 65],

            // ALMACENAMIENTO
            ['nombre' => 'Temperatura ambiente','tipo_control'=> 'ALMACENAMIENTO','unidad_medida'=>'°C','valor_minimo'=>15,'valor_maximo'=>25],
            ['nombre' => 'Humedad',            'tipo_control' => 'ALMACENAMIENTO','unidad_medida'=>'%', 'valor_minimo'=>30,'valor_maximo'=>65],
            ['nombre' => 'Orden / limpieza',   'tipo_control' => 'ALMACENAMIENTO','unidad_medida'=>null,'valor_minimo'=>null,'valor_maximo'=>null],

            // MEZCLA
            ['nombre' => 'pH',                 'tipo_control' => 'MEZCLA', 'unidad_medida' => null,  'valor_minimo' => 4.5, 'valor_maximo' => 7.5],
            ['nombre' => 'Osmolaridad',        'tipo_control' => 'MEZCLA', 'unidad_medida' => 'mOsm','valor_minimo' => 280, 'valor_maximo' => 310],
            ['nombre' => 'Volumen final',      'tipo_control' => 'MEZCLA', 'unidad_medida' => 'mL',  'valor_minimo' => null,'valor_maximo' => null],
            ['nombre' => 'Aspecto / partículas','tipo_control'=> 'MEZCLA', 'unidad_medida' => null,  'valor_minimo' => null,'valor_maximo' => null],

            // PREPARACION
            ['nombre' => 'Esterilidad',        'tipo_control' => 'PREPARACION', 'unidad_medida' => null,'valor_minimo' => null,'valor_maximo' => null],
            ['nombre' => 'Identificación',     'tipo_control' => 'PREPARACION', 'unidad_medida' => null,'valor_minimo' => null,'valor_maximo' => null],

            // REEMPAQUE
            ['nombre' => 'Etiquetado correcto','tipo_control' => 'REEMPAQUE', 'unidad_medida' => null, 'valor_minimo' => null,'valor_maximo' => null],
            ['nombre' => 'Sellado',            'tipo_control' => 'REEMPAQUE', 'unidad_medida' => null, 'valor_minimo' => null,'valor_maximo' => null],

            // PRODUCTO_TERMINADO
            ['nombre' => 'Peso final',         'tipo_control' => 'PRODUCTO_TERMINADO','unidad_medida'=>'g','valor_minimo'=>null,'valor_maximo'=>null],
            ['nombre' => 'Aspecto final',      'tipo_control' => 'PRODUCTO_TERMINADO','unidad_medida'=>null,'valor_minimo'=>null,'valor_maximo'=>null],

            // DISPENSACION
            ['nombre' => 'Verificación de paciente','tipo_control'=>'DISPENSACION','unidad_medida'=>null,'valor_minimo'=>null,'valor_maximo'=>null],
            ['nombre' => 'Estado de calidad del lote','tipo_control'=>'DISPENSACION','unidad_medida'=>null,'valor_minimo'=>null,'valor_maximo'=>null],
        ];

        $parametrosPorTipo = [];
        foreach ($parametros as $p) {
            $par = ControlCalidadParametro::firstOrCreate(
                ['nombre' => $p['nombre'], 'tipo_control' => $p['tipo_control']],
                array_merge($p, ['obligatorio' => true, 'estado' => true])
            );
            $parametrosPorTipo[$p['tipo_control']][] = $par;
        }

        // 2) CONTROLES demo
        $lotes = InventarioLote::take(5)->get();
        $year = now()->format('Y');
        $i = ControlCalidad::whereYear('created_at', now()->year)->count();

        $crearControl = function (string $tipo, string $resultado, ?int $loteId, ?string $obs = null) use (&$i, $year, $userId, $parametrosPorTipo) {
            $i++;
            $codigo = 'CC-' . $year . '-' . str_pad((string)$i, 5, '0', STR_PAD_LEFT);
            $control = ControlCalidad::create([
                'codigo'             => $codigo,
                'tipo_control'       => $tipo,
                'fecha_control'      => now()->subDays(rand(0, 4))->subHours(rand(0, 12)),
                'usuario_control_id' => $userId,
                'resultado'          => $resultado,
                'observaciones'      => $obs,
            ]);

            if ($loteId) {
                ControlCalidadDetalle::create([
                    'control_id'         => $control->id,
                    'inventario_lote_id' => $loteId,
                ]);

                $map = ['APROBADO'=>'LIBERADO','CONDICIONAL'=>'CUARENTENA','RECHAZADO'=>'BLOQUEADO'];
                if (isset($map[$resultado])) {
                    InventarioLote::where('id', $loteId)->update(['estado_calidad' => $map[$resultado]]);
                }
            }

            foreach ($parametrosPorTipo[$tipo] ?? [] as $par) {
                $cumple = $resultado !== 'RECHAZADO';
                $valor = $par->unidad_medida
                    ? (string) round(($par->valor_minimo ?? 1) + (($par->valor_maximo ?? 10) - ($par->valor_minimo ?? 1)) * (mt_rand(20, 80) / 100), 2)
                    : 'OK';
                ControlCalidadResultado::create([
                    'control_id'     => $control->id,
                    'parametro_id'   => $par->id,
                    'valor_obtenido' => $valor,
                    'cumple'         => $cumple,
                    'observaciones'  => null,
                ]);
            }

            return $control;
        };

        // Recepción aprobada
        if ($lotes->count() >= 1) {
            $crearControl('RECEPCION', 'APROBADO', $lotes[0]->id, 'Recepción conforme al pedido');
        }
        // Almacenamiento aprobado
        $crearControl('ALMACENAMIENTO', 'APROBADO', null, 'Inspección de bodega central');
        // Mezcla condicional (queda en cuarentena)
        if ($lotes->count() >= 2) {
            $crearControl('MEZCLA', 'CONDICIONAL', $lotes[1]->id, 'pH ligeramente fuera de rango, requiere revisión');
        }
        // Producción rechazada (bloquea lote)
        if ($lotes->count() >= 3) {
            $rech = $crearControl('PRODUCCION', 'RECHAZADO', $lotes[2]->id, 'Contaminación visible en producto');
            ControlCalidadAccion::create([
                'control_id'      => $rech->id,
                'descripcion'     => 'Iniciar investigación de la causa raíz y desechar lote afectado',
                'responsable_id'  => $userId,
                'fecha_compromiso'=> now()->addDays(3)->toDateString(),
                'estado'          => 'ABIERTA',
            ]);
        }
        // Preparación aprobada
        if ($lotes->count() >= 4) {
            $crearControl('PREPARACION', 'APROBADO', $lotes[3]->id, 'Preparación dentro de norma');
        }
        // Reempaque pendiente
        if ($lotes->count() >= 5) {
            $crearControl('REEMPAQUE', 'PENDIENTE', $lotes[4]->id, 'En espera de revisión final');
        }
        // Cadena de frío aprobado
        $crearControl('CADENA_FRIO', 'APROBADO', null, 'Verificación de neveras del almacén');
        // Producto terminado aprobado
        $crearControl('PRODUCTO_TERMINADO', 'APROBADO', null, 'Producto listo para dispensación');
        // Dispensación aprobado
        $crearControl('DISPENSACION', 'APROBADO', null, 'Verificación previa a entrega');

        $this->command->info('Seeder Controles de Calidad: parámetros y controles demo creados.');
    }
}
