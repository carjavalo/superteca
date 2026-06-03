<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AfectacionLote;
use App\Models\AlertaCadenaFrio;
use App\Models\EquipoCadenaFrio;
use App\Models\InventarioLote;
use App\Models\LoteCadenaFrio;
use App\Models\MonitoreoTemperatura;
use App\Models\SensorTemperatura;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CadenaFrioController extends Controller
{
    public function index()
    {
        $equipos = EquipoCadenaFrio::with('ultimoMonitoreo')->where('estado', true)->get();

        $stats = [
            'equipos_activos'    => $equipos->count(),
            'temp_promedio'      => round((float) $equipos->map(fn($e) => optional($e->ultimoMonitoreo)->temperatura)->filter()->avg(), 2),
            'alertas_hoy'        => AlertaCadenaFrio::whereDate('fecha_inicio', today())->count(),
            'lotes_cuarentena'   => InventarioLote::where('estado_calidad', 'CUARENTENA')->count(),
            'lotes_bloqueados'   => InventarioLote::where('estado_calidad', 'BLOQUEADO')->count(),
            'alertas_abiertas'   => AlertaCadenaFrio::where('estado', 'ABIERTA')->count(),
        ];

        $alertasRecientes = AlertaCadenaFrio::with('equipo')
            ->orderByDesc('fecha_inicio')->limit(8)->get();

        $monitoreoHoy = MonitoreoTemperatura::with('equipo')
            ->whereDate('fecha_hora', today())
            ->orderByDesc('fecha_hora')->limit(20)->get();

        return view('admin.calidad.cadena-frio.index', compact('equipos','stats','alertasRecientes','monitoreoHoy'));
    }

    // ====================== EQUIPOS ======================
    public function equiposIndex()
    {
        $equipos = EquipoCadenaFrio::with('ultimoMonitoreo')->orderBy('nombre')->paginate(30);
        return view('admin.calidad.cadena-frio.equipos.index', compact('equipos'));
    }

    public function equiposStore(Request $r)
    {
        $data = $r->validate([
            'codigo'              => 'required|string|max:50|unique:equipos_cadena_frio,codigo',
            'nombre'              => 'required|string|max:150',
            'tipo'                => 'required|in:NEVERA,CONGELADOR,CUARTO_FRIO,TRANSPORTE',
            'ubicacion'           => 'nullable|string|max:255',
            'temperatura_min'     => 'required|numeric',
            'temperatura_max'     => 'required|numeric|gt:temperatura_min',
            'humedad_min'         => 'nullable|numeric',
            'humedad_max'         => 'nullable|numeric',
            'fabricante'          => 'nullable|string|max:150',
            'modelo'              => 'nullable|string|max:150',
            'serial'              => 'nullable|string|max:150',
            'fecha_calibracion'   => 'nullable|date',
            'proxima_calibracion' => 'nullable|date',
            'estado'              => 'nullable|boolean',
        ]);
        $data['estado'] = $r->boolean('estado', true);
        EquipoCadenaFrio::create($data);
        return redirect()->route('admin.calidad.cadena-frio.equipos.index')->with('success', 'Equipo registrado.');
    }

    public function equiposUpdate(Request $r, EquipoCadenaFrio $equipo)
    {
        $data = $r->validate([
            'codigo'              => 'required|string|max:50|unique:equipos_cadena_frio,codigo,'.$equipo->id,
            'nombre'              => 'required|string|max:150',
            'tipo'                => 'required|in:NEVERA,CONGELADOR,CUARTO_FRIO,TRANSPORTE',
            'ubicacion'           => 'nullable|string|max:255',
            'temperatura_min'     => 'required|numeric',
            'temperatura_max'     => 'required|numeric|gt:temperatura_min',
            'humedad_min'         => 'nullable|numeric',
            'humedad_max'         => 'nullable|numeric',
            'fabricante'          => 'nullable|string|max:150',
            'modelo'              => 'nullable|string|max:150',
            'serial'              => 'nullable|string|max:150',
            'fecha_calibracion'   => 'nullable|date',
            'proxima_calibracion' => 'nullable|date',
            'estado'              => 'nullable|boolean',
        ]);
        $data['estado'] = $r->boolean('estado', true);
        $equipo->update($data);
        return back()->with('success', 'Equipo actualizado.');
    }

    public function equiposDestroy(EquipoCadenaFrio $equipo)
    {
        $equipo->delete();
        return back()->with('success', 'Equipo eliminado.');
    }

    public function equiposShow(EquipoCadenaFrio $equipo)
    {
        $equipo->load(['sensores','alertas' => fn($q)=>$q->orderByDesc('fecha_inicio')->limit(20)]);
        $monitoreos = MonitoreoTemperatura::where('equipo_id', $equipo->id)
            ->orderByDesc('fecha_hora')->limit(50)->get();
        $lotes = LoteCadenaFrio::with('inventarioLote.medicamento')
            ->where('equipo_id', $equipo->id)->whereNull('fecha_salida')->get();
        return view('admin.calidad.cadena-frio.equipos.show', compact('equipo','monitoreos','lotes'));
    }

    // ====================== MONITOREO ======================
    public function monitoreoIndex(Request $r)
    {
        $q = MonitoreoTemperatura::with(['equipo','usuario'])->orderByDesc('fecha_hora');
        if ($r->filled('equipo_id')) $q->where('equipo_id', $r->equipo_id);
        if ($r->filled('fuera_rango')) $q->where('fuera_rango', true);
        $registros = $q->paginate(40)->withQueryString();
        $equipos = EquipoCadenaFrio::where('estado', true)->orderBy('nombre')->get();
        return view('admin.calidad.cadena-frio.monitoreo.index', compact('registros','equipos'));
    }

    public function monitoreoStore(Request $r)
    {
        $data = $r->validate([
            'equipo_id'    => 'required|exists:equipos_cadena_frio,id',
            'sensor_id'    => 'nullable|exists:sensores_temperatura,id',
            'fecha_hora'   => 'required|date',
            'temperatura'  => 'required|numeric',
            'humedad'      => 'nullable|numeric',
            'origen'       => 'nullable|in:MANUAL,AUTOMATICO',
            'observaciones'=> 'nullable|string',
        ]);
        $data['usuario_id'] = Auth::id();
        $data['origen'] = $data['origen'] ?? 'MANUAL';

        $equipo = EquipoCadenaFrio::findOrFail($data['equipo_id']);
        $temp = (float) $data['temperatura'];
        $fuera = $temp < (float) $equipo->temperatura_min || $temp > (float) $equipo->temperatura_max;
        $data['fuera_rango'] = $fuera;

        DB::transaction(function () use ($data, $equipo, $fuera, $temp) {
            $reg = MonitoreoTemperatura::create($data);
            if ($fuera) {
                $this->generarAlerta($equipo, $temp, $reg->fecha_hora);
            }
        });

        return back()->with('success', $fuera ? 'Registro creado. ¡Temperatura FUERA DE RANGO! Alerta generada.' : 'Registro creado correctamente.');
    }

    protected function generarAlerta(EquipoCadenaFrio $equipo, float $temp, $fecha): AlertaCadenaFrio
    {
        $delta = max(abs($temp - (float)$equipo->temperatura_min), abs($temp - (float)$equipo->temperatura_max));
        $sev = 'BAJA';
        if ($delta >= 1)  $sev = 'MEDIA';
        if ($delta >= 3)  $sev = 'ALTA';
        if ($delta >= 6)  $sev = 'CRITICA';

        $alerta = AlertaCadenaFrio::create([
            'equipo_id'                  => $equipo->id,
            'fecha_inicio'               => $fecha,
            'temperatura_registrada'     => $temp,
            'temperatura_permitida_min'  => $equipo->temperatura_min,
            'temperatura_permitida_max'  => $equipo->temperatura_max,
            'severidad'                  => $sev,
            'estado'                     => 'ABIERTA',
            'usuario_id'                 => Auth::id(),
        ]);

        // Marcar lotes en cuarentena y registrar afectación
        $lotes = LoteCadenaFrio::where('equipo_id', $equipo->id)->whereNull('fecha_salida')->pluck('inventario_lote_id');
        foreach ($lotes as $invId) {
            AfectacionLote::create([
                'alerta_id'          => $alerta->id,
                'inventario_lote_id' => $invId,
                'estado'             => 'PENDIENTE_EVALUACION',
                'usuario_id'         => Auth::id(),
            ]);
            InventarioLote::where('id', $invId)->update(['estado_calidad' => 'CUARENTENA']);
        }

        return $alerta;
    }

    // ====================== ALERTAS ======================
    public function alertasIndex(Request $r)
    {
        $q = AlertaCadenaFrio::with('equipo','afectaciones.inventarioLote.medicamento')->orderByDesc('fecha_inicio');
        if ($r->filled('estado')) $q->where('estado', $r->estado);
        if ($r->filled('severidad')) $q->where('severidad', $r->severidad);
        $alertas = $q->paginate(25)->withQueryString();
        return view('admin.calidad.cadena-frio.alertas.index', compact('alertas'));
    }

    public function alertasShow(AlertaCadenaFrio $alerta)
    {
        $alerta->load('equipo','afectaciones.inventarioLote.medicamento','afectaciones.usuario','usuario');
        return view('admin.calidad.cadena-frio.alertas.show', compact('alerta'));
    }

    public function alertasCerrar(Request $r, AlertaCadenaFrio $alerta)
    {
        $r->validate(['observaciones' => 'nullable|string']);
        $alerta->update([
            'estado'        => 'CERRADA',
            'fecha_fin'     => now(),
            'observaciones' => $r->observaciones,
        ]);
        return back()->with('success', 'Alerta cerrada.');
    }

    public function afectacionUpdate(Request $r, AfectacionLote $afectacion)
    {
        $data = $r->validate([
            'estado'        => 'required|in:PENDIENTE_EVALUACION,LIBERADO,BLOQUEADO,DESECHADO',
            'observaciones' => 'nullable|string',
        ]);
        $afectacion->update($data + ['usuario_id' => Auth::id()]);

        $map = [
            'LIBERADO'             => 'LIBERADO',
            'BLOQUEADO'            => 'BLOQUEADO',
            'DESECHADO'            => 'DESECHADO',
            'PENDIENTE_EVALUACION' => 'CUARENTENA',
        ];
        InventarioLote::where('id', $afectacion->inventario_lote_id)
            ->update([
                'estado_calidad'             => $map[$data['estado']],
                'fecha_ultima_verificacion'  => now(),
            ]);

        return back()->with('success', 'Lote actualizado: '.$data['estado']);
    }

    // ====================== LOTES EN EQUIPOS ======================
    public function lotesIndex(Request $r)
    {
        $q = LoteCadenaFrio::with(['inventarioLote.medicamento','equipo'])->orderByDesc('fecha_ingreso');
        if ($r->filled('equipo_id')) $q->where('equipo_id', $r->equipo_id);
        if ($r->filled('activos')) $q->whereNull('fecha_salida');
        $lotes = $q->paginate(30)->withQueryString();
        $equipos = EquipoCadenaFrio::where('estado', true)->orderBy('nombre')->get();
        $inventarioLotes = InventarioLote::with('medicamento')
            ->whereIn('estado_calidad', ['LIBERADO','CUARENTENA'])
            ->orderByDesc('id')->limit(200)->get();
        return view('admin.calidad.cadena-frio.lotes.index', compact('lotes','equipos','inventarioLotes'));
    }

    public function lotesStore(Request $r)
    {
        $data = $r->validate([
            'inventario_lote_id' => 'required|exists:inventario_lotes,id',
            'equipo_id'          => 'required|exists:equipos_cadena_frio,id',
            'fecha_ingreso'      => 'required|date',
            'observaciones'      => 'nullable|string',
        ]);
        DB::transaction(function () use ($data) {
            LoteCadenaFrio::create($data);
            InventarioLote::where('id', $data['inventario_lote_id'])
                ->update(['equipo_cadena_frio_id' => $data['equipo_id']]);
        });
        return back()->with('success', 'Lote ingresado al equipo.');
    }

    public function lotesSalida(Request $r, LoteCadenaFrio $lote)
    {
        $lote->update(['fecha_salida' => now()]);
        InventarioLote::where('id', $lote->inventario_lote_id)
            ->update(['equipo_cadena_frio_id' => null]);
        return back()->with('success', 'Salida registrada.');
    }

    // ====================== SENSORES ======================
    public function sensoresStore(Request $r, EquipoCadenaFrio $equipo)
    {
        $data = $r->validate([
            'codigo_sensor' => 'required|string|max:100',
            'marca'         => 'nullable|string|max:100',
            'modelo'        => 'nullable|string|max:100',
        ]);
        $equipo->sensores()->create($data + ['estado' => true]);
        return back()->with('success', 'Sensor registrado.');
    }

    public function sensoresDestroy(SensorTemperatura $sensor)
    {
        $sensor->delete();
        return back()->with('success', 'Sensor eliminado.');
    }
}
