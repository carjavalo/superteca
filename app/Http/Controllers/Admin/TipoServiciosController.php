<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\TipoServicios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoServiciosController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoServicios::query();

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

        // Cuántos pacientes usan cada servicio (por id).
        $usoPorId = Paciente::select('servicio_id', DB::raw('COUNT(*) as total'))
            ->whereNotNull('servicio_id')
            ->groupBy('servicio_id')
            ->pluck('total', 'servicio_id')
            ->all();

        $totalTipos = TipoServicios::count();
        $enUso = 0;
        foreach (TipoServicios::all() as $t) {
            if (($usoPorId[$t->id] ?? 0) > 0) {
                $enUso++;
            }
        }

        $stats = [
            'total'     => $totalTipos,
            'en_uso'    => $enUso,
            'sin_uso'   => max(0, $totalTipos - $enUso),
            'pacientes' => array_sum($usoPorId),
        ];

        return view('admin.tipos_servicios.index', compact('tipos', 'stats', 'usoPorId'));
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        TipoServicios::create($data);

        return redirect()->route('admin.tipos_servicios.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function update(Request $request, TipoServicios $tipo_servicio)
    {
        $data = $this->validar($request, $tipo_servicio->id);
        $tipo_servicio->update($data);

        return redirect()->route('admin.tipos_servicios.index')
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(TipoServicios $tipo_servicio)
    {
        $enUso = Paciente::where('servicio_id', $tipo_servicio->id)->count();

        if ($enUso > 0) {
            return redirect()->route('admin.tipos_servicios.index')
                ->with('error', "No se puede eliminar «{$tipo_servicio->Detalle}»: hay {$enUso} paciente(s) asociado(s) a este servicio.");
        }

        $tipo_servicio->delete();

        return redirect()->route('admin.tipos_servicios.index')
            ->with('success', 'Servicio eliminado correctamente.');
    }

    private function validar(Request $request, $id = null): array
    {
        return $request->validate([
            'Detalle'     => 'required|string|max:120|unique:TipoServicios,Detalle' . ($id ? ",{$id}" : ''),
            'Observacion' => 'nullable|string|max:300',
        ], [
            'Detalle.required' => 'El detalle (nombre del servicio) es obligatorio.',
            'Detalle.unique'   => 'Ya existe un servicio con ese detalle.',
            'Detalle.max'      => 'El detalle no puede superar los 120 caracteres.',
            'Observacion.max'  => 'La observación no puede superar los 300 caracteres.',
        ]);
    }
}
