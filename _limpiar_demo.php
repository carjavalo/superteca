<?php
/**
 * ============================================================================
 *  LIMPIEZA DE DATOS DEMO / OPERATIVOS  -  Superteca
 * ----------------------------------------------------------------------------
 *  Borra TODOS los registros de las tablas operativas/transaccionales
 *  (datos creados automáticamente por los seeders para poblar las vistas).
 *
 *  CONSERVA INTACTOS:
 *    - Tablas del sistema (migrations, cache, jobs, sessions, etc.)
 *    - users y roles
 *    - Catálogos / Maestros (medicamentos, laboratorios, proveedores,
 *      presentaciones, formas, unidades, vías, bodegas, motivos de ajuste,
 *      servicios, tipos de preparación, parámetros de calidad, catálogos
 *      de interacciones/compatibilidades)
 *    - Las 5 vistas SQL de trazabilidad (vw_*)
 *
 *  USO:
 *    php _limpiar_demo.php            (muestra qué hará, NO borra)
 *    php _limpiar_demo.php --ejecutar (borra de verdad)
 * ============================================================================
 */

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$ejecutar = in_array('--ejecutar', $argv, true);

/* Tablas a vaciar (datos operativos / demo). Orden no importa: se desactivan
   las llaves foráneas durante el proceso. */
$tablas = [
    // Auditoría / actividad
    'activity_logs',

    // Inventario - movimientos y documentos
    'movimientos_inventario',
    'detalle_entradas', 'entradas',
    'detalle_salidas', 'salidas',
    'detalle_ajustes_inventario', 'ajustes_inventario',
    'detalle_traslados', 'recepcion_traslados', 'traslados',
    'inventario_lotes',

    // Producción - Fórmulas
    'formulas_compatibilidades', 'formulas_requerimientos', 'formulas_estabilidad',
    'formulas_diluyentes', 'formulas_detalle', 'formulas',

    // Producción - Mezclas
    'mezclas_producto_final', 'mezclas_control_calidad', 'mezclas_consumo',
    'mezclas_detalle', 'mezclas',

    // Producción - Preparaciones
    'preparaciones_entrega', 'preparaciones_control_calidad', 'preparaciones_consumo',
    'preparaciones_detalle', 'preparaciones',

    // Producción - Reempaques
    'reempaques_producto_final', 'reempaques_control_calidad', 'reempaques_consumo',
    'reempaques_detalle', 'reempaques',

    // Dispensación
    'dispensacion_entregas_recepcion', 'dispensacion_entregas_lotes',
    'dispensacion_entregas_detalle', 'dispensacion_entregas',
    'dispensacion_devoluciones_detalle', 'dispensacion_devoluciones',

    // Pacientes (ficha clínica)
    'pacientes_prescripciones_detalle', 'pacientes_prescripciones',
    'pacientes_tratamientos', 'pacientes_dispensaciones',
    'pacientes_diagnosticos', 'pacientes_alergias', 'pacientes',

    // Validación farmacéutica
    'validaciones_aprobaciones', 'validaciones_alertas',
    'validaciones_detalle', 'validaciones',

    // Calidad - Controles
    'controles_calidad_resultados', 'controles_calidad_evidencias',
    'controles_calidad_acciones', 'controles_calidad_detalle', 'controles_calidad',

    // Calidad - Incidentes
    'incidentes_seguimiento', 'incidentes_evidencias', 'incidentes_afectaciones',
    'incidentes_acciones', 'incidentes_detalle', 'incidentes',
    'afectacion_lotes',

    // Cadena de frío (monitoreo y equipos demo)
    'lotes_cadena_frio', 'alertas_cadena_frio', 'monitoreo_temperatura',
    'sensores_temperatura', 'equipos_cadena_frio',
];

echo str_repeat('=', 70) . PHP_EOL;
echo $ejecutar ? "  MODO: EJECUTAR (se borrarán los datos)" : "  MODO: SIMULACIÓN (no se borra nada, usa --ejecutar)";
echo PHP_EOL . str_repeat('=', 70) . PHP_EOL;

if ($ejecutar) {
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
}

$totalBorrado = 0;
$conn = DB::getDriverName(); // mysql

foreach ($tablas as $t) {
    if (!Schema::hasTable($t)) {
        echo sprintf("  - %-38s (no existe, omitida)%s", $t, PHP_EOL);
        continue;
    }
    $n = DB::table($t)->count();
    if ($ejecutar) {
        DB::table($t)->delete();
        try { DB::statement("ALTER TABLE `$t` AUTO_INCREMENT = 1"); } catch (\Throwable $e) {}
        echo sprintf("  ✔ %-38s %6d registros borrados%s", $t, $n, PHP_EOL);
    } else {
        echo sprintf("  · %-38s %6d registros se borrarían%s", $t, $n, PHP_EOL);
    }
    $totalBorrado += $n;
}

if ($ejecutar) {
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
}

echo str_repeat('-', 70) . PHP_EOL;
echo "  TOTAL: $totalBorrado registros " . ($ejecutar ? "borrados." : "se borrarían.") . PHP_EOL;
echo str_repeat('=', 70) . PHP_EOL;
echo "  Catálogos/maestros, usuarios y roles: CONSERVADOS." . PHP_EOL;
if (!$ejecutar) {
    echo "  Para borrar de verdad ejecuta:  php _limpiar_demo.php --ejecutar" . PHP_EOL;
}
