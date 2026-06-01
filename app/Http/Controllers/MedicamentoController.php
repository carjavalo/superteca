<?php

namespace App\Http\Controllers;

use App\Models\Medicamento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicamentoController extends Controller
{
    public function index(Request $request)
    {
        $query = Medicamento::query();

        // Filtros
        if ($request->filled('nombre')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->nombre . '%')
                  ->orWhere('nombre_generico', 'like', '%' . $request->nombre . '%');
            });
        }
        if ($request->filled('codigo')) {
            $query->where('codigo', 'like', '%' . $request->codigo . '%');
        }
        if ($request->filled('registro_invima')) {
            $query->where('registro_invima', 'like', '%' . $request->registro_invima . '%');
        }
        if ($request->filled('laboratorio')) {
            $query->where('laboratorio', 'like', '%' . $request->laboratorio . '%');
        }
        if ($request->filled('forma_farmaceutica')) {
            $query->where('forma_farmaceutica', 'like', '%' . $request->forma_farmaceutica . '%');
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $medicamentos = $query->latest()->paginate(10)->withQueryString();

        return view('admin.medicamentos.index', compact('medicamentos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo'                 => ['nullable', 'string', 'max:50', 'unique:medicamentos,codigo'],
            'nombre'                 => ['required', 'string', 'max:255'],
            'nombre_generico'        => ['nullable', 'string', 'max:255'],
            'concentracion'          => ['nullable', 'string', 'max:100'],
            'unidad_medida'          => ['nullable', 'string', 'max:50'],
            'forma_farmaceutica'     => ['nullable', 'string', 'max:100'],
            'via_administracion'     => ['nullable', 'string', 'max:100'],
            'laboratorio'            => ['nullable', 'string', 'max:255'],
            'registro_invima'        => ['nullable', 'string', 'max:100'],
            'requiere_refrigeracion' => ['nullable', 'boolean'],
            'fotoproteccion'         => ['nullable', 'boolean'],
            'alto_riesgo'            => ['nullable', 'boolean'],
            'controlado'             => ['nullable', 'boolean'],
            'estabilidad_horas'      => ['nullable', 'integer', 'min:0'],
            'semaforo_sanitario'     => ['nullable', 'string', 'in:verde,amarillo,rojo'],
            'estado'                 => ['nullable', 'boolean'],
        ]);

        Medicamento::create(array_merge($request->except(['requiere_refrigeracion', 'fotoproteccion', 'alto_riesgo', 'controlado', 'estado', 'semaforo_sanitario']), [
            'requiere_refrigeracion' => $request->boolean('requiere_refrigeracion'),
            'fotoproteccion'         => $request->boolean('fotoproteccion'),
            'alto_riesgo'            => $request->boolean('alto_riesgo'),
            'controlado'             => $request->boolean('controlado'),
            'semaforo_sanitario'     => $request->input('semaforo_sanitario', 'verde'),
            'estado'                 => $request->boolean('estado', true),
        ]));

        return redirect()->route('admin.medicamentos.index')->with('success', 'Medicamento creado exitosamente.');
    }

    public function update(Request $request, Medicamento $medicamento)
    {
        $request->validate([
            'codigo'                 => ['nullable', 'string', 'max:50', Rule::unique('medicamentos', 'codigo')->ignore($medicamento->id)],
            'nombre'                 => ['required', 'string', 'max:255'],
            'nombre_generico'        => ['nullable', 'string', 'max:255'],
            'concentracion'          => ['nullable', 'string', 'max:100'],
            'unidad_medida'          => ['nullable', 'string', 'max:50'],
            'forma_farmaceutica'     => ['nullable', 'string', 'max:100'],
            'via_administracion'     => ['nullable', 'string', 'max:100'],
            'laboratorio'            => ['nullable', 'string', 'max:255'],
            'registro_invima'        => ['nullable', 'string', 'max:100'],
            'requiere_refrigeracion' => ['boolean'],
            'fotoproteccion'         => ['boolean'],
            'alto_riesgo'            => ['boolean'],
            'controlado'             => ['boolean'],
            'estabilidad_horas'      => ['nullable', 'integer', 'min:0'],
            'semaforo_sanitario'     => ['nullable', 'string', 'in:verde,amarillo,rojo'],
            'estado'                 => ['boolean'],
        ]);

        $medicamento->update(array_merge($request->except(['requiere_refrigeracion', 'fotoproteccion', 'alto_riesgo', 'controlado', 'estado', 'semaforo_sanitario']), [
            'requiere_refrigeracion' => $request->boolean('requiere_refrigeracion'),
            'fotoproteccion'         => $request->boolean('fotoproteccion'),
            'alto_riesgo'            => $request->boolean('alto_riesgo'),
            'controlado'             => $request->boolean('controlado'),
            'semaforo_sanitario'     => $request->input('semaforo_sanitario', 'verde'),
            'estado'                 => $request->boolean('estado', true),
        ]));

        return redirect()->route('admin.medicamentos.index')->with('success', 'Medicamento actualizado correctamente.');
    }

    public function destroy(Medicamento $medicamento)
    {
        $medicamento->delete();
        return redirect()->route('admin.medicamentos.index')->with('success', 'Medicamento eliminado.');
    }
}
