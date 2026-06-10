<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoAdministracion;
use App\Models\ViaAdministracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoAdministracionController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoAdministracion::query();

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

        // Cuántas vías de administración usan cada tipo (por código derivado).
        $usoPorCodigo = ViaAdministracion::select('tipo', DB::raw('COUNT(*) as total'))
            ->whereNotNull('tipo')
            ->groupBy('tipo')
            ->pluck('total', 'tipo')
            ->all();

        $totalTipos = TipoAdministracion::count();
        $enUso = 0;
        foreach (TipoAdministracion::all() as $t) {
            if (($usoPorCodigo[$t->codigo] ?? 0) > 0) {
                $enUso++;
            }
        }

        $stats = [
            'total'   => $totalTipos,
            'en_uso'  => $enUso,
            'sin_uso' => max(0, $totalTipos - $enUso),
            'vias'    => array_sum($usoPorCodigo),
        ];

        return view('admin.tipos_administracion.index', compact('tipos', 'stats', 'usoPorCodigo'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        TipoAdministracion::create($data);

        return redirect()->route('admin.tipos_administracion.index')
            ->with('success', 'Tipo de administración creado correctamente.');
    }

    public function update(Request $request, TipoAdministracion $tipos_administracion)
    {
        $data = $this->validar($request, $tipos_administracion->id);
        $tipos_administracion->update($data);

        return redirect()->route('admin.tipos_administracion.index')
            ->with('success', 'Tipo de administración actualizado correctamente.');
    }

    public function destroy(TipoAdministracion $tipos_administracion)
    {
        $enUso = ViaAdministracion::where('tipo', $tipos_administracion->codigo)->count();

        if ($enUso > 0) {
            return redirect()->route('admin.tipos_administracion.index')
                ->with('error', "No se puede eliminar «{$tipos_administracion->Detalle}»: hay {$enUso} vía(s) de administración que lo utilizan.");
        }

        $tipos_administracion->delete();

        return redirect()->route('admin.tipos_administracion.index')
            ->with('success', 'Tipo de administración eliminado correctamente.');
    }

    private function validar(Request $request, $id = null): array
    {
        return $request->validate([
            'Detalle'     => 'required|string|max:120|unique:TipoAdministracion,Detalle' . ($id ? ",{$id}" : ''),
            'Observacion' => 'nullable|string|max:300',
        ], [
            'Detalle.required' => 'El detalle (nombre del tipo de administración) es obligatorio.',
            'Detalle.unique'   => 'Ya existe un tipo de administración con ese detalle.',
            'Detalle.max'      => 'El detalle no puede superar los 120 caracteres.',
            'Observacion.max'  => 'La observación no puede superar los 300 caracteres.',
        ]);
    }
}
