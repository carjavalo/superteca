<x-app-layout>
<style>
    :root { --inst:#2e3a75; --inst-dark:#1f2a5c; --accent:#00b4d8; }

    .page-header { background: linear-gradient(135deg, var(--inst) 0%, #7c3aed 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
    .page-header h1 { font-size:1.4rem; font-weight:700; margin:0; }
    .page-header p  { font-size:.82rem; opacity:.85; margin:0; }

    .kpi-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .kpi-card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid #ddd; }
    .kpi-card.blue   { border-color:#3b82f6; } .kpi-card.amber { border-color:#f59e0b; }
    .kpi-card.green  { border-color:#22c55e; } .kpi-card.purple { border-color:#a855f7; }
    .kpi-card.pink   { border-color:#ec4899; }
    .kpi-card .num   { font-size:2rem; font-weight:800; line-height:1; }
    .kpi-card .lbl   { font-size:.72rem; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin-top:.2rem; }

    .toolbar { background:#fff; border-radius:10px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.4rem; display:flex; align-items:center; flex-wrap:wrap; gap:.7rem; }
    .toolbar input, .toolbar select { border:1.5px solid #e2e8f0; border-radius:7px; padding:.38rem .75rem; font-size:.85rem; min-width:130px; }
    .toolbar input:focus, .toolbar select:focus { outline:none; border-color:var(--inst); }

    .view-toggle { display:flex; gap:.5rem; }
    .view-toggle button { background:#f1f5f9; border:1.5px solid #e2e8f0; border-radius:7px; padding:.38rem .75rem; font-size:.8rem; cursor:pointer; transition:all .2s; }
    .view-toggle button.active { background:var(--inst); color:#fff; border-color:var(--inst); }

    .kanban-board { display:grid; grid-template-columns:repeat(6,1fr); gap:.8rem; min-height:60vh; }
    @media (max-width:1400px) { .kanban-board { grid-template-columns:repeat(3,1fr); } }
    @media (max-width:768px)  { .kanban-board { grid-template-columns:1fr; } }

    .kanban-col { background:#f8fafc; border-radius:12px; padding:.8rem; min-height:300px; }
    .kanban-col-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.8rem; padding:.5rem .6rem; border-radius:8px; }
    .kanban-col-header .ttl { font-size:.78rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .kanban-col-header .badge-cnt { background:rgba(255,255,255,.8); border-radius:20px; padding:.1rem .5rem; font-size:.72rem; font-weight:700; }

    .col-PROGRAMADA      .kanban-col-header { background:#dbeafe; color:#1d4ed8; }
    .col-EN_PROCESO      .kanban-col-header { background:#fef9c3; color:#a16207; }
    .col-CONTROL_CALIDAD .kanban-col-header { background:#fce7f3; color:#9d174d; }
    .col-LIBERADA        .kanban-col-header { background:#dcfce7; color:#166534; }
    .col-ENTREGADA       .kanban-col-header { background:#e0e7ff; color:#3730a3; }
    .col-ANULADA         .kanban-col-header { background:#fee2e2; color:#991b1b; }

    .kanban-card { background:#fff; border-radius:10px; padding:.75rem .9rem; margin-bottom:.6rem; box-shadow:0 2px 6px rgba(0,0,0,.06); cursor:pointer; transition:transform .15s, box-shadow .15s; border-left:4px solid transparent; }
    .kanban-card:hover { transform:translateY(-2px); box-shadow:0 4px 16px rgba(0,0,0,.12); }
    .col-PROGRAMADA      .kanban-card { border-left-color:#3b82f6; }
    .col-EN_PROCESO      .kanban-card { border-left-color:#f59e0b; }
    .col-CONTROL_CALIDAD .kanban-card { border-left-color:#ec4899; }
    .col-LIBERADA        .kanban-card { border-left-color:#22c55e; }
    .col-ENTREGADA       .kanban-card { border-left-color:#6366f1; }
    .col-ANULADA         .kanban-card { border-left-color:#ef4444; opacity:.7; }

    .kanban-card .cod   { font-size:.68rem; color:#94a3b8; font-family:monospace; }
    .kanban-card .nombre { font-size:.84rem; font-weight:600; color:#1e293b; margin:.2rem 0; }
    .kanban-card .sub   { font-size:.74rem; color:#64748b; }
    .kanban-card .footer { display:flex; justify-content:space-between; align-items:center; margin-top:.5rem; border-top:1px solid #f1f5f9; padding-top:.4rem; }
    .kanban-card .footer .val { font-size:.76rem; font-weight:600; color:var(--inst); }
    .kanban-card .footer .fecha { font-size:.68rem; color:#94a3b8; }

    .empty-col { text-align:center; padding:1.2rem .5rem; color:#cbd5e1; font-size:.78rem; }

    #view-table { display:none; }
    .tbl-wrap { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); overflow:auto; }
    table.main-table { width:100%; border-collapse:collapse; font-size:.85rem; }
    table.main-table thead th { background:var(--inst); color:#fff; padding:.7rem .9rem; text-align:left; white-space:nowrap; }
    table.main-table tbody tr:hover { background:#f8fafc; }
    table.main-table tbody td { padding:.65rem .9rem; border-bottom:1px solid #f1f5f9; }
    .badge { display:inline-block; padding:.2rem .65rem; border-radius:20px; font-size:.7rem; font-weight:600; }
    .badge-PROGRAMADA      { background:#dbeafe; color:#1d4ed8; }
    .badge-EN_PROCESO      { background:#fef9c3; color:#a16207; }
    .badge-CONTROL_CALIDAD { background:#fce7f3; color:#9d174d; }
    .badge-LIBERADA        { background:#dcfce7; color:#166534; }
    .badge-ENTREGADA       { background:#e0e7ff; color:#3730a3; }
    .badge-ANULADA         { background:#fee2e2; color:#991b1b; }
    .badge-tipo            { background:#ede9fe; color:#5b21b6; font-size:.66rem; }

    .btn-primary { background:var(--inst); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.85rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; transition:background .2s; }
    .btn-primary:hover { background:var(--inst-dark); }
    .btn-sm { padding:.3rem .7rem; font-size:.78rem; border-radius:6px; }
    .btn-icon { background:transparent; border:1.5px solid #e2e8f0; border-radius:6px; padding:.3rem .5rem; cursor:pointer; font-size:.85rem; transition:all .2s; text-decoration:none; }
    .btn-icon:hover { background:var(--inst); color:#fff; border-color:var(--inst); }

    .alert { padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.9rem; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .alert-error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
</style>

<div class="page-header">
    <div>
        <h1>&#128137; Producción · Preparaciones</h1>
        <p>Central de Mezclas Hospitalaria · Trazabilidad por lote, control de calidad y entrega</p>
    </div>
    @puede('Preparaciones','Crear')<a href="{{ route('admin.preparaciones.create') }}" class="btn-primary">&#43; Nueva Preparación</a>@endpuede
</div>

@if(session('success'))<div class="alert alert-success">&#10003; {{ session('success') }}</div>@endif
@if(session('error'))  <div class="alert alert-error">&#9888; {{ session('error') }}</div>@endif

<div class="kpi-row">
    <div class="kpi-card blue"><div class="num">{{ $stats['hoy'] }}</div><div class="lbl">Programadas hoy</div></div>
    <div class="kpi-card amber"><div class="num">{{ $stats['en_proceso'] }}</div><div class="lbl">En proceso</div></div>
    <div class="kpi-card pink"><div class="num">{{ $stats['control'] }}</div><div class="lbl">En control de calidad</div></div>
    <div class="kpi-card green"><div class="num">{{ $stats['liberadas'] }}</div><div class="lbl">Liberadas hoy</div></div>
    <div class="kpi-card purple"><div class="num">{{ $stats['entregadas'] }}</div><div class="lbl">Entregadas hoy</div></div>
</div>

<form method="GET" action="{{ route('admin.preparaciones.index') }}" class="toolbar">
    <input type="text" name="search" placeholder="&#128269; Código, paciente…" value="{{ request('search') }}">
    <select name="estado">
        <option value="">Todos los estados</option>
        @foreach(\App\Models\Preparacion::ESTADOS as $k => $v)
            <option value="{{ $k }}" @selected(request('estado') === $k)>{{ $v }}</option>
        @endforeach
    </select>
    <select name="tipo">
        <option value="">Todos los tipos</option>
        @foreach($tipos as $t)
            <option value="{{ $t->id }}" @selected((string)request('tipo') === (string)$t->id)>{{ $t->nombre }}</option>
        @endforeach
    </select>
    <input type="date" name="desde" value="{{ request('desde') }}" title="Desde">
    <input type="date" name="hasta" value="{{ request('hasta') }}" title="Hasta">
    <button type="submit" class="btn-primary btn-sm">Filtrar</button>
    <a href="{{ route('admin.preparaciones.index') }}" class="btn-icon" title="Limpiar">&#10005;</a>

    <div class="view-toggle" style="margin-left:auto">
        <button type="button" id="btn-kanban" class="active" onclick="setView('kanban')">&#9776; Kanban</button>
        <button type="button" id="btn-table" onclick="setView('table')">&#9776; Tabla</button>
    </div>
</form>

<div id="view-kanban">
    <div class="kanban-board">
        @foreach(\App\Models\Preparacion::ESTADOS as $estado => $label)
        <div class="kanban-col col-{{ $estado }}">
            <div class="kanban-col-header">
                <span class="ttl">{{ $label }}</span>
                <span class="badge-cnt">{{ $kanban[$estado]->count() }}</span>
            </div>
            @forelse($kanban[$estado] as $p)
            <a href="{{ route('admin.preparaciones.show', $p) }}" style="text-decoration:none">
                <div class="kanban-card">
                    <div class="cod">{{ $p->codigo }}</div>
                    <div class="nombre">{{ $p->paciente?->nombre_completo ?? 'Sin paciente' }}</div>
                    <div class="sub">
                        <span class="badge badge-tipo">{{ $p->tipo?->nombre ?? '—' }}</span>
                        @if($p->servicio) · {{ $p->servicio->nombre }} @endif
                    </div>
                    <div class="sub" style="margin-top:.2rem">
                        {{ $p->detalles_count }} componente{{ $p->detalles_count !== 1 ? 's' : '' }}
                    </div>
                    <div class="footer">
                        <span class="val">$ {{ number_format($p->costo_total, 0, ',', '.') }}</span>
                        <span class="fecha">{{ \Carbon\Carbon::parse($p->fecha_programada)->format('d/m H:i') }}</span>
                    </div>
                </div>
            </a>
            @empty
                <div class="empty-col">Sin preparaciones</div>
            @endforelse
        </div>
        @endforeach
    </div>
</div>

<div id="view-table">
    <div class="tbl-wrap">
        <table class="main-table">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Paciente</th>
                    <th>Servicio</th>
                    <th>Programada</th>
                    <th>Componentes</th>
                    <th>Costo</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($todos as $p)
                <tr>
                    <td><code style="font-size:.78rem">{{ $p->codigo }}</code></td>
                    <td><span class="badge badge-tipo">{{ $p->tipo?->nombre ?? '—' }}</span></td>
                    <td>{{ $p->paciente?->nombre_completo ?? '—' }}</td>
                    <td>{{ $p->servicio?->nombre ?? '—' }}</td>
                    <td>{{ \Carbon\Carbon::parse($p->fecha_programada)->format('d/m/Y H:i') }}</td>
                    <td style="text-align:center">{{ $p->detalles_count }}</td>
                    <td>$ {{ number_format($p->costo_total, 0, ',', '.') }}</td>
                    <td><span class="badge badge-{{ $p->estado }}">{{ \App\Models\Preparacion::ESTADOS[$p->estado] ?? $p->estado }}</span></td>
                    <td><a href="{{ route('admin.preparaciones.show', $p) }}" class="btn-icon" title="Ver">&#128065;</a></td>
                </tr>
                @empty
                <tr><td colspan="9" style="text-align:center;padding:2rem;color:#94a3b8">No hay preparaciones con los filtros seleccionados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function setView(v) {
    document.getElementById('view-kanban').style.display = v==='kanban' ? '' : 'none';
    document.getElementById('view-table').style.display  = v==='table'  ? '' : 'none';
    document.getElementById('btn-kanban').classList.toggle('active', v==='kanban');
    document.getElementById('btn-table').classList.toggle('active', v==='table');
    localStorage.setItem('preparaciones_view', v);
}
const saved = localStorage.getItem('preparaciones_view') || 'kanban';
setView(saved);
</script>
</x-app-layout>
