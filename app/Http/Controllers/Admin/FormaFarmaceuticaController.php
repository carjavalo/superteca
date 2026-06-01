<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FormaFarmaceutica;
use Illuminate\Http\Request;

class FormaFarmaceuticaController extends Controller
{
    public function index(Request $request)
    {
        $query = FormaFarmaceutica::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('nombre_corto', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->get('tipo'));
        }

        if ($request->filled('riesgo')) {
            $query->where('riesgo_contaminacion', $request->get('riesgo'));
        }

        if ($request->filled('esteril')) {
            $query->where('esteril', $request->get('esteril'));
        }

        if ($request->filled('cadena_frio')) {
            $query->where('requiere_cadena_frio', $request->get('cadena_frio'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        $formas = $query->orderBy('nombre')->paginate(12)->withQueryString();

        $stats = [
            'total'        => FormaFarmaceutica::count(),
            'esteriles'    => FormaFarmaceutica::where('esteril', 1)->count(),
            'cadena_frio'  => FormaFarmaceutica::where('requiere_cadena_frio', 1)->count(),
            'alto_riesgo'  => FormaFarmaceutica::where('riesgo_contaminacion', 'ALTO')->count(),
        ];

        return view('admin.formas_farmaceuticas.index', compact('formas', 'stats'));
    }

    public function show(FormaFarmaceutica $formas_farmaceutica)
    {
        $formas_farmaceutica->load(['presentaciones.medicamento']);

        return view('admin.formas_farmaceuticas.show', [
            'forma' => $formas_farmaceutica,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateForma($request);

        FormaFarmaceutica::create($validated);

        return redirect()->route('admin.formas_farmaceuticas.index')
                         ->with('success', 'Forma farmacéutica registrada exitosamente.');
    }

    public function update(Request $request, FormaFarmaceutica $formas_farmaceutica)
    {
        $validated = $this->validateForma($request);

        $formas_farmaceutica->update($validated);

        return redirect()->route('admin.formas_farmaceuticas.index')
                         ->with('success', 'Forma farmacéutica actualizada exitosamente.');
    }

    public function destroy(FormaFarmaceutica $formas_farmaceutica)
    {
        if ($formas_farmaceutica->presentaciones()->exists()) {
            return redirect()->route('admin.formas_farmaceuticas.index')
                             ->with('error', 'No se puede eliminar: hay presentaciones que usan esta forma farmacéutica.');
        }

        $formas_farmaceutica->delete();

        return redirect()->route('admin.formas_farmaceuticas.index')
                         ->with('success', 'Forma farmacéutica eliminada correctamente.');
    }

    private function validateForma(Request $request): array
    {
        return $request->validate([
            'codigo'                    => 'nullable|string|max:50',
            'nombre'                    => 'required|string|max:100',
            'nombre_corto'              => 'nullable|string|max:50',
            'descripcion'               => 'nullable|string',
            'tipo'                      => 'nullable|in:' . implode(',', array_keys(FormaFarmaceutica::TIPOS)),
            'requiere_reconstitucion'   => 'nullable|boolean',
            'requiere_dilucion'         => 'nullable|boolean',
            'esteril'                   => 'nullable|boolean',
            'multidosis'                => 'nullable|boolean',
            'reutilizable'              => 'nullable|boolean',
            'requiere_cadena_frio'      => 'nullable|boolean',
            'temperatura_min'           => 'nullable|numeric',
            'temperatura_max'           => 'nullable|numeric',
            'tiempo_estabilidad_horas'  => 'nullable|integer|min:0',
            'permite_fraccionamiento'   => 'nullable|boolean',
            'riesgo_contaminacion'      => 'nullable|in:' . implode(',', array_keys(FormaFarmaceutica::RIESGOS)),
            'color_identificacion'      => 'nullable|string|max:20',
            'icono'                     => 'nullable|string|max:255',
            'observaciones'             => 'nullable|string',
            'estado'                    => 'nullable|boolean',
        ]);
    }
}
