<x-app-layout>
<style>
    :root { --inst:#2e3a75; --inst-dark:#1f2a5c; }
    .page-header { background: linear-gradient(135deg, var(--inst) 0%, #1a6ba3 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
    .page-header h1 { font-size:1.4rem; font-weight:700; margin:0; }
    .page-header p { font-size:.82rem; opacity:.85; margin:0; }

    .kpi-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .kpi-card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid #ddd; }
    .kpi-card.blue   { border-color:#3b82f6; }
    .kpi-card.amber  { border-color:#f59e0b; }
    .kpi-card.green  { border-color:#22c55e; }
    .kpi-card.red    { border-color:#ef4444; }
    .kpi-card.violet { border-color:#8b5cf6; }
    .kpi-card.cyan   { border-color:#06b6d4; }
    .kpi-card .num { font-size:2rem; font-weight:800; line-height:1; }
    .kpi-card .lbl { font-size:.72rem; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin-top:.2rem; }

    .toolbar { background:#fff; border-radius:10px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.4rem; display:flex; align-items:center; flex-wrap:wrap; gap:.7rem; }
    .toolbar input, .toolbar select { border:1.5px solid #e2e8f0; border-radius:7px; padding:.38rem .75rem; font-size:.85rem; min-width:130px; }
    .toolbar input:focus, .toolbar select:focus { outline:none; border-color:var(--inst); }
    .view-toggle { display:flex; gap:.5rem; }
    .view-toggle button { background:#f1f5f9; border:1.5px solid #e2e8f0; border-radius:7px; padding:.38rem .75rem; font-size:.8rem; cursor:pointer; }
    .view-toggle button.active { background:var(--inst); color:#fff; border-color:var(--inst); }

    /* Tarjetas */
    .formula-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(290px,1fr)); gap:1.1rem; }
    .formula-card { background:#fff; border-radius:14px; padding:1.2rem; box-shadow:0 4px 14px rgba(0,0,0,.07); border-top:5px solid var(--inst); display:flex; flex-direction:column; gap:.7rem; transition:transform .18s, box-shadow .18s; }
    .formula-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.13); }
    .formula-card.tipo-NUTRICION_PARENTERAL { border-color:#22c55e; }
    .formula-card.tipo-ANTIBIOTICO { border-color:#3b82f6; }
    .formula-card.tipo-ONCOLOGIA { border-color:#ef4444; }
    .formula-card.tipo-PEDIATRIA { border-color:#f59e0b; }
    .formula-card.tipo-MAGISTRAL { border-color:#8b5cf6; }
    .formula-card.tipo-ESTANDAR { border-color:#64748b; }
    .formula-card .codigo { font-size:.7rem; color:#94a3b8; font-family:monospace; }
    .formula-card .nombre { font-size:1.05rem; font-weight:700; color:#1e293b; }
    .formula-card .tipo-badge { display:inline-block; padding:.2rem .6rem; border-radius:20px; font-size:.7rem; font-weight:600; background:#f1f5f9; color:#475569; align-self:flex-start; }
    .formula-card .meta { display:flex; justify-content:space-between; font-size:.78rem; color:#64748b; padding:.4rem 0; border-top:1px dashed #e2e8f0; }
    .formula-card .meta strong { color:#1e293b; }
    .formula-card .actions { display:flex; gap:.4rem; margin-top:auto; }
    .formula-card .actions a { flex:1; text-align:center; padding:.4rem; border-radius:7px; font-size:.78rem; text-decoration:none; font-weight:600; }
    .btn-ver { background:var(--inst); color:#fff; }
    .btn-sim { background:#06b6d4; color:#fff; }

    /* Tabla */
    #view-table { display:none; }
    .tbl-wrap { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); overflow:auto; }
    table.main-table { width:100%; border-collapse:collapse; font-size:.85rem; }
    table.main-table thead th { background:var(--inst); color:#fff; padding:.7rem .9rem; text-align:left; }
    table.main-table tbody tr:hover { background:#f8fafc; }
    table.main-table tbody td { padding:.65rem .9rem; border-bottom:1px solid #f1f5f9; }
    .badge-tipo { display:inline-block; padding:.18rem .55rem; border-radius:14px; font-size:.7rem; font-weight:600; }
    .badge-NUTRICION_PARENTERAL { background:#dcfce7; color:#166534; }
    .badge-ANTIBIOTICO { background:#dbeafe; color:#1d4ed8; }
    .badge-ONCOLOGIA { background:#fee2e2; color:#991b1b; }
    .badge-PEDIATRIA { background:#fef3c7; color:#92400e; }
    .badge-MAGISTRAL { background:#ede9fe; color:#6d28d9; }
    .badge-ESTANDAR { background:#f1f5f9; color:#475569; }

    .btn-primary { background:var(--inst); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.85rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; }
    .btn-primary:hover { background:var(--inst-dark); }
    .btn-sm { padding:.3rem .7rem; font-size:.78rem; border-radius:6px; }
    .btn-icon { background:transparent; border:1.5px solid #e2e8f0; border-radius:6px; padding:.3rem .5rem; cursor:pointer; font-size:.85rem; text-decoration:none; }
    .btn-icon:hover { background:var(--inst); color:#fff; border-color:var(--inst); }

    .alert { padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.9rem; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .empty { text-align:center; padding:3rem 1rem; color:#94a3b8; }
</style>

<div class="page-header">
    <div>
        <h1>&#9881; Catálogo de Fórmulas Magistrales</h1>
        <p>Recetas maestras reutilizables — base de las preparaciones clínicas</p>
    </div>
    @puede('Fórmulas magistrales','Crear')<a href="{{ route('admin.formulas.create') }}" class="btn-primary">&#43; Nueva Fórmula</a>@endpuede
</div>

@if(session('success'))<div class="alert alert-success">&#10003; {{ session('success') }}</div>@endif

<div class="kpi-row">
    <div class="kpi-card violet"><div class="num">{{ $stats['total'] }}</div><div class="lbl">Total Fórmulas</div></div>
    <div class="kpi-card green"><div class="num">{{ $stats['nutricion'] }}</div><div class="lbl">Nutrición Parenteral</div></div>
    <div class="kpi-card red"><div class="num">{{ $stats['oncologia'] }}</div><div class="lbl">Oncología</div></div>
    <div class="kpi-card blue"><div class="num">{{ $stats['antibiotico'] }}</div><div class="lbl">Antibiótico</div></div>
    <div class="kpi-card amber"><div class="num">{{ $stats['pediatria'] }}</div><div class="lbl">Pediatría</div></div>
    <div class="kpi-card cyan"><div class="num">{{ $stats['magistral'] }}</div><div class="lbl">Magistrales</div></div>
</div>

<form method="GET" action="{{ route('admin.formulas.index') }}" class="toolbar">
    <input type="text" name="search" placeholder="&#128269; Buscar código o nombre…" value="{{ request('search') }}">
    <select name="tipo">
        <option value="">Todos los tipos</option>
        @foreach(\App\Models\Formula::TIPOS as $k => $v)
            <option value="{{ $k }}" @selected(request('tipo') === $k)>{{ $v }}</option>
        @endforeach
    </select>
    <select name="estado">
        <option value="">Activas e inactivas</option>
        <option value="1" @selected(request('estado') === '1')>Activas</option>
        <option value="0" @selected(request('estado') === '0')>Inactivas</option>
    </select>
    <button type="submit" class="btn-primary btn-sm">Filtrar</button>
    <a href="{{ route('admin.formulas.index') }}" class="btn-icon" title="Limpiar">&#10005;</a>
    <div class="view-toggle" style="margin-left:auto">
        <button type="button" id="btn-cards" class="active" onclick="setView('cards')">&#9783; Tarjetas</button>
        <button type="button" id="btn-table" onclick="setView('table')">&#9776; Tabla</button>
    </div>
</form>

<div id="view-cards">
    @if($formulas->isEmpty())
        <div class="empty">No hay fórmulas registradas. Crea la primera para comenzar.</div>
    @else
    <div class="formula-grid">
        @foreach($formulas as $f)
        <div class="formula-card tipo-{{ $f->tipo_formula }}">
            <div class="codigo">{{ $f->codigo }}</div>
            <div class="nombre">{{ $f->nombre }}</div>
            <span class="tipo-badge badge-{{ $f->tipo_formula }}">{{ \App\Models\Formula::TIPOS[$f->tipo_formula] }}</span>
            @if($f->descripcion)
                <p style="font-size:.78rem; color:#64748b; margin:0;">{{ \Illuminate\Support\Str::limit($f->descripcion, 90) }}</p>
            @endif
            <div class="meta">
                <span>Componentes: <strong>{{ $f->detalles_count }}</strong></span>
                @if($f->tiempo_estabilidad_horas)
                    <span>Estabilidad: <strong>{{ $f->tiempo_estabilidad_horas }}h</strong></span>
                @endif
            </div>
            @if($f->volumen_final)
                <div style="font-size:.78rem; color:#0f172a;">Volumen final: <strong>{{ $f->volumen_final }} mL</strong></div>
            @endif
            <div class="actions">
                <a href="{{ route('admin.formulas.show', $f) }}" class="btn-ver">Ver</a>
                <a href="{{ route('admin.formulas.simulador', $f) }}" class="btn-sim">Simular</a>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

<div id="view-table">
    <div class="tbl-wrap">
        <table class="main-table">
            <thead>
                <tr><th>Código</th><th>Fórmula</th><th>Tipo</th><th>Componentes</th><th>Volumen</th><th>Estabilidad</th><th>Estado</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($formulas as $f)
                <tr>
                    <td><code style="font-size:.78rem">{{ $f->codigo }}</code></td>
                    <td>{{ $f->nombre }}</td>
                    <td><span class="badge-tipo badge-{{ $f->tipo_formula }}">{{ \App\Models\Formula::TIPOS[$f->tipo_formula] }}</span></td>
                    <td style="text-align:center">{{ $f->detalles_count }}</td>
                    <td>{{ $f->volumen_final ? $f->volumen_final.' mL' : '—' }}</td>
                    <td>{{ $f->tiempo_estabilidad_horas ? $f->tiempo_estabilidad_horas.' h' : '—' }}</td>
                    <td>{!! $f->estado ? '<span style="color:#16a34a">&#10003; Activa</span>' : '<span style="color:#94a3b8">Inactiva</span>' !!}</td>
                    <td>
                        <a href="{{ route('admin.formulas.show', $f) }}" class="btn-icon" title="Ver">&#128065;</a>
                        <a href="{{ route('admin.formulas.simulador', $f) }}" class="btn-icon" title="Simulador">&#128202;</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:#94a3b8">Sin resultados</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function setView(v) {
    document.getElementById('view-cards').style.display = v==='cards' ? '' : 'none';
    document.getElementById('view-table').style.display = v==='table' ? '' : 'none';
    document.getElementById('btn-cards').classList.toggle('active', v==='cards');
    document.getElementById('btn-table').classList.toggle('active', v==='table');
    localStorage.setItem('formulas_view', v);
}
setView(localStorage.getItem('formulas_view') || 'cards');
</script>
</x-app-layout>
