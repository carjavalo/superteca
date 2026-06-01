<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    public function index(Request $request)
    {
        $query = UnidadMedida::query()->with('unidadBase');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('abreviatura', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%")
                  ->orWhere('simbolo', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo'))   $query->where('tipo', $request->get('tipo'));
        if ($request->filled('estado')) $query->where('estado', $request->get('estado'));
        if ($request->filled('calculos')) $query->where('activa_calculos', $request->get('calculos'));

        $unidades = $query->orderBy('tipo')->orderBy('factor_conversion')->paginate(12)->withQueryString();

        $stats = [
            'total'       => UnidadMedida::count(),
            'tipos'       => UnidadMedida::distinct('tipo')->whereNotNull('tipo')->count('tipo'),
            'calculo'     => UnidadMedida::where('activa_calculos', 1)->count(),
            'activas'     => UnidadMedida::where('estado', 1)->count(),
        ];

        $unidadesPorTipo = UnidadMedida::where('estado', 1)
            ->orderBy('factor_conversion')
            ->get()
            ->groupBy('tipo');

        $unidadesJson = UnidadMedida::where('estado', 1)
            ->whereNotNull('tipo')
            ->orderBy('tipo')
            ->orderBy('factor_conversion')
            ->get(['id','codigo','nombre','abreviatura','tipo','factor_conversion','precision_decimal','unidad_base_id'])
            ->toArray();

        return view('admin.unidades_medida.index', compact('unidades', 'stats', 'unidadesPorTipo', 'unidadesJson'));
    }

    public function show(UnidadMedida $unidad_medida)
    {
        $unidad_medida->load(['unidadBase', 'derivadas', 'presentaciones.medicamento']);

        $hermanas = UnidadMedida::where('tipo', $unidad_medida->tipo)
            ->where('id', '!=', $unidad_medida->id)
            ->orderBy('factor_conversion')
            ->get();

        return view('admin.unidades_medida.show', [
            'unidad'   => $unidad_medida,
            'hermanas' => $hermanas,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateUnidad($request);
        UnidadMedida::create($validated);

        return redirect()->route('admin.unidades_medida.index')
                         ->with('success', 'Unidad de medida registrada exitosamente.');
    }

    public function update(Request $request, UnidadMedida $unidad_medida)
    {
        $validated = $this->validateUnidad($request, $unidad_medida->id);
        $unidad_medida->update($validated);

        return redirect()->route('admin.unidades_medida.index')
                         ->with('success', 'Unidad de medida actualizada exitosamente.');
    }

    public function destroy(UnidadMedida $unidad_medida)
    {
        if ($unidad_medida->presentaciones()->exists() || $unidad_medida->lotes()->exists() || $unidad_medida->derivadas()->exists()) {
            return redirect()->route('admin.unidades_medida.index')
                             ->with('error', 'No se puede eliminar: la unidad está en uso o tiene unidades derivadas.');
        }

        $unidad_medida->delete();

        return redirect()->route('admin.unidades_medida.index')
                         ->with('success', 'Unidad de medida eliminada correctamente.');
    }

    private function validateUnidad(Request $request, $id = null): array
    {
        return $request->validate([
            'codigo'               => 'required|string|max:20|unique:unidades_medida,codigo' . ($id ? ",{$id}" : ''),
            'nombre'               => 'required|string|max:100',
            'abreviatura'          => 'required|string|max:20',
            'tipo'                 => 'nullable|in:' . implode(',', array_keys(UnidadMedida::TIPOS)),
            'unidad_base_id'       => 'nullable|exists:unidades_medida,id',
            'factor_conversion'    => 'nullable|numeric|min:0',
            'simbolo'              => 'nullable|string|max:20',
            'precision_decimal'    => 'nullable|integer|min:0|max:10',
            'permite_fracciones'   => 'nullable|boolean',
            'activa_calculos'      => 'nullable|boolean',
            'color_identificacion' => 'nullable|string|max:20',
            'icono'                => 'nullable|string|max:255',
            'observaciones'        => 'nullable|string',
            'estado'               => 'nullable|boolean',
        ]);
    }
}
