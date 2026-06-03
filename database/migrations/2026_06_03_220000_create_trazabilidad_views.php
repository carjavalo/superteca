<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // ===== Vista: Trazabilidad de lotes hacia paciente =====
        DB::statement("DROP VIEW IF EXISTS vw_trazabilidad_lotes");
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
                s.nombre AS servicio,
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
            LEFT JOIN servicios_hospitalarios s ON s.id = de.servicio_id
        ");

        // ===== Vista: Trazabilidad de producción (mezclas/preparaciones/reempaques que consumen un lote) =====
        DB::statement("DROP VIEW IF EXISTS vw_trazabilidad_produccion");
        DB::statement("
            CREATE VIEW vw_trazabilidad_produccion AS
            SELECT
                'MEZCLA' AS tipo,
                mc.inventario_lote_id AS lote_id,
                mz.id AS produccion_id,
                mz.codigo AS produccion_codigo,
                mz.fecha_programada AS fecha,
                mz.estado,
                mc.cantidad_consumida,
                mc.costo_total,
                NULL AS paciente_id
            FROM mezclas_consumo mc
            INNER JOIN mezclas mz ON mz.id = mc.mezcla_id
            UNION ALL
            SELECT
                'PREPARACION' AS tipo,
                pc.inventario_lote_id AS lote_id,
                pr.id AS produccion_id,
                pr.codigo AS produccion_codigo,
                pr.fecha_programada AS fecha,
                pr.estado,
                pc.cantidad_consumida,
                pc.costo_total,
                pr.paciente_id
            FROM preparaciones_consumo pc
            INNER JOIN preparaciones pr ON pr.id = pc.preparacion_id
            UNION ALL
            SELECT
                'REEMPAQUE' AS tipo,
                rc.inventario_lote_id AS lote_id,
                rp.id AS produccion_id,
                rp.codigo AS produccion_codigo,
                rp.fecha_programada AS fecha,
                rp.estado,
                rc.cantidad_consumida,
                rc.costo_total,
                NULL AS paciente_id
            FROM reempaques_consumo rc
            INNER JOIN reempaques rp ON rp.id = rc.reempaque_id
        ");

        // ===== Vista: Impacto de incidentes (lote -> mezcla -> preparación -> paciente) =====
        DB::statement("DROP VIEW IF EXISTS vw_trazabilidad_calidad");
        DB::statement("
            CREATE VIEW vw_trazabilidad_calidad AS
            SELECT
                i.id AS incidente_id,
                i.codigo AS incidente_codigo,
                i.fecha_incidente,
                i.tipo_incidente,
                i.severidad,
                i.estado AS incidente_estado,
                ia.inventario_lote_id,
                ia.mezcla_id,
                ia.preparacion_id,
                ia.reempaque_id,
                ia.entrega_id,
                ia.paciente_id,
                ia.equipo_cadena_frio_id,
                il.lote,
                m.nombre AS medicamento
            FROM incidentes i
            LEFT JOIN incidentes_afectaciones ia ON ia.incidente_id = i.id
            LEFT JOIN inventario_lotes il ON il.id = ia.inventario_lote_id
            LEFT JOIN medicamentos m ON m.id = il.medicamento_id
        ");

        // ===== Vista: Cadena de frío (lotes asociados a equipo) =====
        DB::statement("DROP VIEW IF EXISTS vw_trazabilidad_cadena_frio");
        DB::statement("
            CREATE VIEW vw_trazabilidad_cadena_frio AS
            SELECT
                e.id AS equipo_id,
                e.codigo AS equipo_codigo,
                e.nombre AS equipo,
                e.ubicacion,
                il.id AS lote_id,
                il.lote,
                il.fecha_vencimiento,
                il.cantidad_actual,
                m.id AS medicamento_id,
                m.nombre AS medicamento
            FROM equipos_cadena_frio e
            LEFT JOIN inventario_lotes il ON il.equipo_cadena_frio_id = e.id
            LEFT JOIN medicamentos m ON m.id = il.medicamento_id
        ");

        // ===== Vista resumen: KPIs por lote =====
        DB::statement("DROP VIEW IF EXISTS vw_trazabilidad_lote_resumen");
        DB::statement("
            CREATE VIEW vw_trazabilidad_lote_resumen AS
            SELECT
                il.id AS lote_id,
                il.lote,
                il.medicamento_id,
                m.nombre AS medicamento,
                il.fecha_ingreso,
                il.fecha_vencimiento,
                il.cantidad_inicial,
                il.cantidad_actual,
                il.estado,
                il.estado_calidad,
                il.bloqueado_incidente,
                (SELECT COUNT(*) FROM mezclas_consumo mc WHERE mc.inventario_lote_id = il.id) AS total_mezclas,
                (SELECT COUNT(*) FROM preparaciones_consumo pc WHERE pc.inventario_lote_id = il.id) AS total_preparaciones,
                (SELECT COUNT(*) FROM reempaques_consumo rc WHERE rc.inventario_lote_id = il.id) AS total_reempaques,
                (SELECT COUNT(DISTINCT de.paciente_id)
                 FROM dispensacion_entregas_lotes del
                 INNER JOIN dispensacion_entregas_detalle ded ON ded.id = del.entrega_detalle_id
                 INNER JOIN dispensacion_entregas de ON de.id = ded.entrega_id
                 WHERE del.inventario_lote_id = il.id AND de.paciente_id IS NOT NULL) AS total_pacientes,
                (SELECT COUNT(*)
                 FROM dispensacion_entregas_lotes del
                 WHERE del.inventario_lote_id = il.id) AS total_dispensaciones,
                (SELECT COUNT(*) FROM incidentes_afectaciones ia WHERE ia.inventario_lote_id = il.id) AS total_incidentes
            FROM inventario_lotes il
            INNER JOIN medicamentos m ON m.id = il.medicamento_id
        ");
    }

    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_trazabilidad_lote_resumen");
        DB::statement("DROP VIEW IF EXISTS vw_trazabilidad_cadena_frio");
        DB::statement("DROP VIEW IF EXISTS vw_trazabilidad_calidad");
        DB::statement("DROP VIEW IF EXISTS vw_trazabilidad_produccion");
        DB::statement("DROP VIEW IF EXISTS vw_trazabilidad_lotes");
    }
};
