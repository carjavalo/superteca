<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\FormulaDetalle;
use App\Models\InventarioLote;
use App\Models\Medicamento;
use App\Models\Presentacion;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormulaController extends Controller
{
    public function index(Request $request)
    {
        $query = Formula::withCount('detalles');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('codigo','like',"%$s%")->orWhere('nombre','like',"%$s%");
            });
        }
        if ($request->filled('tipo'))   $query->where('tipo_formula', $request->tipo);
        if ($request->filled('estado')) $query->where('estado', $request->estado);

        $formulas = $query->orderBy('nombre')->get();

        $stats = [
            'total'         => Formula::count(),
            'nutricion'     => Formula::where('tipo_formula','NUTRICION_PARENTERAL')->count(),
            'oncologia'     => Formula::where('tipo_formula','ONCOLOGIA')->count(),
            'antibiotico'   => Formula::where('tipo_formula','ANTIBIOTICO')->count(),
            'pediatria'     => Formula::where('tipo_formula','PEDIATRIA')->count(),
            'magistral'     => Formula::where('tipo_formula','MAGISTRAL')->count(),
        ];

        return view('admin.formulas.index', compact('formulas','stats'));
    }

    public function create()
    {
        $medicamentos = Medicamento::orderBy('nombre')->get();
        $presentaciones = Presentacion::orderBy('nombre')->get();
        $unidades = UnidadMedida::orderBy('nombre')->get();

        return view('admin.formulas.create', compact('medicamentos','presentaciones','unidades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_formula' => 'required|in:'.implode(',', array_keys(Formula::TIPOS)),
            'descripcion' => 'nullable|string',
            'volumen_final' => 'nullable|numeric|min:0',
            'unidad_volumen_id' => 'nullable|exists:unidades_medida,id',
            'tiempo_estabilidad_horas' => 'nullable|integer|min:0',
            'temperatura_min' => 'nullable|numeric',
            'temperatura_max' => 'nullable|numeric',
            'requiere_refrigeracion' => 'nullable|boolean',
            'observaciones' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.medicamento_id' => 'required|exists:medicamentos,id',
            'detalles.*.presentacion_id' => 'nullable|exists:presentaciones,id',
            'detalles.*.dosis' => 'required|numeric|min:0',
            'detalles.*.unidad_medida_id' => 'nullable|exists:unidades_medida,id',
            'detalles.*.observaciones' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $request) {
            $formula = Formula::create([
                'nombre' => $data['nombre'],
                'tipo_formula' => $data['tipo_formula'],
                'descripcion' => $data['descripcion'] ?? null,
                'volumen_final' => $data['volumen_final'] ?? null,
                'unidad_volumen_id' => $data['unidad_volumen_id'] ?? null,
                'tiempo_estabilidad_horas' => $data['tiempo_estabilidad_horas'] ?? null,
                'temperatura_min' => $data['temperatura_min'] ?? null,
                'temperatura_max' => $data['temperatura_max'] ?? null,
                'requiere_refrigeracion' => $request->boolean('requiere_refrigeracion'),
                'observaciones' => $data['observaciones'] ?? null,
                'estado' => true,
            ]);

            foreach ($data['detalles'] as $i => $det) {
                FormulaDetalle::create([
                    'formula_id' => $formula->id,
                    'medicamento_id' => $det['medicamento_id'],
                    'presentacion_id' => $det['presentacion_id'] ?? null,
                    'dosis' => $det['dosis'],
                    'unidad_medida_id' => $det['unidad_medida_id'] ?? null,
                    'orden_preparacion' => $i + 1,
                    'obligatorio' => true,
                    'observaciones' => $det['observaciones'] ?? null,
                ]);
            }
        });

        return redirect()->route('admin.formulas.index')->with('success','Fórmula creada exitosamente.');
    }

    public function show(Formula $formula)
    {
        $formula->load([
            'detalles.medicamento',
            'detalles.presentacion',
            'detalles.unidadMedida',
            'unidadVolumen',
        ]);

        // Calcular impacto en inventario actual por componente
        $impacto = [];
        foreach ($formula->detalles as $d) {
            $stock = InventarioLote::where('medicamento_id', $d->medicamento_id)
                ->where('estado', 1)->sum('cantidad_actual');
            $impacto[] = [
                'medicamento' => $d->medicamento->nombre ?? '—',
                'dosis' => $d->dosis,
                'unidad' => $d->unidadMedida->nombre ?? ($d->unidadMedida->codigo ?? ''),
                'stock_actual' => $stock,
            ];
        }

        return view('admin.formulas.show', compact('formula','impacto'));
    }

    public function simulador(Formula $formula, Request $request)
    {
        $cantidad = max(1, (int) $request->input('pacientes', 10));
        $formula->load('detalles.medicamento','detalles.unidadMedida');

        $simulacion = [];
        foreach ($formula->detalles as $d) {
            $necesario = $d->dosis * $cantidad;
            $stock = InventarioLote::where('medicamento_id', $d->medicamento_id)
                ->where('estado', 1)->sum('cantidad_actual');
            $proyectado = $stock - $necesario;
            $simulacion[] = [
                'medicamento'  => $d->medicamento->nombre ?? '—',
                'dosis_unitaria' => $d->dosis,
                'unidad' => $d->unidadMedida->nombre ?? '',
                'consumo_total' => $necesario,
                'stock_actual' => $stock,
                'stock_proyectado' => $proyectado,
                'alerta' => $proyectado < 0,
            ];
        }

        return view('admin.formulas.simulador', compact('formula','simulacion','cantidad'));
    }

    public function destroy(Formula $formula)
    {
        $formula->delete();
        return redirect()->route('admin.formulas.index')->with('success','Fórmula eliminada.');
    }
}
