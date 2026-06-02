<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogoCompatibilidad;
use App\Models\CatalogoInteraccion;
use App\Models\InventarioLote;
use App\Models\Medicamento;
use App\Models\Paciente;
use App\Models\PacientePrescripcion;
use App\Models\UnidadMedida;
use App\Models\User;
use App\Models\Validacion;
use App\Models\ValidacionAlerta;
use App\Models\ValidacionAprobacion;
use App\Models\ValidacionDetalle;
use App\Models\ViaAdministracion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ValidacionController extends Controller
{
    public function index(Request $request)
    {
        $q = Validacion::with(['paciente.servicio','farmaceutico','alertas','prescripcion']);

        if ($request->filled('search')) {
            $s = $request->search;
            $q->where(function ($w) use ($s) {
                $w->where('codigo','like',"%$s%")
                  ->orWhereHas('paciente', fn($p)=>$p->where('documento','like',"%$s%")
                      ->orWhere('nombres','like',"%$s%")->orWhere('apellidos','like',"%$s%"));
            });
        }
        if ($request->filled('resultado'))       $q->where('resultado', $request->resultado);
        if ($request->filled('tipo_validacion')) $q->where('tipo_validacion', $request->tipo_validacion);
        if ($request->filled('prioridad'))       $q->where('prioridad', $request->prioridad);

        $validaciones = $q->orderByRaw("FIELD(prioridad,'CRITICA','ALTA','NORMAL','BAJA')")
                          ->orderByDesc('fecha_validacion')
                          ->paginate(25)->withQueryString();

        $stats = [
            'pendientes' => Validacion::where('resultado','PENDIENTE')->count(),
            'aprobadas_hoy' => Validacion::where('resultado','APROBADA')->whereDate('updated_at', today())->count(),
            'observadas' => Validacion::where('resultado','OBSERVADA')->count(),
            'criticas'   => Validacion::where('prioridad','CRITICA')->whereIn('resultado',['PENDIENTE','OBSERVADA'])->count(),
        ];

        return view('admin.dispensacion.validaciones.index', compact('validaciones','stats'));
    }

    public function create(Request $request)
    {
        $pacientes      = Paciente::orderBy('apellidos')->orderBy('nombres')->get();
        $prescripciones = PacientePrescripcion::with('paciente','detalles.medicamento')
                                ->where('estado','ACTIVA')->orderByDesc('fecha_prescripcion')->get();
        $medicamentos   = Medicamento::orderBy('nombre')->get();
        $unidades       = UnidadMedida::orderBy('nombre')->get();
        $vias           = ViaAdministracion::orderBy('nombre')->get();
        $preselect      = $request->get('prescripcion_id');

        return view('admin.dispensacion.validaciones.create', compact(
            'pacientes','prescripciones','medicamentos','unidades','vias','preselect'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'paciente_id'      => 'nullable|exists:pacientes,id',
            'prescripcion_id'  => 'nullable|exists:pacientes_prescripciones,id',
            'preparacion_id'   => 'nullable|exists:preparaciones,id',
            'mezcla_id'        => 'nullable|exists:mezclas,id',
            'tipo_validacion'  => 'required|in:PRESCRIPCION,PREPARACION,MEZCLA,DISPENSACION',
            'prioridad'        => 'required|in:BAJA,NORMAL,ALTA,CRITICA',
            'observaciones'    => 'nullable|string',
            'detalles'                       => 'required|array|min:1',
            'detalles.*.medicamento_id'      => 'required|exists:medicamentos,id',
            'detalles.*.dosis_prescrita'     => 'nullable|numeric',
            'detalles.*.dosis_recomendada'   => 'nullable|numeric',
            'detalles.*.unidad_medida_id'    => 'nullable|exists:unidades_medida,id',
            'detalles.*.via_administracion_id'=> 'nullable|exists:vias_administracion,id',
            'detalles.*.observaciones'       => 'nullable|string',
        ]);

        $validacion = DB::transaction(function () use ($data) {
            $val = Validacion::create([
                'paciente_id'      => $data['paciente_id'] ?? null,
                'prescripcion_id'  => $data['prescripcion_id'] ?? null,
                'preparacion_id'   => $data['preparacion_id'] ?? null,
                'mezcla_id'        => $data['mezcla_id'] ?? null,
                'fecha_validacion' => now(),
                'tipo_validacion'  => $data['tipo_validacion'],
                'resultado'        => 'PENDIENTE',
                'prioridad'        => $data['prioridad'],
                'observaciones'    => $data['observaciones'] ?? null,
                'farmaceutico_id'  => Auth::id(),
            ]);

            foreach ($data['detalles'] as $det) {
                ValidacionDetalle::create(array_merge($det, [
                    'validacion_id' => $val->id,
                    'estado'        => 'VALIDO',
                ]));
            }

            $this->generarAlertasAutomaticas($val);
            return $val;
        });

        return redirect()->route('admin.dispensacion.validaciones.show', $validacion)
                         ->with('success','Validación creada. Revise las alertas farmacéuticas.');
    }

    public function show(Validacion $validacion)
    {
        $validacion->load([
            'paciente.alergias.medicamento','paciente.diagnosticos','paciente.servicio',
            'prescripcion.detalles.medicamento','prescripcion.medico',
            'preparacion','mezcla','farmaceutico',
            'detalles.medicamento','detalles.unidadMedida','detalles.viaAdministracion',
            'alertas','aprobaciones.usuario',
        ]);

        $medicamentosIds = $validacion->detalles->pluck('medicamento_id')->filter()->unique();
        $stockInfo = InventarioLote::whereIn('medicamento_id', $medicamentosIds)
            ->where('estado', 1)
            ->where('cantidad_actual','>',0)
            ->get()
            ->groupBy('medicamento_id');

        return view('admin.dispensacion.validaciones.show', compact('validacion','stockInfo'));
    }

    public function aprobar(Request $request, Validacion $validacion)
    {
        $request->validate([
            'accion'        => 'required|in:APROBADA,RECHAZADA,DEVUELTA,OBSERVADA',
            'observaciones' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $validacion) {
            ValidacionAprobacion::create([
                'validacion_id'    => $validacion->id,
                'usuario_id'       => Auth::id(),
                'fecha_aprobacion' => now(),
                'accion'           => $request->accion,
                'observaciones'    => $request->observaciones,
            ]);

            $map = ['APROBADA'=>'APROBADA','RECHAZADA'=>'RECHAZADA','DEVUELTA'=>'PENDIENTE','OBSERVADA'=>'OBSERVADA'];
            $validacion->update(['resultado' => $map[$request->accion]]);
        });

        return back()->with('success','Acción registrada: '.$request->accion);
    }

    public function resolverAlerta(Validacion $validacion, ValidacionAlerta $alerta)
    {
        $alerta->update(['resuelta' => !$alerta->resuelta]);
        return back()->with('success','Alerta actualizada.');
    }

    public function destroy(Validacion $validacion)
    {
        $validacion->delete();
        return redirect()->route('admin.dispensacion.validaciones.index')->with('success','Validación eliminada.');
    }

    /**
     * Análisis farmacéutico automático: alergias, interacciones,
     * compatibilidades, dosis, stock, vencimientos, duplicidades.
     */
    protected function generarAlertasAutomaticas(Validacion $val): void
    {
        $val->load('detalles.medicamento','paciente.alergias');
        $medsIds = $val->detalles->pluck('medicamento_id')->filter()->all();

        // 1. Alergias
        if ($val->paciente) {
            foreach ($val->paciente->alergias as $alergia) {
                if ($alergia->medicamento_id && in_array($alergia->medicamento_id, $medsIds)) {
                    $sev = ['LEVE'=>'MEDIA','MODERADA'=>'ALTA','SEVERA'=>'CRITICA'][$alergia->severidad] ?? 'ALTA';
                    ValidacionAlerta::create([
                        'validacion_id' => $val->id,
                        'tipo_alerta'   => 'ALERGIA',
                        'severidad'     => $sev,
                        'descripcion'   => "Paciente presenta alergia ({$alergia->severidad}) al medicamento: ".($alergia->medicamento->nombre ?? '').'. '.($alergia->descripcion ?? ''),
                    ]);
                }
            }
        }

        // 2. Interacciones medicamentosas
        $interacciones = CatalogoInteraccion::where(function ($q) use ($medsIds) {
            $q->whereIn('medicamento_1_id', $medsIds)->whereIn('medicamento_2_id', $medsIds);
        })->with('medicamento1','medicamento2')->get();
        foreach ($interacciones as $i) {
            $sev = ['LEVE'=>'BAJA','MODERADA'=>'MEDIA','GRAVE'=>'CRITICA'][$i->severidad] ?? 'MEDIA';
            ValidacionAlerta::create([
                'validacion_id' => $val->id,
                'tipo_alerta'   => 'INTERACCION',
                'severidad'     => $sev,
                'descripcion'   => "Interacción {$i->severidad} entre ".($i->medicamento1->nombre ?? '').' y '.($i->medicamento2->nombre ?? '').'. '.($i->descripcion ?? ''),
            ]);
        }

        // 3. Compatibilidades (solo incompatibles)
        $incomp = CatalogoCompatibilidad::where('compatible', false)
            ->whereIn('medicamento_1_id', $medsIds)
            ->whereIn('medicamento_2_id', $medsIds)
            ->with('medicamento1','medicamento2')->get();
        foreach ($incomp as $c) {
            ValidacionAlerta::create([
                'validacion_id' => $val->id,
                'tipo_alerta'   => 'COMPATIBILIDAD',
                'severidad'     => 'ALTA',
                'descripcion'   => "Incompatibilidad entre ".($c->medicamento1->nombre ?? '').' y '.($c->medicamento2->nombre ?? '').'. '.($c->observaciones ?? ''),
            ]);
        }

        // 4. Duplicidad terapéutica
        $dupes = collect($medsIds)->countBy()->filter(fn($n) => $n > 1);
        foreach ($dupes as $medId => $n) {
            $med = Medicamento::find($medId);
            ValidacionAlerta::create([
                'validacion_id' => $val->id,
                'tipo_alerta'   => 'DUPLICIDAD',
                'severidad'     => 'MEDIA',
                'descripcion'   => "Duplicidad: el medicamento ".($med->nombre ?? '')." aparece $n veces en la orden.",
            ]);
        }

        // 5. Stock + 6. Vencimiento
        foreach ($val->detalles as $det) {
            if (!$det->medicamento_id) continue;
            $stockTotal = InventarioLote::where('medicamento_id', $det->medicamento_id)
                ->where('estado', 1)->sum('cantidad_actual');
            $req = (float) ($det->dosis_prescrita ?? 0);
            if ($stockTotal <= 0) {
                ValidacionAlerta::create([
                    'validacion_id' => $val->id,
                    'tipo_alerta'   => 'STOCK',
                    'severidad'     => 'CRITICA',
                    'descripcion'   => "Sin stock disponible de ".($det->medicamento->nombre ?? ''),
                ]);
            } elseif ($req > 0 && $stockTotal < $req) {
                ValidacionAlerta::create([
                    'validacion_id' => $val->id,
                    'tipo_alerta'   => 'STOCK',
                    'severidad'     => 'ALTA',
                    'descripcion'   => "Stock insuficiente de ".($det->medicamento->nombre ?? '').": disponible $stockTotal, requerido $req",
                ]);
            }

            $proxVencer = InventarioLote::where('medicamento_id', $det->medicamento_id)
                ->where('estado', 1)->where('cantidad_actual','>',0)
                ->whereDate('fecha_vencimiento','<=', now()->addDays(30))
                ->orderBy('fecha_vencimiento')->first();
            if ($proxVencer) {
                ValidacionAlerta::create([
                    'validacion_id' => $val->id,
                    'tipo_alerta'   => 'VENCIMIENTO',
                    'severidad'     => 'MEDIA',
                    'descripcion'   => "Lote {$proxVencer->lote} de ".($det->medicamento->nombre ?? '')." vence el ".$proxVencer->fecha_vencimiento->format('d/m/Y'),
                ]);
            }

            // 7. Dosis
            if ($det->dosis_prescrita && $det->dosis_recomendada && $det->dosis_recomendada > 0) {
                $diff = abs($det->dosis_prescrita - $det->dosis_recomendada) / $det->dosis_recomendada;
                if ($diff > 0.20) {
                    $sev = $diff > 0.50 ? 'CRITICA' : 'ALTA';
                    ValidacionAlerta::create([
                        'validacion_id' => $val->id,
                        'tipo_alerta'   => 'DOSIS',
                        'severidad'     => $sev,
                        'descripcion'   => "Dosis fuera del rango recomendado para ".($det->medicamento->nombre ?? '').": prescrita {$det->dosis_prescrita}, recomendada {$det->dosis_recomendada}",
                    ]);
                }
            }
        }
    }
}
