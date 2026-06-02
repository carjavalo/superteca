<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bodega;
use App\Models\InventarioLote;
use App\Models\Medicamento;
use App\Models\MovimientoInventario;
use App\Models\Proveedor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KardexController extends Controller
{
    /**
     * Vista 1: Kardex General (libro mayor estilo ERP).
     */
    public function index(Request $request)
    {
        $q = MovimientoInventario::query()
            ->with(['medicamento','presentacion','lote','usuario','bodegaOrigen','bodegaDestino','proveedor'])
            ->orderByDesc('fecha_movimiento')
            ->orderByDesc('id');

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($w) use ($s) {
                $w->where('observacion', 'like', "%$s%")
                  ->orWhereHas('lote', fn($l) => $l->where('lote', 'like', "%$s%")
                      ->orWhereHas('medicamento', fn($m) => $m->where('nombre', 'like', "%$s%")));
            });
        }
        if ($request->filled('tipo'))           $q->where('tipo_movimiento', $request->tipo);
        if ($request->filled('medicamento_id')) $q->whereHas('lote', fn($l) => $l->where('medicamento_id', $request->medicamento_id));
        if ($request->filled('lote_id'))        $q->where('inventario_lote_id', $request->lote_id);
        if ($request->filled('usuario_id'))     $q->where('usuario_id', $request->usuario_id);
        if ($request->filled('desde'))          $q->whereDate('fecha_movimiento', '>=', $request->desde);
        if ($request->filled('hasta'))          $q->whereDate('fecha_movimiento', '<=', $request->hasta);

        $movimientos = $q->paginate(50)->withQueryString();

        // KPIs
        $stats = [
            'stock_total'       => (float) InventarioLote::where('estado', 1)->sum('cantidad_actual'),
            'movimientos_hoy'   => MovimientoInventario::whereDate('fecha_movimiento', today())->count(),
            'lotes_activos'     => InventarioLote::where('estado', 1)->where('cantidad_actual', '>', 0)->count(),
            'valor_inventario'  => (float) DB::table('inventario_lotes')
                                        ->where('estado', 1)
                                        ->sum(DB::raw('cantidad_actual * costo_unitario')),
        ];

        $medicamentos = Medicamento::orderBy('nombre')->get(['id','nombre']);
        $usuarios     = User::orderBy('name')->get(['id','name','apellido1']);
        $tipos        = MovimientoInventario::TIPOS;

        return view('admin.kardex.index', compact('movimientos','stats','medicamentos','usuarios','tipos'));
    }

    /**
     * Vista 2: Timeline por lote (trazabilidad completa).
     */
    public function porLote(Request $request, ?int $lote_id = null)
    {
        $lotes = InventarioLote::with(['medicamento','presentacion'])
            ->orderByDesc('fecha_ingreso')
            ->limit(500)
            ->get();

        $loteSeleccionado = null;
        $movimientos = collect();
        $resumen = ['entradas' => 0, 'salidas' => 0, 'ajustes' => 0, 'saldo' => 0, 'valor' => 0];

        $lote_id = $lote_id ?? $request->lote_id;
        if ($lote_id) {
            $loteSeleccionado = InventarioLote::with(['medicamento','presentacion','proveedor'])->find($lote_id);
            if ($loteSeleccionado) {
                $movimientos = MovimientoInventario::with(['usuario','bodegaOrigen','bodegaDestino'])
                    ->where('inventario_lote_id', $lote_id)
                    ->orderBy('fecha_movimiento')
                    ->orderBy('id')
                    ->get();

                $resumen['entradas'] = (float) $movimientos->where('cantidad','>',0)->sum('cantidad');
                $resumen['salidas']  = (float) abs($movimientos->where('cantidad','<',0)->sum('cantidad'));
                $resumen['ajustes']  = $movimientos->whereIn('tipo_movimiento', ['AJUSTE','AJUSTE_POSITIVO','AJUSTE_NEGATIVO'])->count();
                $resumen['saldo']    = (float) $loteSeleccionado->cantidad_actual;
                $resumen['valor']    = (float) ($loteSeleccionado->cantidad_actual * $loteSeleccionado->costo_unitario);
            }
        }

        return view('admin.kardex.lote', compact('lotes','loteSeleccionado','movimientos','resumen'));
    }

    /**
     * Vista 3: Dashboard analítico con gráficas.
     */
    public function analytics(Request $request)
    {
        $desde = $request->desde ? Carbon::parse($request->desde) : Carbon::now()->subDays(30);
        $hasta = $request->hasta ? Carbon::parse($request->hasta) : Carbon::now();

        // Movimientos por día
        $porDia = MovimientoInventario::selectRaw("DATE(fecha_movimiento) as fecha, tipo_movimiento, COUNT(*) as cnt")
            ->whereBetween('fecha_movimiento', [$desde->copy()->startOfDay(), $hasta->copy()->endOfDay()])
            ->groupBy('fecha','tipo_movimiento')
            ->orderBy('fecha')
            ->get();

        // Top medicamentos consumidos — JOIN a través de inventario_lotes (compatible con y sin migración)
        $topConsumo = DB::table('movimientos_inventario as m')
            ->join('inventario_lotes as il', 'il.id', '=', 'm.inventario_lote_id')
            ->leftJoin('medicamentos as med', 'med.id', '=', 'il.medicamento_id')
            ->selectRaw('il.medicamento_id, med.nombre as medicamento_nombre, SUM(ABS(m.cantidad)) as total')
            ->where('m.cantidad', '<', 0)
            ->whereBetween('m.fecha_movimiento', [$desde, $hasta])
            ->groupBy('il.medicamento_id', 'med.nombre')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Ajustes por usuario — JOIN para obtener nombre sin depender de columnas opcionales
        $ajustesUsuario = DB::table('movimientos_inventario as m')
            ->leftJoin('users as u', 'u.id', '=', 'm.usuario_id')
            ->selectRaw('m.usuario_id, u.name, u.apellido1, COUNT(*) as cnt')
            ->whereIn('m.tipo_movimiento', ['AJUSTE','AJUSTE_POSITIVO','AJUSTE_NEGATIVO'])
            ->whereBetween('m.fecha_movimiento', [$desde, $hasta])
            ->groupBy('m.usuario_id', 'u.name', 'u.apellido1')
            ->orderByDesc('cnt')
            ->limit(8)
            ->get();

        // Distribución por tipo
        $distTipos = MovimientoInventario::selectRaw('tipo_movimiento, COUNT(*) as cnt, SUM(ABS(cantidad)) as und')
            ->whereBetween('fecha_movimiento', [$desde, $hasta])
            ->groupBy('tipo_movimiento')
            ->get();

        // Vencimientos próximos (90 días)
        $vencimientos = InventarioLote::where('estado', 1)
            ->where('cantidad_actual', '>', 0)
            ->whereNotNull('fecha_vencimiento')
            ->whereBetween('fecha_vencimiento', [now(), now()->addDays(90)])
            ->with('medicamento:id,nombre')
            ->orderBy('fecha_vencimiento')
            ->limit(15)
            ->get();

        $kpis = [
            'movimientos'   => MovimientoInventario::whereBetween('fecha_movimiento',[$desde,$hasta])->count(),
            'entradas'      => (float) MovimientoInventario::where('cantidad','>',0)->whereBetween('fecha_movimiento',[$desde,$hasta])->sum('cantidad'),
            'salidas'       => (float) abs(MovimientoInventario::where('cantidad','<',0)->whereBetween('fecha_movimiento',[$desde,$hasta])->sum('cantidad')),
            'valor_movido'  => (float) MovimientoInventario::whereBetween('fecha_movimiento',[$desde,$hasta])->sum('costo_total'),
        ];

        return view('admin.kardex.analytics', compact('desde','hasta','porDia','topConsumo','ajustesUsuario','distTipos','vencimientos','kpis'));
    }
}
