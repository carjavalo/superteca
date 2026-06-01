<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AjusteInventario;
use App\Models\AjusteMotivo;
use App\Models\DetalleAjusteInventario;
use App\Models\InventarioLote;
use App\Models\MovimientoInventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AjusteInventarioController extends Controller
{
    public function index(Request $request)
    {
        $query = AjusteInventario::with(['motivo', 'solicitante', 'aprobador'])
            ->withCount('detalles');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo', 'like', "%$s%")
                  ->orWhere('observaciones', 'like', "%$s%");
            });
        }
        if ($request->filled('tipo'))   $query->where('tipo_ajuste', $request->tipo);
        if ($request->filled('estado')) $query->where('estado', $request->estado);
        if ($request->filled('desde'))  $query->whereDate('fecha_ajuste', '>=', $request->desde);
        if ($request->filled('hasta'))  $query->whereDate('fecha_ajuste', '<=', $request->hasta);

        $ajustes = $query->latest('fecha_ajuste')->paginate(15)->withQueryString();

        $stats = [
            'hoy'       => AjusteInventario::whereDate('fecha_ajuste', today())->count(),
            'positivos' => AjusteInventario::whereMonth('fecha_ajuste', now()->month)->where('tipo_ajuste', 'POSITIVO')->count(),
            'negativos' => AjusteInventario::whereMonth('fecha_ajuste', now()->month)->where('tipo_ajuste', 'NEGATIVO')->count(),
            'pendientes'=> AjusteInventario::where('estado', 'BORRADOR')->count(),
            'valor_mes' => AjusteInventario::whereMonth('fecha_ajuste', now()->month)->where('estado', 'APROBADO')->sum('valor_total'),
        ];

        return view('admin.ajustes.index', compact('ajustes', 'stats'));
    }

    public function create()
    {
        $motivos = AjusteMotivo::where('estado', true)->orderBy('nombre')->get();
        $lotes = InventarioLote::with('medicamento')
            ->where('estado', 1)
            ->whereNotNull('medicamento_id')
            ->orderBy('lote')
            ->get();

        return view('admin.ajustes.create', compact('motivos', 'lotes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tipo_ajuste'      => 'required|in:POSITIVO,NEGATIVO',
            'motivo_ajuste_id' => 'required|exists:ajuste_motivos,id',
            'fecha_ajuste'     => 'required|date',
            'observaciones'    => 'nullable|string',
            'detalles'                       => 'required|array|min:1',
            'detalles.*.inventario_lote_id'  => 'required|exists:inventario_lotes,id',
            'detalles.*.stock_fisico'        => 'required|numeric|min:0',
            'detalles.*.observacion'         => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $ajuste = AjusteInventario::create([
                'tipo_ajuste'         => $data['tipo_ajuste'],
                'fecha_ajuste'        => $data['fecha_ajuste'],
                'motivo_ajuste_id'    => $data['motivo_ajuste_id'],
                'observaciones'       => $data['observaciones'] ?? null,
                'usuario_solicita_id' => Auth::id(),
                'estado'              => 'BORRADOR',
            ]);

            $total = 0;
            foreach ($data['detalles'] as $det) {
                $lote = InventarioLote::findOrFail($det['inventario_lote_id']);
                $diff = (float)$det['stock_fisico'] - (float)$lote->cantidad_actual;

                if ($data['tipo_ajuste'] === 'POSITIVO' && $diff < 0) {
                    throw new \Exception("En ajuste POSITIVO, el stock físico no puede ser menor al del sistema (lote {$lote->lote}).");
                }
                if ($data['tipo_ajuste'] === 'NEGATIVO' && $diff > 0) {
                    throw new \Exception("En ajuste NEGATIVO, el stock físico no puede ser mayor al del sistema (lote {$lote->lote}).");
                }

                $valor = abs($diff) * (float)($lote->costo_unitario ?? 0);
                $total += $valor;

                DetalleAjusteInventario::create([
                    'ajuste_id'         => $ajuste->id,
                    'inventario_lote_id'=> $lote->id,
                    'medicamento_id'    => $lote->medicamento_id,
                    'lote'              => $lote->lote,
                    'fecha_vencimiento' => $lote->fecha_vencimiento,
                    'stock_sistema'     => $lote->cantidad_actual,
                    'stock_fisico'      => $det['stock_fisico'],
                    'diferencia'        => $diff,
                    'costo_unitario'    => $lote->costo_unitario ?? 0,
                    'valor_ajuste'      => $valor,
                    'observacion'       => $det['observacion'] ?? null,
                ]);
            }

            $ajuste->update(['valor_total' => $total]);

            DB::commit();
            return redirect()->route('admin.ajustes.show', $ajuste)
                ->with('success', "Ajuste {$ajuste->codigo} creado en estado BORRADOR. Pendiente de aprobación.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(AjusteInventario $ajuste)
    {
        $ajuste->load(['motivo', 'solicitante', 'aprobador', 'detalles.medicamento', 'detalles.lote']);
        return view('admin.ajustes.show', compact('ajuste'));
    }

    public function approve(AjusteInventario $ajuste)
    {
        if ($ajuste->estado !== 'BORRADOR') {
            return back()->with('error', 'Sólo se pueden aprobar ajustes en estado BORRADOR.');
        }

        DB::beginTransaction();
        try {
            $ajuste->load('detalles');
            $userId = Auth::id();

            foreach ($ajuste->detalles as $det) {
                $lote = InventarioLote::lockForUpdate()->findOrFail($det->inventario_lote_id);
                $stockAnterior = (float)$lote->cantidad_actual;
                $stockNuevo    = (float)$det->stock_fisico;

                $lote->cantidad_actual = $stockNuevo;
                $lote->save();

                MovimientoInventario::create([
                    'tipo_movimiento'   => 'AJUSTE',
                    'referencia_tipo'   => 'AjusteInventario',
                    'referencia_id'     => $ajuste->id,
                    'inventario_lote_id'=> $lote->id,
                    'cantidad'          => $det->diferencia,
                    'stock_anterior'    => $stockAnterior,
                    'stock_nuevo'       => $stockNuevo,
                    'fecha_movimiento'  => now(),
                    'usuario_id'        => $userId,
                    'observacion'       => "Ajuste {$ajuste->codigo} ({$ajuste->tipo_ajuste}) · " . ($det->observacion ?? $ajuste->motivo->nombre ?? ''),
                ]);
            }

            $ajuste->update([
                'estado'             => 'APROBADO',
                'usuario_aprueba_id' => $userId,
                'fecha_aprobacion'   => now(),
            ]);

            DB::commit();
            return back()->with('success', "Ajuste {$ajuste->codigo} aprobado. Inventario y kardex actualizados.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar: ' . $e->getMessage());
        }
    }

    public function annul(AjusteInventario $ajuste)
    {
        if ($ajuste->estado === 'ANULADO') {
            return back()->with('error', 'El ajuste ya estaba anulado.');
        }

        DB::beginTransaction();
        try {
            if ($ajuste->estado === 'APROBADO') {
                // Revertir cantidades
                foreach ($ajuste->detalles as $det) {
                    $lote = InventarioLote::lockForUpdate()->find($det->inventario_lote_id);
                    if (! $lote) continue;
                    $stockAnterior = (float)$lote->cantidad_actual;
                    $stockNuevo    = (float)$det->stock_sistema;
                    $lote->cantidad_actual = $stockNuevo;
                    $lote->save();

                    MovimientoInventario::create([
                        'tipo_movimiento'   => 'AJUSTE',
                        'referencia_tipo'   => 'AjusteInventario:ANULACION',
                        'referencia_id'     => $ajuste->id,
                        'inventario_lote_id'=> $lote->id,
                        'cantidad'          => -$det->diferencia,
                        'stock_anterior'    => $stockAnterior,
                        'stock_nuevo'       => $stockNuevo,
                        'fecha_movimiento'  => now(),
                        'usuario_id'        => Auth::id(),
                        'observacion'       => "Anulación del ajuste {$ajuste->codigo}",
                    ]);
                }
            }

            $ajuste->update(['estado' => 'ANULADO']);
            DB::commit();
            return back()->with('success', "Ajuste {$ajuste->codigo} anulado.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error al anular: ' . $e->getMessage());
        }
    }

    public function destroy(AjusteInventario $ajuste)
    {
        if ($ajuste->estado !== 'BORRADOR') {
            return back()->with('error', 'Sólo se pueden eliminar ajustes en BORRADOR.');
        }
        $codigo = $ajuste->codigo;
        $ajuste->detalles()->delete();
        $ajuste->delete();
        return redirect()->route('admin.ajustes.index')->with('success', "Ajuste {$codigo} eliminado.");
    }

    // ---- Motivos (creación dinámica vía AJAX) ----

    public function storeMotivo(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:150|unique:ajuste_motivos,nombre',
            'tipo'   => 'required|in:POSITIVO,NEGATIVO,AMBOS',
            'requiere_observacion' => 'sometimes|boolean',
            'requiere_aprobacion'  => 'sometimes|boolean',
        ]);

        $next = (AjusteMotivo::max('id') ?? 0) + 1;
        $motivo = AjusteMotivo::create([
            'codigo' => 'AJM-' . str_pad($next, 3, '0', STR_PAD_LEFT),
            'nombre' => $data['nombre'],
            'tipo'   => $data['tipo'],
            'requiere_observacion' => $request->boolean('requiere_observacion', true),
            'requiere_aprobacion'  => $request->boolean('requiere_aprobacion', true),
            'estado' => true,
        ]);

        return response()->json(['success' => true, 'motivo' => $motivo]);
    }
}
