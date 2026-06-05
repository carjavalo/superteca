<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #6366f1 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }

    .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(190px,1fr)); gap:1rem; margin-bottom:1.2rem; }
    .kpi { background:#fff; border-radius:12px; padding:1rem 1.1rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid #6366f1; }
    .kpi.k-pen { border-left-color:#f59e0b; }
    .kpi.k-apr { border-left-color:#22c55e; }
    .kpi.k-obs { border-left-color:#a855f7; }
    .kpi.k-cri { border-left-color:#ef4444; }
    .kpi .lbl { font-size:.7rem; color:#94a3b8; text-transform:uppercase; font-weight:600; }
    .kpi .val { font-size:1.7rem; font-weight:700; color:#1e293b; margin-top:.2rem; }

    .filters { background:#fff; padding:1rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); display:flex; gap:.6rem; align-items:end; flex-wrap:wrap; margin-bottom:1.2rem; }
    .filters input, .filters select { padding:.5rem .8rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; }
    .filters label { font-size:.7rem; color:#475569; font-weight:600; display:block; margin-bottom:.2rem; }
    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:#6366f1; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }

    .tabla { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .tabla table { width:100%; border-collapse:collapse; }
    .tabla th { background:#f1f5f9; padding:.75rem .9rem; text-align:left; font-size:.7rem; color:#475569; text-transform:uppercase; }
    .tabla td { padding:.7rem .9rem; font-size:.84rem; border-top:1px solid #f1f5f9; }
    .tabla tr:hover td { background:#f8fafc; cursor:pointer; }

    .badge { display:inline-block; padding:.18rem .65rem; border-radius:10px; font-size:.7rem; font-weight:700; }
    .b-pen { background:#fef3c7; color:#92400e; }
    .b-apr { background:#dcfce7; color:#166534; }
    .b-obs { background:#ede9fe; color:#6b21a8; }
    .b-rec { background:#fee2e2; color:#991b1b; }

    .semaforo { display:inline-flex; align-items:center; gap:.4rem; font-weight:600; font-size:.78rem; }
    .dot { width:10px; height:10px; border-radius:50%; display:inline-block; }
    .d-verde { background:#22c55e; box-shadow:0 0 0 3px #22c55e22; }
    .d-amarillo { background:#facc15; box-shadow:0 0 0 3px #facc1522; }
    .d-naranja { background:#fb923c; box-shadow:0 0 0 3px #fb923c22; }
    .d-rojo { background:#ef4444; box-shadow:0 0 0 3px #ef444422; animation:pulse 1.5s infinite; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.5} }

    .prio { font-size:.7rem; font-weight:700; padding:.15rem .55rem; border-radius:6px; }
    .p-baja { background:#e0f2fe; color:#075985; }
    .p-norm { background:#f1f5f9; color:#475569; }
    .p-alta { background:#ffedd5; color:#9a3412; }
    .p-cri  { background:#fee2e2; color:#991b1b; }
</style>

<div class="page-header">
    <div>
        <h1>Dispensación · Validación Farmacéutica</h1>
        <p>Centro de control farmacéutico — autorización clínica antes de afectar inventario</p>
    </div>
    @puede('Validación farmacéutica','Crear')<a href="{{ route('admin.dispensacion.validaciones.create') }}" class="btn btn-primary">+ Nueva Validación</a>@endpuede
</div>

@if(session('success'))<div style="background:#dcfce7;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#10003; {{ session('success') }}</div>@endif

<div class="kpi-grid">
    <div class="kpi k-pen"><div class="lbl">Pendientes</div><div class="val">{{ $stats['pendientes'] }}</div></div>
    <div class="kpi k-apr"><div class="lbl">Aprobadas hoy</div><div class="val">{{ $stats['aprobadas_hoy'] }}</div></div>
    <div class="kpi k-obs"><div class="lbl">Observadas</div><div class="val">{{ $stats['observadas'] }}</div></div>
    <div class="kpi k-cri"><div class="lbl">Críticas</div><div class="val">{{ $stats['criticas'] }}</div></div>
</div>

<form class="filters" method="GET">
    <div><label>Buscar</label><input type="text" name="search" value="{{ request('search') }}" placeholder="código o paciente"></div>
    <div><label>Resultado</label>
        <select name="resultado">
            <option value="">— Todos —</option>
            @foreach(\App\Models\Validacion::RESULTADOS as $k=>$v)
                <option value="{{ $k }}" {{ request('resultado')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div><label>Tipo</label>
        <select name="tipo_validacion">
            <option value="">— Todos —</option>
            @foreach(\App\Models\Validacion::TIPOS as $k=>$v)
                <option value="{{ $k }}" {{ request('tipo_validacion')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div><label>Prioridad</label>
        <select name="prioridad">
            <option value="">— Todas —</option>
            @foreach(\App\Models\Validacion::PRIORIDADES as $k=>$v)
                <option value="{{ $k }}" {{ request('prioridad')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">Filtrar</button>
    <a href="{{ route('admin.dispensacion.validaciones.index') }}" class="btn btn-secondary">Limpiar</a>
</form>

<div class="tabla">
    <table>
        <thead>
            <tr>
                <th>Semáforo</th><th>Código</th><th>Paciente</th><th>Servicio</th>
                <th>Tipo</th><th>Prioridad</th><th>Alertas</th><th>Resultado</th><th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            @forelse($validaciones as $v)
                @php
                    $criticas = $v->alertas->where('severidad','CRITICA')->where('resuelta',false)->count();
                    $altas    = $v->alertas->where('severidad','ALTA')->where('resuelta',false)->count();
                    $medias   = $v->alertas->where('severidad','MEDIA')->where('resuelta',false)->count();
                    if ($criticas) { $semColor='d-rojo'; $semTxt='Crítico'; }
                    elseif ($altas) { $semColor='d-naranja'; $semTxt='Riesgo moderado'; }
                    elseif ($medias) { $semColor='d-amarillo'; $semTxt='Revisar'; }
                    else { $semColor='d-verde'; $semTxt='Sin observaciones'; }
                    $bMap=['PENDIENTE'=>'b-pen','APROBADA'=>'b-apr','OBSERVADA'=>'b-obs','RECHAZADA'=>'b-rec'];
                    $pMap=['BAJA'=>'p-baja','NORMAL'=>'p-norm','ALTA'=>'p-alta','CRITICA'=>'p-cri'];
                @endphp
                <tr onclick="window.location='{{ route('admin.dispensacion.validaciones.show', $v) }}'">
                    <td><span class="semaforo"><span class="dot {{ $semColor }}"></span> {{ $semTxt }}</span></td>
                    <td><strong style="font-family:monospace;color:#6366f1">{{ $v->codigo }}</strong></td>
                    <td>{{ $v->paciente ? trim($v->paciente->apellidos.' '.$v->paciente->nombres) : '—' }}</td>
                    <td>{{ $v->paciente->servicio->nombre ?? '—' }}</td>
                    <td>{{ \App\Models\Validacion::TIPOS[$v->tipo_validacion] ?? $v->tipo_validacion }}</td>
                    <td><span class="prio {{ $pMap[$v->prioridad] }}">{{ \App\Models\Validacion::PRIORIDADES[$v->prioridad] }}</span></td>
                    <td>
                        @if($criticas)<span style="color:#ef4444;font-weight:700">{{ $criticas }} críticas</span>@endif
                        @if($altas)<span style="color:#f97316;margin-left:.3rem">{{ $altas }} altas</span>@endif
                        @if(!$criticas && !$altas && !$medias)<span style="color:#94a3b8">—</span>@endif
                    </td>
                    <td><span class="badge {{ $bMap[$v->resultado] }}">{{ \App\Models\Validacion::RESULTADOS[$v->resultado] }}</span></td>
                    <td style="font-size:.78rem;color:#64748b">{{ $v->fecha_validacion->format('d/m/Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="9" style="text-align:center;padding:2rem;color:#94a3b8">No hay validaciones registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:1rem">{{ $validaciones->links() }}</div>
</div>
</x-app-layout>
