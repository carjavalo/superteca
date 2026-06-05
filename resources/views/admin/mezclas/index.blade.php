<x-app-layout>
<style>
    :root { --inst:#2e3a75; --inst-dark:#1f2a5c; }
    .page-header { background: linear-gradient(135deg, #6d28d9 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
    .page-header h1 { font-size:1.4rem; font-weight:700; margin:0; }
    .page-header p { font-size:.82rem; opacity:.85; margin:0; }

    .kpi-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(150px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .kpi-card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid #ddd; }
    .kpi-card.violet { border-color:#8b5cf6; } .kpi-card.amber { border-color:#f59e0b; }
    .kpi-card.green  { border-color:#22c55e; } .kpi-card.red   { border-color:#ef4444; }
    .kpi-card.blue { border-color:#3b82f6; }
    .kpi-card .num { font-size:2rem; font-weight:800; line-height:1; }
    .kpi-card .lbl { font-size:.72rem; color:#64748b; text-transform:uppercase; letter-spacing:.05em; margin-top:.2rem; }

    .toolbar { background:#fff; border-radius:10px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.4rem; display:flex; align-items:center; flex-wrap:wrap; gap:.7rem; }
    .toolbar input, .toolbar select { border:1.5px solid #e2e8f0; border-radius:7px; padding:.38rem .75rem; font-size:.85rem; min-width:130px; }
    .toolbar input:focus, .toolbar select:focus { outline:none; border-color:var(--inst); }

    .kanban-board { display:grid; grid-template-columns:repeat(5,1fr); gap:1rem; min-height:60vh; }
    @media (max-width:1200px) { .kanban-board { grid-template-columns:repeat(3,1fr); } }
    @media (max-width:768px)  { .kanban-board { grid-template-columns:1fr; } }

    .kanban-col { background:#f8fafc; border-radius:12px; padding:.8rem; min-height:300px; }
    .kanban-col-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.8rem; padding:.5rem .6rem; border-radius:8px; }
    .kanban-col-header .ttl { font-size:.82rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
    .kanban-col-header .badge-cnt { background:rgba(255,255,255,.7); border-radius:20px; padding:.1rem .5rem; font-size:.75rem; font-weight:700; }

    .col-PROGRAMADA      .kanban-col-header { background:#e0f2fe; color:#0284c7; }
    .col-EN_PROCESO      .kanban-col-header { background:#fef9c3; color:#b45309; }
    .col-CONTROL_CALIDAD .kanban-col-header { background:#ede9fe; color:#6d28d9; }
    .col-LIBERADA        .kanban-col-header { background:#dcfce7; color:#166534; }
    .col-CANCELADA       .kanban-col-header { background:#fee2e2; color:#991b1b; }

    .kanban-card { background:#fff; border-radius:10px; padding:.85rem 1rem; margin-bottom:.65rem; box-shadow:0 2px 6px rgba(0,0,0,.06); cursor:pointer; transition:transform .15s, box-shadow .15s; border-left:4px solid transparent; }
    .kanban-card:hover { transform:translateY(-2px); box-shadow:0 4px 16px rgba(0,0,0,.12); }
    .col-PROGRAMADA      .kanban-card { border-left-color:#0284c7; }
    .col-EN_PROCESO      .kanban-card { border-left-color:#f59e0b; }
    .col-CONTROL_CALIDAD .kanban-card { border-left-color:#8b5cf6; }
    .col-LIBERADA        .kanban-card { border-left-color:#22c55e; }
    .col-CANCELADA       .kanban-card { border-left-color:#ef4444; }

    .kanban-card .cod  { font-size:.7rem; color:#94a3b8; font-family:monospace; }
    .kanban-card .nombre { font-size:.85rem; font-weight:600; color:#1e293b; margin:.2rem 0; }
    .kanban-card .sub   { font-size:.75rem; color:#64748b; }
    .kanban-card .footer { display:flex; justify-content:space-between; margin-top:.5rem; border-top:1px solid #f1f5f9; padding-top:.4rem; font-size:.72rem; color:#94a3b8; }
    .badge-tipo { display:inline-block; padding:.15rem .55rem; border-radius:14px; font-size:.65rem; font-weight:600; background:#e0f2fe; color:#0369a1; }
    .empty-col { text-align:center; padding:1.5rem; color:#cbd5e1; font-size:.82rem; }

    .btn-primary { background:var(--inst); color:#fff; border:none; border-radius:8px; padding:.5rem 1.1rem; font-size:.85rem; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; }
    .btn-primary:hover { background:var(--inst-dark); }
    .btn-sm { padding:.3rem .7rem; font-size:.78rem; border-radius:6px; }
    .btn-icon { background:transparent; border:1.5px solid #e2e8f0; border-radius:6px; padding:.3rem .5rem; cursor:pointer; font-size:.85rem; text-decoration:none; }
    .btn-icon:hover { background:var(--inst); color:#fff; border-color:var(--inst); }

    .alert { padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.9rem; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .alert-error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
</style>

<div class="page-header">
    <div>
        <h1>&#9879; Mezclas — Línea de Producción</h1>
        <p>Proceso de elaboración con trazabilidad de lotes y control de calidad</p>
    </div>
    @puede('Mezclas IV','Crear')<a href="{{ route('admin.mezclas.create') }}" class="btn-primary">&#43; Nueva Mezcla</a>@endpuede
</div>

@if(session('success'))<div class="alert alert-success">&#10003; {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">&#9888; {{ session('error') }}</div>@endif

<div class="kpi-row">
    <div class="kpi-card blue"><div class="num">{{ $stats['hoy'] }}</div><div class="lbl">Programadas hoy</div></div>
    <div class="kpi-card amber"><div class="num">{{ $stats['en_proceso'] }}</div><div class="lbl">En Proceso</div></div>
    <div class="kpi-card violet"><div class="num">{{ $stats['control'] }}</div><div class="lbl">En Control Calidad</div></div>
    <div class="kpi-card green"><div class="num">{{ $stats['liberadas'] }}</div><div class="lbl">Liberadas hoy</div></div>
    <div class="kpi-card red"><div class="num">{{ $stats['canceladas'] }}</div><div class="lbl">Canceladas</div></div>
</div>

<form method="GET" action="{{ route('admin.mezclas.index') }}" class="toolbar">
    <input type="text" name="search" placeholder="&#128269; Código, fórmula…" value="{{ request('search') }}">
    <select name="estado">
        <option value="">Todos los estados</option>
        @foreach(\App\Models\Mezcla::ESTADOS as $k => $v)
            <option value="{{ $k }}" @selected(request('estado') === $k)>{{ $v }}</option>
        @endforeach
    </select>
    <select name="tipo">
        <option value="">Todos los tipos</option>
        @foreach(\App\Models\Mezcla::TIPOS as $k => $v)
            <option value="{{ $k }}" @selected(request('tipo') === $k)>{{ $v }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn-primary btn-sm">Filtrar</button>
    <a href="{{ route('admin.mezclas.index') }}" class="btn-icon" title="Limpiar">&#10005;</a>
</form>

<div class="kanban-board">
    @foreach(\App\Models\Mezcla::ESTADOS as $estado => $label)
    <div class="kanban-col col-{{ $estado }}">
        <div class="kanban-col-header">
            <span class="ttl">{{ $label }}</span>
            <span class="badge-cnt">{{ $kanban[$estado]->count() }}</span>
        </div>
        @forelse($kanban[$estado] as $m)
        <a href="{{ route('admin.mezclas.show', $m) }}" style="text-decoration:none">
            <div class="kanban-card">
                <div class="cod">{{ $m->codigo }}</div>
                <div class="nombre">{{ $m->formula->nombre ?? 'Mezcla manual' }}</div>
                <div class="sub">
                    <span class="badge-tipo">{{ \App\Models\Mezcla::TIPOS[$m->tipo_mezcla] ?? $m->tipo_mezcla }}</span>
                </div>
                <div class="sub" style="margin-top:.3rem">{{ $m->detalles_count }} componentes &middot; {{ $m->cantidad_preparaciones }} preparaciones</div>
                <div class="footer">
                    <span>{{ \Carbon\Carbon::parse($m->fecha_programada)->format('d/m H:i') }}</span>
                    @if($m->costo_total > 0)<span>$ {{ number_format($m->costo_total, 0, ',', '.') }}</span>@endif
                </div>
            </div>
        </a>
        @empty
            <div class="empty-col">Sin mezclas</div>
        @endforelse
    </div>
    @endforeach
</div>
</x-app-layout>
