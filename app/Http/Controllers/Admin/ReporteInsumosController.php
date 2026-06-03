<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventarioLote;
use App\Models\Laboratorio;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\Proveedor;
use App\Models\ServicioHospitalario;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteInsumosController extends Controller
{
    /** Tipos de movimiento que representan CONSUMO (salidas reales del stock) */
    protected const TIPOS_CONSUMO = [
        'SALIDA','TRASLADO_SALIDA','REEMPAQUE_SALIDA','DISPENSACION',
        'AJUSTE_NEGATIVO','PRODUCCION','VENCIMIENTO',
    ];

    public function index(Request $r)
    {
        // ===== Filtros =====
        $desde = $r->filled('desde') ? Carbon::parse($r->desde)->startOfDay() : Carbon::now()->startOfMonth();
        $hasta = $r->filled('hasta') ? Carbon::parse($r->hasta)->endOfDay()   : Carbon::now()->endOfDay();

        $medicamentoId = $r->get('medicamento_id');
        $laboratorioId = $r->get('laboratorio_id');
        $proveedorId   = $r->get('proveedor_id');
        $servicioId    = $r->get('servicio_id');
        $loteId        = $r->get('lote_id');
        $tipoMov       = $r->get('tipo_mov');

        // Base query: movimientos de consumo (cantidad negativa o tipos de consumo)
        $base = DB::table('movimientos_inventario as mi')
            ->leftJoin('inventario_lotes as il', 'il.id', '=', 'mi.inventario_lote_id')
            ->leftJoin('medicamentos as m', 'm.id', '=', 'mi.medicamento_id')
            ->leftJoin('laboratorios as lb', 'lb.id', '=', 'm.laboratorio_id')
            ->leftJoin('proveedores as pv', 'pv.id', '=', 'mi.proveedor_id')
            ->whereBetween('mi.fecha_movimiento', [$desde, $hasta])
            ->whereIn('mi.tipo_movimiento', $tipoMov ? [$tipoMov] : self::TIPOS_CONSUMO);

        if ($medicamentoId) $base->where('mi.medicamento_id', $medicamentoId);
        if ($laboratorioId) $base->where('m.laboratorio_id', $laboratorioId);
        if ($proveedorId)   $base->where('mi.proveedor_id', $proveedorId);
        if ($loteId)        $base->where('mi.inventario_lote_id', $loteId);

        // Helper: cantidad consumida = ABS(cantidad) cuando es negativa, sino cantidad
        $cantidadConsumida = "ABS(mi.cantidad)";
        $costoConsumido    = "ABS(mi.cantidad) * COALESCE(mi.costo_unitario, il.costo_unitario, 0)";

        // ===== KPIs =====
        $tot = (clone $base)->selectRaw("
            SUM($cantidadConsumida) as cant_total,
            SUM($costoConsumido) as costo_total,
            COUNT(DISTINCT mi.medicamento_id) as medicamentos,
            COUNT(DISTINCT mi.inventario_lote_id) as lotes,
            COUNT(*) as movimientos
        ")->first();

        $kpis = [
            'consumo_total'  => (float) ($tot->costo_total ?? 0),
            'cantidad_total' => (float) ($tot->cant_total ?? 0),
            'medicamentos'   => (int) ($tot->medicamentos ?? 0),
            'lotes'          => (int) ($tot->lotes ?? 0),
            'movimientos'    => (int) ($tot->movimientos ?? 0),
        ];

        // Pacientes atendidos y preparaciones (en el rango)
        $kpis['pacientes'] = DB::table('dispensacion_entregas')
            ->whereBetween('fecha_entrega', [$desde, $hasta])
            ->whereNotNull('paciente_id')
            ->distinct('paciente_id')->count('paciente_id');

        $kpis['preparaciones'] = DB::table('preparaciones')
            ->whereBetween('fecha_programada', [$desde, $hasta])
            ->count();

        $kpis['costo_promedio_paciente'] = $kpis['pacientes'] > 0
            ? $kpis['consumo_total'] / $kpis['pacientes'] : 0;

        // ===== Serie mensual (últimos 12 meses) =====
        $serie = DB::table('movimientos_inventario as mi')
            ->leftJoin('inventario_lotes as il', 'il.id', '=', 'mi.inventario_lote_id')
            ->whereIn('mi.tipo_movimiento', self::TIPOS_CONSUMO)
            ->where('mi.fecha_movimiento', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->selectRaw("DATE_FORMAT(mi.fecha_movimiento, '%Y-%m') as ym,
                         SUM(ABS(mi.cantidad)) as cant,
                         SUM(ABS(mi.cantidad) * COALESCE(mi.costo_unitario, il.costo_unitario, 0)) as costo")
            ->groupBy('ym')->orderBy('ym')->get();

        // ===== Top 20 medicamentos =====
        $topMedicamentos = (clone $base)
            ->selectRaw("m.id, m.nombre,
                         SUM($cantidadConsumida) as cantidad,
                         SUM($costoConsumido) as costo")
            ->groupBy('m.id', 'm.nombre')
            ->orderByDesc('cantidad')
            ->limit(20)->get();

        // ===== Consumo por servicio (vía dispensaciones) =====
        $porServicio = DB::table('dispensacion_entregas as de')
            ->leftJoin('servicios_hospitalarios as s', 's.id', '=', 'de.servicio_id')
            ->leftJoin('dispensacion_entregas_detalle as ded', 'ded.entrega_id', '=', 'de.id')
            ->leftJoin('dispensacion_entregas_lotes as del', 'del.entrega_detalle_id', '=', 'ded.id')
            ->whereBetween('de.fecha_entrega', [$desde, $hasta])
            ->when($servicioId, fn($q) => $q->where('de.servicio_id', $servicioId))
            ->selectRaw("COALESCE(s.nombre, de.tipo_entrega, 'Sin servicio') as servicio,
                         SUM(COALESCE(del.cantidad_entregada, 0)) as cantidad,
                         SUM(COALESCE(del.cantidad_entregada,0) * COALESCE(del.costo_unitario,0)) as costo")
            ->groupByRaw("COALESCE(s.nombre, de.tipo_entrega, 'Sin servicio')")
            ->orderByDesc('cantidad')
            ->limit(15)->get();

        // ===== Consumo por laboratorio =====
        $porLaboratorio = (clone $base)
            ->selectRaw("COALESCE(lb.nombre,'Sin laboratorio') as laboratorio,
                         SUM($cantidadConsumida) as cantidad,
                         SUM($costoConsumido) as costo")
            ->groupByRaw("COALESCE(lb.nombre,'Sin laboratorio')")
            ->orderByDesc('costo')
            ->limit(15)->get();

        // ===== Consumo por proveedor =====
        $porProveedor = (clone $base)
            ->selectRaw("COALESCE(pv.nombre_comercial, pv.razon_social, 'Sin proveedor') as proveedor,
                         SUM($cantidadConsumida) as cantidad,
                         SUM($costoConsumido) as costo")
            ->groupByRaw("COALESCE(pv.nombre_comercial, pv.razon_social, 'Sin proveedor')")
            ->orderByDesc('costo')
            ->limit(15)->get();

        // ===== Top pacientes =====
        $topPacientes = DB::table('dispensacion_entregas as de')
            ->join('pacientes as p', 'p.id', '=', 'de.paciente_id')
            ->leftJoin('dispensacion_entregas_detalle as ded', 'ded.entrega_id', '=', 'de.id')
            ->leftJoin('dispensacion_entregas_lotes as del', 'del.entrega_detalle_id', '=', 'ded.id')
            ->whereBetween('de.fecha_entrega', [$desde, $hasta])
            ->whereNotNull('de.paciente_id')
            ->selectRaw("p.id, CONCAT(COALESCE(p.nombres,''),' ',COALESCE(p.apellidos,'')) as paciente,
                         p.documento,
                         SUM(COALESCE(del.cantidad_entregada,0)) as cantidad,
                         SUM(COALESCE(del.cantidad_entregada,0) * COALESCE(del.costo_unitario,0)) as costo")
            ->groupBy('p.id', 'p.nombres', 'p.apellidos', 'p.documento')
            ->orderByDesc('costo')
            ->limit(15)->get();

        // ===== FEFO: consumo por lote =====
        $porLote = (clone $base)
            ->selectRaw("il.id, il.lote, il.fecha_vencimiento, m.nombre as medicamento,
                         il.cantidad_inicial,
                         il.cantidad_actual,
                         SUM($cantidadConsumida) as consumido")
            ->groupBy('il.id','il.lote','il.fecha_vencimiento','m.nombre','il.cantidad_inicial','il.cantidad_actual')
            ->orderBy('il.fecha_vencimiento')
            ->limit(20)->get();

        // ===== Mapa de calor: medicamento × servicio =====
        $heatRaw = DB::table('dispensacion_entregas as de')
            ->leftJoin('servicios_hospitalarios as s', 's.id', '=', 'de.servicio_id')
            ->join('dispensacion_entregas_detalle as ded', 'ded.entrega_id', '=', 'de.id')
            ->join('dispensacion_entregas_lotes as del', 'del.entrega_detalle_id', '=', 'ded.id')
            ->join('medicamentos as m', 'm.id', '=', 'ded.medicamento_id')
            ->whereBetween('de.fecha_entrega', [$desde, $hasta])
            ->selectRaw("m.nombre as medicamento,
                         COALESCE(s.nombre, de.tipo_entrega, 'Sin servicio') as servicio,
                         SUM(del.cantidad_entregada) as cantidad")
            ->groupByRaw("m.nombre, COALESCE(s.nombre, de.tipo_entrega, 'Sin servicio')")
            ->orderByDesc('cantidad')
            ->limit(60)->get();

        $heatMeds = $heatRaw->pluck('medicamento')->unique()->take(15)->values()->all();
        $heatSrvs = $heatRaw->pluck('servicio')->unique()->take(8)->values()->all();
        $heatMap = [];
        $heatMax = 0;
        foreach ($heatMeds as $med) {
            foreach ($heatSrvs as $srv) {
                $val = (float) optional($heatRaw->first(fn($x) => $x->medicamento===$med && $x->servicio===$srv))->cantidad ?? 0;
                $heatMap[$med][$srv] = $val;
                if ($val > $heatMax) $heatMax = $val;
            }
        }

        // ===== Predictiva: días de inventario =====
        $diasRango = max(1, $desde->diffInDays($hasta) + 1);
        $consumoPorMed = (clone $base)
            ->selectRaw("mi.medicamento_id, SUM($cantidadConsumida) as cant")
            ->groupBy('mi.medicamento_id')->pluck('cant', 'medicamento_id');

        $stockPorMed = DB::table('inventario_lotes')
            ->selectRaw('medicamento_id, SUM(cantidad_actual) as stock')
            ->groupBy('medicamento_id')->pluck('stock', 'medicamento_id');

        $predictivo = [];
        foreach ($consumoPorMed as $medId => $cant) {
            $promedioDiario = (float) $cant / $diasRango;
            if ($promedioDiario <= 0) continue;
            $stock = (float) ($stockPorMed[$medId] ?? 0);
            $dias  = $stock / $promedioDiario;
            $predictivo[] = (object) [
                'medicamento_id' => $medId,
                'nombre'         => optional(Medicamento::find($medId))->nombre ?? '—',
                'stock'          => $stock,
                'promedio_diario'=> $promedioDiario,
                'dias'           => $dias,
            ];
        }
        usort($predictivo, fn($a,$b) => $a->dias <=> $b->dias);
        $predictivo = array_slice($predictivo, 0, 20);

        // ===== Producción: mezclas / preparaciones / reempaques =====
        $produccion = [
            'mezclas' => DB::table('mezclas_consumo as mc')
                ->join('mezclas as mz', 'mz.id', '=', 'mc.mezcla_id')
                ->leftJoin('inventario_lotes as il','il.id','=','mc.inventario_lote_id')
                ->leftJoin('medicamentos as m','m.id','=','il.medicamento_id')
                ->whereBetween('mz.fecha_programada', [$desde, $hasta])
                ->selectRaw("'MEZCLAS' as proceso, COUNT(DISTINCT mz.id) as procesos,
                             SUM(mc.cantidad_consumida) as cant,
                             SUM(mc.cantidad_consumida * COALESCE(mc.costo_unitario,0)) as costo")
                ->first(),
            'preparaciones' => DB::table('preparaciones_consumo as pc')
                ->join('preparaciones as pr', 'pr.id', '=', 'pc.preparacion_id')
                ->whereBetween('pr.fecha_programada', [$desde, $hasta])
                ->selectRaw("'PREPARACIONES' as proceso, COUNT(DISTINCT pr.id) as procesos,
                             SUM(pc.cantidad_consumida) as cant,
                             SUM(pc.cantidad_consumida * COALESCE(pc.costo_unitario,0)) as costo")
                ->first(),
            'reempaques' => DB::table('reempaques_consumo as rc')
                ->join('reempaques as re', 're.id', '=', 'rc.reempaque_id')
                ->whereBetween('re.fecha_programada', [$desde, $hasta])
                ->selectRaw("'REEMPAQUES' as proceso, COUNT(DISTINCT re.id) as procesos,
                             SUM(rc.cantidad_consumida) as cant,
                             SUM(rc.cantidad_consumida * COALESCE(rc.costo_unitario,0)) as costo")
                ->first(),
        ];

        // ===== Catálogos para filtros =====
        $medicamentos = Medicamento::orderBy('nombre')->limit(500)->get(['id','nombre']);
        $laboratorios = Laboratorio::orderBy('nombre')->get(['id','nombre']);
        $proveedores  = Proveedor::orderBy('razon_social')->get(['id','razon_social','nombre_comercial']);
        $servicios    = ServicioHospitalario::orderBy('nombre')->get(['id','nombre']);
        $lotes        = InventarioLote::with('medicamento')->orderByDesc('id')->limit(300)->get();

        return view('admin.reportes.insumos', compact(
            'desde','hasta','medicamentoId','laboratorioId','proveedorId','servicioId','loteId','tipoMov',
            'kpis','serie','topMedicamentos','porServicio','porLaboratorio','porProveedor',
            'topPacientes','porLote','heatMeds','heatSrvs','heatMap','heatMax','predictivo','produccion',
            'medicamentos','laboratorios','proveedores','servicios','lotes'
        ));
    }

    /** Exportación CSV simple del top de medicamentos del rango filtrado */
    public function exportar(Request $r)
    {
        $desde = $r->filled('desde') ? Carbon::parse($r->desde)->startOfDay() : Carbon::now()->startOfMonth();
        $hasta = $r->filled('hasta') ? Carbon::parse($r->hasta)->endOfDay()   : Carbon::now()->endOfDay();

        $rows = DB::table('movimientos_inventario as mi')
            ->leftJoin('inventario_lotes as il','il.id','=','mi.inventario_lote_id')
            ->leftJoin('medicamentos as m','m.id','=','mi.medicamento_id')
            ->whereBetween('mi.fecha_movimiento', [$desde, $hasta])
            ->whereIn('mi.tipo_movimiento', self::TIPOS_CONSUMO)
            ->selectRaw("m.nombre as medicamento, il.lote, mi.fecha_movimiento, mi.tipo_movimiento,
                         ABS(mi.cantidad) as cantidad,
                         COALESCE(mi.costo_unitario, il.costo_unitario, 0) as costo_unitario,
                         (ABS(mi.cantidad) * COALESCE(mi.costo_unitario, il.costo_unitario, 0)) as costo_total")
            ->orderBy('mi.fecha_movimiento')
            ->get();

        $filename = 'consumos_'.$desde->format('Ymd').'_'.$hasta->format('Ymd').'.csv';

        return response()->streamDownload(function() use ($rows) {
            $h = fopen('php://output','w');
            fputcsv($h, ['Medicamento','Lote','Fecha','Tipo','Cantidad','Costo unitario','Costo total']);
            foreach ($rows as $r) {
                fputcsv($h, [$r->medicamento, $r->lote, $r->fecha_movimiento, $r->tipo_movimiento,
                             $r->cantidad, $r->costo_unitario, $r->costo_total]);
            }
            fclose($h);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
