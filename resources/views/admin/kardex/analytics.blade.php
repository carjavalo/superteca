<x-app-layout>
<x-slot name="header"><h2>Kardex · Dashboard Analítico</h2></x-slot>

<style>
    :root { --inst:#2e3a75; }
    .page-hero { background:linear-gradient(135deg,#2e3a75 0%,#0ea5e9 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.4rem; display:flex; justify-content:space-between; flex-wrap:wrap; gap:1rem; align-items:center; }
    .page-hero h1 { margin:0; font-size:1.4rem; font-weight:700; }
    .page-hero p { margin:.25rem 0 0; opacity:.85; font-size:.85rem; }
    .tab-bar { display:flex; gap:.4rem; margin-bottom:1rem; }
    .tab-bar a { padding:.45rem 1rem; border-radius:8px; text-decoration:none; font-size:.85rem; color:#64748b; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.05); transition:all .2s; }
    .tab-bar a.active, .tab-bar a:hover { background:var(--inst); color:#fff; }

    .filters { background:#fff; border-radius:10px; padding:1rem; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:1.2rem; display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; }
    .filters input { border:1.5px solid #e2e8f0; border-radius:7px; padding:.4rem .75rem; font-size:.85rem; }
    .btn-primary { background:var(--inst); color:#fff; border:none; border-radius:8px; padding:.45rem 1.1rem; font-size:.85rem; cursor:pointer; }

    .kpi-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.4rem; }
    .kpi-card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid var(--inst); }
    .kpi-card.green { border-color:#22c55e; } .kpi-card.red { border-color:#ef4444; } .kpi-card.purple { border-color:#a855f7; }
    .kpi-card .num { font-size:1.7rem; font-weight:800; color:#1e293b; line-height:1; }
    .kpi-card .lbl { font-size:.72rem; color:#64748b; text-transform:uppercase; margin-top:.3rem; letter-spacing:.04em; }

    .grid-2 { display:grid; grid-template-columns:repeat(auto-fit,minmax(420px,1fr)); gap:1.2rem; margin-bottom:1.2rem; }
    .panel { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); padding:1.1rem 1.2rem; }
    .panel h3 { margin:0 0 1rem; font-size:.95rem; color:var(--inst); border-bottom:1px solid #f1f5f9; padding-bottom:.5rem; }

    .bar-row { display:flex; align-items:center; gap:.6rem; margin-bottom:.45rem; font-size:.82rem; }
    .bar-row .nm { flex:0 0 30%; color:#1e293b; font-weight:500; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .bar-row .bar { flex:1; height:18px; border-radius:6px; background:#f1f5f9; overflow:hidden; position:relative; }
    .bar-row .bar > span { display:block; height:100%; border-radius:6px; }
    .bar-row .val { flex:0 0 70px; text-align:right; font-weight:700; color:#1e293b; font-variant-numeric:tabular-nums; }

    table.mini { width:100%; border-collapse:collapse; font-size:.82rem; }
    table.mini th { background:#f8fafc; padding:.55rem .7rem; text-align:left; color:#475569; font-weight:600; border-bottom:1px solid #e2e8f0; }
    table.mini td { padding:.55rem .7rem; border-bottom:1px solid #f1f5f9; }
    .pill { display:inline-block; padding:.18rem .55rem; border-radius:14px; font-size:.7rem; font-weight:700; color:#fff; }
    .vence-soon { color:#dc2626; font-weight:700; }
    .empty { padding:1.5rem; text-align:center; color:#94a3b8; font-style:italic; }

    /* Sparkline / barras por día */
    .daily-chart { display:flex; align-items:flex-end; gap:3px; height:120px; padding:.5rem 0; }
    .daily-chart .col { flex:1; min-width:6px; background:linear-gradient(to top,#2e3a75,#7c3aed); border-radius:3px 3px 0 0; position:relative; transition:opacity .2s; }
    .daily-chart .col:hover { opacity:.75; }
    .daily-chart .col[title]:hover::after { content:attr(title); position:absolute; bottom:100%; left:50%; transform:translateX(-50%); background:#1e293b; color:#fff; padding:.2rem .5rem; border-radius:4px; font-size:.7rem; white-space:nowrap; z-index:10; }
</style>

<div class="page-hero">
    <div>
        <h1>📊 Dashboard de Kardex</h1>
        <p>Indicadores gerenciales · Auditoría · Consumo · Trazabilidad</p>
    </div>
</div>

<div class="tab-bar">
    <a href="{{ route('admin.kardex.index') }}">Kardex General</a>
    <a href="{{ route('admin.kardex.lote') }}">Trazabilidad por Lote</a>
    <a href="{{ route('admin.kardex.analytics') }}" class="active">Dashboard Analítico</a>
</div>

<form method="GET" class="filters">
    <label style="font-size:.82rem;color:#64748b">Desde</label>
    <input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}">
    <label style="font-size:.82rem;color:#64748b">Hasta</label>
    <input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}">
    <button class="btn-primary">Aplicar</button>
    <span style="margin-left:auto; font-size:.78rem; color:#64748b">
        Período: {{ $desde->format('d/m/Y') }} → {{ $hasta->format('d/m/Y') }}
    </span>
</form>

<div class="kpi-row">
    <div class="kpi-card">
        <div class="num">{{ number_format($kpis['movimientos']) }}</div>
        <div class="lbl">Movimientos en el período</div>
    </div>
    <div class="kpi-card green">
        <div class="num">+{{ number_format($kpis['entradas'], 0) }}</div>
        <div class="lbl">Unidades ingresadas</div>
    </div>
    <div class="kpi-card red">
        <div class="num">-{{ number_format($kpis['salidas'], 0) }}</div>
        <div class="lbl">Unidades despachadas</div>
    </div>
    <div class="kpi-card purple">
        <div class="num">$ {{ number_format($kpis['valor_movido'], 0, ',', '.') }}</div>
        <div class="lbl">Valor movido</div>
    </div>
</div>

<div class="grid-2">
    <div class="panel">
        <h3>📈 Movimientos por día</h3>
        @php
            $dias = $porDia->groupBy('fecha')->map(fn($g) => $g->sum('cnt'));
            $max = max(1, $dias->max() ?? 1);
        @endphp
        @if($dias->isEmpty())
            <div class="empty">Sin movimientos en el período</div>
        @else
            <div class="daily-chart">
                @foreach($dias as $fecha => $cnt)
                    <div class="col" style="height:{{ ($cnt/$max)*100 }}%" title="{{ $fecha }} · {{ $cnt }} mov."></div>
                @endforeach
            </div>
            <div style="display:flex; justify-content:space-between; font-size:.7rem; color:#94a3b8; margin-top:.4rem;">
                <span>{{ $dias->keys()->first() }}</span>
                <span>{{ $dias->keys()->last() }}</span>
            </div>
        @endif
    </div>

    <div class="panel">
        <h3>🥇 Top medicamentos consumidos</h3>
        @if($topConsumo->isEmpty())
            <div class="empty">Sin salidas en el período</div>
        @else
            @php $maxC = max(1, $topConsumo->max('total')); @endphp
            @foreach($topConsumo as $t)
                <div class="bar-row">
                    <div class="nm" title="{{ $t->medicamento_nombre ?? '—' }}">{{ $t->medicamento_nombre ?? 'Sin nombre' }}</div>
                    <div class="bar"><span style="width:{{ ($t->total/$maxC)*100 }}%; background:linear-gradient(to right,#ef4444,#f97316)"></span></div>
                    <div class="val">{{ number_format($t->total, 0) }}</div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="panel">
        <h3>🥧 Distribución por tipo de movimiento</h3>
        @if($distTipos->isEmpty())
            <div class="empty">Sin datos</div>
        @else
            @php $maxT = max(1, $distTipos->max('cnt')); @endphp
            @foreach($distTipos as $d)
                @php $cfg = \App\Models\MovimientoInventario::TIPOS[$d->tipo_movimiento] ?? ['label' => $d->tipo_movimiento, 'color' => '#94a3b8']; @endphp
                <div class="bar-row">
                    <div class="nm">{{ $cfg['label'] }}</div>
                    <div class="bar"><span style="width:{{ ($d->cnt/$maxT)*100 }}%; background:{{ $cfg['color'] }}"></span></div>
                    <div class="val">{{ $d->cnt }}</div>
                </div>
            @endforeach
        @endif
    </div>

    <div class="panel">
        <h3>👥 Ajustes por usuario</h3>
        @if($ajustesUsuario->isEmpty())
            <div class="empty">Sin ajustes en el período</div>
        @else
            <table class="mini">
                <thead><tr><th>Usuario</th><th style="text-align:right">Ajustes</th></tr></thead>
                <tbody>
                @foreach($ajustesUsuario as $u)
                    <tr>
                        <td>{{ $u->name ? trim($u->name.' '.$u->apellido1) : 'Sistema' }}</td>
                        <td style="text-align:right; font-weight:700">{{ $u->cnt }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

<div class="panel">
    <h3>⏰ Vencimientos próximos (90 días)</h3>
    @if($vencimientos->isEmpty())
        <div class="empty">No hay lotes con vencimiento en los próximos 90 días.</div>
    @else
        <table class="mini">
            <thead>
                <tr>
                    <th>Medicamento</th>
                    <th>Lote</th>
                    <th>Vence</th>
                    <th style="text-align:right">Días</th>
                    <th style="text-align:right">Stock</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($vencimientos as $v)
                @php $dias = (int) now()->diffInDays($v->fecha_vencimiento, false); @endphp
                <tr>
                    <td>{{ $v->medicamento->nombre ?? '—' }}</td>
                    <td><code style="background:#f1f5f9; padding:.1rem .4rem; border-radius:4px">{{ $v->lote }}</code></td>
                    <td>{{ \Carbon\Carbon::parse($v->fecha_vencimiento)->format('d/m/Y') }}</td>
                    <td style="text-align:right" class="{{ $dias < 30 ? 'vence-soon' : '' }}">{{ $dias }}d</td>
                    <td style="text-align:right">{{ rtrim(rtrim(number_format($v->cantidad_actual,2,'.',''),'0'),'.') }}</td>
                    <td><a href="{{ route('admin.kardex.lote', ['lote_id' => $v->id]) }}" style="font-size:.78rem; color:var(--inst)">Ver trazabilidad →</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
</x-app-layout>
