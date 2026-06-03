<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TrazabilidadController extends Controller
{
    /**
     * Dashboard principal: KPIs ejecutivos + buscador universal + top lotes trazables
     */
    public function index(Request $r)
    {
        // ===== KPIs ejecutivos =====
        $kpis = [
            'lotes_activos'       => DB::table('inventario_lotes')->where('cantidad_actual', '>', 0)->count(),
            'lotes_trazables'     => DB::table('inventario_lotes')->count(),
            'pacientes_impactados'=> DB::table('dispensacion_entregas')->whereNotNull('paciente_id')->distinct()->count('paciente_id'),
            'preparaciones'       => DB::table('preparaciones')->count(),
            'mezclas'             => DB::table('mezclas')->count(),
            'reempaques'          => DB::table('reempaques')->count(),
            'incidentes'          => DB::table('incidentes')->count(),
            'incidentes_abiertos' => DB::table('incidentes')->whereNotIn('estado', ['CERRADO','RESUELTO'])->count(),
            'alertas_cf'          => DB::table('alertas_cadena_frio')->where('estado', 'ACTIVA')->count(),
            'lotes_bloqueados'    => DB::table('inventario_lotes')->where('bloqueado_incidente', true)->count(),
        ];

        // ===== Top lotes con más impacto (más trazabilidad) =====
        $topLotes = DB::table('vw_trazabilidad_lote_resumen')
            ->orderByDesc('total_pacientes')
            ->orderByDesc('total_preparaciones')
            ->limit(10)
            ->get();

        // ===== Distribución por área (dona) =====
        $distribucion = [
            'inventario'    => $kpis['lotes_activos'],
            'produccion'    => $kpis['mezclas'] + $kpis['preparaciones'] + $kpis['reempaques'],
            'dispensacion'  => DB::table('dispensacion_entregas')->count(),
            'pacientes'     => $kpis['pacientes_impactados'],
        ];

        // ===== Movimientos por día (últimos 30 días) =====
        $movimientosDia = DB::table('movimientos_inventario')
            ->selectRaw("DATE(fecha_movimiento) as dia, COUNT(*) as total")
            ->where('fecha_movimiento', '>=', now()->subDays(30))
            ->groupByRaw("DATE(fecha_movimiento)")
            ->orderBy('dia')
            ->get();

        // ===== Top 10 medicamentos más trazados =====
        $topMedicamentos = DB::table('vw_trazabilidad_lote_resumen')
            ->select('medicamento_id', 'medicamento', DB::raw('COUNT(*) as lotes'),
                     DB::raw('SUM(total_pacientes) as pacientes'),
                     DB::raw('SUM(total_preparaciones + total_mezclas + total_reempaques) as producciones'))
            ->groupBy('medicamento_id', 'medicamento')
            ->orderByDesc('pacientes')
            ->limit(10)
            ->get();

        // ===== Heatmap servicio x lotes =====
        $heatmap = DB::table('vw_trazabilidad_lotes')
            ->whereNotNull('servicio')
            ->select('servicio',
                     DB::raw('COUNT(DISTINCT lote_id) as lotes'),
                     DB::raw('COUNT(DISTINCT paciente_id) as pacientes'))
            ->groupBy('servicio')
            ->orderByDesc('lotes')
            ->limit(12)
            ->get();

        return view('admin.reportes.trazabilidad.index', compact(
            'kpis', 'topLotes', 'distribucion', 'movimientosDia', 'topMedicamentos', 'heatmap'
        ));
    }

    /**
     * Buscador universal: detecta tipo de búsqueda y redirige
     */
    public function buscar(Request $r)
    {
        $q = trim($r->input('q', ''));
        if ($q === '') return redirect()->route('admin.reportes.trazabilidad');

        // Lote
        $lote = DB::table('inventario_lotes')->where('lote', $q)->orWhere('id', $q)->first();
        if ($lote) return redirect()->route('admin.reportes.trazabilidad.lote', $lote->id);

        // Paciente por documento
        $paciente = DB::table('pacientes')->where('documento', $q)->orWhere('id', $q)->first();
        if ($paciente) return redirect()->route('admin.reportes.trazabilidad.paciente', $paciente->id);

        // Preparación / Mezcla / Reempaque por código
        $prep = DB::table('preparaciones')->where('codigo', $q)->first();
        if ($prep) return redirect()->route('admin.reportes.trazabilidad.preparacion', $prep->id);

        $mz = DB::table('mezclas')->where('codigo', $q)->first();
        if ($mz) return redirect()->route('admin.reportes.trazabilidad.mezcla', $mz->id);

        $rp = DB::table('reempaques')->where('codigo', $q)->first();
        if ($rp) return redirect()->route('admin.reportes.trazabilidad.reempaque', $rp->id);

        // Incidente
        $inc = DB::table('incidentes')->where('codigo', $q)->orWhere('id', $q)->first();
        if ($inc) return redirect()->route('admin.reportes.trazabilidad.incidente', $inc->id);

        // Equipo cadena frío
        $eq = DB::table('equipos_cadena_frio')->where('codigo', $q)->first();
        if ($eq) return redirect()->route('admin.reportes.trazabilidad.equipo', $eq->id);

        return redirect()->route('admin.reportes.trazabilidad')
            ->with('warning', "No se encontraron resultados para: {$q}");
    }

    /**
     * Trazabilidad 360° de un lote
     */
    public function lote($id)
    {
        $lote = DB::table('inventario_lotes as il')
            ->leftJoin('medicamentos as m', 'm.id', '=', 'il.medicamento_id')
            ->leftJoin('proveedores as pv', 'pv.id', '=', 'il.proveedor_id')
            ->leftJoin('equipos_cadena_frio as e', 'e.id', '=', 'il.equipo_cadena_frio_id')
            ->where('il.id', $id)
            ->select('il.*', 'm.nombre as medicamento', 'm.codigo as medicamento_codigo',
                     'pv.nombre_comercial as proveedor', 'pv.razon_social as proveedor_razon',
                     'e.codigo as equipo_codigo', 'e.nombre as equipo_nombre')
            ->firstOrFail();

        // Movimientos del lote
        $movimientos = DB::table('movimientos_inventario')
            ->where('inventario_lote_id', $id)
            ->orderBy('fecha_movimiento', 'desc')
            ->limit(50)
            ->get();

        // Mezclas que consumieron el lote
        $mezclas = DB::table('mezclas_consumo as mc')
            ->join('mezclas as mz', 'mz.id', '=', 'mc.mezcla_id')
            ->where('mc.inventario_lote_id', $id)
            ->select('mz.id', 'mz.codigo', 'mz.fecha_programada', 'mz.estado',
                     'mc.cantidad_consumida', 'mc.costo_total')
            ->orderByDesc('mz.fecha_programada')
            ->get();

        // Preparaciones que consumieron el lote
        $preparaciones = DB::table('preparaciones_consumo as pc')
            ->join('preparaciones as pr', 'pr.id', '=', 'pc.preparacion_id')
            ->leftJoin('pacientes as p', 'p.id', '=', 'pr.paciente_id')
            ->where('pc.inventario_lote_id', $id)
            ->select('pr.id', 'pr.codigo', 'pr.fecha_programada', 'pr.estado', 'pr.paciente_id',
                     DB::raw("CONCAT(COALESCE(p.nombres,''),' ',COALESCE(p.apellidos,'')) as paciente"),
                     'p.documento',
                     'pc.cantidad_consumida', 'pc.costo_total')
            ->orderByDesc('pr.fecha_programada')
            ->get();

        // Reempaques que consumieron el lote
        $reempaques = DB::table('reempaques_consumo as rc')
            ->join('reempaques as rp', 'rp.id', '=', 'rc.reempaque_id')
            ->where('rc.inventario_lote_id', $id)
            ->select('rp.id', 'rp.codigo', 'rp.fecha_programada', 'rp.estado',
                     'rc.cantidad_consumida', 'rc.costo_total')
            ->orderByDesc('rp.fecha_programada')
            ->get();

        // Pacientes que recibieron el lote (vía dispensación)
        $pacientes = DB::table('dispensacion_entregas_lotes as del')
            ->join('dispensacion_entregas_detalle as ded', 'ded.id', '=', 'del.entrega_detalle_id')
            ->join('dispensacion_entregas as de', 'de.id', '=', 'ded.entrega_id')
            ->leftJoin('pacientes as p', 'p.id', '=', 'de.paciente_id')
            ->leftJoin('servicios_hospitalarios as s', 's.id', '=', 'de.servicio_id')
            ->where('del.inventario_lote_id', $id)
            ->select('de.id as entrega_id', 'de.codigo as entrega_codigo', 'de.fecha_entrega',
                     'de.tipo_entrega', 'p.id as paciente_id', 'p.documento',
                     DB::raw("CONCAT(COALESCE(p.nombres,''),' ',COALESCE(p.apellidos,'')) as paciente"),
                     's.nombre as servicio',
                     'del.cantidad_entregada', 'del.costo_unitario')
            ->orderByDesc('de.fecha_entrega')
            ->get();

        // Incidentes asociados
        $incidentes = DB::table('incidentes_afectaciones as ia')
            ->join('incidentes as i', 'i.id', '=', 'ia.incidente_id')
            ->where('ia.inventario_lote_id', $id)
            ->select('i.id', 'i.codigo', 'i.fecha_incidente', 'i.tipo_incidente',
                     'i.severidad', 'i.estado')
            ->orderByDesc('i.fecha_incidente')
            ->get();

        // Controles de calidad relacionados (si la tabla los enlaza por lote)
        $controles = collect();
        if (Schema::hasColumn('controles_calidad', 'inventario_lote_id')) {
            $controles = DB::table('controles_calidad')
                ->where('inventario_lote_id', $id)
                ->orderByDesc('fecha_control')
                ->get();
        }

        // KPIs del lote
        $kpis = [
            'mezclas'        => $mezclas->count(),
            'preparaciones'  => $preparaciones->count(),
            'reempaques'     => $reempaques->count(),
            'pacientes'      => $pacientes->pluck('paciente_id')->filter()->unique()->count(),
            'dispensaciones' => $pacientes->count(),
            'incidentes'     => $incidentes->count(),
            'movimientos'    => $movimientos->count(),
        ];

        return view('admin.reportes.trazabilidad.lote', compact(
            'lote', 'movimientos', 'mezclas', 'preparaciones', 'reempaques',
            'pacientes', 'incidentes', 'controles', 'kpis'
        ));
    }

    /**
     * Trazabilidad de un paciente
     */
    public function paciente($id)
    {
        $paciente = DB::table('pacientes as p')
            ->leftJoin('servicios_hospitalarios as s', 's.id', '=', 'p.servicio_id')
            ->where('p.id', $id)
            ->select('p.*', 's.nombre as servicio')
            ->firstOrFail();

        // Lotes recibidos
        $lotes = DB::table('vw_trazabilidad_lotes')
            ->where('paciente_id', $id)
            ->orderByDesc('fecha_entrega')
            ->get();

        // Preparaciones del paciente
        $preparaciones = DB::table('preparaciones')
            ->where('paciente_id', $id)
            ->orderByDesc('fecha_programada')
            ->get();

        // Entregas
        $entregas = DB::table('dispensacion_entregas as de')
            ->leftJoin('servicios_hospitalarios as s', 's.id', '=', 'de.servicio_id')
            ->where('de.paciente_id', $id)
            ->select('de.*', 's.nombre as servicio')
            ->orderByDesc('de.fecha_entrega')
            ->get();

        $kpis = [
            'lotes'         => $lotes->pluck('lote_id')->unique()->count(),
            'medicamentos'  => $lotes->pluck('medicamento_id')->unique()->count(),
            'preparaciones' => $preparaciones->count(),
            'entregas'      => $entregas->count(),
        ];

        return view('admin.reportes.trazabilidad.paciente', compact(
            'paciente', 'lotes', 'preparaciones', 'entregas', 'kpis'
        ));
    }

    /**
     * Mapa de impacto de un incidente (Recall por incidente)
     */
    public function incidente($id)
    {
        $incidente = DB::table('incidentes')->where('id', $id)->firstOrFail();

        $afectaciones = DB::table('incidentes_afectaciones as ia')
            ->leftJoin('inventario_lotes as il', 'il.id', '=', 'ia.inventario_lote_id')
            ->leftJoin('medicamentos as m', 'm.id', '=', 'il.medicamento_id')
            ->leftJoin('mezclas as mz', 'mz.id', '=', 'ia.mezcla_id')
            ->leftJoin('preparaciones as pr', 'pr.id', '=', 'ia.preparacion_id')
            ->leftJoin('reempaques as rp', 'rp.id', '=', 'ia.reempaque_id')
            ->leftJoin('dispensacion_entregas as de', 'de.id', '=', 'ia.entrega_id')
            ->leftJoin('pacientes as p', 'p.id', '=', 'ia.paciente_id')
            ->leftJoin('equipos_cadena_frio as e', 'e.id', '=', 'ia.equipo_cadena_frio_id')
            ->where('ia.incidente_id', $id)
            ->select('ia.*',
                'il.lote', 'm.nombre as medicamento',
                'mz.codigo as mezcla_codigo', 'pr.codigo as preparacion_codigo',
                'rp.codigo as reempaque_codigo', 'de.codigo as entrega_codigo',
                DB::raw("CONCAT(COALESCE(p.nombres,''),' ',COALESCE(p.apellidos,'')) as paciente"),
                'p.documento', 'e.codigo as equipo_codigo', 'e.nombre as equipo_nombre')
            ->get();

        $loteIds = $afectaciones->pluck('inventario_lote_id')->filter()->unique();

        // Pacientes impactados (vía lotes afectados)
        $pacientesImpactados = collect();
        if ($loteIds->isNotEmpty()) {
            $pacientesImpactados = DB::table('vw_trazabilidad_lotes')
                ->whereIn('lote_id', $loteIds)
                ->whereNotNull('paciente_id')
                ->select('paciente_id', 'paciente', 'documento', 'lote', 'medicamento',
                         'fecha_entrega', 'cantidad_entregada', 'servicio')
                ->orderByDesc('fecha_entrega')
                ->get();
        }

        $kpis = [
            'lotes_afectados'    => $loteIds->count(),
            'mezclas'            => $afectaciones->pluck('mezcla_id')->filter()->unique()->count(),
            'preparaciones'      => $afectaciones->pluck('preparacion_id')->filter()->unique()->count(),
            'reempaques'         => $afectaciones->pluck('reempaque_id')->filter()->unique()->count(),
            'entregas'           => $afectaciones->pluck('entrega_id')->filter()->unique()->count(),
            'pacientes_directos' => $afectaciones->pluck('paciente_id')->filter()->unique()->count(),
            'pacientes_indirectos' => $pacientesImpactados->pluck('paciente_id')->unique()->count(),
        ];

        return view('admin.reportes.trazabilidad.incidente', compact(
            'incidente', 'afectaciones', 'pacientesImpactados', 'kpis'
        ));
    }

    /**
     * Vista de Recall INVIMA: dado un lote, todos los pacientes/preparaciones/entregas + stock
     */
    public function recall(Request $r)
    {
        $loteCodigo = $r->input('lote');
        $lote = null;
        $pacientes = collect();
        $preparaciones = collect();
        $mezclas = collect();
        $reempaques = collect();
        $stock = null;

        if ($loteCodigo) {
            $lote = DB::table('inventario_lotes as il')
                ->leftJoin('medicamentos as m', 'm.id', '=', 'il.medicamento_id')
                ->leftJoin('proveedores as pv', 'pv.id', '=', 'il.proveedor_id')
                ->where(function($q) use ($loteCodigo) {
                    $q->where('il.lote', $loteCodigo)->orWhere('il.id', $loteCodigo);
                })
                ->select('il.*', 'm.nombre as medicamento', 'pv.nombre_comercial as proveedor')
                ->first();

            if ($lote) {
                $pacientes = DB::table('vw_trazabilidad_lotes')
                    ->where('lote_id', $lote->id)
                    ->whereNotNull('paciente_id')
                    ->orderByDesc('fecha_entrega')
                    ->get();

                $preparaciones = DB::table('preparaciones_consumo as pc')
                    ->join('preparaciones as pr', 'pr.id', '=', 'pc.preparacion_id')
                    ->where('pc.inventario_lote_id', $lote->id)
                    ->select('pr.id', 'pr.codigo', 'pr.fecha_programada', 'pr.estado', 'pr.paciente_id',
                             'pc.cantidad_consumida')
                    ->get();

                $mezclas = DB::table('mezclas_consumo as mc')
                    ->join('mezclas as mz', 'mz.id', '=', 'mc.mezcla_id')
                    ->where('mc.inventario_lote_id', $lote->id)
                    ->select('mz.id', 'mz.codigo', 'mz.fecha_programada', 'mz.estado',
                             'mc.cantidad_consumida')
                    ->get();

                $reempaques = DB::table('reempaques_consumo as rc')
                    ->join('reempaques as rp', 'rp.id', '=', 'rc.reempaque_id')
                    ->where('rc.inventario_lote_id', $lote->id)
                    ->select('rp.id', 'rp.codigo', 'rp.fecha_programada', 'rp.estado',
                             'rc.cantidad_consumida')
                    ->get();

                $stock = [
                    'cantidad_actual' => $lote->cantidad_actual,
                    'cantidad_inicial' => $lote->cantidad_inicial,
                    'consumido' => $lote->cantidad_inicial - $lote->cantidad_actual,
                    'ubicacion' => $lote->ubicacion,
                    'estado' => $lote->estado,
                    'estado_calidad' => $lote->estado_calidad,
                    'bloqueado' => (bool)$lote->bloqueado_incidente,
                ];
            }
        }

        return view('admin.reportes.trazabilidad.recall', compact(
            'loteCodigo', 'lote', 'pacientes', 'preparaciones', 'mezclas', 'reempaques', 'stock'
        ));
    }

    /**
     * Trazabilidad de equipo de cadena de frío
     */
    public function equipo($id)
    {
        $equipo = DB::table('equipos_cadena_frio')->where('id', $id)->firstOrFail();

        $lotes = DB::table('inventario_lotes as il')
            ->leftJoin('medicamentos as m', 'm.id', '=', 'il.medicamento_id')
            ->where('il.equipo_cadena_frio_id', $id)
            ->select('il.*', 'm.nombre as medicamento')
            ->orderByDesc('il.fecha_ingreso')
            ->get();

        $alertas = DB::table('alertas_cadena_frio')
            ->where('equipo_id', $id)
            ->orderByDesc('fecha_inicio')
            ->limit(50)
            ->get();

        $monitoreos = DB::table('monitoreo_temperatura')
            ->where('equipo_id', $id)
            ->orderByDesc('fecha_hora')
            ->limit(100)
            ->get();

        $loteIds = $lotes->pluck('id');
        $pacientesAfectados = collect();
        if ($loteIds->isNotEmpty()) {
            $pacientesAfectados = DB::table('vw_trazabilidad_lotes')
                ->whereIn('lote_id', $loteIds)
                ->whereNotNull('paciente_id')
                ->select('paciente_id', 'paciente', 'documento', 'lote', 'medicamento', 'fecha_entrega')
                ->orderByDesc('fecha_entrega')
                ->limit(200)
                ->get();
        }

        $kpis = [
            'lotes'    => $lotes->count(),
            'alertas'  => $alertas->count(),
            'lecturas' => DB::table('monitoreo_temperatura')->where('equipo_id', $id)->count(),
            'pacientes'=> $pacientesAfectados->pluck('paciente_id')->unique()->count(),
        ];

        return view('admin.reportes.trazabilidad.equipo', compact(
            'equipo', 'lotes', 'alertas', 'monitoreos', 'pacientesAfectados', 'kpis'
        ));
    }

    /**
     * Trazabilidad de una preparación
     */
    public function preparacion($id)
    {
        $preparacion = DB::table('preparaciones as pr')
            ->leftJoin('pacientes as p', 'p.id', '=', 'pr.paciente_id')
            ->leftJoin('servicios_hospitalarios as s', 's.id', '=', 'pr.servicio_id')
            ->where('pr.id', $id)
            ->select('pr.*',
                DB::raw("CONCAT(COALESCE(p.nombres,''),' ',COALESCE(p.apellidos,'')) as paciente"),
                'p.documento', 'p.id as paciente_id', 's.nombre as servicio')
            ->firstOrFail();

        $consumos = DB::table('preparaciones_consumo as pc')
            ->leftJoin('inventario_lotes as il', 'il.id', '=', 'pc.inventario_lote_id')
            ->leftJoin('medicamentos as m', 'm.id', '=', 'pc.medicamento_id')
            ->where('pc.preparacion_id', $id)
            ->select('pc.*', 'il.lote as lote_codigo', 'il.fecha_vencimiento', 'm.nombre as medicamento')
            ->get();

        return view('admin.reportes.trazabilidad.produccion', [
            'tipo' => 'PREPARACION',
            'documento' => $preparacion,
            'consumos' => $consumos,
        ]);
    }

    public function mezcla($id)
    {
        $mezcla = DB::table('mezclas')->where('id', $id)->firstOrFail();

        $consumos = DB::table('mezclas_consumo as mc')
            ->leftJoin('inventario_lotes as il', 'il.id', '=', 'mc.inventario_lote_id')
            ->leftJoin('medicamentos as m', 'm.id', '=', 'mc.medicamento_id')
            ->where('mc.mezcla_id', $id)
            ->select('mc.*', 'il.lote as lote_codigo', 'il.fecha_vencimiento', 'm.nombre as medicamento')
            ->get();

        return view('admin.reportes.trazabilidad.produccion', [
            'tipo' => 'MEZCLA',
            'documento' => $mezcla,
            'consumos' => $consumos,
        ]);
    }

    public function reempaque($id)
    {
        $reempaque = DB::table('reempaques')->where('id', $id)->firstOrFail();

        $consumos = DB::table('reempaques_consumo as rc')
            ->leftJoin('inventario_lotes as il', 'il.id', '=', 'rc.inventario_lote_id')
            ->leftJoin('medicamentos as m', 'm.id', '=', 'rc.medicamento_id')
            ->where('rc.reempaque_id', $id)
            ->select('rc.*', 'il.lote as lote_codigo', 'il.fecha_vencimiento', 'm.nombre as medicamento')
            ->get();

        return view('admin.reportes.trazabilidad.produccion', [
            'tipo' => 'REEMPAQUE',
            'documento' => $reempaque,
            'consumos' => $consumos,
        ]);
    }

    /**
     * Endpoint AJAX para autocompletar el buscador
     */
    public function autocomplete(Request $r)
    {
        $q = trim($r->input('q', ''));
        if (strlen($q) < 2) return response()->json([]);

        $results = [];

        // Lotes
        DB::table('inventario_lotes as il')
            ->leftJoin('medicamentos as m', 'm.id', '=', 'il.medicamento_id')
            ->where('il.lote', 'like', "%{$q}%")
            ->select('il.id', 'il.lote', 'm.nombre as medicamento')
            ->limit(5)->get()
            ->each(function($x) use (&$results) {
                $results[] = ['tipo' => 'lote', 'id' => $x->id,
                    'label' => "Lote {$x->lote} — {$x->medicamento}",
                    'url' => route('admin.reportes.trazabilidad.lote', $x->id)];
            });

        // Pacientes
        DB::table('pacientes')
            ->where('documento', 'like', "%{$q}%")
            ->orWhere('nombres', 'like', "%{$q}%")
            ->orWhere('apellidos', 'like', "%{$q}%")
            ->limit(5)->get()
            ->each(function($x) use (&$results) {
                $results[] = ['tipo' => 'paciente', 'id' => $x->id,
                    'label' => "Paciente {$x->documento} — {$x->nombres} {$x->apellidos}",
                    'url' => route('admin.reportes.trazabilidad.paciente', $x->id)];
            });

        // Preparaciones / Mezclas / Reempaques / Incidentes / Equipos
        foreach ([
            ['preparaciones','preparacion'],
            ['mezclas','mezcla'],
            ['reempaques','reempaque'],
            ['incidentes','incidente'],
            ['equipos_cadena_frio','equipo'],
        ] as [$tabla, $ruta]) {
            DB::table($tabla)->where('codigo', 'like', "%{$q}%")->limit(3)->get()
                ->each(function($x) use (&$results, $tabla, $ruta) {
                    $results[] = ['tipo' => $ruta, 'id' => $x->id,
                        'label' => strtoupper($ruta) . " {$x->codigo}",
                        'url' => route("admin.reportes.trazabilidad.{$ruta}", $x->id)];
                });
        }

        return response()->json($results);
    }
}
