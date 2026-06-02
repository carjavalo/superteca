<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #0891b2 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }

    .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1rem; margin-bottom:1.2rem; }
    .kpi { background:#fff; border-radius:12px; padding:1rem 1.1rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid #0891b2; }
    .kpi .lbl { font-size:.7rem; color:#94a3b8; text-transform:uppercase; font-weight:600; }
    .kpi .val { font-size:1.6rem; font-weight:700; color:#1e293b; margin-top:.2rem; }

    .filters { background:#fff; padding:1rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); display:flex; gap:.6rem; align-items:end; flex-wrap:wrap; margin-bottom:1.2rem; }
    .filters input, .filters select { padding:.5rem .8rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; }
    .filters label { font-size:.7rem; color:#475569; font-weight:600; display:block; margin-bottom:.2rem; }
    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:#0891b2; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }

    .kanban { display:grid; grid-template-columns:repeat(5,1fr); gap:.7rem; }
    @media (max-width:1100px) { .kanban { grid-template-columns:repeat(2,1fr); } }
    .col { background:#f8fafc; border-radius:12px; padding:.7rem; min-height:300px; }
    .col h3 { margin:0 0 .7rem 0; font-size:.85rem; padding:.4rem .6rem; border-radius:6px; color:#fff; display:flex; justify-content:space-between; align-items:center; }
    .c-prog h3 { background:#64748b; }
    .c-proc h3 { background:#f59e0b; }
    .c-ctrl h3 { background:#0891b2; }
    .c-lib h3 { background:#22c55e; }
    .c-anu h3 { background:#ef4444; }
    .col h3 .count { background:rgba(255,255,255,.25); padding:.1rem .55rem; border-radius:14px; font-size:.75rem; }

    .card { background:#fff; border-radius:8px; padding:.7rem .8rem; margin-bottom:.55rem; box-shadow:0 1px 3px rgba(0,0,0,.08); border-left:3px solid #0891b2; cursor:pointer; transition:transform .12s; }
    .card:hover { transform:translateX(2px); }
    .card .codigo { font-size:.78rem; font-family:monospace; color:#0891b2; font-weight:700; }
    .card .titulo { font-size:.88rem; font-weight:600; color:#1e293b; margin:.15rem 0; }
    .card .meta { font-size:.72rem; color:#64748b; display:flex; justify-content:space-between; }
    .empty { text-align:center; padding:1.5rem 1rem; color:#94a3b8; font-size:.78rem; }
</style>

<div class="page-header">
    <div>
        <h1>Producción · Reempaque</h1>
        <p>Conversión de presentaciones · Trazabilidad lote origen → lote reempaque</p>
    </div>
    <a href="{{ route('admin.reempaques.create') }}" class="btn btn-primary">+ Nuevo Reempaque</a>
</div>

@if(session('success'))<div style="background:#dcfce7;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#10003; {{ session('success') }}</div>@endif
@if(session('error'))<div style="background:#fee2e2;color:#991b1b;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#9888; {{ session('error') }}</div>@endif

<div class="kpi-grid">
    <div class="kpi"><div class="lbl">Hoy</div><div class="val">{{ $stats['hoy'] }}</div></div>
    <div class="kpi"><div class="lbl">En Proceso</div><div class="val">{{ $stats['en_proceso'] }}</div></div>
    <div class="kpi"><div class="lbl">Control Calidad</div><div class="val">{{ $stats['control'] }}</div></div>
    <div class="kpi"><div class="lbl">Liberados Hoy</div><div class="val">{{ $stats['liberados'] }}</div></div>
    <div class="kpi"><div class="lbl">Unidades Generadas Hoy</div><div class="val">{{ rtrim(rtrim(number_format($stats['unidades_hoy'],2,'.',''),'0'),'.') ?: '0' }}</div></div>
</div>

<form class="filters" method="GET">
    <div><label>Buscar</label><input type="text" name="search" value="{{ request('search') }}" placeholder="código o medicamento"></div>
    <div><label>Estado</label>
        <select name="estado">
            <option value="">— Todos —</option>
            @foreach(\App\Models\Reempaque::ESTADOS as $k=>$v)
                <option value="{{ $k }}" {{ request('estado')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <button class="btn btn-primary">Filtrar</button>
    <a href="{{ route('admin.reempaques.index') }}" class="btn btn-secondary">Limpiar</a>
</form>

<div class="kanban">
    @php $cols = ['PROGRAMADO'=>'c-prog','EN_PROCESO'=>'c-proc','CONTROL_CALIDAD'=>'c-ctrl','LIBERADO'=>'c-lib','ANULADO'=>'c-anu']; @endphp
    @foreach($cols as $estado => $clase)
    <div class="col {{ $clase }}">
        <h3>{{ \App\Models\Reempaque::ESTADOS[$estado] }} <span class="count">{{ $kanban[$estado]->count() }}</span></h3>
        @forelse($kanban[$estado] as $r)
            <a href="{{ route('admin.reempaques.show', $r) }}" style="text-decoration:none;color:inherit">
            <div class="card">
                <div class="codigo">{{ $r->codigo }}</div>
                <div class="titulo">{{ $r->medicamentoOrigen->nombre ?? '—' }}</div>
                <div class="meta">
                    <span>{{ $r->fecha_programada->format('d/m H:i') }}</span>
                    <span>{{ $r->productos_finales_count ?? 0 }} prod.</span>
                </div>
            </div>
            </a>
        @empty
            <div class="empty">Sin registros</div>
        @endforelse
    </div>
    @endforeach
</div>
</x-app-layout>
