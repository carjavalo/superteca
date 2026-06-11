<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\PacienteAlergia;
use App\Models\PacienteDiagnostico;
use App\Models\PacientePrescripcion;
use App\Models\PacientePrescripcionDetalle;
use App\Models\PacienteTratamiento;
use App\Models\TipoServicios;
use App\Models\Eps;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Models\ViaAdministracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PacienteClinicoController extends Controller
{
    public function index(Request $request)
    {
        $q = Paciente::with('servicio');

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($w) use ($s) {
                $w->where('documento', 'like', "%$s%")
                  ->orWhere('nombres', 'like', "%$s%")
                  ->orWhere('apellidos', 'like', "%$s%")
                  ->orWhere('cama', 'like', "%$s%");
            });
        }
        if ($request->filled('estado_clinico')) $q->where('estado_clinico', $request->estado_clinico);
        if ($request->filled('servicio_id'))    $q->where('servicio_id', $request->servicio_id);

        $pacientes = $q->orderBy('apellidos')->orderBy('nombres')->paginate(30)->withQueryString();
        $servicios = TipoServicios::orderBy('Detalle')->get();

        $stats = [
            'activos'         => Paciente::where('estado_clinico','ACTIVO')->count(),
            'egresados_mes'   => Paciente::where('estado_clinico','EGRESADO')->whereMonth('fecha_egreso', now()->month)->count(),
            'prescripciones'  => PacientePrescripcion::where('estado','ACTIVA')->count(),
            'tratamientos'    => PacienteTratamiento::where('estado','ACTIVO')->count(),
        ];

        return view('admin.dispensacion.pacientes.index', compact('pacientes','servicios','stats'));
    }

    public function show(Paciente $paciente, Request $request)
    {
        $tab = $request->get('tab', 'general');

        $paciente->load([
            'servicio',
            'alergias.medicamento',
            'diagnosticos',
            'prescripciones.medico','prescripciones.detalles.medicamento','prescripciones.detalles.unidadMedida','prescripciones.detalles.viaAdministracion',
            'tratamientos.preparacion','tratamientos.mezcla',
            'dispensaciones.medicamento','dispensaciones.entrega','dispensaciones.inventarioLote',
            'preparaciones.tipo',
            'entregas.detalles',
        ]);

        $totalDispensado = $paciente->dispensaciones->sum('costo_total');
        $totalEntregas   = $paciente->entregas->count();
        $totalPrep       = $paciente->preparaciones->count();
        $alertas = [];
        foreach ($paciente->alergias as $al) {
            if ($al->severidad === 'SEVERA') $alertas[] = "ALERGIA SEVERA: {$al->descripcion}";
        }

        $medicamentos    = Medicamento::orderBy('nombre')->get();
        $unidades        = UnidadMedida::orderBy('nombre')->get();
        $vias            = ViaAdministracion::orderBy('nombre')->get();
        $medicos         = User::orderBy('name')->get();

        return view('admin.dispensacion.pacientes.show', compact(
            'paciente','tab','totalDispensado','totalEntregas','totalPrep','alertas',
            'medicamentos','unidades','vias','medicos'
        ));
    }

    public function create()
    {
        $servicios = TipoServicios::orderBy('Detalle')->get();
        $eps       = Eps::activas()->orderBy('Detalle')->get();
        return view('admin.dispensacion.pacientes.create', compact('servicios', 'eps'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'documento'       => 'required|string|max:30|unique:pacientes,documento',
            'tipo_documento'  => 'required|string|max:10',
            'nombres'         => 'required|string|max:120',
            'apellidos'       => 'required|string|max:120',
            'fecha_nacimiento'=> 'nullable|date',
            'sexo'            => 'nullable|in:M,F,O',
            'peso'            => 'nullable|numeric',
            'talla'           => 'nullable|numeric',
            'telefono'        => 'nullable|string|max:50',
            'correo'          => 'nullable|email|max:150',
            'direccion'       => 'nullable|string|max:255',
            'eps'             => 'nullable|string|max:120',
            'cama'            => 'nullable|string|max:30',
            'servicio_id'     => 'nullable|exists:TipoServicios,id',
            'fecha_ingreso'   => 'nullable|date',
            'estado_clinico'  => 'required|in:ACTIVO,EGRESADO,FALLECIDO',
            'observaciones'   => 'nullable|string',
        ]);
        $data['estado'] = true;
        $paciente = Paciente::create($data);
        return redirect()->route('admin.dispensacion.pacientes.show', $paciente)->with('success','Paciente registrado.');
    }

    public function update(Request $request, Paciente $paciente)
    {
        $data = $request->validate([
            'documento'       => 'required|string|max:30|unique:pacientes,documento,'.$paciente->id,
            'tipo_documento'  => 'required|string|max:10',
            'nombres'         => 'required|string|max:120',
            'apellidos'       => 'required|string|max:120',
            'fecha_nacimiento'=> 'nullable|date',
            'sexo'            => 'nullable|in:M,F,O',
            'peso'            => 'nullable|numeric',
            'talla'           => 'nullable|numeric',
            'telefono'        => 'nullable|string|max:50',
            'correo'          => 'nullable|email|max:150',
            'direccion'       => 'nullable|string|max:255',
            'eps'             => 'nullable|string|max:120',
            'cama'            => 'nullable|string|max:30',
            'servicio_id'     => 'nullable|exists:TipoServicios,id',
            'fecha_ingreso'   => 'nullable|date',
            'fecha_egreso'    => 'nullable|date',
            'estado_clinico'  => 'required|in:ACTIVO,EGRESADO,FALLECIDO',
            'observaciones'   => 'nullable|string',
        ]);
        $paciente->update($data);
        return back()->with('success','Datos actualizados.');
    }

    public function destroy(Paciente $paciente)
    {
        $paciente->delete();
        return redirect()->route('admin.dispensacion.pacientes.index')->with('success','Paciente eliminado.');
    }

    // ---------------- Alergias ----------------
    public function storeAlergia(Request $request, Paciente $paciente)
    {
        $data = $request->validate([
            'medicamento_id' => 'nullable|exists:medicamentos,id',
            'descripcion'    => 'required|string|max:255',
            'severidad'      => 'required|in:LEVE,MODERADA,SEVERA',
            'observaciones'  => 'nullable|string',
        ]);
        $paciente->alergias()->create($data);
        return back()->with('success','Alergia registrada.');
    }

    public function destroyAlergia(Paciente $paciente, PacienteAlergia $alergia)
    {
        $alergia->delete();
        return back()->with('success','Alergia eliminada.');
    }

    // ---------------- Diagnósticos ----------------
    public function storeDiagnostico(Request $request, Paciente $paciente)
    {
        $data = $request->validate([
            'codigo_cie10'      => 'nullable|string|max:20',
            'descripcion'       => 'required|string|max:255',
            'principal'         => 'nullable|boolean',
            'fecha_diagnostico' => 'nullable|date',
        ]);
        $data['principal'] = (bool) ($data['principal'] ?? false);
        $paciente->diagnosticos()->create($data);
        return back()->with('success','Diagnóstico registrado.');
    }

    public function destroyDiagnostico(Paciente $paciente, PacienteDiagnostico $diagnostico)
    {
        $diagnostico->delete();
        return back()->with('success','Diagnóstico eliminado.');
    }

    // ---------------- Prescripciones ----------------
    public function storePrescripcion(Request $request, Paciente $paciente)
    {
        $data = $request->validate([
            'medico_id'           => 'nullable|exists:users,id',
            'fecha_prescripcion'  => 'required|date',
            'observaciones'       => 'nullable|string',
            'detalles'                       => 'required|array|min:1',
            'detalles.*.medicamento_id'      => 'required|exists:medicamentos,id',
            'detalles.*.dosis'               => 'required|numeric',
            'detalles.*.unidad_medida_id'    => 'nullable|exists:unidades_medida,id',
            'detalles.*.frecuencia'          => 'nullable|string',
            'detalles.*.duracion_dias'       => 'nullable|integer',
            'detalles.*.via_administracion_id'=> 'nullable|exists:vias_administracion,id',
            'detalles.*.observaciones'       => 'nullable|string',
        ]);

        DB::transaction(function () use ($paciente, $data) {
            $rx = $paciente->prescripciones()->create([
                'medico_id'          => $data['medico_id'] ?? Auth::id(),
                'fecha_prescripcion' => $data['fecha_prescripcion'],
                'estado'             => 'ACTIVA',
                'observaciones'      => $data['observaciones'] ?? null,
            ]);
            foreach ($data['detalles'] as $det) {
                PacientePrescripcionDetalle::create(array_merge($det, ['prescripcion_id' => $rx->id]));
            }
        });

        return back()->with('success','Prescripción creada.');
    }

    public function cambiarEstadoPrescripcion(Request $request, Paciente $paciente, PacientePrescripcion $prescripcion)
    {
        $request->validate(['estado' => 'required|in:ACTIVA,SUSPENDIDA,FINALIZADA']);
        $prescripcion->update(['estado' => $request->estado]);
        return back()->with('success','Prescripción actualizada.');
    }

    public function destroyPrescripcion(Paciente $paciente, PacientePrescripcion $prescripcion)
    {
        $prescripcion->delete();
        return back()->with('success','Prescripción eliminada.');
    }
}
