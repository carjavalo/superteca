<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Unifica el catálogo de servicios: todo el sistema (Pacientes, Entregas,
 * Preparaciones, Reportes, Trazabilidad) pasa a usar `TipoServicios` en lugar
 * de `servicios_hospitalarios`.
 *
 * Como los ids de `TipoServicios` coinciden 1:1 con los de la tabla anterior
 * (mismos servicios, mismo orden), los registros existentes siguen resolviendo
 * el servicio correcto. Aquí se liberan las FKs que aún apuntaban a la tabla
 * vieja y se recrea la vista de trazabilidad para que lea del catálogo nuevo.
 *
 * La tabla `servicios_hospitalarios` y su modelo se conservan (sólo los usan ya
 * los seeders de demo), pero dejan de alimentar la aplicación en tiempo real.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) Liberar las FKs hacia servicios_hospitalarios (la de pacientes ya
        //    se soltó en una migración anterior).
        Schema::table('dispensacion_entregas', fn (Blueprint $t) => $t->dropForeign(['servicio_id']));
        Schema::table('preparaciones',          fn (Blueprint $t) => $t->dropForeign(['servicio_id']));
        Schema::table('preparaciones_entrega',  fn (Blueprint $t) => $t->dropForeign(['servicio_destino_id']));

        // 2) Recrear la vista de trazabilidad de lotes apuntando a TipoServicios.
        $this->crearVistaLotes('TipoServicios', 's.Detalle');
    }

    public function down(): void
    {
        Schema::table('dispensacion_entregas', fn (Blueprint $t) => $t->foreign('servicio_id')->references('id')->on('servicios_hospitalarios')->nullOnDelete());
        Schema::table('preparaciones',          fn (Blueprint $t) => $t->foreign('servicio_id')->references('id')->on('servicios_hospitalarios')->nullOnDelete());
        Schema::table('preparaciones_entrega',  fn (Blueprint $t) => $t->foreign('servicio_destino_id')->references('id')->on('servicios_hospitalarios')->nullOnDelete());

        $this->crearVistaLotes('servicios_hospitalarios', 's.nombre');
    }

    /** (Re)crea vw_trazabilidad_lotes con el catálogo de servicios indicado. */
    private function crearVistaLotes(string $tablaServicio, string $colServicio): void
    {
        DB::statement('DROP VIEW IF EXISTS vw_trazabilidad_lotes');
        DB::statement("
            CREATE VIEW vw_trazabilidad_lotes AS
            SELECT
                il.id AS lote_id,
                il.lote,
                il.fecha_ingreso,
                il.fecha_vencimiento,
                il.cantidad_inicial,
                il.cantidad_actual,
                il.estado AS estado_lote,
                il.estado_calidad,
                il.bloqueado_incidente,
                il.proveedor_id,
                m.id AS medicamento_id,
                m.nombre AS medicamento,
                m.codigo AS medicamento_codigo,
                de.id AS entrega_id,
                de.codigo AS entrega_codigo,
                de.fecha_entrega,
                de.tipo_entrega,
                de.servicio_id,
                {$colServicio} AS servicio,
                p.id AS paciente_id,
                p.documento,
                p.tipo_documento,
                CONCAT(COALESCE(p.nombres,''),' ',COALESCE(p.apellidos,'')) AS paciente,
                del.cantidad_entregada,
                del.costo_unitario,
                (del.cantidad_entregada * COALESCE(del.costo_unitario,0)) AS costo_total
            FROM inventario_lotes il
            INNER JOIN medicamentos m ON m.id = il.medicamento_id
            LEFT JOIN dispensacion_entregas_lotes del ON del.inventario_lote_id = il.id
            LEFT JOIN dispensacion_entregas_detalle ded ON ded.id = del.entrega_detalle_id
            LEFT JOIN dispensacion_entregas de ON de.id = ded.entrega_id
            LEFT JOIN pacientes p ON p.id = de.paciente_id
            LEFT JOIN {$tablaServicio} s ON s.id = de.servicio_id
        ");
    }
};
