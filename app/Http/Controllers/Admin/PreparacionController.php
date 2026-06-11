<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventarioLote;
use App\Models\Medicamento;
use App\Models\MovimientoInventario;
use App\Models\Paciente;
use App\Models\Preparacion;
use App\Models\PreparacionConsumo;
use App\Models\PreparacionControlCalidad;
use App\Models\PreparacionDetalle;
use App\Models\PreparacionEntrega;
use App\Models\TipoServicios;
use App\Models\TipoPreparacion;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PreparacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Preparacion::with(['paciente','tipo','servicio','preparador'])
            ->withCount('detalles');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'like', "%$s%")
                  ->orWhereHas('paciente', fn($p) => $p->where('nombres','like',"%$s%")->orWhere('apellidos','like',"%$s%")->orWhere('documento','like',"%$s%"));
            });
        }
        if ($request->filled('estado')) $query->where('estado', $request->estado);
        if ($request->filled('tipo'))   $query->where('tipo_preparacion_id', $request->tipo);
        if ($request->filled('desde'))  $query->whereDate('fecha_programada', '>=', $request->desde);
        if ($request->filled('hasta'))  $query->whereDate('fecha_programada', '<=', $request->hasta);

        $todos = $query->latest('fecha_programada')->get();

        $kanban = [];
        foreach (array_keys(Preparacion::ESTADOS) as $est) {
            $kanban[$est] = $todos->where('estado', $est)->values();
        }

        $stats = [
            'hoy'         => Preparacion::whereDate('fecha_programada', today())->count(),
            'en_proceso'  => Preparacion::where('estado', 'EN_PROCESO')->count(),
            'control'     => Preparacion::where('estado', 'CONTROL_CALIDAD')->count(),
            'liberadas'   => Preparacion::where('estado', 'LIBERADA')->whereDate('updated_at', today())->count(),
            'entregadas'  => Preparacion::where('estado', 'ENTREGADA')->whereDate('updated_at', today())->count(),
        ];

        $tipos = TipoPreparacion::where('estado', true)->orderBy('nombre')->get();

        return view('admin.preparaciones.index', compact('kanban','stats','tipos','todos'));
    }

    public function create()
    {
        $tipos       = TipoPreparacion::where('estado', true)->orderBy('nombre')->get();
        $servicios   = TipoServicios::orderBy('Detalle')->get();
        $pacientes   = Paciente::where('estado', true)->orderBy('nombres')->get();
        $unidades    = UnidadMedida::orderBy('nombre')->get();
        $medicamentos = Medicamento::orderBy('nombre')->get();
        $lotes       = InventarioLote::with('medicamento')
            ->where('estado', 1)
            ->where('cantidad_actual', '>', 0)
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('admin.preparaciones.create', compact('tipos','servicios','pacientes','unidades','medicamentos','lotes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_preparacion_id' => 'required|exists:tipo_preparaciones,id',
            'paciente_id'         => 'nullable|exists:pacientes,id',
            'servicio_id'         => 'nullable|exists:TipoServicios,id',
            'fecha_programada'    => 'required|date',
            'volumen_final'       => 'nullable|numeric|min:0',
            'unidad_volumen_id'   => 'nullable|exists:unidades_medida,id',
            'observaciones'       => 'nullable|string',
            'detalles'                        => 'required|array|min:1',
            'detalles.*.medicamento_id'       => 'required|exists:medicamentos,id',
            'detalles.*.presentacion_id'      => 'nullable|exists:presentaciones,id',
            'detalles.*.dosis'                => 'required|numeric|min:0.0001',
            'detalles.*.unidad_medida_id'     => 'nullable|exists:unidades_medida,id',
            'detalles.*.concentracion'        => 'nullable|numeric',
            'detalles.*.observaciones'        => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $prep = Preparacion::create([
                'tipo_preparacion_id'    => $data['tipo_preparacion_id'],
                'paciente_id'            => $data['paciente_id']      ?? null,
                'servicio_id'            => $data['servicio_id']      ?? null,
                'fecha_programada'       => $data['fecha_programada'],
                'volumen_final'          => $data['volumen_final']    ?? null,
                'unidad_volumen_id'      => $data['unidad_volumen_id']?? null,
                'observaciones'          => $data['observaciones']    ?? null,
                'estado'                 => 'PROGRAMADA',
                'usuario_preparador_id'  => Auth::id(),
            ]);

            foreach ($data['detalles'] as $d) {
                PreparacionDetalle::create([
                    'preparacion_id'   => $prep->id,
                    'medicamento_id'   => $d['medicamento_id'],
                    'presentacion_id'  => $d['presentacion_id']  ?? null,
                    'dosis'            => $d['dosis'],
                    'unidad_medida_id' => $d['unidad_medida_id'] ?? null,
                    'concentracion'    => $d['concentracion']    ?? null,
                    'observaciones'    => $d['observaciones']    ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('admin.preparaciones.show', $prep)
                ->with('success', "Preparación {$prep->codigo} creada.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Preparacion $preparacion)
    {
        $preparacion->load([
            'paciente.servicio','tipo','servicio','preparador','validador','unidadVolumen',
            'detalles.medicamento','detalles.presentacion','detalles.unidadMedida',
            'consumos.inventarioLote','consumos.medicamento',
            'controles.usuario','entrega.usuario','entrega.servicio',
        ]);

        $lotes = InventarioLote::with('medicamento')
            ->where('estado', 1)
            ->where('cantidad_actual', '>', 0)
            ->orderBy('fecha_vencimiento')
            ->get();

        return view('admin.preparaciones.show', compact('preparacion','lotes'));
    }

    public function iniciar(Preparacion $preparacion)
    {
        abort_if($preparacion->estado !== 'PROGRAMADA', 422, 'Solo preparaciones PROGRAMADAS pueden iniciarse.');
        $preparacion->update(['estado' => 'EN_PROCESO', 'fecha_inicio' => now()]);
        return back()->with('success', "Preparación {$preparacion->codigo} en proceso.");
    }

    public function registrarConsumo(Request $request, Preparacion $preparacion)
    {
        abort_if(!in_array($preparacion->estado, ['EN_PROCESO','PROGRAMADA']), 422, 'No se puede registrar consumo en este estado.');

        $data = $request->validate([
            'inventario_lote_id'  => 'required|exists:inventario_lotes,id',
            'cantidad_consumida'  => 'required|numeric|min:0.0001',
            'unidad_medida_id'    => 'nullable|exists:unidades_medida,id',
        ]);

        $lote = InventarioLote::findOrFail($data['inventario_lote_id']);
        if ((float)$data['cantidad_consumida'] > (float)$lote->cantidad_actual) {
            return back()->with('error', "Stock insuficiente en lote {$lote->lote}: disponible {$lote->cantidad_actual}.");
        }

        $costoUnit = (float)($lote->costo_unitario ?? 0);
        $costoTot  = $costoUnit * (float)$data['cantidad_consumida'];

        PreparacionConsumo::create([
            'preparacion_id'     => $preparacion->id,
            'inventario_lote_id' => $lote->id,
            'medicamento_id'     => $lote->medicamento_id,
            'lote'               => $lote->lote,
            'fecha_vencimiento'  => $lote->fecha_vencimiento,
            'cantidad_consumida' => $data['cantidad_consumida'],
            'unidad_medida_id'   => $data['unidad_medida_id'] ?? null,
            'costo_unitario'     => $costoUnit,
            'costo_total'        => $costoTot,
        ]);

        return back()->with('success', "Consumo registrado del lote {$lote->lote}.");
    }

    public function eliminarConsumo(Preparacion $preparacion, PreparacionConsumo $consumo)
    {
        abort_if($consumo->preparacion_id !== $preparacion->id, 404);
        abort_if($preparacion->estado === 'LIBERADA' || $preparacion->estado === 'ENTREGADA', 422, 'No se puede modificar consumo en este estado.');
        $consumo->delete();
        return back()->with('success', 'Consumo eliminado.');
    }

    public function enviarControl(Preparacion $preparacion)
    {
        abort_if($preparacion->estado !== 'EN_PROCESO', 422, 'Solo preparaciones EN PROCESO pasan a control.');
        abort_if($preparacion->consumos()->count() === 0, 422, 'Registre al menos un consumo de inventario antes de enviar a control.');
        $preparacion->update(['estado' => 'CONTROL_CALIDAD']);
        return back()->with('success', "Preparación {$preparacion->codigo} enviada a Control de Calidad.");
    }

    public function registrarControl(Request $request, Preparacion $preparacion)
    {
        abort_if($preparacion->estado !== 'CONTROL_CALIDAD', 422, 'La preparación no está en control de calidad.');

        $data = $request->validate([
            'aspecto_visual'     => 'nullable|string|max:255',
            'volumen_verificado' => 'nullable|numeric|min:0',
            'ph'                 => 'nullable|numeric',
            'osmolaridad'        => 'nullable|numeric',
            'cumple'             => 'required|boolean',
            'observaciones'      => 'nullable|string',
        ]);

        PreparacionControlCalidad::create(array_merge($data, [
            'preparacion_id'     => $preparacion->id,
            'fecha_control'      => now(),
            'usuario_control_id' => Auth::id(),
        ]));

        return back()->with('success', 'Control de calidad registrado.');
    }

    public function liberar(Preparacion $preparacion)
    {
        abort_if($preparacion->estado !== 'CONTROL_CALIDAD', 422, 'Solo se libera desde Control de Calidad.');

        $ultimoControl = $preparacion->controles()->latest('fecha_control')->first();
        abort_if(!$ultimoControl || !$ultimoControl->cumple, 422, 'Debe existir un control de calidad aprobado (cumple = sí).');

        DB::beginTransaction();
        try {
            $costoTotal = 0;
            foreach ($preparacion->consumos as $c) {
                $lote = InventarioLote::lockForUpdate()->find($c->inventario_lote_id);
                if (!$lote) throw new \Exception("Lote no encontrado para consumo #{$c->id}.");
                if ((float)$c->cantidad_consumida > (float)$lote->cantidad_actual) {
                    throw new \Exception("Stock insuficiente en lote {$lote->lote} al liberar.");
                }

                $anterior = (float)$lote->cantidad_actual;
                $nuevo    = $anterior - (float)$c->cantidad_consumida;
                $lote->cantidad_actual = $nuevo;
                $lote->save();

                $movData = [
                    'tipo_movimiento'    => 'PRODUCCION',
                    'referencia_tipo'    => 'Preparacion',
                    'referencia_id'      => $preparacion->id,
                    'inventario_lote_id' => $lote->id,
                    'cantidad'           => -$c->cantidad_consumida,
                    'stock_anterior'     => $anterior,
                    'stock_nuevo'        => $nuevo,
                    'fecha_movimiento'   => now(),
                    'usuario_id'         => Auth::id(),
                    'observacion'        => "Liberación preparación {$preparacion->codigo}",
                ];
                if (Schema::hasColumn('movimientos_inventario','medicamento_id')) {
                    $movData['medicamento_id'] = $lote->medicamento_id;
                }
                if (Schema::hasColumn('movimientos_inventario','lote_codigo')) {
                    $movData['lote_codigo'] = $lote->lote;
                }
                if (Schema::hasColumn('movimientos_inventario','fecha_vencimiento')) {
                    $movData['fecha_vencimiento'] = $lote->fecha_vencimiento;
                }
                MovimientoInventario::create($movData);

                $costoTotal += (float)$c->costo_total;
            }

            $preparacion->update([
                'estado'                 => 'LIBERADA',
                'fecha_fin'              => now(),
                'costo_total'            => $costoTotal,
                'usuario_validador_id'   => Auth::id(),
            ]);

            DB::commit();
            return back()->with('success', "Preparación {$preparacion->codigo} liberada. Inventario y Kardex actualizados.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function entregar(Request $request, Preparacion $preparacion)
    {
        abort_if($preparacion->estado !== 'LIBERADA', 422, 'Solo se puede entregar una preparación LIBERADA.');

        $data = $request->validate([
            'recibido_por'        => 'nullable|string|max:150',
            'servicio_destino_id' => 'nullable|exists:TipoServicios,id',
            'observaciones'       => 'nullable|string',
        ]);

        PreparacionEntrega::create([
            'preparacion_id'      => $preparacion->id,
            'paciente_id'         => $preparacion->paciente_id,
            'fecha_entrega'       => now(),
            'usuario_entrega_id'  => Auth::id(),
            'servicio_destino_id' => $data['servicio_destino_id'] ?? $preparacion->servicio_id,
            'recibido_por'        => $data['recibido_por']        ?? null,
            'observaciones'       => $data['observaciones']       ?? null,
        ]);

        $preparacion->update(['estado' => 'ENTREGADA']);
        return back()->with('success', "Preparación {$preparacion->codigo} entregada.");
    }

    public function anular(Preparacion $preparacion)
    {
        abort_if(in_array($preparacion->estado, ['ENTREGADA','ANULADA']), 422, 'No se puede anular en este estado.');

        DB::beginTransaction();
        try {
            // Si ya estaba LIBERADA, devolver stock
            if ($preparacion->estado === 'LIBERADA') {
                foreach ($preparacion->consumos as $c) {
                    $lote = InventarioLote::lockForUpdate()->find($c->inventario_lote_id);
                    if ($lote) {
                        $ant   = (float)$lote->cantidad_actual;
                        $nuevo = $ant + (float)$c->cantidad_consumida;
                        $lote->cantidad_actual = $nuevo;
                        $lote->save();

                        $movData = [
                            'tipo_movimiento'    => 'PRODUCCION',
                            'referencia_tipo'    => 'Preparacion:ANULACION',
                            'referencia_id'      => $preparacion->id,
                            'inventario_lote_id' => $lote->id,
                            'cantidad'           => $c->cantidad_consumida,
                            'stock_anterior'     => $ant,
                            'stock_nuevo'        => $nuevo,
                            'fecha_movimiento'   => now(),
                            'usuario_id'         => Auth::id(),
                            'observacion'        => "Anulación preparación {$preparacion->codigo}",
                        ];
                        if (Schema::hasColumn('movimientos_inventario','medicamento_id')) {
                            $movData['medicamento_id'] = $lote->medicamento_id;
                        }
                        MovimientoInventario::create($movData);
                    }
                }
            }
            $preparacion->update(['estado' => 'ANULADA']);
            DB::commit();
            return back()->with('success', "Preparación {$preparacion->codigo} anulada.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(Preparacion $preparacion)
    {
        abort_if($preparacion->estado !== 'PROGRAMADA', 403, 'Solo se eliminan preparaciones PROGRAMADAS.');
        $cod = $preparacion->codigo;
        $preparacion->detalles()->delete();
        $preparacion->delete();
        return redirect()->route('admin.preparaciones.index')->with('success', "Preparación {$cod} eliminada.");
    }
}
