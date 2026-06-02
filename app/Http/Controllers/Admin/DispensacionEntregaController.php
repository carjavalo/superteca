<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DispensacionDevolucion;
use App\Models\DispensacionDevolucionDetalle;
use App\Models\DispensacionEntrega;
use App\Models\DispensacionEntregaDetalle;
use App\Models\DispensacionEntregaLote;
use App\Models\DispensacionEntregaRecepcion;
use App\Models\InventarioLote;
use App\Models\Medicamento;
use App\Models\MovimientoInventario;
use App\Models\Paciente;
use App\Models\PacienteDispensacion;
use App\Models\ServicioHospitalario;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DispensacionEntregaController extends Controller
{
    public function index(Request $request)
    {
        $q = DispensacionEntrega::with(['paciente','servicio','dispensador'])
            ->withCount('detalles');

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($w) use ($s) {
                $w->where('codigo', 'like', "%$s%")
                  ->orWhereHas('paciente', fn($p)=>$p->where('nombres','like',"%$s%")->orWhere('apellidos','like',"%$s%")->orWhere('documento','like',"%$s%"))
                  ->orWhereHas('servicio', fn($p)=>$p->where('nombre','like',"%$s%"));
            });
        }
        if ($request->filled('estado')) $q->where('estado', $request->estado);
        if ($request->filled('tipo'))   $q->where('tipo_entrega', $request->tipo);
        if ($request->filled('desde'))  $q->whereDate('fecha_entrega', '>=', $request->desde);
        if ($request->filled('hasta'))  $q->whereDate('fecha_entrega', '<=', $request->hasta);

        $todos = $q->latest('fecha_entrega')->limit(500)->get();

        $kanban = [];
        foreach (array_keys(DispensacionEntrega::ESTADOS) as $est) {
            $kanban[$est] = $todos->where('estado', $est)->values();
        }

        $stats = [
            'hoy'         => DispensacionEntrega::whereDate('fecha_entrega', today())->count(),
            'pendientes'  => DispensacionEntrega::where('estado','PENDIENTE')->count(),
            'entregadas'  => DispensacionEntrega::where('estado','ENTREGADA')->whereDate('fecha_entrega', today())->count(),
            'pacientes'   => DispensacionEntrega::whereDate('fecha_entrega', today())->whereNotNull('paciente_id')->distinct('paciente_id')->count('paciente_id'),
            'devoluciones'=> DispensacionDevolucion::whereDate('fecha_devolucion', today())->count(),
        ];

        return view('admin.dispensacion.entregas.index', compact('todos','kanban','stats'));
    }

    public function create()
    {
        $pacientes  = Paciente::orderBy('apellidos')->orderBy('nombres')->get();
        $servicios  = ServicioHospitalario::where('estado', 1)->orderBy('nombre')->get();
        $medicamentos = Medicamento::orderBy('nombre')->get();
        $unidades   = UnidadMedida::orderBy('nombre')->get();
        $lotes      = InventarioLote::with('medicamento','presentacion')
            ->where('estado', 1)->where('cantidad_actual', '>', 0)
            ->orderBy('fecha_vencimiento')->get();

        return view('admin.dispensacion.entregas.create', compact('pacientes','servicios','medicamentos','unidades','lotes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_entrega'     => 'required|in:'.implode(',', array_keys(DispensacionEntrega::TIPOS)),
            'paciente_id'      => 'nullable|exists:pacientes,id',
            'servicio_id'      => 'nullable|exists:servicios_hospitalarios,id',
            'fecha_entrega'    => 'required|date',
            'recibe_nombre'    => 'nullable|string|max:255',
            'recibe_documento' => 'nullable|string|max:50',
            'observaciones'    => 'nullable|string',
            'detalles'                    => 'required|array|min:1',
            'detalles.*.medicamento_id'   => 'nullable|exists:medicamentos,id',
            'detalles.*.presentacion_id'  => 'nullable|exists:presentaciones,id',
            'detalles.*.cantidad'         => 'required|numeric|min:0.0001',
            'detalles.*.unidad_medida_id' => 'nullable|exists:unidades_medida,id',
            'detalles.*.observaciones'    => 'nullable|string',
            'detalles.*.lotes'                       => 'required|array|min:1',
            'detalles.*.lotes.*.inventario_lote_id'  => 'required|exists:inventario_lotes,id',
            'detalles.*.lotes.*.cantidad'            => 'required|numeric|min:0.0001',
        ]);

        $entrega = DB::transaction(function () use ($data) {
            $entrega = DispensacionEntrega::create([
                'tipo_entrega'           => $data['tipo_entrega'],
                'paciente_id'            => $data['paciente_id'] ?? null,
                'servicio_id'            => $data['servicio_id'] ?? null,
                'fecha_entrega'          => $data['fecha_entrega'],
                'recibe_nombre'          => $data['recibe_nombre'] ?? null,
                'recibe_documento'       => $data['recibe_documento'] ?? null,
                'observaciones'          => $data['observaciones'] ?? null,
                'estado'                 => 'PENDIENTE',
                'usuario_dispensador_id' => Auth::id(),
            ]);

            $costoEntrega = 0;
            foreach ($data['detalles'] as $det) {
                $costoLineaTotal = 0;
                $cantidadLinea = 0;
                $detalle = DispensacionEntregaDetalle::create([
                    'entrega_id'        => $entrega->id,
                    'medicamento_id'    => $det['medicamento_id'] ?? null,
                    'presentacion_id'   => $det['presentacion_id'] ?? null,
                    'cantidad'          => $det['cantidad'],
                    'unidad_medida_id'  => $det['unidad_medida_id'] ?? null,
                    'observaciones'     => $det['observaciones'] ?? null,
                    'costo_unitario'    => 0,
                    'costo_total'       => 0,
                ]);

                foreach ($det['lotes'] as $lt) {
                    $lote = InventarioLote::find($lt['inventario_lote_id']);
                    if (!$lote) continue;
                    $cu = (float) ($lote->costo_unitario ?: 0);
                    $cant = (float) $lt['cantidad'];

                    DispensacionEntregaLote::create([
                        'entrega_detalle_id'  => $detalle->id,
                        'inventario_lote_id'  => $lote->id,
                        'lote'                => $lote->lote,
                        'fecha_vencimiento'   => $lote->fecha_vencimiento,
                        'cantidad_entregada'  => $cant,
                        'costo_unitario'      => $cu,
                    ]);

                    $costoLineaTotal += $cu * $cant;
                    $cantidadLinea   += $cant;
                }

                $cuLinea = $cantidadLinea > 0 ? $costoLineaTotal / $cantidadLinea : 0;
                $detalle->update([
                    'costo_unitario' => round($cuLinea, 4),
                    'costo_total'    => round($costoLineaTotal, 2),
                ]);
                $costoEntrega += $costoLineaTotal;
            }

            $entrega->update(['costo_total' => round($costoEntrega, 2)]);

            return $entrega;
        });

        return redirect()->route('admin.dispensacion.entregas.show', $entrega)
            ->with('success', "Entrega {$entrega->codigo} creada en estado Pendiente.");
    }

    public function show(DispensacionEntrega $entrega)
    {
        $entrega->load([
            'paciente.servicio','servicio','dispensador','recibe',
            'detalles.medicamento','detalles.presentacion','detalles.unidadMedida',
            'detalles.lotes.inventarioLote',
            'recepciones.usuario',
            'devoluciones.usuario','devoluciones.detalles.inventarioLote',
        ]);

        return view('admin.dispensacion.entregas.show', compact('entrega'));
    }

    public function entregar(DispensacionEntrega $entrega)
    {
        if ($entrega->estado !== 'PENDIENTE') {
            return back()->with('error', 'Solo se pueden entregar dispensaciones en estado Pendiente.');
        }
        if ($entrega->detalles()->count() === 0) {
            return back()->with('error', 'La entrega no tiene productos.');
        }

        DB::transaction(function () use ($entrega) {
            // Validar stock antes de descontar
            foreach ($entrega->detalles as $d) {
                foreach ($d->lotes as $eLote) {
                    $lote = InventarioLote::find($eLote->inventario_lote_id);
                    if (!$lote || $lote->cantidad_actual < $eLote->cantidad_entregada) {
                        throw new \RuntimeException("Lote {$eLote->lote} sin stock suficiente.");
                    }
                }
            }

            foreach ($entrega->detalles as $d) {
                foreach ($d->lotes as $eLote) {
                    $lote = InventarioLote::find($eLote->inventario_lote_id);
                    $stockAnt = $lote->cantidad_actual;
                    $lote->cantidad_actual = max(0, $lote->cantidad_actual - $eLote->cantidad_entregada);
                    $lote->save();

                    $mov = [
                        'tipo_movimiento'    => 'DISPENSACION',
                        'referencia_tipo'    => 'DispensacionEntrega',
                        'referencia_id'      => $entrega->id,
                        'inventario_lote_id' => $lote->id,
                        'cantidad'           => -1 * (float) $eLote->cantidad_entregada,
                        'stock_anterior'     => $stockAnt,
                        'stock_nuevo'        => $lote->cantidad_actual,
                        'fecha_movimiento'   => now(),
                        'usuario_id'         => Auth::id(),
                        'observacion'        => "Dispensación {$entrega->codigo} → {$entrega->destino}",
                    ];
                    if (Schema::hasColumn('movimientos_inventario','medicamento_id'))    $mov['medicamento_id']    = $lote->medicamento_id;
                    if (Schema::hasColumn('movimientos_inventario','presentacion_id'))   $mov['presentacion_id']   = $lote->presentacion_id;
                    if (Schema::hasColumn('movimientos_inventario','lote_codigo'))       $mov['lote_codigo']       = $lote->lote;
                    if (Schema::hasColumn('movimientos_inventario','fecha_vencimiento')) $mov['fecha_vencimiento'] = $lote->fecha_vencimiento;
                    if (Schema::hasColumn('movimientos_inventario','costo_unitario'))    $mov['costo_unitario']    = $eLote->costo_unitario;
                    if (Schema::hasColumn('movimientos_inventario','costo_total'))       $mov['costo_total']       = (float) $eLote->costo_unitario * (float) $eLote->cantidad_entregada;
                    MovimientoInventario::create($mov);

                    // Trazabilidad clínica: vincular lote con paciente
                    if ($entrega->paciente_id) {
                        PacienteDispensacion::create([
                            'paciente_id'        => $entrega->paciente_id,
                            'entrega_id'         => $entrega->id,
                            'entrega_detalle_id' => $d->id,
                            'medicamento_id'     => $lote->medicamento_id,
                            'inventario_lote_id' => $lote->id,
                            'lote'               => $lote->lote,
                            'fecha_vencimiento'  => $lote->fecha_vencimiento,
                            'cantidad'           => $eLote->cantidad_entregada,
                            'fecha_entrega'      => now(),
                            'costo_total'        => (float) $eLote->costo_unitario * (float) $eLote->cantidad_entregada,
                        ]);
                    }
                }
            }

            $entrega->update([
                'estado'        => 'ENTREGADA',
                'fecha_entrega' => $entrega->fecha_entrega ?: now(),
            ]);
        });

        return back()->with('success', 'Entrega completada. Inventario y Kardex actualizados.');
    }

    public function recibir(Request $request, DispensacionEntrega $entrega)
    {
        if ($entrega->estado !== 'ENTREGADA') {
            return back()->with('error', 'Solo se confirma recepción de entregas en estado Entregada.');
        }

        $data = $request->validate([
            'recibe_nombre'    => 'required|string|max:255',
            'recibe_documento' => 'nullable|string|max:50',
            'observaciones'    => 'nullable|string',
        ]);

        DispensacionEntregaRecepcion::create([
            'entrega_id'        => $entrega->id,
            'fecha_recepcion'   => now(),
            'usuario_recibe_id' => Auth::id(),
            'recibe_nombre'     => $data['recibe_nombre'],
            'recibe_documento'  => $data['recibe_documento'] ?? null,
            'observaciones'     => $data['observaciones'] ?? null,
            'recibido'          => true,
            'created_at'        => now(),
        ]);

        $entrega->update([
            'usuario_recibe_id' => Auth::id(),
            'recibe_nombre'     => $data['recibe_nombre'],
            'recibe_documento'  => $data['recibe_documento'] ?? $entrega->recibe_documento,
        ]);

        return back()->with('success', 'Recepción confirmada.');
    }

    public function devolver(Request $request, DispensacionEntrega $entrega)
    {
        if (!in_array($entrega->estado, ['ENTREGADA','PARCIAL'])) {
            return back()->with('error', 'Solo se pueden devolver entregas Entregadas.');
        }

        $data = $request->validate([
            'motivo' => 'required|string',
            'lineas' => 'required|array|min:1',
            'lineas.*.entrega_lote_id' => 'required|exists:dispensacion_entregas_lotes,id',
            'lineas.*.cantidad'        => 'required|numeric|min:0.0001',
            'lineas.*.reingresa_stock' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($data, $entrega) {
            $devolucion = DispensacionDevolucion::create([
                'entrega_id'       => $entrega->id,
                'fecha_devolucion' => now(),
                'motivo'           => $data['motivo'],
                'usuario_id'       => Auth::id(),
                'estado'           => 'APROBADA',
            ]);

            $costoTotal = 0;
            $cantTotal  = 0;

            foreach ($data['lineas'] as $ln) {
                $eLote = DispensacionEntregaLote::find($ln['entrega_lote_id']);
                if (!$eLote) continue;
                if ($eLote->detalle->entrega_id !== $entrega->id) continue;
                $cant = (float) $ln['cantidad'];
                if ($cant <= 0 || $cant > (float) $eLote->cantidad_entregada) continue;

                $reingresa = (bool) ($ln['reingresa_stock'] ?? true);
                $cu = (float) $eLote->costo_unitario;

                DispensacionDevolucionDetalle::create([
                    'devolucion_id'      => $devolucion->id,
                    'entrega_lote_id'    => $eLote->id,
                    'inventario_lote_id' => $eLote->inventario_lote_id,
                    'cantidad'           => $cant,
                    'costo_unitario'     => $cu,
                    'costo_total'        => $cu * $cant,
                    'reingresa_stock'    => $reingresa,
                ]);

                if ($reingresa && $eLote->inventario_lote_id) {
                    $lote = InventarioLote::find($eLote->inventario_lote_id);
                    if ($lote) {
                        $stockAnt = $lote->cantidad_actual;
                        $lote->cantidad_actual = $lote->cantidad_actual + $cant;
                        $lote->save();

                        $mov = [
                            'tipo_movimiento'    => 'DEVOLUCION',
                            'referencia_tipo'    => 'DispensacionDevolucion',
                            'referencia_id'      => $devolucion->id,
                            'inventario_lote_id' => $lote->id,
                            'cantidad'           => $cant,
                            'stock_anterior'     => $stockAnt,
                            'stock_nuevo'        => $lote->cantidad_actual,
                            'fecha_movimiento'   => now(),
                            'usuario_id'         => Auth::id(),
                            'observacion'        => "Devolución de entrega {$entrega->codigo}",
                        ];
                        if (Schema::hasColumn('movimientos_inventario','medicamento_id'))    $mov['medicamento_id']    = $lote->medicamento_id;
                        if (Schema::hasColumn('movimientos_inventario','presentacion_id'))   $mov['presentacion_id']   = $lote->presentacion_id;
                        if (Schema::hasColumn('movimientos_inventario','lote_codigo'))       $mov['lote_codigo']       = $lote->lote;
                        if (Schema::hasColumn('movimientos_inventario','fecha_vencimiento')) $mov['fecha_vencimiento'] = $lote->fecha_vencimiento;
                        if (Schema::hasColumn('movimientos_inventario','costo_unitario'))    $mov['costo_unitario']    = $cu;
                        if (Schema::hasColumn('movimientos_inventario','costo_total'))       $mov['costo_total']       = $cu * $cant;
                        MovimientoInventario::create($mov);
                    }
                }

                $costoTotal += $cu * $cant;
                $cantTotal  += $cant;
            }

            $devolucion->update([
                'cantidad_devuelta' => $cantTotal,
                'costo_total'       => round($costoTotal, 2),
            ]);

            $totalEntregado = $entrega->detalles()
                ->withSum('lotes as total', 'cantidad_entregada')->get()->sum('total');
            $totalDevuelto = DB::table('dispensacion_devoluciones_detalle as dd')
                ->join('dispensacion_devoluciones as d','dd.devolucion_id','=','d.id')
                ->where('d.entrega_id', $entrega->id)
                ->sum('dd.cantidad');

            if ($totalDevuelto >= $totalEntregado && $totalEntregado > 0) {
                $entrega->update(['estado' => 'DEVUELTA']);
            } else {
                $entrega->update(['estado' => 'PARCIAL']);
            }
        });

        return back()->with('success', 'Devolución registrada.');
    }

    public function anular(DispensacionEntrega $entrega)
    {
        if ($entrega->estado !== 'PENDIENTE') {
            return back()->with('error', 'Solo se pueden anular entregas Pendientes.');
        }
        $entrega->update(['estado' => 'ANULADA']);
        return back()->with('success', 'Entrega anulada.');
    }

    public function destroy(DispensacionEntrega $entrega)
    {
        if (!in_array($entrega->estado, ['PENDIENTE','ANULADA'])) {
            return back()->with('error', 'No se puede eliminar una entrega procesada.');
        }
        $entrega->delete();
        return redirect()->route('admin.dispensacion.entregas.index')->with('success','Entrega eliminada.');
    }
}
