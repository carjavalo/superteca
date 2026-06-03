<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ControlCalidad;
use App\Models\ControlCalidadAccion;
use App\Models\ControlCalidadDetalle;
use App\Models\ControlCalidadEvidencia;
use App\Models\ControlCalidadParametro;
use App\Models\ControlCalidadResultado;
use App\Models\InventarioLote;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ControlCalidadController extends Controller
{
    /** Mapeo Resultado del control → estado_calidad de inventario_lotes */
    protected const MAP_RESULTADO_LOTE = [
        'APROBADO'    => 'LIBERADO',
        'CONDICIONAL' => 'CUARENTENA',
        'RECHAZADO'   => 'BLOQUEADO',
    ];

    /** Dashboard 360° */
    public function index()
    {
        $hoy   = today();
        $stats = [
            'controles_hoy' => ControlCalidad::whereDate('fecha_control', $hoy)->count(),
            'aprobados'     => ControlCalidad::whereDate('fecha_control', $hoy)->where('resultado','APROBADO')->count(),
            'condicionales' => ControlCalidad::whereDate('fecha_control', $hoy)->where('resultado','CONDICIONAL')->count(),
            'rechazados'    => ControlCalidad::whereDate('fecha_control', $hoy)->where('resultado','RECHAZADO')->count(),
            'pendientes'    => ControlCalidad::where('resultado','PENDIENTE')->count(),
            'cuarentena'    => InventarioLote::where('estado_calidad','CUARENTENA')->count(),
            'bloqueados'    => InventarioLote::where('estado_calidad','BLOQUEADO')->count(),
            'acciones_abiertas' => ControlCalidadAccion::whereIn('estado',['ABIERTA','EN_PROCESO'])->count(),
        ];

        // Conteos por tipo (semáforo)
        $semaforo = [];
        foreach (ControlCalidad::TIPOS as $k => $label) {
            $totales = ControlCalidad::where('tipo_control', $k)
                ->selectRaw("SUM(CASE WHEN resultado='APROBADO' THEN 1 ELSE 0 END) as aprobados")
                ->selectRaw("SUM(CASE WHEN resultado='CONDICIONAL' THEN 1 ELSE 0 END) as condicionales")
                ->selectRaw("SUM(CASE WHEN resultado='RECHAZADO' THEN 1 ELSE 0 END) as rechazados")
                ->selectRaw("SUM(CASE WHEN resultado='PENDIENTE' THEN 1 ELSE 0 END) as pendientes")
                ->first();

            $rech = (int)($totales->rechazados ?? 0);
            $cond = (int)($totales->condicionales ?? 0);
            $pend = (int)($totales->pendientes ?? 0);
            $aprob = (int)($totales->aprobados ?? 0);

            $estado = 'GRIS';
            if ($rech > 0)        $estado = 'ROJO';
            elseif ($cond > 0 || $pend > 0) $estado = 'AMARILLO';
            elseif ($aprob > 0)   $estado = 'VERDE';

            $semaforo[$k] = [
                'label' => $label,
                'aprobados' => $aprob,
                'condicionales' => $cond,
                'rechazados' => $rech,
                'pendientes' => $pend,
                'estado' => $estado,
            ];
        }

        $recientes = ControlCalidad::with('usuario','detalle.inventarioLote.medicamento')
            ->orderByDesc('fecha_control')->limit(10)->get();

        return view('admin.calidad.controles.index', compact('stats','semaforo','recientes'));
    }

    /** Bandeja completa */
    public function bandeja(Request $r)
    {
        $q = ControlCalidad::with('usuario','detalle');
        if ($r->filled('tipo'))      $q->where('tipo_control', $r->tipo);
        if ($r->filled('resultado')) $q->where('resultado', $r->resultado);
        if ($r->filled('desde'))     $q->whereDate('fecha_control','>=',$r->desde);
        if ($r->filled('hasta'))     $q->whereDate('fecha_control','<=',$r->hasta);
        if ($r->filled('q'))         $q->where('codigo','like','%'.$r->q.'%');

        $controles = $q->orderByDesc('fecha_control')->paginate(25)->withQueryString();
        return view('admin.calidad.controles.bandeja', compact('controles'));
    }

    public function create(Request $r)
    {
        $tipoSel    = $r->get('tipo','RECEPCION');
        $parametros = ControlCalidadParametro::where('estado', true)
            ->where('tipo_control', $tipoSel)->orderBy('nombre')->get();
        $lotes = InventarioLote::with('medicamento')
            ->orderByDesc('id')->limit(300)->get();
        return view('admin.calidad.controles.create', compact('tipoSel','parametros','lotes'));
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'tipo_control'       => 'required|in:'.implode(',', array_keys(ControlCalidad::TIPOS)),
            'fecha_control'      => 'required|date',
            'resultado'          => 'required|in:PENDIENTE,APROBADO,CONDICIONAL,RECHAZADO',
            'observaciones'      => 'nullable|string',
            'inventario_lote_id' => 'nullable|exists:inventario_lotes,id',
            'parametros'         => 'nullable|array',
            'parametros.*.parametro_id'   => 'required_with:parametros|exists:controles_calidad_parametros,id',
            'parametros.*.valor_obtenido' => 'nullable|string|max:255',
            'parametros.*.cumple'         => 'nullable|boolean',
            'parametros.*.observaciones'  => 'nullable|string',
        ]);

        $control = DB::transaction(function () use ($data) {
            $codigo = 'CC-' . now()->format('Y') . '-' . str_pad((string)(ControlCalidad::whereYear('created_at', now()->year)->count() + 1), 5, '0', STR_PAD_LEFT);

            $control = ControlCalidad::create([
                'codigo'             => $codigo,
                'tipo_control'       => $data['tipo_control'],
                'fecha_control'      => $data['fecha_control'],
                'usuario_control_id' => Auth::id(),
                'resultado'          => $data['resultado'],
                'observaciones'      => $data['observaciones'] ?? null,
            ]);

            if (!empty($data['inventario_lote_id'])) {
                ControlCalidadDetalle::create([
                    'control_id'         => $control->id,
                    'inventario_lote_id' => $data['inventario_lote_id'],
                ]);

                // Si hay resultado definitivo, refleja sobre el lote
                if (isset(self::MAP_RESULTADO_LOTE[$data['resultado']])) {
                    InventarioLote::where('id', $data['inventario_lote_id'])
                        ->update([
                            'estado_calidad' => self::MAP_RESULTADO_LOTE[$data['resultado']],
                        ]);
                }
            }

            foreach (($data['parametros'] ?? []) as $p) {
                ControlCalidadResultado::create([
                    'control_id'     => $control->id,
                    'parametro_id'   => $p['parametro_id'],
                    'valor_obtenido' => $p['valor_obtenido'] ?? null,
                    'cumple'         => (bool)($p['cumple'] ?? true),
                    'observaciones'  => $p['observaciones'] ?? null,
                ]);
            }

            return $control;
        });

        return redirect()->route('admin.calidad.controles.show', $control)
            ->with('success', 'Control de calidad ' . $control->codigo . ' registrado.');
    }

    public function show(ControlCalidad $control)
    {
        $control->load([
            'usuario',
            'detalle.inventarioLote.medicamento',
            'resultados.parametro',
            'acciones.responsable',
            'evidencias',
        ]);
        return view('admin.calidad.controles.show', compact('control'));
    }

    public function destroy(ControlCalidad $control)
    {
        $control->delete();
        return redirect()->route('admin.calidad.controles.bandeja')
            ->with('success','Control eliminado.');
    }

    /** Cambio rápido de resultado desde el detalle */
    public function actualizarResultado(Request $r, ControlCalidad $control)
    {
        $data = $r->validate([
            'resultado'     => 'required|in:PENDIENTE,APROBADO,CONDICIONAL,RECHAZADO',
            'observaciones' => 'nullable|string',
        ]);

        DB::transaction(function () use ($control, $data) {
            $control->update([
                'resultado'     => $data['resultado'],
                'observaciones' => $data['observaciones'] ?? $control->observaciones,
            ]);
            // Propaga a los lotes vinculados
            if (isset(self::MAP_RESULTADO_LOTE[$data['resultado']])) {
                $loteIds = $control->detalle()->whereNotNull('inventario_lote_id')->pluck('inventario_lote_id');
                if ($loteIds->count()) {
                    InventarioLote::whereIn('id', $loteIds)
                        ->update(['estado_calidad' => self::MAP_RESULTADO_LOTE[$data['resultado']]]);
                }
            }
        });

        return back()->with('success','Resultado actualizado.');
    }

    /** ============================ ACCIONES CORRECTIVAS ============================ */
    public function accionStore(Request $r, ControlCalidad $control)
    {
        $data = $r->validate([
            'descripcion'      => 'required|string',
            'responsable_id'   => 'nullable|exists:users,id',
            'fecha_compromiso' => 'nullable|date',
        ]);
        ControlCalidadAccion::create(array_merge($data, [
            'control_id' => $control->id,
            'estado'     => 'ABIERTA',
        ]));
        return back()->with('success','Acción correctiva registrada.');
    }

    public function accionUpdate(Request $r, ControlCalidadAccion $accion)
    {
        $data = $r->validate([
            'estado'       => 'required|in:ABIERTA,EN_PROCESO,CERRADA',
            'fecha_cierre' => 'nullable|date',
        ]);
        if ($data['estado'] === 'CERRADA' && empty($data['fecha_cierre'])) {
            $data['fecha_cierre'] = now()->toDateString();
        }
        $accion->update($data);
        return back()->with('success','Acción actualizada.');
    }

    /** ============================ EVIDENCIAS ============================ */
    public function evidenciaStore(Request $r, ControlCalidad $control)
    {
        $r->validate([
            'archivo'       => 'required|file|max:10240',
            'observaciones' => 'nullable|string',
        ]);

        $file = $r->file('archivo');
        $path = $file->store('controles_calidad', 'public');

        ControlCalidadEvidencia::create([
            'control_id'     => $control->id,
            'nombre_archivo' => $file->getClientOriginalName(),
            'ruta_archivo'   => $path,
            'tipo_archivo'   => $file->getClientMimeType(),
            'observaciones'  => $r->observaciones,
        ]);

        return back()->with('success','Evidencia adjuntada.');
    }

    public function evidenciaDestroy(ControlCalidadEvidencia $evidencia)
    {
        if ($evidencia->ruta_archivo && Storage::disk('public')->exists($evidencia->ruta_archivo)) {
            Storage::disk('public')->delete($evidencia->ruta_archivo);
        }
        $evidencia->delete();
        return back()->with('success','Evidencia eliminada.');
    }

    /** ============================ PARÁMETROS (Catálogo) ============================ */
    public function parametrosIndex()
    {
        $parametros = ControlCalidadParametro::orderBy('tipo_control')->orderBy('nombre')->paginate(50);
        return view('admin.calidad.controles.parametros.index', compact('parametros'));
    }

    public function parametrosStore(Request $r)
    {
        $data = $r->validate([
            'nombre'        => 'required|string|max:150',
            'tipo_control'  => 'required|in:'.implode(',', array_keys(ControlCalidad::TIPOS)),
            'unidad_medida' => 'nullable|string|max:50',
            'valor_minimo'  => 'nullable|numeric',
            'valor_maximo'  => 'nullable|numeric',
            'obligatorio'   => 'nullable|boolean',
            'estado'        => 'nullable|boolean',
        ]);
        $data['obligatorio'] = $r->boolean('obligatorio', true);
        $data['estado']      = $r->boolean('estado', true);
        ControlCalidadParametro::create($data);
        return back()->with('success','Parámetro creado.');
    }

    public function parametrosUpdate(Request $r, ControlCalidadParametro $parametro)
    {
        $data = $r->validate([
            'nombre'        => 'required|string|max:150',
            'tipo_control'  => 'required|in:'.implode(',', array_keys(ControlCalidad::TIPOS)),
            'unidad_medida' => 'nullable|string|max:50',
            'valor_minimo'  => 'nullable|numeric',
            'valor_maximo'  => 'nullable|numeric',
            'obligatorio'   => 'nullable|boolean',
            'estado'        => 'nullable|boolean',
        ]);
        $data['obligatorio'] = $r->boolean('obligatorio', true);
        $data['estado']      = $r->boolean('estado', true);
        $parametro->update($data);
        return back()->with('success','Parámetro actualizado.');
    }

    public function parametrosDestroy(ControlCalidadParametro $parametro)
    {
        $parametro->delete();
        return back()->with('success','Parámetro eliminado.');
    }

    /** Trazabilidad por lote */
    public function trazabilidad(InventarioLote $lote)
    {
        $lote->load('medicamento');
        $controles = ControlCalidad::whereHas('detalle', function ($q) use ($lote) {
            $q->where('inventario_lote_id', $lote->id);
        })->with('usuario','resultados.parametro')->orderBy('fecha_control')->get();

        return view('admin.calidad.controles.trazabilidad', compact('lote','controles'));
    }
}
