<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #14b8a6 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }

    .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:1rem; margin-bottom:1.2rem; }
    .kpi { background:#fff; border-radius:12px; padding:1rem 1.1rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid #14b8a6; }
    .kpi .lbl { font-size:.7rem; color:#94a3b8; text-transform:uppercase; font-weight:600; }
    .kpi .val { font-size:1.7rem; font-weight:700; color:#1e293b; margin-top:.2rem; }

    .filters { background:#fff; padding:1rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); display:flex; gap:.6rem; align-items:end; flex-wrap:wrap; margin-bottom:1.2rem; }
    .filters input, .filters select { padding:.5rem .8rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; }
    .filters label { font-size:.7rem; color:#475569; font-weight:600; display:block; margin-bottom:.2rem; }
    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:#14b8a6; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }

    .tabla { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .tabla table { width:100%; border-collapse:collapse; }
    .tabla th { background:#f1f5f9; padding:.75rem .9rem; text-align:left; font-size:.7rem; color:#475569; text-transform:uppercase; }
    .tabla td { padding:.7rem .9rem; font-size:.84rem; border-top:1px solid #f1f5f9; }
    .tabla tr:hover td { background:#f8fafc; cursor:pointer; }

    .badge { display:inline-block; padding:.18rem .6rem; border-radius:10px; font-size:.7rem; font-weight:600; }
    .b-act { background:#dcfce7; color:#166534; }
    .b-egr { background:#e0e7ff; color:#3730a3; }
    .b-fal { background:#fee2e2; color:#991b1b; }
    .cama-tag { background:#f1f5f9; color:#475569; padding:.15rem .55rem; border-radius:8px; font-family:monospace; font-size:.75rem; }
</style>

<div class="page-header">
    <div>
        <h1>Dispensación · Pacientes</h1>
        <p>Censo hospitalario y trazabilidad clínico-farmacéutica</p>
    </div>
    <a href="{{ route('admin.dispensacion.pacientes.create') }}" class="btn btn-primary">+ Registrar Paciente</a>
</div>

@if(session('success'))<div style="background:#dcfce7;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#10003; {{ session('success') }}</div>@endif

<div class="kpi-grid">
    <div class="kpi"><div class="lbl">Pacientes Activos</div><div class="val">{{ $stats['activos'] }}</div></div>
    <div class="kpi"><div class="lbl">Egresados (mes)</div><div class="val">{{ $stats['egresados_mes'] }}</div></div>
    <div class="kpi"><div class="lbl">Prescripciones Activas</div><div class="val">{{ $stats['prescripciones'] }}</div></div>
    <div class="kpi"><div class="lbl">Tratamientos Activos</div><div class="val">{{ $stats['tratamientos'] }}</div></div>
</div>

<form class="filters" method="GET">
    <div><label>Buscar</label><input type="text" name="search" value="{{ request('search') }}" placeholder="documento, nombre, cama"></div>
    <div><label>Estado clínico</label>
        <select name="estado_clinico">
            <option value="">— Todos —</option>
            @foreach(\App\Models\Paciente::ESTADOS_CLINICOS as $k=>$v)
                <option value="{{ $k }}" {{ request('estado_clinico')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div><label>Servicio</label>
        <select name="servicio_id">
            <option value="">— Todos —</option>
            @foreach($servicios as $s)
                <option value="{{ $s->id }}" {{ request('servicio_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">Filtrar</button>
    <a href="{{ route('admin.dispensacion.pacientes.index') }}" class="btn btn-secondary">Limpiar</a>
</form>

<div class="tabla">
    <table>
        <thead>
            <tr>
                <th>Cama</th><th>Documento</th><th>Paciente</th><th>Edad / Sexo</th>
                <th>Servicio</th><th>Ingreso</th><th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pacientes as $p)
                @php $bclass = ['ACTIVO'=>'b-act','EGRESADO'=>'b-egr','FALLECIDO'=>'b-fal'][$p->estado_clinico ?? 'ACTIVO'] ?? 'b-act'; @endphp
                <tr onclick="window.location='{{ route('admin.dispensacion.pacientes.show', $p) }}'">
                    <td>@if($p->cama)<span class="cama-tag">{{ $p->cama }}</span>@else <span style="color:#cbd5e1">—</span>@endif</td>
                    <td style="font-family:monospace">{{ $p->documento }}</td>
                    <td><strong>{{ trim($p->apellidos.' '.$p->nombres) }}</strong></td>
                    <td>{{ $p->edad ?? '—' }} / {{ $p->sexo ?? '—' }}</td>
                    <td>{{ $p->servicio->nombre ?? '—' }}</td>
                    <td>{{ $p->fecha_ingreso?->format('d/m/Y') ?? '—' }}</td>
                    <td><span class="badge {{ $bclass }}">{{ \App\Models\Paciente::ESTADOS_CLINICOS[$p->estado_clinico ?? 'ACTIVO'] ?? 'Activo' }}</span></td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:2rem;color:#94a3b8">No hay pacientes registrados.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div style="padding:1rem">{{ $pacientes->links() }}</div>
</div>
</x-app-layout>
