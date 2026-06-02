<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventarioLote;
use App\Models\Medicamento;
use App\Models\MovimientoInventario;
use App\Models\Presentacion;
use App\Models\Reempaque;
use App\Models\ReempaqueConsumo;
use App\Models\ReempaqueControlCalidad;
use App\Models\ReempaqueDetalle;
use App\Models\ReempaqueProductoFinal;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReempaqueController extends Controller
{
    public function index(Request $request)
    {
        $query = Reempaque::with(['medicamentoOrigen','responsable'])->withCount('consumos','productosFinales');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'like', "%$s%")
                  ->orWhereHas('medicamentoOrigen', fn($m) => $m->where('nombre','like',"%$s%"));
            });
        }
        if ($request->filled('estado')) $query->where('estado', $request->estado);

        $todos = $query->latest('fecha_programada')->get();

        $kanban = [];
        foreach (array_keys(Reempaque::ESTADOS) as $est) {
            $kanban[$est] = $todos->where('estado', $est)->values();
        }

        $unidadesGeneradas = ReempaqueProductoFinal::whereDate('fecha_reempaque', today())->sum('cantidad_generada');

        $stats = [
            'hoy' => Reempaque::whereDate('fecha_programada', today())->count(),
            'en_proceso' => Reempaque::where('estado','EN_PROCESO')->count(),
            'control' => Reempaque::where('estado','CONTROL_CALIDAD')->count(),
            'liberados' => Reempaque::where('estado','LIBERADO')->whereDate('updated_at', today())->count(),
            'unidades_hoy' => $unidadesGeneradas,
        ];

        return view('admin.reempaques.index', compact('kanban','stats','todos'));
    }

    public function create()
    {
        $medicamentos = Medicamento::orderBy('nombre')->get();
        $presentaciones = Presentacion::orderBy('nombre')->get();
        $unidades = UnidadMedida::orderBy('nombre')->get();
        $lotes = InventarioLote::with('medicamento','presentacion')
            ->where('estado', 1)->where('cantidad_actual', '>', 0)
            ->orderBy('fecha_vencimiento')->get();

        return view('admin.reempaques.create', compact('medicamentos','presentaciones','unidades','lotes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'medicamento_origen_id' => 'required|exists:medicamentos,id',
            'presentacion_origen_id' => 'nullable|exists:presentaciones,id',
            'presentacion_destino_id' => 'nullable|exists:presentaciones,id',
            'unidad_medida_destino_id' => 'nullable|exists:unidades_medida,id',
            'factor_conversion' => 'required|numeric|min:0.0001',
            'cantidad_esperada' => 'nullable|numeric|min:0',
            'fecha_programada' => 'required|date',
            'observaciones' => 'nullable|string',
            'detalles' => 'nullable|array',
            'detalles.*.insumo' => 'required_with:detalles|string|max:255',
            'detalles.*.cantidad' => 'required_with:detalles|numeric|min:0',
            'detalles.*.unidad_medida_id' => 'nullable|exists:unidades_medida,id',
            'detalles.*.costo_unitario' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($data) {
            $reempaque = Reempaque::create([
                'medicamento_origen_id' => $data['medicamento_origen_id'],
                'presentacion_origen_id' => $data['presentacion_origen_id'] ?? null,
                'presentacion_destino_id' => $data['presentacion_destino_id'] ?? null,
                'unidad_medida_destino_id' => $data['unidad_medida_destino_id'] ?? null,
                'factor_conversion' => $data['factor_conversion'],
                'cantidad_esperada' => $data['cantidad_esperada'] ?? 0,
                'fecha_programada' => $data['fecha_programada'],
                'observaciones' => $data['observaciones'] ?? null,
                'estado' => 'PROGRAMADO',
                'usuario_responsable_id' => Auth::id(),
            ]);

            foreach ($data['detalles'] ?? [] as $det) {
                $cu = $det['costo_unitario'] ?? 0;
                ReempaqueDetalle::create([
                    'reempaque_id' => $reempaque->id,
                    'insumo' => $det['insumo'],
                    'cantidad' => $det['cantidad'],
                    'unidad_medida_id' => $det['unidad_medida_id'] ?? null,
                    'costo_unitario' => $cu,
                    'costo_total' => $cu * $det['cantidad'],
                ]);
            }
        });

        return redirect()->route('admin.reempaques.index')->with('success','Reempaque programado exitosamente.');
    }

    public function show(Reempaque $reempaque)
    {
        $reempaque->load([
            'medicamentoOrigen','presentacionOrigen','presentacionDestino','unidadDestino',
            'detalles.unidadMedida','consumos.medicamento','consumos.unidadMedida',
            'productosFinales.medicamento','productosFinales.presentacion','productosFinales.unidadMedida',
            'controles.usuario','responsable','aprobador',
        ]);

        $lotesDisponibles = InventarioLote::with('medicamento')
            ->where('medicamento_id', $reempaque->medicamento_origen_id)
            ->where('estado', 1)->where('cantidad_actual','>',0)
            ->orderBy('fecha_vencimiento')->get();

        return view('admin.reempaques.show', compact('reempaque','lotesDisponibles'));
    }

    public function iniciar(Reempaque $reempaque)
    {
        if ($reempaque->estado !== 'PROGRAMADO') {
            return back()->with('error','Solo se pueden iniciar reempaques en estado Programado.');
        }
        $reempaque->update(['estado' => 'EN_PROCESO', 'fecha_inicio' => now()]);
        return back()->with('success','Reempaque iniciado.');
    }

    public function registrarConsumo(Request $request, Reempaque $reempaque)
    {
        if ($reempaque->estado !== 'EN_PROCESO') {
            return back()->with('error','Solo se puede consumir cuando el reempaque está En Proceso.');
        }

        $data = $request->validate([
            'inventario_lote_id' => 'required|exists:inventario_lotes,id',
            'cantidad_consumida' => 'required|numeric|min:0.0001',
        ]);

        $lote = InventarioLote::with('medicamento')->findOrFail($data['inventario_lote_id']);
        if ($lote->cantidad_actual < $data['cantidad_consumida']) {
            return back()->with('error','El lote no tiene suficiente stock.');
        }

        $costoU = $lote->costo_unitario ?: 0;

        ReempaqueConsumo::create([
            'reempaque_id' => $reempaque->id,
            'inventario_lote_id' => $lote->id,
            'medicamento_id' => $lote->medicamento_id,
            'lote_origen' => $lote->lote,
            'fecha_vencimiento' => $lote->fecha_vencimiento,
            'cantidad_consumida' => $data['cantidad_consumida'],
            'unidad_medida_id' => $lote->unidad_medida_id,
            'costo_unitario' => $costoU,
            'costo_total' => $costoU * $data['cantidad_consumida'],
        ]);

        return back()->with('success','Consumo registrado.');
    }

    public function eliminarConsumo(Reempaque $reempaque, ReempaqueConsumo $consumo)
    {
        if ($reempaque->estado !== 'EN_PROCESO') {
            return back()->with('error','No se puede eliminar consumo en este estado.');
        }
        $consumo->delete();
        return back()->with('success','Consumo eliminado.');
    }

    public function enviarControl(Reempaque $reempaque)
    {
        if ($reempaque->estado !== 'EN_PROCESO') {
            return back()->with('error','El reempaque debe estar En Proceso.');
        }
        if ($reempaque->consumos()->count() === 0) {
            return back()->with('error','Debe registrar al menos un consumo antes de enviar a control.');
        }
        $reempaque->update(['estado' => 'CONTROL_CALIDAD']);
        return back()->with('success','Reempaque enviado a Control de Calidad.');
    }

    public function registrarControl(Request $request, Reempaque $reempaque)
    {
        $data = $request->validate([
            'cantidad_verificada' => 'nullable|numeric',
            'etiquetado_correcto' => 'required|boolean',
            'lote_visible' => 'required|boolean',
            'fecha_vencimiento_visible' => 'required|boolean',
            'cumple' => 'required|boolean',
            'observaciones' => 'nullable|string',
        ]);

        ReempaqueControlCalidad::create(array_merge($data, [
            'reempaque_id' => $reempaque->id,
            'fecha_control' => now(),
            'usuario_control_id' => Auth::id(),
        ]));

        return back()->with('success','Control de calidad registrado.');
    }

    public function liberar(Request $request, Reempaque $reempaque)
    {
        if ($reempaque->estado !== 'CONTROL_CALIDAD') {
            return back()->with('error','El reempaque debe estar en Control de Calidad para liberarlo.');
        }
        if ($reempaque->controles()->where('cumple', true)->count() === 0) {
            return back()->with('error','Debe haber al menos un control de calidad aprobado.');
        }
        if ($reempaque->consumos()->count() === 0) {
            return back()->with('error','No hay consumos registrados.');
        }

        $request->validate([
            'fecha_vencimiento_producto' => 'nullable|date',
        ]);

        DB::transaction(function () use ($reempaque, $request) {
            $costoTotal = 0;
            $totalGenerado = 0;
            $factor = (float) $reempaque->factor_conversion ?: 1;
            $loteReempaque = $reempaque->codigo;
            $fechaVenc = $request->input('fecha_vencimiento_producto');

            foreach ($reempaque->consumos as $c) {
                $lote = InventarioLote::find($c->inventario_lote_id);
                if (!$lote) continue;

                // 1. Descontar inventario origen
                $stockAnterior = $lote->cantidad_actual;
                $lote->cantidad_actual = max(0, $lote->cantidad_actual - $c->cantidad_consumida);
                $lote->save();

                // 2. Movimiento REEMPAQUE_SALIDA
                $movSalida = [
                    'tipo_movimiento' => 'REEMPAQUE_SALIDA',
                    'referencia_tipo' => 'Reempaque',
                    'referencia_id' => $reempaque->id,
                    'inventario_lote_id' => $lote->id,
                    'cantidad' => -1 * $c->cantidad_consumida,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $lote->cantidad_actual,
                    'fecha_movimiento' => now(),
                    'usuario_id' => Auth::id(),
                    'observacion' => "Salida por reempaque {$reempaque->codigo}",
                ];
                if (Schema::hasColumn('movimientos_inventario','medicamento_id')) $movSalida['medicamento_id'] = $c->medicamento_id;
                if (Schema::hasColumn('movimientos_inventario','lote_codigo')) $movSalida['lote_codigo'] = $c->lote_origen;
                if (Schema::hasColumn('movimientos_inventario','fecha_vencimiento')) $movSalida['fecha_vencimiento'] = $c->fecha_vencimiento;
                MovimientoInventario::create($movSalida);

                // 3. Calcular cantidad generada y crear nuevo lote
                $cantidadGenerada = (float) $c->cantidad_consumida * $factor;
                $totalGenerado += $cantidadGenerada;
                $costoTotal += (float) $c->costo_total;
                $costoUnitGenerado = $cantidadGenerada > 0 ? ((float) $c->costo_total / $cantidadGenerada) : 0;
                $fvFinal = $fechaVenc ?: ($c->fecha_vencimiento?->format('Y-m-d'));

                $nuevoLote = InventarioLote::create([
                    'medicamento_id' => $reempaque->medicamento_origen_id,
                    'presentacion_id' => $reempaque->presentacion_destino_id ?? $lote->presentacion_id,
                    'lote' => $loteReempaque,
                    'fecha_vencimiento' => $fvFinal,
                    'fecha_ingreso' => now(),
                    'cantidad_inicial' => $cantidadGenerada,
                    'cantidad_actual' => $cantidadGenerada,
                    'unidad_medida_id' => $reempaque->unidad_medida_destino_id ?? $lote->unidad_medida_id,
                    'costo_unitario' => round($costoUnitGenerado, 2),
                    'estado' => 1,
                ]);

                // 4. Movimiento REEMPAQUE_ENTRADA
                $movEntrada = [
                    'tipo_movimiento' => 'REEMPAQUE_ENTRADA',
                    'referencia_tipo' => 'Reempaque',
                    'referencia_id' => $reempaque->id,
                    'inventario_lote_id' => $nuevoLote->id,
                    'cantidad' => $cantidadGenerada,
                    'stock_anterior' => 0,
                    'stock_nuevo' => $cantidadGenerada,
                    'fecha_movimiento' => now(),
                    'usuario_id' => Auth::id(),
                    'observacion' => "Entrada por reempaque {$reempaque->codigo} (origen lote {$c->lote_origen})",
                ];
                if (Schema::hasColumn('movimientos_inventario','medicamento_id')) $movEntrada['medicamento_id'] = $reempaque->medicamento_origen_id;
                if (Schema::hasColumn('movimientos_inventario','lote_codigo')) $movEntrada['lote_codigo'] = $loteReempaque;
                if (Schema::hasColumn('movimientos_inventario','fecha_vencimiento')) $movEntrada['fecha_vencimiento'] = $fvFinal;
                MovimientoInventario::create($movEntrada);

                // 5. Producto Final
                ReempaqueProductoFinal::create([
                    'reempaque_id' => $reempaque->id,
                    'medicamento_id' => $reempaque->medicamento_origen_id,
                    'presentacion_id' => $reempaque->presentacion_destino_id,
                    'lote_reempaque' => $loteReempaque,
                    'lote_origen' => $c->lote_origen,
                    'fecha_reempaque' => now(),
                    'fecha_vencimiento' => $fvFinal,
                    'cantidad_generada' => $cantidadGenerada,
                    'unidad_medida_id' => $reempaque->unidad_medida_destino_id,
                    'costo_unitario' => round($costoUnitGenerado, 4),
                    'inventario_lote_generado_id' => $nuevoLote->id,
                ]);
            }

            // Sumar costos de insumos
            $costoTotal += (float) $reempaque->detalles()->sum('costo_total');

            $reempaque->update([
                'estado' => 'LIBERADO',
                'fecha_fin' => now(),
                'costo_total' => $costoTotal,
                'usuario_aprobador_id' => Auth::id(),
            ]);
        });

        return back()->with('success','Reempaque liberado. Inventario actualizado y nuevo lote generado.');
    }

    public function anular(Reempaque $reempaque)
    {
        if (in_array($reempaque->estado, ['LIBERADO','ANULADO'])) {
            return back()->with('error','No se puede anular en este estado.');
        }
        $reempaque->update(['estado' => 'ANULADO']);
        return back()->with('success','Reempaque anulado.');
    }

    public function destroy(Reempaque $reempaque)
    {
        if ($reempaque->estado === 'LIBERADO') {
            return back()->with('error','No se puede eliminar un reempaque liberado.');
        }
        $reempaque->delete();
        return redirect()->route('admin.reempaques.index')->with('success','Reempaque eliminado.');
    }
}
