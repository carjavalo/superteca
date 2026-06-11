<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use App\Models\TProveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = Proveedor::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('razon_social', 'like', "%{$search}%")
                  ->orWhere('nombre_comercial', 'like', "%{$search}%")
                  ->orWhere('nit', 'like', "%{$search}%")
                  ->orWhere('codigo', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo_proveedor')) {
            $query->where('tipo_proveedor', $request->get('tipo_proveedor'));
        }

        if ($request->filled('ciudad')) {
            $query->where('ciudad', $request->get('ciudad'));
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        if ($request->filled('cadena_frio')) {
            $query->where('maneja_cadena_frio', $request->get('cadena_frio'));
        }

        $proveedores = $query->orderBy('razon_social')->paginate(12)->withQueryString();

        $ciudades = Proveedor::select('ciudad')->distinct()->whereNotNull('ciudad')->orderBy('ciudad')->pluck('ciudad');

        $stats = [
            'activos'      => Proveedor::where('estado', 1)->count(),
            'cadena_frio'  => Proveedor::where('maneja_cadena_frio', 1)->count(),
            'total'        => Proveedor::count(),
            'distribuidores' => Proveedor::where('tipo_proveedor', 'DISTRIBUIDOR')->count(),
        ];

        return view('admin.proveedores.index', compact('proveedores', 'ciudades', 'stats'));
    }

    public function show(Proveedor $proveedor)
    {
        $proveedor->load(['inventarioLotes.medicamento', 'inventarioLotes.presentacion']);

        return view('admin.proveedores.show', compact('proveedor'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProveedor($request);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('proveedores/logos', 'public');
        }

        Proveedor::create($validated);

        return redirect()->route('admin.proveedores.index')
                         ->with('success', 'Proveedor registrado exitosamente.');
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $this->validateProveedor($request);

        if ($request->hasFile('logo')) {
            if ($proveedor->logo) {
                Storage::disk('public')->delete($proveedor->logo);
            }
            $validated['logo'] = $request->file('logo')->store('proveedores/logos', 'public');
        }

        $proveedor->update($validated);

        return redirect()->route('admin.proveedores.index')
                         ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        if ($proveedor->inventarioLotes()->exists()) {
            return redirect()->route('admin.proveedores.index')
                             ->with('error', 'No se puede eliminar: el proveedor tiene lotes de inventario asociados.');
        }

        if ($proveedor->logo) {
            Storage::disk('public')->delete($proveedor->logo);
        }

        $proveedor->delete();

        return redirect()->route('admin.proveedores.index')
                         ->with('success', 'Proveedor eliminado correctamente.');
    }

    private function validateProveedor(Request $request): array
    {
        return $request->validate([
            'codigo'                     => 'nullable|string|max:50',
            'tipo_proveedor'             => ['nullable', Rule::in(TProveedor::codigosDisponibles())],
            'razon_social'               => 'required|string|max:255',
            'nombre_comercial'           => 'nullable|string|max:255',
            'nit'                        => 'required|string|max:50',
            'digito_verificacion'        => 'nullable|string|max:5',
            'registro_invima'            => 'nullable|string|max:100',
            'habilitacion_salud'         => 'nullable|string|max:100',
            'direccion'                  => 'nullable|string|max:255',
            'ciudad'                     => 'nullable|string|max:100',
            'departamento'               => 'nullable|string|max:100',
            'pais'                       => 'nullable|string|max:100',
            'telefono'                   => 'nullable|string|max:100',
            'celular'                    => 'nullable|string|max:100',
            'email'                      => 'nullable|email|max:150',
            'sitio_web'                  => 'nullable|string|max:255',
            'contacto_comercial'         => 'nullable|string|max:255',
            'telefono_contacto'          => 'nullable|string|max:100',
            'email_contacto'             => 'nullable|email|max:150',
            'contacto_farmacovigilancia' => 'nullable|string|max:255',
            'contacto_logistica'         => 'nullable|string|max:255',
            'condiciones_pago'           => 'nullable|string|max:255',
            'dias_credito'               => 'nullable|integer|min:0|max:365',
            'maneja_cadena_frio'         => 'nullable|boolean',
            'temperatura_min'            => 'nullable|numeric',
            'temperatura_max'            => 'nullable|numeric',
            'tiempo_entrega_horas'       => 'nullable|integer|min:0',
            'horario_entrega'            => 'nullable|string|max:255',
            'certificaciones'            => 'nullable|string',
            'observaciones'              => 'nullable|string',
            'logo'                       => 'nullable|image|max:2048',
            'estado'                     => 'nullable|boolean',
        ]);
    }
}
