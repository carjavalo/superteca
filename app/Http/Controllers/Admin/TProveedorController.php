<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use App\Models\TProveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TProveedorController extends Controller
{
    public function index(Request $request)
    {
        $query = TProveedor::query();

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('Detalle', 'like', "%{$search}%")
                  ->orWhere('Observacion', 'like', "%{$search}%");
            });
        }

        // Ordenamiento: por Código (id) o por Detalle (alfabético).
        $orden = $request->get('orden') === 'detalle' ? 'Detalle' : 'id';
        $dir   = $request->get('dir') === 'desc' ? 'desc' : 'asc';

        $tipos = $query->orderBy($orden, $dir)->paginate(12)->withQueryString();

        // Cuántos proveedores usan cada tipo (por código derivado).
        $usoPorCodigo = Proveedor::select('tipo_proveedor', DB::raw('COUNT(*) as total'))
            ->whereNotNull('tipo_proveedor')
            ->groupBy('tipo_proveedor')
            ->pluck('total', 'tipo_proveedor')
            ->all();

        $totalTipos = TProveedor::count();
        $enUso = 0;
        foreach (TProveedor::all() as $t) {
            if (($usoPorCodigo[$t->codigo] ?? 0) > 0) {
                $enUso++;
            }
        }

        $stats = [
            'total'        => $totalTipos,
            'en_uso'       => $enUso,
            'sin_uso'      => max(0, $totalTipos - $enUso),
            'proveedores'  => array_sum($usoPorCodigo),
        ];

        return view('admin.tproveedor.index', compact('tipos', 'stats', 'usoPorCodigo'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        TProveedor::create($data);

        return redirect()->route('admin.tproveedor.index')
            ->with('success', 'Tipo de proveedor creado correctamente.');
    }

    public function update(Request $request, TProveedor $tproveedor)
    {
        $data = $this->validar($request, $tproveedor->id);
        $tproveedor->update($data);

        return redirect()->route('admin.tproveedor.index')
            ->with('success', 'Tipo de proveedor actualizado correctamente.');
    }

    public function destroy(TProveedor $tproveedor)
    {
        $enUso = Proveedor::where('tipo_proveedor', $tproveedor->codigo)->count();

        if ($enUso > 0) {
            return redirect()->route('admin.tproveedor.index')
                ->with('error', "No se puede eliminar «{$tproveedor->Detalle}»: hay {$enUso} proveedor(es) que lo utilizan.");
        }

        $tproveedor->delete();

        return redirect()->route('admin.tproveedor.index')
            ->with('success', 'Tipo de proveedor eliminado correctamente.');
    }

    private function validar(Request $request, $id = null): array
    {
        return $request->validate([
            'Detalle'     => 'required|string|max:120|unique:TProveedor,Detalle' . ($id ? ",{$id}" : ''),
            'Observacion' => 'nullable|string|max:300',
        ], [
            'Detalle.required' => 'El detalle (nombre del tipo de proveedor) es obligatorio.',
            'Detalle.unique'   => 'Ya existe un tipo de proveedor con ese detalle.',
            'Detalle.max'      => 'El detalle no puede superar los 120 caracteres.',
            'Observacion.max'  => 'La observación no puede superar los 300 caracteres.',
        ]);
    }
}
