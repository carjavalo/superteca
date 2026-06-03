<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incidente;
use App\Models\IncidenteAccion;
use App\Models\IncidenteAfectacion;
use App\Models\IncidenteDetalle;
use App\Models\IncidenteEvidencia;
use App\Models\IncidenteSeguimiento;
use App\Models\InventarioLote;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class IncidenteController extends Controller
{
    /** Dashboard 360° de incidentes */
    public function index()
    {
        $hoy = today();
        $mes = Carbon::now()->startOfMonth();

        $stats = [
            'abiertos'        => Incidente::whereIn('estado', ['ABIERTO','INVESTIGACION','ACCION_CORRECTIVA'])->count(),
            'criticos'        => Incidente::where('severidad','CRITICA')
                                    ->whereIn('estado',['ABIERTO','INVESTIGACION','ACCION_CORRECTIVA'])->count(),
            'investigacion'   => Incidente::where('estado','INVESTIGACION')->count(),
            'accion'          => Incidente::where('estado','ACCION_CORRECTIVA')->count(),
            'cerrados_mes'    => Incidente::where('estado','CERRADO')->where('updated_at','>=',$mes)->count(),
            'hoy'             => Incidente::whereDate('fecha_incidente', $hoy)->count(),
            'lotes_bloqueados'=> InventarioLote::where('bloqueado_incidente', true)->count(),
            'acciones_pend'   => IncidenteAccion::whereIn('estado',['PENDIENTE','EN_PROCESO'])->count(),
        ];

        // Kanban
        $kanban = [
            'ABIERTO'           => Incidente::with('usuarioReporta')->where('estado','ABIERTO')->orderByDesc('fecha_incidente')->limit(10)->get(),
            'INVESTIGACION'     => Incidente::with('usuarioReporta')->where('estado','INVESTIGACION')->orderByDesc('fecha_incidente')->limit(10)->get(),
            'ACCION_CORRECTIVA' => Incidente::with('usuarioReporta')->where('estado','ACCION_CORRECTIVA')->orderByDesc('fecha_incidente')->limit(10)->get(),
            'CERRADO'           => Incidente::with('usuarioReporta')->where('estado','CERRADO')->orderByDesc('updated_at')->limit(10)->get(),
        ];

        // Matriz de riesgo: severidad x clasificación
        $matriz = [];
        foreach (array_keys(Incidente::SEVERIDADES) as $sev) {
            foreach (array_keys(Incidente::CLASIFICACIONES) as $cls) {
                $matriz[$sev][$cls] = Incidente::where('severidad',$sev)->where('clasificacion',$cls)->count();
            }
        }

        // Conteos por tipo (semáforo)
        $semaforo = [];
        foreach (Incidente::TIPOS as $k => $label) {
            $abiertos = Incidente::where('tipo_incidente',$k)
                ->whereIn('estado',['ABIERTO','INVESTIGACION','ACCION_CORRECTIVA'])->count();
            $criticos = Incidente::where('tipo_incidente',$k)
                ->where('severidad','CRITICA')
                ->whereIn('estado',['ABIERTO','INVESTIGACION','ACCION_CORRECTIVA'])->count();
            $cerrados = Incidente::where('tipo_incidente',$k)->where('estado','CERRADO')->count();

            $estado = 'GRIS';
            if ($criticos > 0)        $estado = 'ROJO';
            elseif ($abiertos > 0)    $estado = 'AMARILLO';
            elseif ($cerrados > 0)    $estado = 'VERDE';

            $semaforo[$k] = compact('label','abiertos','criticos','cerrados','estado');
        }

        $recientes = Incidente::with('usuarioReporta')
            ->orderByDesc('fecha_incidente')->limit(10)->get();

        return view('admin.calidad.incidentes.index', compact('stats','kanban','matriz','semaforo','recientes'));
    }

    /** Bandeja completa con filtros */
    public function bandeja(Request $r)
    {
        $q = Incidente::with('usuarioReporta');
        if ($r->filled('tipo'))         $q->where('tipo_incidente', $r->tipo);
        if ($r->filled('estado'))       $q->where('estado', $r->estado);
        if ($r->filled('severidad'))    $q->where('severidad', $r->severidad);
        if ($r->filled('clasificacion'))$q->where('clasificacion', $r->clasificacion);
        if ($r->filled('desde'))        $q->whereDate('fecha_incidente','>=',$r->desde);
        if ($r->filled('hasta'))        $q->whereDate('fecha_incidente','<=',$r->hasta);
        if ($r->filled('q'))            $q->where(function($w) use ($r){
            $w->where('codigo','like','%'.$r->q.'%')
              ->orWhere('descripcion','like','%'.$r->q.'%');
        });

        $incidentes = $q->orderByDesc('fecha_incidente')->paginate(25)->withQueryString();
        return view('admin.calidad.incidentes.bandeja', compact('incidentes'));
    }

    /** Formulario de creación */
    public function create()
    {
        $lotes = InventarioLote::with('medicamento')->orderByDesc('id')->limit(300)->get();
        return view('admin.calidad.incidentes.create', compact('lotes'));
    }

    /** Almacena un nuevo incidente */
    public function store(Request $r)
    {
        $data = $r->validate([
            'fecha_incidente' => 'required|date',
            'tipo_incidente'  => 'required|in:'.implode(',', array_keys(Incidente::TIPOS)),
            'clasificacion'   => 'required|in:'.implode(',', array_keys(Incidente::CLASIFICACIONES)),
            'severidad'       => 'required|in:'.implode(',', array_keys(Incidente::SEVERIDADES)),
            'descripcion'     => 'required|string',
            'lotes'           => 'array',
            'lotes.*'         => 'nullable|integer|exists:inventario_lotes,id',
            'observaciones_afect' => 'nullable|string',
            'bloquear_lotes'  => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($data, $r) {
            $codigo = 'INC-'.now()->format('Ymd-His');

            $inc = Incidente::create([
                'codigo'             => $codigo,
                'fecha_incidente'    => $data['fecha_incidente'],
                'tipo_incidente'     => $data['tipo_incidente'],
                'clasificacion'      => $data['clasificacion'],
                'severidad'          => $data['severidad'],
                'descripcion'        => $data['descripcion'],
                'usuario_reporta_id' => Auth::id(),
                'fecha_reporte'      => now(),
                'estado'             => 'ABIERTO',
            ]);

            // Crear detalle vacío
            IncidenteDetalle::create(['incidente_id' => $inc->id]);

            // Afectaciones por lotes seleccionados
            $bloquear = $r->boolean('bloquear_lotes');
            foreach ((array)($data['lotes'] ?? []) as $loteId) {
                if (!$loteId) continue;
                IncidenteAfectacion::create([
                    'incidente_id'       => $inc->id,
                    'inventario_lote_id' => $loteId,
                    'observaciones'      => $data['observaciones_afect'] ?? null,
                ]);

                if ($bloquear) {
                    InventarioLote::where('id', $loteId)->update([
                        'bloqueado_incidente' => true,
                        'incidente_id'        => $inc->id,
                        'estado_calidad'      => 'BLOQUEADO',
                    ]);
                }
            }

            // Bitácora inicial
            IncidenteSeguimiento::create([
                'incidente_id' => $inc->id,
                'fecha'        => now(),
                'comentario'   => 'Incidente reportado: '.substr($data['descripcion'], 0, 200),
                'usuario_id'   => Auth::id(),
            ]);
        });

        return redirect()->route('admin.calidad.incidentes.index')
            ->with('success', 'Incidente registrado correctamente.');
    }

    /** Detalle / trazabilidad 360° */
    public function show(Incidente $incidente)
    {
        $incidente->load([
            'usuarioReporta','detalle.responsable','acciones.responsable',
            'evidencias','seguimientos.usuario',
            'afectaciones.inventarioLote.medicamento',
            'afectaciones.equipoCadenaFrio',
            'afectaciones.controlCalidad',
        ]);

        return view('admin.calidad.incidentes.show', compact('incidente'));
    }

    /** Cambia estado del incidente */
    public function actualizarEstado(Request $r, Incidente $incidente)
    {
        $data = $r->validate([
            'estado'     => 'required|in:ABIERTO,INVESTIGACION,ACCION_CORRECTIVA,CERRADO',
            'comentario' => 'nullable|string',
        ]);

        DB::transaction(function () use ($data, $incidente) {
            $incidente->update(['estado' => $data['estado']]);

            if ($data['estado'] === 'CERRADO') {
                $det = $incidente->detalle ?: IncidenteDetalle::create(['incidente_id' => $incidente->id]);
                $det->update(['fecha_cierre' => now()]);
            }

            IncidenteSeguimiento::create([
                'incidente_id' => $incidente->id,
                'fecha'        => now(),
                'comentario'   => 'Estado → '.$data['estado'].($data['comentario'] ? ' · '.$data['comentario'] : ''),
                'usuario_id'   => Auth::id(),
            ]);
        });

        return back()->with('success', 'Estado actualizado.');
    }

    /** Actualiza la investigación / detalle */
    public function detalleUpdate(Request $r, Incidente $incidente)
    {
        $data = $r->validate([
            'causa_raiz'                  => 'nullable|string',
            'impacto'                     => 'nullable|string',
            'conclusion'                  => 'nullable|string',
            'responsable_investigacion_id'=> 'nullable|exists:users,id',
        ]);

        $det = $incidente->detalle ?: IncidenteDetalle::create(['incidente_id' => $incidente->id]);
        $det->update($data);

        IncidenteSeguimiento::create([
            'incidente_id' => $incidente->id,
            'fecha'        => now(),
            'comentario'   => 'Investigación actualizada.',
            'usuario_id'   => Auth::id(),
        ]);

        return back()->with('success', 'Investigación actualizada.');
    }

    /** CAPA: registrar acción correctiva/preventiva */
    public function accionStore(Request $r, Incidente $incidente)
    {
        $data = $r->validate([
            'tipo_accion'      => 'required|in:CORRECTIVA,PREVENTIVA',
            'descripcion'      => 'required|string',
            'responsable_id'   => 'nullable|exists:users,id',
            'fecha_compromiso' => 'nullable|date',
        ]);
        $data['incidente_id'] = $incidente->id;
        $data['estado'] = 'PENDIENTE';
        IncidenteAccion::create($data);

        return back()->with('success', 'Acción registrada.');
    }

    /** Cambiar estado de una acción (kanban CAPA) */
    public function accionUpdate(Request $r, IncidenteAccion $accion)
    {
        $data = $r->validate([
            'estado'          => 'required|in:PENDIENTE,EN_PROCESO,CERRADA',
            'fecha_ejecucion' => 'nullable|date',
        ]);
        if ($data['estado'] === 'CERRADA' && empty($data['fecha_ejecucion'])) {
            $data['fecha_ejecucion'] = now()->toDateString();
        }
        $accion->update($data);
        return back()->with('success', 'Acción actualizada.');
    }

    /** Bitácora */
    public function seguimientoStore(Request $r, Incidente $incidente)
    {
        $data = $r->validate(['comentario' => 'required|string']);
        IncidenteSeguimiento::create([
            'incidente_id' => $incidente->id,
            'fecha'        => now(),
            'comentario'   => $data['comentario'],
            'usuario_id'   => Auth::id(),
        ]);
        return back()->with('success', 'Comentario agregado.');
    }

    /** Subir evidencia */
    public function evidenciaStore(Request $r, Incidente $incidente)
    {
        $r->validate([
            'archivo'       => 'required|file|max:10240',
            'observaciones' => 'nullable|string',
        ]);
        $f = $r->file('archivo');
        $ruta = $f->store('incidentes/'.$incidente->id, 'public');
        IncidenteEvidencia::create([
            'incidente_id'   => $incidente->id,
            'nombre_archivo' => $f->getClientOriginalName(),
            'ruta_archivo'   => $ruta,
            'tipo_archivo'   => $f->getClientMimeType(),
            'observaciones'  => $r->observaciones,
        ]);
        return back()->with('success', 'Evidencia subida.');
    }

    public function evidenciaDestroy(IncidenteEvidencia $evidencia)
    {
        if ($evidencia->ruta_archivo && Storage::disk('public')->exists($evidencia->ruta_archivo)) {
            Storage::disk('public')->delete($evidencia->ruta_archivo);
        }
        $evidencia->delete();
        return back()->with('success', 'Evidencia eliminada.');
    }

    /** Bloquear/desbloquear un lote afectado */
    public function bloquearLote(Request $r, Incidente $incidente)
    {
        $data = $r->validate([
            'inventario_lote_id' => 'required|exists:inventario_lotes,id',
            'bloquear'           => 'required|boolean',
        ]);

        $lote = InventarioLote::find($data['inventario_lote_id']);
        if ($data['bloquear']) {
            $lote->update([
                'bloqueado_incidente' => true,
                'incidente_id'        => $incidente->id,
                'estado_calidad'      => 'BLOQUEADO',
            ]);
            $msg = 'Lote bloqueado por incidente.';
        } else {
            $lote->update([
                'bloqueado_incidente' => false,
                'incidente_id'        => null,
                'estado_calidad'      => 'CUARENTENA',
            ]);
            $msg = 'Lote liberado a cuarentena.';
        }

        IncidenteSeguimiento::create([
            'incidente_id' => $incidente->id,
            'fecha'        => now(),
            'comentario'   => $msg.' Lote ID '.$lote->id,
            'usuario_id'   => Auth::id(),
        ]);

        return back()->with('success', $msg);
    }

    public function destroy(Incidente $incidente)
    {
        $incidente->delete();
        return redirect()->route('admin.calidad.incidentes.bandeja')->with('success','Incidente eliminado.');
    }
}
