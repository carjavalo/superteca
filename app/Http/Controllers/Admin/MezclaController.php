<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\InventarioLote;
use App\Models\Medicamento;
use App\Models\Mezcla;
use App\Models\MezclaConsumo;
use App\Models\MezclaControlCalidad;
use App\Models\MezclaDetalle;
use App\Models\MezclaProductoFinal;
use App\Models\MovimientoInventario;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MezclaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mezcla::with(['formula','preparador'])->withCount('detalles');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo','like',"%$s%")
                  ->orWhereHas('formula', fn($f) => $f->where('nombre','like',"%$s%"));
            });
        }
        if ($request->filled('estado')) $query->where('estado', $request->estado);
        if ($request->filled('tipo'))   $query->where('tipo_mezcla', $request->tipo);

        $todos = $query->latest('fecha_programada')->get();

        $kanban = [];
        foreach (array_keys(Mezcla::ESTADOS) as $est) {
            $kanban[$est] = $todos->where('estado', $est)->values();
        }

        $stats = [
            'hoy'         => Mezcla::whereDate('fecha_programada', today())->count(),
            'en_proceso'  => Mezcla::where('estado','EN_PROCESO')->count(),
            'control'     => Mezcla::where('estado','CONTROL_CALIDAD')->count(),
            'liberadas'   => Mezcla::where('estado','LIBERADA')->whereDate('updated_at', today())->count(),
            'canceladas'  => Mezcla::where('estado','CANCELADA')->count(),
        ];

        return view('admin.mezclas.index', compact('kanban','stats','todos'));
    }

    public function create()
    {
        $formulas = Formula::with('detalles.medicamento','detalles.unidadMedida')
            ->where('estado', true)->orderBy('nombre')->get();
        $medicamentos = Medicamento::orderBy('nombre')->get();
        $unidades = UnidadMedida::orderBy('nombre')->get();
        $lotes = InventarioLote::with('medicamento')
            ->where('estado', 1)->where('cantidad_actual','>',0)
            ->orderBy('fecha_vencimiento')->get();

        return view('admin.mezclas.create', compact('formulas','medicamentos','unidades','lotes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'formula_id' => 'nullable|exists:formulas,id',
            'tipo_mezcla' => 'required|in:'.implode(',', array_keys(Mezcla::TIPOS)),
            'fecha_programada' => 'required|date',
            'volumen_programado' => 'nullable|numeric|min:0',
            'unidad_volumen_id' => 'nullable|exists:unidades_medida,id',
            'cantidad_preparaciones' => 'required|integer|min:1',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.medicamento_id' => 'required|exists:medicamentos,id',
            'detalles.*.dosis_requerida' => 'required|numeric|min:0',
            'detalles.*.unidad_medida_id' => 'nullable|exists:unidades_medida,id',
        ]);

        DB::transaction(function () use ($data, $request) {
            $mezcla = Mezcla::create([
                'formula_id' => $data['formula_id'] ?? null,
                'tipo_mezcla' => $data['tipo_mezcla'],
                'fecha_programada' => $data['fecha_programada'],
                'volumen_programado' => $data['volumen_programado'] ?? null,
                'unidad_volumen_id' => $data['unidad_volumen_id'] ?? null,
                'cantidad_preparaciones' => $data['cantidad_preparaciones'],
                'observaciones' => $data['observaciones'] ?? null,
                'estado' => 'PROGRAMADA',
                'usuario_preparador_id' => Auth::id(),
            ]);

            foreach ($data['detalles'] as $i => $det) {
                MezclaDetalle::create([
                    'mezcla_id' => $mezcla->id,
                    'medicamento_id' => $det['medicamento_id'],
                    'presentacion_id' => $det['presentacion_id'] ?? null,
                    'dosis_requerida' => $det['dosis_requerida'],
                    'unidad_medida_id' => $det['unidad_medida_id'] ?? null,
                    'orden_preparacion' => $i + 1,
                    'observaciones' => $det['observaciones'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.mezclas.index')->with('success','Mezcla programada exitosamente.');
    }

    public function show(Mezcla $mezcla)
    {
        $mezcla->load([
            'formula','detalles.medicamento','detalles.unidadMedida',
            'consumos.medicamento','consumos.lote',
            'controles.usuario','productosFinales','preparador','validador','unidadVolumen'
        ]);

        $lotesDisponibles = InventarioLote::with('medicamento')
            ->where('estado', 1)->where('cantidad_actual','>',0)
            ->orderBy('fecha_vencimiento')->get();

        return view('admin.mezclas.show', compact('mezcla','lotesDisponibles'));
    }

    public function iniciar(Mezcla $mezcla)
    {
        if ($mezcla->estado !== 'PROGRAMADA') {
            return back()->with('error','Solo se pueden iniciar mezclas en estado Programada.');
        }
        $mezcla->update(['estado' => 'EN_PROCESO', 'fecha_inicio' => now()]);
        return back()->with('success','Mezcla iniciada.');
    }

    public function registrarConsumo(Request $request, Mezcla $mezcla)
    {
        if (!in_array($mezcla->estado, ['EN_PROCESO'])) {
            return back()->with('error','Solo se puede consumir cuando la mezcla está En Proceso.');
        }

        $data = $request->validate([
            'inventario_lote_id' => 'required|exists:inventario_lotes,id',
            'cantidad_consumida' => 'required|numeric|min:0.0001',
        ]);

        $lote = InventarioLote::with('medicamento')->findOrFail($data['inventario_lote_id']);
        if ($lote->cantidad_actual < $data['cantidad_consumida']) {
            return back()->with('error','El lote no tiene suficiente stock para registrar el consumo.');
        }

        $costoU = $lote->costo_unitario ?: 0;

        MezclaConsumo::create([
            'mezcla_id' => $mezcla->id,
            'inventario_lote_id' => $lote->id,
            'medicamento_id' => $lote->medicamento_id,
            'presentacion_id' => $lote->presentacion_id ?? null,
            'lote' => $lote->lote,
            'fecha_vencimiento' => $lote->fecha_vencimiento,
            'cantidad_consumida' => $data['cantidad_consumida'],
            'costo_unitario' => $costoU,
            'costo_total' => $costoU * $data['cantidad_consumida'],
        ]);

        return back()->with('success','Consumo registrado en la mezcla.');
    }

    public function eliminarConsumo(Mezcla $mezcla, MezclaConsumo $consumo)
    {
        if ($mezcla->estado !== 'EN_PROCESO') {
            return back()->with('error','No se puede eliminar consumo en este estado.');
        }
        $consumo->delete();
        return back()->with('success','Consumo eliminado.');
    }

    public function enviarControl(Mezcla $mezcla)
    {
        if ($mezcla->estado !== 'EN_PROCESO') {
            return back()->with('error','La mezcla debe estar En Proceso.');
        }
        if ($mezcla->consumos()->count() === 0) {
            return back()->with('error','Debe registrar al menos un consumo antes de enviar a control.');
        }
        $mezcla->update(['estado' => 'CONTROL_CALIDAD']);
        return back()->with('success','Mezcla enviada a Control de Calidad.');
    }

    public function registrarControl(Request $request, Mezcla $mezcla)
    {
        $data = $request->validate([
            'aspecto_visual' => 'nullable|string|max:255',
            'volumen_verificado' => 'nullable|numeric',
            'ph' => 'nullable|numeric',
            'osmolaridad' => 'nullable|numeric',
            'temperatura' => 'nullable|numeric',
            'cumple' => 'required|boolean',
            'observaciones' => 'nullable|string',
        ]);

        MezclaControlCalidad::create(array_merge($data, [
            'mezcla_id' => $mezcla->id,
            'fecha_control' => now(),
            'usuario_control_id' => Auth::id(),
        ]));

        return back()->with('success','Control de calidad registrado.');
    }

    public function liberar(Request $request, Mezcla $mezcla)
    {
        if ($mezcla->estado !== 'CONTROL_CALIDAD') {
            return back()->with('error','La mezcla debe estar en Control de Calidad para liberarla.');
        }
        if ($mezcla->controles()->where('cumple', true)->count() === 0) {
            return back()->with('error','Debe haber al menos un control de calidad aprobado.');
        }

        DB::transaction(function () use ($mezcla, $request) {
            $costoTotal = 0;

            foreach ($mezcla->consumos as $c) {
                $lote = InventarioLote::find($c->inventario_lote_id);
                if (!$lote) continue;

                $stockAnterior = $lote->cantidad_actual;
                $lote->cantidad_actual -= $c->cantidad_consumida;
                if ($lote->cantidad_actual < 0) $lote->cantidad_actual = 0;
                $lote->save();

                $movData = [
                    'tipo_movimiento' => 'PRODUCCION',
                    'referencia_tipo' => 'Mezcla',
                    'referencia_id' => $mezcla->id,
                    'inventario_lote_id' => $lote->id,
                    'cantidad' => -1 * $c->cantidad_consumida,
                    'stock_anterior' => $stockAnterior,
                    'stock_nuevo' => $lote->cantidad_actual,
                    'fecha_movimiento' => now(),
                    'usuario_id' => Auth::id(),
                    'observacion' => "Liberación mezcla {$mezcla->codigo}",
                ];
                if (Schema::hasColumn('movimientos_inventario','medicamento_id')) {
                    $movData['medicamento_id'] = $c->medicamento_id;
                }
                if (Schema::hasColumn('movimientos_inventario','lote_codigo')) {
                    $movData['lote_codigo'] = $c->lote;
                }
                if (Schema::hasColumn('movimientos_inventario','fecha_vencimiento')) {
                    $movData['fecha_vencimiento'] = $c->fecha_vencimiento;
                }
                MovimientoInventario::create($movData);

                $costoTotal += $c->costo_total;
            }

            // Producto final
            MezclaProductoFinal::create([
                'mezcla_id' => $mezcla->id,
                'lote_produccion' => 'PROD-' . $mezcla->codigo,
                'fecha_produccion' => now(),
                'fecha_vencimiento' => $request->input('fecha_vencimiento_producto') ?: now()->addHours(24),
                'volumen_final' => $request->input('volumen_final') ?: $mezcla->volumen_programado,
                'unidad_volumen_id' => $mezcla->unidad_volumen_id,
                'cantidad_unidades' => $mezcla->cantidad_preparaciones,
                'observaciones' => $request->input('observaciones_producto'),
            ]);

            $mezcla->update([
                'estado' => 'LIBERADA',
                'fecha_fin' => now(),
                'costo_total' => $costoTotal,
                'usuario_validador_id' => Auth::id(),
            ]);
        });

        return back()->with('success','Mezcla liberada. Inventario actualizado y producto final generado.');
    }

    public function cancelar(Mezcla $mezcla)
    {
        if (in_array($mezcla->estado, ['LIBERADA','CANCELADA'])) {
            return back()->with('error','No se puede cancelar en este estado.');
        }
        $mezcla->update(['estado' => 'CANCELADA']);
        return back()->with('success','Mezcla cancelada.');
    }

    public function destroy(Mezcla $mezcla)
    {
        if ($mezcla->estado === 'LIBERADA') {
            return back()->with('error','No se puede eliminar una mezcla liberada.');
        }
        $mezcla->delete();
        return redirect()->route('admin.mezclas.index')->with('success','Mezcla eliminada.');
    }
}
