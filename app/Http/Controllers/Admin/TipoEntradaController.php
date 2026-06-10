<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Entrada;
use App\Models\TipoEntrada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoEntradaController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoEntrada::query();

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

        // Cuántas entradas usan cada tipo (por código derivado).
        $usoPorCodigo = Entrada::select('tipo_entrada', DB::raw('COUNT(*) as total'))
            ->groupBy('tipo_entrada')
            ->pluck('total', 'tipo_entrada')
            ->all();

        $totalTipos = TipoEntrada::count();
        $enUso = 0;
        foreach (TipoEntrada::all() as $t) {
            if (($usoPorCodigo[$t->codigo] ?? 0) > 0) {
                $enUso++;
            }
        }

        $stats = [
            'total'    => $totalTipos,
            'en_uso'   => $enUso,
            'sin_uso'  => max(0, $totalTipos - $enUso),
            'entradas' => array_sum($usoPorCodigo),
        ];

        return view('admin.tipos_entrada.index', compact('tipos', 'stats', 'usoPorCodigo'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        TipoEntrada::create($data);

        return redirect()->route('admin.tipos_entrada.index')
            ->with('success', 'Tipo de entrada creado correctamente.');
    }

    public function update(Request $request, TipoEntrada $tipos_entrada)
    {
        $data = $this->validar($request, $tipos_entrada->id);
        $tipos_entrada->update($data);

        return redirect()->route('admin.tipos_entrada.index')
            ->with('success', 'Tipo de entrada actualizado correctamente.');
    }

    public function destroy(TipoEntrada $tipos_entrada)
    {
        $enUso = Entrada::where('tipo_entrada', $tipos_entrada->codigo)->count();

        if ($enUso > 0) {
            return redirect()->route('admin.tipos_entrada.index')
                ->with('error', "No se puede eliminar «{$tipos_entrada->Detalle}»: hay {$enUso} entrada(s) que lo utilizan.");
        }

        $tipos_entrada->delete();

        return redirect()->route('admin.tipos_entrada.index')
            ->with('success', 'Tipo de entrada eliminado correctamente.');
    }

    private function validar(Request $request, $id = null): array
    {
        return $request->validate([
            'Detalle'     => 'required|string|max:120|unique:tipoEntrada,Detalle' . ($id ? ",{$id}" : ''),
            'Observacion' => 'nullable|string|max:300',
        ], [
            'Detalle.required' => 'El detalle (nombre del tipo de entrada) es obligatorio.',
            'Detalle.unique'   => 'Ya existe un tipo de entrada con ese detalle.',
            'Detalle.max'      => 'El detalle no puede superar los 120 caracteres.',
            'Observacion.max'  => 'La observación no puede superar los 300 caracteres.',
        ]);
    }
}
