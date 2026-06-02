<x-app-layout>
<x-slot name="header"><h2>Kardex · Historial de Movimientos</h2></x-slot>

<style>
    :root { --inst:#2e3a75; --inst-dark:#1f2a5c; }

    .page-hero { background:linear-gradient(135deg,#2e3a75 0%,#1a6ba3 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.4rem; display:flex; justify-content:space-between; gap:1rem; flex-wrap:wrap; align-items:center;}
    .page-hero h1 { margin:0; font-size:1.4rem; font-weight:700; }
    .page-hero p  { margin:0; opacity:.85; font-size:.85rem; }
    .hero-actions a { background:rgba(255,255,255,.15); color:#fff; padding:.5rem 1rem; border-radius:8px; text-decoration:none; font-size:.85rem; margin-left:.4rem; transition:background .2s; }
    .hero-actions a:hover { background:rgba(255,255,255,.3); }

    .kpi-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.4rem; }
    .kpi-card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid var(--inst); }
    .kpi-card .num { font-size:1.7rem; font-weight:800; color:#1e293b; line-height:1; }
    .kpi-card .lbl { font-size:.72rem; color:#64748b; text-transform:uppercase; letter-spacing:.04em; margin-top:.3rem; }
    .kpi-card.green  { border-color:#22c55e; }
    .kpi-card.amber  { border-color:#f59e0b; }
    .kpi-card.purple { border-color:#a855f7; }

    .toolbar { background:#fff; border-radius:10px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.4rem; display:flex; align-items:center; flex-wrap:wrap; gap:.65rem; }
    .toolbar input, .toolbar select { border:1.5px solid #e2e8f0; border-radius:7px; padding:.4rem .75rem; font-size:.85rem; min-width:140px; }
    .toolbar input:focus, .toolbar select:focus { outline:none; border-color:var(--inst); }
    .btn-primary { background:var(--inst); color:#fff; border:none; border-radius:8px; padding:.4rem 1rem; font-size:.85rem; cursor:pointer; text-decoration:none; }
    .btn-primary:hover { background:var(--inst-dark); }
    .btn-icon { background:transparent; border:1.5px solid #e2e8f0; border-radius:6px; padding:.3rem .6rem; cursor:pointer; font-size:.85rem; text-decoration:none; }
    .btn-icon:hover { background:var(--inst); color:#fff; border-color:var(--inst); }

    .tbl-wrap { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); overflow:auto; }
    table.kardex { width:100%; border-collapse:collapse; font-size:.83rem; }
    table.kardex thead th { background:var(--inst); color:#fff; padding:.7rem .85rem; text-align:left; white-space:nowrap; position:sticky; top:0; }
    table.kardex tbody td { padding:.6rem .85rem; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    table.kardex tbody tr:hover { background:#f8fafc; }
    table.kardex .num { text-align:right; font-variant-numeric:tabular-nums; font-weight:600; }
    table.kardex .ent { color:#16a34a; }
    table.kardex .sal { color:#dc2626; }
    table.kardex .saldo { color:var(--inst); font-weight:700; }
    .pill { display:inline-block; padding:.18rem .55rem; border-radius:14px; font-size:.7rem; font-weight:700; color:#fff; }
    code.lote { background:#f1f5f9; padding:.1rem .4rem; border-radius:4px; font-size:.75rem; color:#475569; }
    .empty-row td { text-align:center; padding:2rem; color:#94a3b8; }

    .tab-bar { display:flex; gap:.4rem; margin-bottom:1rem; }
    .tab-bar a { padding:.45rem 1rem; border-radius:8px; text-decoration:none; font-size:.85rem; color:#64748b; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.05); transition:all .2s; }
    .tab-bar a.active, .tab-bar a:hover { background:var(--inst); color:#fff; }
</style>

<div class="page-hero">
    <div>
        <h1>📒 Kardex Inteligente</h1>
        <p>Libro mayor de movimientos del inventario · Trazabilidad ISO 9001 / INVIMA</p>
    </div>
    <div class="hero-actions">
        <a href="{{ route('admin.kardex.lote') }}">🧬 Por Lote</a>
        <a href="{{ route('admin.kardex.analytics') }}">📊 Analytics</a>
    </div>
</div>

<div class="tab-bar">
    <a href="{{ route('admin.kardex.index') }}" class="active">Kardex General</a>
    <a href="{{ route('admin.kardex.lote') }}">Trazabilidad por Lote</a>
    <a href="{{ route('admin.kardex.analytics') }}">Dashboard Analítico</a>
</div>

<div class="kpi-row">
    <div class="kpi-card green">
        <div class="num">{{ number_format($stats['stock_total'], 0, ',', '.') }}</div>
        <div class="lbl">Stock total (und)</div>
    </div>
    <div class="kpi-card amber">
        <div class="num">{{ number_format($stats['movimientos_hoy']) }}</div>
        <div class="lbl">Movimientos hoy</div>
    </div>
    <div class="kpi-card">
        <div class="num">{{ number_format($stats['lotes_activos']) }}</div>
        <div class="lbl">Lotes activos</div>
    </div>
    <div class="kpi-card purple">
        <div class="num">$ {{ number_format($stats['valor_inventario'], 0, ',', '.') }}</div>
        <div class="lbl">Valor inventario</div>
    </div>
</div>

<form method="GET" action="{{ route('admin.kardex.index') }}" class="toolbar">
    <input type="text" name="search" placeholder="🔍 Lote, observación, medicamento…" value="{{ request('search') }}">
    <select name="tipo">
        <option value="">Todos los tipos</option>
        @foreach($tipos as $k => $cfg)
            <option value="{{ $k }}" @selected(request('tipo') === $k)>{{ $cfg['label'] }}</option>
        @endforeach
    </select>
    <select name="medicamento_id">
        <option value="">Todos los medicamentos</option>
        @foreach($medicamentos as $m)
            <option value="{{ $m->id }}" @selected(request('medicamento_id') == $m->id)>{{ $m->nombre }}</option>
        @endforeach
    </select>
    <select name="usuario_id">
        <option value="">Todos los usuarios</option>
        @foreach($usuarios as $u)
            <option value="{{ $u->id }}" @selected(request('usuario_id') == $u->id)>{{ $u->name }} {{ $u->apellido1 }}</option>
        @endforeach
    </select>
    <input type="date" name="desde" value="{{ request('desde') }}" title="Desde">
    <input type="date" name="hasta" value="{{ request('hasta') }}" title="Hasta">
    <button type="submit" class="btn-primary">Filtrar</button>
    <a href="{{ route('admin.kardex.index') }}" class="btn-icon" title="Limpiar">✕</a>
</form>

<div class="tbl-wrap">
    <table class="kardex">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Movimiento</th>
                <th>Medicamento</th>
                <th>Lote</th>
                <th>Vence</th>
                <th class="num">Entrada</th>
                <th class="num">Salida</th>
                <th class="num">Saldo</th>
                <th class="num">Costo unit.</th>
                <th>Usuario</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimientos as $m)
            <tr>
                <td>{{ $m->fecha_movimiento?->format('d/m/Y H:i') }}</td>
                <td>
                    <span class="pill" style="background:{{ $m->tipo_color }}">{{ $m->tipo_label }}</span>
                </td>
                <td>{{ $m->medicamento->nombre ?? $m->lote->medicamento->nombre ?? '—' }}</td>
                <td><code class="lote">{{ $m->lote_codigo ?? $m->lote->lote ?? '—' }}</code></td>
                <td>{{ optional($m->fecha_vencimiento ?? $m->lote->fecha_vencimiento)->format('d/m/Y') ?? '—' }}</td>
                <td class="num ent">{{ $m->cantidad_entrada > 0 ? '+'.number_format($m->cantidad_entrada,2) : '' }}</td>
                <td class="num sal">{{ $m->cantidad_salida > 0 ? '-'.number_format($m->cantidad_salida,2) : '' }}</td>
                <td class="num saldo">{{ number_format((float)$m->stock_nuevo, 2) }}</td>
                <td class="num">{{ $m->costo_unitario ? '$ '.number_format($m->costo_unitario,0,',','.') : '—' }}</td>
                <td>{{ $m->usuario ? $m->usuario->name.' '.$m->usuario->apellido1 : '—' }}</td>
                <td style="max-width:240px; color:#64748b; font-size:.78rem;">{{ $m->observacion }}</td>
            </tr>
            @empty
            <tr class="empty-row"><td colspan="11">Sin movimientos para los filtros aplicados.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div style="margin-top:1rem">{{ $movimientos->links() }}</div>
</x-app-layout>
