<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrada;
use App\Models\DetalleEntrada;
use App\Models\InventarioLote;
use App\Models\MovimientoInventario;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\Presentacion;
use App\Models\Proveedor;
use App\Models\Laboratorio;
use App\Models\UnidadMedida;
use App\Models\TipoEntrada;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class EntradaController extends Controller
{
    public function index(Request $request)
    {
        $query = Entrada::query()->with(['proveedor','usuario'])->withCount('detalles');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('numero_factura', 'like', "%{$search}%")
                  ->orWhere('numero_remision', 'like', "%{$search}%");
            });
        }
        if ($request->filled('tipo'))     $query->where('tipo_entrada', $request->get('tipo'));
        if ($request->filled('estado'))   $query->where('estado', $request->get('estado'));
        if ($request->filled('proveedor'))$query->where('proveedor_id', $request->get('proveedor'));
        if ($request->filled('desde'))    $query->whereDate('fecha_entrada', '>=', $request->get('desde'));
        if ($request->filled('hasta'))    $query->whereDate('fecha_entrada', '<=', $request->get('hasta'));

        $entradas = $query->orderByDesc('fecha_entrada')->paginate(15)->withQueryString();

        $hoy = Carbon::today();
        $stats = [
            'hoy'         => Entrada::whereDate('fecha_entrada', $hoy)->count(),
            'recibidos'   => DetalleEntrada::whereHas('entrada', fn($q) => $q->where('estado','CONFIRMADA'))
                                ->whereMonth('created_at', $hoy->month)->sum('cantidad'),
            'pendientes'  => Entrada::where('estado','BORRADOR')->count(),
            'por_vencer'  => DetalleEntrada::whereHas('entrada', fn($q) => $q->where('estado','CONFIRMADA'))
                                ->whereBetween('fecha_vencimiento', [$hoy, $hoy->copy()->addDays(90)])->count(),
        ];

        $proveedores = Proveedor::orderBy('razon_social')->get(['id','razon_social','nombre_comercial']);

        return view('admin.entradas.index', compact('entradas','stats','proveedores'));
    }

    public function create()
    {
        $entrada = new Entrada(['fecha_entrada' => now(), 'tipo_entrada' => 'COMPRA', 'estado' => 'BORRADOR']);
        return view('admin.entradas.create', $this->formData($entrada, collect()));
    }

    public function store(Request $request)
    {
        $data = $this->validateEntrada($request);
        $items = $this->validateItems($request);

        return DB::transaction(function () use ($data, $items, $request) {
            $data['codigo']     = ($data['codigo'] ?? null) ?: $this->generarCodigo();
            $data['usuario_id'] = auth()->id();
            $entrada = Entrada::create($data);

            $this->guardarDetalles($entrada, $items);
            $this->recalcularTotales($entrada);

            if ($request->boolean('confirmar')) {
                $this->confirmar($entrada->fresh('detalles'));
            }

            return redirect()->route('admin.entradas.show', $entrada)
                             ->with('success', 'Entrada registrada correctamente.');
        });
    }

    public function show(Entrada $entrada)
    {
        $entrada->load(['proveedor','paciente','usuario','detalles.medicamento','detalles.presentacion','detalles.laboratorio','detalles.unidadMedida']);

        $movimientos = MovimientoInventario::where('referencia_tipo', Entrada::class)
            ->where('referencia_id', $entrada->id)
            ->with('lote.medicamento','usuario')
            ->orderBy('fecha_movimiento')
            ->get();

        return view('admin.entradas.show', compact('entrada','movimientos'));
    }

    public function edit(Entrada $entrada)
    {
        if ($entrada->estado !== 'BORRADOR') {
            return redirect()->route('admin.entradas.show', $entrada)
                             ->with('error', 'Solo se pueden editar entradas en estado BORRADOR.');
        }

        return view('admin.entradas.create', $this->formData($entrada, $entrada->detalles));
    }

    public function update(Request $request, Entrada $entrada)
    {
        if ($entrada->estado !== 'BORRADOR') {
            return redirect()->route('admin.entradas.show', $entrada)
                             ->with('error', 'Solo se pueden editar entradas en estado BORRADOR.');
        }

        $data  = $this->validateEntrada($request, $entrada->id);
        $items = $this->validateItems($request);

        return DB::transaction(function () use ($entrada, $data, $items, $request) {
            $entrada->update($data);
            $entrada->detalles()->delete();
            $this->guardarDetalles($entrada, $items);
            $this->recalcularTotales($entrada);

            if ($request->boolean('confirmar')) {
                $this->confirmar($entrada->fresh('detalles'));
            }

            return redirect()->route('admin.entradas.show', $entrada)
                             ->with('success', 'Entrada actualizada correctamente.');
        });
    }

    public function confirm(Entrada $entrada)
    {
        if ($entrada->estado !== 'BORRADOR') {
            return back()->with('error', 'Solo entradas en BORRADOR pueden confirmarse.');
        }
        $entrada->load('detalles');
        try {
            DB::transaction(fn() => $this->confirmar($entrada));
            return redirect()->route('admin.entradas.show', $entrada)
                             ->with('success', 'Entrada confirmada: stock y kardex actualizados.');
        } catch (\Throwable $e) {
            return back()->with('error', 'No se pudo confirmar: ' . $e->getMessage());
        }
    }

    public function annul(Entrada $entrada)
    {
        if ($entrada->estado === 'ANULADA') return back()->with('error', 'La entrada ya está anulada.');

        DB::transaction(function () use ($entrada) {
            if ($entrada->estado === 'CONFIRMADA') {
                // revertir stock y registrar movimientos negativos
                foreach ($entrada->detalles as $det) {
                    $lote = InventarioLote::where('medicamento_id', $det->medicamento_id)
                                          ->where('presentacion_id', $det->presentacion_id)
                                          ->where('lote', $det->lote)
                                          ->first();
                    if ($lote) {
                        $anterior = (float) $lote->cantidad_actual;
                        $nuevo    = max(0, $anterior - (float) $det->cantidad);
                        $lote->cantidad_actual = $nuevo;
                        $lote->save();

                        MovimientoInventario::create([
                            'tipo_movimiento'    => 'AJUSTE',
                            'referencia_tipo'    => Entrada::class,
                            'referencia_id'      => $entrada->id,
                            'inventario_lote_id' => $lote->id,
                            'cantidad'           => -1 * (float) $det->cantidad,
                            'stock_anterior'     => $anterior,
                            'stock_nuevo'        => $nuevo,
                            'fecha_movimiento'   => now(),
                            'usuario_id'         => auth()->id(),
                            'observacion'        => 'Anulación de entrada ' . $entrada->codigo,
                            'created_at'         => now(),
                        ]);
                    }
                }
            }
            $entrada->update(['estado' => 'ANULADA']);
        });

        return redirect()->route('admin.entradas.show', $entrada)
                         ->with('success', 'Entrada anulada correctamente.');
    }

    public function destroy(Entrada $entrada)
    {
        if ($entrada->estado === 'CONFIRMADA') {
            return back()->with('error', 'No se puede eliminar una entrada confirmada. Anúlela primero.');
        }
        $entrada->delete();
        return redirect()->route('admin.entradas.index')->with('success', 'Entrada eliminada.');
    }

    // ---------- helpers ----------

    private function validateEntrada(Request $request, $id = null): array
    {
        $data = $request->validate([
            'codigo'           => 'nullable|string|max:50|unique:entradas,codigo' . ($id ? ",{$id}" : ''),
            'tipo_entrada'     => ['required', Rule::in(TipoEntrada::codigosDisponibles())],
            'proveedor_id'     => 'nullable|exists:proveedores,id',
            'paciente_id'      => 'nullable|required_if:tipo_entrada,ASIGNACION|exists:pacientes,id',
            'numero_factura'   => 'nullable|string|max:100',
            'numero_remision'  => 'nullable|string|max:100',
            'fecha_entrada'    => 'required|date',
            'fecha_documento'  => 'nullable|date',
            'observaciones'    => 'nullable|string',
            'impuestos'        => 'nullable|numeric|min:0',
            'bodega_destino_id'=> 'nullable|integer',
        ], [
            'paciente_id.required_if' => 'Debe seleccionar un paciente (busque por su número de identificación) cuando el tipo de entrada es Asignación.',
        ]);

        // El paciente solo aplica para entradas de tipo Asignación.
        if (($data['tipo_entrada'] ?? null) !== 'ASIGNACION') {
            $data['paciente_id'] = null;
        }

        return $data;
    }

    /**
     * Busca un paciente por su número de identificación (documento).
     * Usado por la vista de entrada cuando el tipo es "Asignación".
     */
    public function buscarPaciente(Request $request)
    {
        $documento = trim((string) $request->query('documento', ''));

        if ($documento === '') {
            return response()->json(['message' => 'Indique el número de identificación.'], 422);
        }

        $paciente = Paciente::where('documento', $documento)->first();

        if (! $paciente) {
            return response()->json(['message' => 'No existe un paciente con ese número de identificación.'], 404);
        }

        return response()->json([
            'id'             => $paciente->id,
            'documento'      => $paciente->documento,
            'tipo_documento' => $paciente->tipo_documento,
            'nombre'         => $paciente->nombre_completo,
            'eps'            => $paciente->eps,
        ]);
    }

    private function validateItems(Request $request): array
    {
        $request->validate([
            'items'                       => 'required|array|min:1',
            'items.*.medicamento_id'      => 'required|exists:medicamentos,id',
            'items.*.presentacion_id'     => 'required|exists:presentaciones,id',
            'items.*.lote'                => 'required|string|max:100',
            'items.*.cantidad'            => 'required|numeric|min:0.01',
            'items.*.fecha_vencimiento'   => 'nullable|date',
            'items.*.fecha_fabricacion'   => 'nullable|date',
            'items.*.costo_unitario'      => 'nullable|numeric|min:0',
            'items.*.unidad_medida_id'    => 'nullable|exists:unidades_medida,id',
            'items.*.laboratorio_id'      => 'nullable|exists:laboratorios,id',
            'items.*.ubicacion'           => 'nullable|string|max:100',
            'items.*.registro_invima'     => 'nullable|string|max:100',
            'items.*.temperatura_min'     => 'nullable|numeric',
            'items.*.temperatura_max'     => 'nullable|numeric',
            'items.*.observaciones'       => 'nullable|string',
        ]);

        return $request->input('items');
    }

    private function guardarDetalles(Entrada $entrada, array $items): void
    {
        foreach ($items as $it) {
            $cantidad = (float) $it['cantidad'];
            $costo    = (float) ($it['costo_unitario'] ?? 0);

            DetalleEntrada::create([
                'entrada_id'        => $entrada->id,
                'medicamento_id'    => $it['medicamento_id'],
                'presentacion_id'   => $it['presentacion_id'],
                'laboratorio_id'    => $it['laboratorio_id']   ?? null,
                'proveedor_id'      => $entrada->proveedor_id,
                'lote'              => $it['lote'],
                'fecha_vencimiento' => $it['fecha_vencimiento'] ?? null,
                'fecha_fabricacion' => $it['fecha_fabricacion'] ?? null,
                'cantidad'          => $cantidad,
                'unidad_medida_id'  => $it['unidad_medida_id'] ?? null,
                'costo_unitario'    => $costo,
                'costo_total'       => $cantidad * $costo,
                'temperatura_min'   => $it['temperatura_min'] ?? null,
                'temperatura_max'   => $it['temperatura_max'] ?? null,
                'ubicacion'         => $it['ubicacion']       ?? null,
                'registro_invima'   => $it['registro_invima'] ?? null,
                'observaciones'     => $it['observaciones']   ?? null,
            ]);
        }
    }

    private function recalcularTotales(Entrada $entrada): void
    {
        $subtotal = (float) $entrada->detalles()->sum('costo_total');
        $imp      = (float) $entrada->impuestos;
        $entrada->update([
            'subtotal' => $subtotal,
            'total'    => $subtotal + $imp,
        ]);
    }

    private function confirmar(Entrada $entrada): void
    {
        // Validar vencidos
        foreach ($entrada->detalles as $det) {
            if ($det->fecha_vencimiento && Carbon::parse($det->fecha_vencimiento)->isPast()) {
                throw new \RuntimeException("Lote {$det->lote} ya está vencido. No se puede ingresar al stock.");
            }
        }

        foreach ($entrada->detalles as $det) {
            $lote = InventarioLote::where('medicamento_id', $det->medicamento_id)
                                  ->where('presentacion_id', $det->presentacion_id)
                                  ->where('lote', $det->lote)
                                  ->first();

            $anterior = $lote ? (float) $lote->cantidad_actual : 0;

            if ($lote) {
                $lote->cantidad_actual = $anterior + (float) $det->cantidad;
                $lote->save();
            } else {
                $lote = InventarioLote::create([
                    'medicamento_id'    => $det->medicamento_id,
                    'presentacion_id'   => $det->presentacion_id,
                    'lote'              => $det->lote,
                    'fecha_vencimiento' => $det->fecha_vencimiento,
                    'fecha_ingreso'     => $entrada->fecha_entrada,
                    'cantidad_inicial'  => $det->cantidad,
                    'cantidad_actual'   => $det->cantidad,
                    'unidad_medida'     => optional($det->unidadMedida)->abreviatura,
                    'unidad_medida_id'  => $det->unidad_medida_id,
                    'costo_unitario'    => $det->costo_unitario,
                    'ubicacion'         => $det->ubicacion,
                    'temperatura_min'   => $det->temperatura_min,
                    'temperatura_max'   => $det->temperatura_max,
                    'proveedor_id'      => $det->proveedor_id ?: $entrada->proveedor_id,
                    'estado'            => 1,
                ]);
            }

            MovimientoInventario::create([
                'tipo_movimiento'    => 'ENTRADA',
                'referencia_tipo'    => Entrada::class,
                'referencia_id'      => $entrada->id,
                'inventario_lote_id' => $lote->id,
                'cantidad'           => $det->cantidad,
                'stock_anterior'     => $anterior,
                'stock_nuevo'        => $anterior + (float) $det->cantidad,
                'fecha_movimiento'   => now(),
                'usuario_id'         => auth()->id(),
                'observacion'        => 'Entrada ' . $entrada->codigo . ' (' . $entrada->tipo_label . ')'
                    . (($entrada->tipo_entrada === 'ASIGNACION' && $entrada->paciente)
                        ? ' · Asignada a paciente ' . $entrada->paciente->nombre_completo . ' (' . $entrada->paciente->documento . ')'
                        : ''),
                'created_at'         => now(),
            ]);
        }

        $entrada->update(['estado' => 'CONFIRMADA']);
    }

    private function generarCodigo(): string
    {
        $prefijo = 'ENT-' . now()->format('Ymd') . '-';
        $ultimo  = Entrada::where('codigo', 'like', $prefijo . '%')->orderByDesc('codigo')->value('codigo');
        $n       = $ultimo ? ((int) substr($ultimo, -4)) + 1 : 1;
        return $prefijo . str_pad($n, 4, '0', STR_PAD_LEFT);
    }

    private function formData(Entrada $entrada, $detalles): array
    {
        $proveedores    = Proveedor::orderBy('razon_social')->get();
        $medicamentos   = Medicamento::orderBy('nombre')->get();
        $presentaciones = Presentacion::orderBy('nombre')->get(['id','nombre','medicamento_id','unidad_medida_id']);
        $laboratorios   = Laboratorio::orderBy('nombre')->get();
        $unidades       = UnidadMedida::where('estado',1)->orderBy('tipo')->orderBy('nombre')->get();
        $tiposEntrada   = TipoEntrada::orderBy('Detalle')->get();

        $medicamentosJson = $medicamentos->map(fn($m) => [
            'id' => $m->id, 'nombre' => $m->nombre,
            'registro_invima' => $m->registro_invima ?? null,
        ])->values()->all();

        $presentacionesJson = $presentaciones->map(fn($p) => [
            'id' => $p->id, 'nombre' => $p->nombre,
            'medicamento_id' => $p->medicamento_id,
            'unidad_medida_id' => $p->unidad_medida_id,
        ])->values()->all();

        $laboratoriosJson = $laboratorios->map(fn($l) => [
            'id' => $l->id, 'nombre' => $l->nombre,
        ])->values()->all();

        $unidadesJson = $unidades->map(fn($u) => [
            'id' => $u->id, 'nombre' => $u->nombre,
            'abreviatura' => $u->abreviatura, 'tipo' => $u->tipo,
        ])->values()->all();

        $existingJson = collect($detalles)->map(fn($d) => [
            'medicamento_id'    => $d->medicamento_id,
            'presentacion_id'   => $d->presentacion_id,
            'lote'              => $d->lote,
            'fecha_vencimiento' => optional($d->fecha_vencimiento)->format('Y-m-d'),
            'cantidad'          => $d->cantidad,
            'unidad_medida_id'  => $d->unidad_medida_id,
            'costo_unitario'    => $d->costo_unitario,
            'registro_invima'   => $d->registro_invima,
            'ubicacion'         => $d->ubicacion,
            'laboratorio_id'    => $d->laboratorio_id,
        ])->values()->all();

        return compact(
            'entrada','detalles',
            'proveedores','medicamentos','presentaciones','laboratorios','unidades','tiposEntrada',
            'medicamentosJson','presentacionesJson','laboratoriosJson','unidadesJson','existingJson'
        );
    }
}
