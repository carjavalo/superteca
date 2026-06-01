<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Laboratorio;
use Illuminate\Http\Request;

class LaboratorioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Laboratorio::query();

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('nit', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('pais_origen')) {
            $query->where('pais_origen', $request->get('pais_origen'));
        }

        $laboratorios = $query->orderBy('nombre')->paginate(12);

        $paises = Laboratorio::select('pais_origen')->distinct()->whereNotNull('pais_origen')->pluck('pais_origen');

        return view('admin.laboratorios.index', compact('laboratorios', 'paises'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // View generated within modally index for "Catálogo" style
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'nit'    => 'nullable|string|max:50',
            'registro_invima' => 'nullable|string|max:100',
            'pais_origen' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:100',
            'email' => 'nullable|string|email|max:150',
            'sitio_web' => 'nullable|string|max:255',
            'contacto_comercial' => 'nullable|string|max:255',
            'contacto_farmacovigilancia' => 'nullable|string|max:255',
            'requiere_cadena_frio' => 'boolean',
            'semaforo_sanitario' => 'required|string|in:verde,amarillo,rojo',
            'estado' => 'boolean',
            'observaciones' => 'nullable|string',
        ]);

        Laboratorio::create($validated);

        return redirect()->route('admin.laboratorios.index')
                         ->with('success', 'Laboratorio registrado exitosamente en el catálogo.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Laboratorio $laboratorio)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Laboratorio $laboratorio)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $laboratorio = Laboratorio::findOrFail($id);

        $validated = $request->validate([
            'codigo' => 'nullable|string|max:50',
            'nombre' => 'required|string|max:255',
            'nit'    => 'nullable|string|max:50',
            'registro_invima' => 'nullable|string|max:100',
            'pais_origen' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:100',
            'email' => 'nullable|string|email|max:150',
            'sitio_web' => 'nullable|string|max:255',
            'contacto_comercial' => 'nullable|string|max:255',
            'contacto_farmacovigilancia' => 'nullable|string|max:255',
            'requiere_cadena_frio' => 'boolean',
            'semaforo_sanitario' => 'required|string|in:verde,amarillo,rojo',
            'estado' => 'boolean',
            'observaciones' => 'nullable|string',
        ]);

        $laboratorio->update($validated);

        return redirect()->route('admin.laboratorios.index')
                         ->with('success', 'Laboratorio actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $laboratorio = Laboratorio::findOrFail($id);
        $laboratorio->delete();

        return redirect()->route('admin.laboratorios.index')
                         ->with('success', 'Laboratorio eliminado correctamente.');
    }
}
