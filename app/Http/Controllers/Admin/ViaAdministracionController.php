<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ViaAdministracion;
use Illuminate\Http\Request;

class ViaAdministracionController extends Controller
{
    public function index(Request $request)
    {
        $query = ViaAdministracion::query();

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
            $query->where('riesgo_clinico', $request->get('riesgo'));
        }

        if ($request->filled('esteril')) {
            $query->where('esteril_requerido', $request->get('esteril'));
        }

        if ($request->filled('bomba')) {
            $query->where('requiere_bomba_infusion', $request->get('bomba'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        $vias = $query->orderBy('nombre')->paginate(12)->withQueryString();

        $stats = [
            'total'        => ViaAdministracion::count(),
            'parenterales' => ViaAdministracion::where('tipo', 'PARENTERAL')->count(),
            'criticas'     => ViaAdministracion::where('riesgo_clinico', 'CRITICO')->count(),
            'esteriles'    => ViaAdministracion::where('esteril_requerido', 1)->count(),
        ];

        return view('admin.vias_administracion.index', compact('vias', 'stats'));
    }

    public function show(ViaAdministracion $vias_administracion)
    {
        $vias_administracion->load(['presentaciones.medicamento']);

        return view('admin.vias_administracion.show', [
            'via' => $vias_administracion,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateVia($request);
        ViaAdministracion::create($validated);

        return redirect()->route('admin.vias_administracion.index')
                         ->with('success', 'Vía de administración registrada exitosamente.');
    }

    public function update(Request $request, ViaAdministracion $vias_administracion)
    {
        $validated = $this->validateVia($request, $vias_administracion->id);
        $vias_administracion->update($validated);

        return redirect()->route('admin.vias_administracion.index')
                         ->with('success', 'Vía de administración actualizada exitosamente.');
    }

    public function destroy(ViaAdministracion $vias_administracion)
    {
        if ($vias_administracion->presentaciones()->exists()) {
            return redirect()->route('admin.vias_administracion.index')
                             ->with('error', 'No se puede eliminar: hay presentaciones que usan esta vía.');
        }

        $vias_administracion->delete();

        return redirect()->route('admin.vias_administracion.index')
                         ->with('success', 'Vía de administración eliminada correctamente.');
    }

    private function validateVia(Request $request, $id = null): array
    {
        return $request->validate([
            'codigo'                    => 'nullable|string|max:20|unique:vias_administracion,codigo' . ($id ? ",{$id}" : ''),
            'nombre'                    => 'required|string|max:100',
            'nombre_corto'              => 'nullable|string|max:50',
            'descripcion'               => 'nullable|string',
            'tipo'                      => 'nullable|in:' . implode(',', array_keys(ViaAdministracion::TIPOS)),
            'esteril_requerido'         => 'nullable|boolean',
            'requiere_bomba_infusion'   => 'nullable|boolean',
            'requiere_filtro'           => 'nullable|boolean',
            'permite_bolo'              => 'nullable|boolean',
            'permite_infusion_continua' => 'nullable|boolean',
            'velocidad_min_ml_h'        => 'nullable|numeric|min:0',
            'velocidad_max_ml_h'        => 'nullable|numeric|min:0',
            'osmolaridad_max'           => 'nullable|numeric|min:0',
            'fotosensible'              => 'nullable|boolean',
            'requiere_monitorizacion'   => 'nullable|boolean',
            'riesgo_clinico'            => 'nullable|in:' . implode(',', array_keys(ViaAdministracion::RIESGOS)),
            'color_identificacion'      => 'nullable|string|max:20',
            'icono'                     => 'nullable|string|max:255',
            'observaciones'             => 'nullable|string',
            'estado'                    => 'nullable|boolean',
        ]);
    }
}
