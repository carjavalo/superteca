<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eps;
use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EpsController extends Controller
{
    public function index(Request $request)
    {
        $query = Eps::query();

        if ($search = trim((string) $request->get('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('Detalle', 'like', "%{$search}%")
                  ->orWhere('Observacion', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado') && in_array($request->get('estado'), ['0', '1'], true)) {
            $query->where('Estado', (int) $request->get('estado'));
        }

        // Ordenamiento: por Código (id) o por Detalle (alfabético).
        $orden = $request->get('orden') === 'detalle' ? 'Detalle' : 'id';
        $dir   = $request->get('dir') === 'desc' ? 'desc' : 'asc';

        $tipos = $query->orderBy($orden, $dir)->paginate(12)->withQueryString();

        // Cuántos pacientes usan cada EPS (por nombre, ya que pacientes.eps es texto).
        $usoPorNombre = Paciente::select('eps', DB::raw('COUNT(*) as total'))
            ->whereNotNull('eps')->where('eps', '!=', '')
            ->groupBy('eps')
            ->pluck('total', 'eps')
            ->all();

        $stats = [
            'total'     => Eps::count(),
            'activas'   => Eps::where('Estado', 1)->count(),
            'inactivas' => Eps::where('Estado', 0)->count(),
            'pacientes' => array_sum($usoPorNombre),
        ];

        return view('admin.eps.index', compact('tipos', 'stats', 'usoPorNombre'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        Eps::create($data);

        return redirect()->route('admin.eps.index')
            ->with('success', 'EPS creada correctamente.');
    }

    public function update(Request $request, Eps $eps)
    {
        $data = $this->validar($request, $eps->id);
        $eps->update($data);

        return redirect()->route('admin.eps.index')
            ->with('success', 'EPS actualizada correctamente.');
    }

    public function destroy(Eps $eps)
    {
        $enUso = Paciente::where('eps', $eps->Detalle)->count();

        if ($enUso > 0) {
            return redirect()->route('admin.eps.index')
                ->with('error', "No se puede eliminar «{$eps->Detalle}»: hay {$enUso} paciente(s) con esta EPS. Puede marcarla como Inactiva.");
        }

        $eps->delete();

        return redirect()->route('admin.eps.index')
            ->with('success', 'EPS eliminada correctamente.');
    }

    private function validar(Request $request, $id = null): array
    {
        return $request->validate([
            'Detalle'     => 'required|string|max:120|unique:EPS,Detalle' . ($id ? ",{$id}" : ''),
            'Estado'      => 'required|boolean',
            'Observacion' => 'nullable|string|max:300',
        ], [
            'Detalle.required' => 'El detalle (nombre de la EPS) es obligatorio.',
            'Detalle.unique'   => 'Ya existe una EPS con ese detalle.',
            'Detalle.max'      => 'El detalle no puede superar los 120 caracteres.',
            'Estado.required'  => 'Debe indicar el estado (Activo o Inactivo).',
            'Observacion.max'  => 'La observación no puede superar los 300 caracteres.',
        ]);
    }
}
