<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Salida;
use App\Models\DetalleSalida;
use App\Models\InventarioLote;
use App\Models\MovimientoInventario;
use App\Models\Medicamento;
use App\Models\Presentacion;
use App\Models\UnidadMedida;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalidaController extends Controller
{
    public function index(Request $request)
    {
        $query = Salida::query()->with(['usuario','autorizador'])->withCount('detalles');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('numero_documento', 'like', "%{$search}%");
            });
        }
        if ($request->filled('tipo'))   $query->where('tipo_salida', $request->get('tipo'));
        if ($request->filled('estado')) $query->where('estado', $request->get('estado'));
        if ($request->filled('desde'))  $query->whereDate('fecha_salida', '>=', $request->get('desde'));
        if ($request->filled('hasta'))  $query->whereDate('fecha_salida', '<=', $request->get('hasta'));

        $salidas = $query->orderByDesc('fecha_salida')->paginate(15)->withQueryString();

        $hoy = Carbon::today();
        $stats = [
            'hoy'             => Salida::whereDate('fecha_salida', $hoy)->count(),
            'preparaciones'   => Salida::where('tipo_salida','PRODUCCION')->whereMonth('fecha_salida', $hoy->month)->count(),
            'dispensaciones'  => Salida::where('tipo_salida','DISPENSACION')->whereMonth('fecha_salida', $hoy->month)->count(),
            'vencimientos'    => Salida::where('tipo_salida','VENCIMIENTO')->whereMonth('fecha_salida', $hoy->month)->count(),
            'pendientes'      => Salida::where('estado','BORRADOR')->count(),
        ];

        return view('admin.salidas.index', compact('salidas','stats'));
    }

    public function create()
    {
        $salida = new Salida(['fecha_salida' => now(), 'tipo_salida' => 'DISPENSACION', 'estado' => 'BORRADOR']);
        return view('admin.salidas.create', $this->formData($salida, collect()));
    }

    public function store(Request $request)
    {
        $data  = $this->validateSalida($request);
        $items = $this->validateItems($request);

        return DB::transaction(function () use ($data, $items, $request) {
            $data['codigo']     = $data['codigo'] ?: $this->generarCodigo();
            $data['usuario_id'] = auth()->id();
            $salida = Salida::create($data);

            $this->guardarDetalles($salida, $items);
            $this->recalcularTotales($salida);

            if ($request->boolean('confirmar')) {
                $this->confirmar($salida->fresh('detalles'));
            }

            return redirect()->route('admin.salidas.show', $salida)
                             ->with('success', 'Salida registrada correctamente.');
        });
    }

    public function show(Salida $salida)
    {
        $salida->load([
            'usuario','autorizador',
            'detalles.medicamento','detalles.presentacion',
            'detalles.unidadMedida','detalles.lotInventario',
        ]);

        $movimientos = MovimientoInventario::where('referencia_tipo', Salida::class)
            ->where('referencia_id', $salida->id)
            ->with('lote.medicamento','usuario')
            ->orderBy('fecha_movimiento')
            ->get();

        return view('admin.salidas.show', compact('salida','movimientos'));
    }

    public function edit(Salida $salida)
    {
        if ($salida->estado !== 'BORRADOR') {
            return redirect()->route('admin.salidas.show', $salida)
                             ->with('error', 'Solo se pueden editar salidas en estado BORRADOR.');
        }
        return view('admin.salidas.create', $this->formData($salida, $salida->detalles));
    }

    public function update(Request $request, Salida $salida)
    {
        if ($salida->estado !== 'BORRADOR') {
            return redirect()->route('admin.salidas.show', $salida)
                             ->with('error', 'Solo se pueden editar salidas en estado BORRADOR.');
        }

        $data  = $this->validateSalida($request, $salida->id);
        $items = $this->validateItems($request);

        return DB::transaction(function () use ($salida, $data, $items, $request) {
            $salida->update($data);
            $salida->detalles()->delete();
            $this->guardarDetalles($salida, $items);
            $this->recalcularTotales($salida);

            if ($request->boolean('confirmar')) {
                $this->confirmar($salida->fresh('detalles'));
            }

            return redirect()->route('admin.salidas.show', $salida)
                             ->with('success', 'Salida actualizada correctamente.');
        });
    }

    public function confirm(Salida $salida)
    {
        if ($salida->estado !== 'BORRADOR') {
            return back()->with('error', 'Solo salidas en BORRADOR pueden confirmarse.');
        }
        $salida->load('detalles');
        try {
            DB::transaction(fn() => $this->confirmar($salida));
            return redirect()->route('admin.salidas.show', $salida)
                             ->with('success', 'Salida confirmada: stock descontado y kardex actualizado.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo confirmar: ' . $e->getMessage());
        }
    }

    public function annul(Salida $salida)
    {
        if ($salida->estado === 'ANULADA') return back()->with('error', 'La salida ya está anulada.');

        DB::transaction(function () use ($salida) {
            if ($salida->estado === 'CONFIRMADA') {
                // Reintegrar stock
                foreach ($salida->detalles as $det) {
                    $lote = InventarioLote::find($det->inventario_lote_id);
                    if ($lote) {
                        $anterior = (float) $lote->cantidad_actual;
                        $nuevo    = $anterior + (float) $det->cantidad;
                        $lote->cantidad_actual = $nuevo;
                        $lote->save();

                        MovimientoInventario::create([
                            'tipo_movimiento'    => 'AJUSTE',
                            'referencia_tipo'    => Salida::class,
                            'referencia_id'      => $salida->id,
                            'inventario_lote_id' => $lote->id,
                            'cantidad'           => (float) $det->cantidad,
                            'stock_anterior'     => $anterior,
                            'stock_nuevo'        => $nuevo,
                            'fecha_movimiento'   => now(),
                            'usuario_id'         => auth()->id(),
                            'observacion'        => 'Anulación de salida ' . $salida->codigo,
                            'created_at'         => now(),
                        ]);
                    }
                }
            }
            $salida->update(['estado' => 'ANULADA']);
        });

        return redirect()->route('admin.salidas.show', $salida)
                         ->with('success', 'Salida anulada correctamente.');
    }

    public function destroy(Salida $salida)
    {
        if ($salida->estado === 'CONFIRMADA') {
            return back()->with('error', 'No se puede eliminar una salida confirmada. Anúlela primero.');
        }
        $salida->delete();
        return redirect()->route('admin.salidas.index')->with('success', 'Salida eliminada.');
    }

    // ---------- helpers ----------

    private function validateSalida(Request $request, $id = null): array
    {
        return $request->validate([
            'codigo'             => 'nullable|string|max:50|unique:salidas,codigo' . ($id ? ",{$id}" : ''),
            'tipo_salida'        => 'required|in:' . implode(',', array_keys(Salida::TIPOS)),
            'fecha_salida'       => 'required|date',
            'paciente_id'        => 'nullable|integer',
            'servicio_id'        => 'nullable|integer',
            'bodega_origen_id'   => 'nullable|integer',
            'bodega_destino_id'  => 'nullable|integer',
            'numero_documento'   => 'nullable|string|max:100',
            'observaciones'      => 'nullable|string',
            'impuestos'          => 'nullable|numeric|min:0',
            'autorizado_por'     => 'nullable|exists:users,id',
        ]);
    }

    private function validateItems(Request $request): array
    {
        $request->validate([
            'items'                       => 'required|array|min:1',
            'items.*.inventario_lote_id'  => 'required|exists:inventario_lotes,id',
            'items.*.cantidad'            => 'required|numeric|min:0.01',
            'items.*.costo_unitario'      => 'nullable|numeric|min:0',
            'items.*.unidad_medida_id'    => 'nullable|exists:unidades_medida,id',
            'items.*.motivo_salida'       => 'nullable|string|max:255',
            'items.*.numero_preparacion'  => 'nullable|string|max:100',
            'items.*.paciente_id'         => 'nullable|integer',
            'items.*.observaciones'       => 'nullable|string',
        ]);

        return $request->input('items');
    }

    private function guardarDetalles(Salida $salida, array $items): void
    {
        foreach ($items as $it) {
            $lote = InventarioLote::with('presentacion')->findOrFail($it['inventario_lote_id']);
            $cantidad = (float) $it['cantidad'];
            $costo    = isset($it['costo_unitario']) && $it['costo_unitario'] !== ''
                ? (float) $it['costo_unitario']
                : (float) $lote->costo_unitario;

            DetalleSalida::create([
                'salida_id'          => $salida->id,
                'inventario_lote_id' => $lote->id,
                'medicamento_id'     => $lote->medicamento_id,
                'presentacion_id'    => $lote->presentacion_id,
                'lote'               => $lote->lote,
                'fecha_vencimiento'  => $lote->fecha_vencimiento,
                'cantidad'           => $cantidad,
                'unidad_medida_id'   => $it['unidad_medida_id'] ?? $lote->unidad_medida_id,
                'costo_unitario'     => $costo,
                'costo_total'        => $cantidad * $costo,
                'motivo_salida'      => $it['motivo_salida']      ?? null,
                'numero_preparacion' => $it['numero_preparacion'] ?? null,
                'paciente_id'        => $it['paciente_id']        ?? $salida->paciente_id,
                'observaciones'      => $it['observaciones']      ?? null,
            ]);
        }
    }

    private function recalcularTotales(Salida $salida): void
    {
        $subtotal = (float) $salida->detalles()->sum('costo_total');
        $imp      = (float) $salida->impuestos;
        $salida->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal + $imp,
        ]);
    }

    private function confirmar(Salida $salida): void
    {
        // Validar stock disponible y no vencido (salvo que sea salida por VENCIMIENTO)
        $consumosPorLote = [];
        foreach ($salida->detalles as $det) {
            $consumosPorLote[$det->inventario_lote_id] =
                ($consumosPorLote[$det->inventario_lote_id] ?? 0) + (float) $det->cantidad;
        }

        foreach ($consumosPorLote as $loteId => $cant) {
            $lote = InventarioLote::find($loteId);
            if (! $lote) {
                throw new \RuntimeException("Lote no encontrado (ID {$loteId}).");
            }
            if ((float) $lote->cantidad_actual < $cant) {
                throw new \RuntimeException(
                    "Stock insuficiente en lote {$lote->lote}. Disponible: {$lote->cantidad_actual}, requerido: {$cant}."
                );
            }
            if ($salida->tipo_salida !== 'VENCIMIENTO'
                && $lote->fecha_vencimiento
                && Carbon::parse($lote->fecha_vencimiento)->isPast()) {
                throw new \RuntimeException(
                    "El lote {$lote->lote} está vencido. Use tipo 'Vencimiento' para retirarlo."
                );
            }
        }

        // Aplicar descuentos y registrar movimientos
        foreach ($salida->detalles as $det) {
            $lote     = InventarioLote::find($det->inventario_lote_id);
            $anterior = (float) $lote->cantidad_actual;
            $nuevo    = $anterior - (float) $det->cantidad;

            $lote->cantidad_actual = $nuevo;
            $lote->save();

            MovimientoInventario::create([
                'tipo_movimiento'    => 'SALIDA',
                'referencia_tipo'    => Salida::class,
                'referencia_id'      => $salida->id,
                'inventario_lote_id' => $lote->id,
                'cantidad'           => -1 * (float) $det->cantidad,
                'stock_anterior'     => $anterior,
                'stock_nuevo'        => $nuevo,
                'fecha_movimiento'   => now(),
                'usuario_id'         => auth()->id(),
                'observacion'        => 'Salida ' . $salida->codigo . ' (' . $salida->tipo_label . ')',
                'created_at'         => now(),
            ]);
        }

        $salida->update(['estado' => 'CONFIRMADA']);
    }

    private function generarCodigo(): string
    {
        $prefijo = 'SAL-' . now()->format('Ymd') . '-';
        $ultimo  = Salida::where('codigo', 'like', $prefijo . '%')->orderByDesc('codigo')->value('codigo');
        $n       = $ultimo ? ((int) substr($ultimo, -4)) + 1 : 1;
        return $prefijo . str_pad($n, 4, '0', STR_PAD_LEFT);
    }

    private function formData(Salida $salida, $detalles): array
    {
        // Solo lotes con stock disponible y activos, ordenados por FEFO
        $lotes = InventarioLote::with(['medicamento','presentacion','unidadMedida'])
            ->where('cantidad_actual', '>', 0)
            ->where('estado', 1)
            ->orderByRaw('CASE WHEN fecha_vencimiento IS NULL THEN 1 ELSE 0 END, fecha_vencimiento ASC')
            ->get();

        $medicamentosJson = $lotes->pluck('medicamento')->filter()->unique('id')->values()
            ->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre])->all();

        $presentacionesJson = $lotes->pluck('presentacion')->filter()->unique('id')->values()
            ->map(fn($p) => [
                'id'              => $p->id,
                'nombre'          => $p->nombre,
                'medicamento_id'  => $p->medicamento_id,
                'unidad_medida_id'=> $p->unidad_medida_id,
            ])->all();

        $lotesJson = $lotes->map(fn($l) => [
            'id'                => $l->id,
            'medicamento_id'    => $l->medicamento_id,
            'presentacion_id'   => $l->presentacion_id,
            'lote'              => $l->lote,
            'fecha_vencimiento' => optional($l->fecha_vencimiento)->format('Y-m-d'),
            'cantidad_actual'   => (float) $l->cantidad_actual,
            'costo_unitario'    => (float) $l->costo_unitario,
            'unidad_medida_id'  => $l->unidad_medida_id,
            'ubicacion'         => $l->ubicacion,
        ])->values()->all();

        $unidades = UnidadMedida::where('estado', 1)->orderBy('tipo')->orderBy('nombre')->get();
        $unidadesJson = $unidades->map(fn($u) => [
            'id' => $u->id, 'nombre' => $u->nombre,
            'abreviatura' => $u->abreviatura, 'tipo' => $u->tipo,
        ])->all();

        $existingJson = collect($detalles)->map(fn($d) => [
            'inventario_lote_id'  => $d->inventario_lote_id,
            'medicamento_id'      => $d->medicamento_id,
            'presentacion_id'     => $d->presentacion_id,
            'cantidad'            => (float) $d->cantidad,
            'unidad_medida_id'    => $d->unidad_medida_id,
            'costo_unitario'      => (float) $d->costo_unitario,
            'motivo_salida'       => $d->motivo_salida,
            'numero_preparacion'  => $d->numero_preparacion,
        ])->values()->all();

        $usuarios = \App\Models\User::orderBy('name')->get(['id','name']);

        return compact(
            'salida','detalles','usuarios',
            'medicamentosJson','presentacionesJson','lotesJson','unidadesJson','existingJson'
        );
    }
}
