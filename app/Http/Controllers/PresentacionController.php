<?php

namespace App\Http\Controllers;

use App\Models\Presentacion;
use App\Models\Medicamento;
use Illuminate\Http\Request;

class PresentacionController extends Controller
{
    public function index(Request $request)
    {
        $query = Presentacion::with('medicamento');

        // Búsqueda general
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('medicamento', function($med) use ($search) {
                    $med->where('nombre', 'like', "%{$search}%");
                })->orWhere('nombre', 'like', "%{$search}%");
            });
        }
        
        if ($forma = $request->get('forma_farmaceutica')) {
            $query->where('forma_farmaceutica', $forma);
        }

        if ($via = $request->get('via_administracion')) {
            $query->where('via_administracion', $via);
        }

        if ($request->filled('refrigeracion')) {
            $query->where('requiere_refrigeracion', $request->get('refrigeracion'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        $presentaciones = $query->latest()->paginate(10)->withQueryString();
        $medicamentos = Medicamento::orderBy('nombre')->get();
        
        $formas = Presentacion::whereNotNull('forma_farmaceutica')->distinct()->pluck('forma_farmaceutica');
        $vias = Presentacion::whereNotNull('via_administracion')->distinct()->pluck('via_administracion');

        return view('admin.presentaciones.index', compact('presentaciones', 'medicamentos', 'formas', 'vias', 'request'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'medicamento_id' => 'required|exists:medicamentos,id',
            'codigo' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'concentracion' => 'nullable|numeric',
            'unidad_concentracion' => 'nullable|string|max:20',
            'volumen' => 'nullable|numeric',
            'unidad_volumen' => 'nullable|string|max:20',
            'forma_farmaceutica' => 'nullable|string|max:100',
            'via_administracion' => 'nullable|string|max:100',
            'tipo_envase' => 'nullable|string|max:100',
            'requiere_refrigeracion' => 'nullable|boolean',
            'estado' => 'nullable|boolean',
        ]);

        $data['requiere_refrigeracion'] = $request->has('requiere_refrigeracion');
        $data['estado'] = $request->has('estado');

        Presentacion::create($data);

        return redirect()->route('admin.presentaciones.index')->with('success', 'Presentación creada correctamente.');
    }

    public function update(Request $request, Presentacion $presentacion)
    {
        $data = $request->validate([
            'medicamento_id' => 'required|exists:medicamentos,id',
            'codigo' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'concentracion' => 'nullable|numeric',
            'unidad_concentracion' => 'nullable|string|max:20',
            'volumen' => 'nullable|numeric',
            'unidad_volumen' => 'nullable|string|max:20',
            'forma_farmaceutica' => 'nullable|string|max:100',
            'via_administracion' => 'nullable|string|max:100',
            'tipo_envase' => 'nullable|string|max:100',
            'requiere_refrigeracion' => 'nullable|boolean',
            'estado' => 'nullable|boolean',
        ]);

        $data['requiere_refrigeracion'] = $request->has('requiere_refrigeracion');
        $data['estado'] = $request->has('estado');

        $presentacion->update($data);

        return redirect()->route('admin.presentaciones.index')->with('success', 'Presentación actualizada correctamente.');
    }

    public function destroy(Presentacion $presentacion)
    {
        $presentacion->delete();
        return redirect()->route('admin.presentaciones.index')->with('success', 'Presentación eliminada correctamente.');
    }
}
