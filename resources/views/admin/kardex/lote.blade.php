<x-app-layout>
<x-slot name="header"><h2>Kardex · Trazabilidad por Lote</h2></x-slot>

<style>
    :root { --inst:#2e3a75; --inst-dark:#1f2a5c; }

    .page-hero { background:linear-gradient(135deg,#2e3a75 0%,#7c3aed 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.4rem; }
    .page-hero h1 { margin:0; font-size:1.4rem; font-weight:700; }
    .page-hero p { margin:.3rem 0 0; opacity:.85; font-size:.85rem; }

    .tab-bar { display:flex; gap:.4rem; margin-bottom:1rem; }
    .tab-bar a { padding:.45rem 1rem; border-radius:8px; text-decoration:none; font-size:.85rem; color:#64748b; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.05); transition:all .2s; }
    .tab-bar a.active, .tab-bar a:hover { background:var(--inst); color:#fff; }

    .layout { display:grid; grid-template-columns: 320px 1fr; gap:1.4rem; }
    @media (max-width:900px) { .layout { grid-template-columns:1fr; } }

    .panel { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); padding:1rem; }
    .panel h3 { margin:0 0 .8rem; font-size:.95rem; color:var(--inst); border-bottom:1px solid #f1f5f9; padding-bottom:.5rem; }

    .lote-search { width:100%; padding:.5rem .75rem; border:1.5px solid #e2e8f0; border-radius:7px; margin-bottom:.7rem; font-size:.85rem; }
    .lote-list { max-height:65vh; overflow:auto; }
    .lote-item { display:block; padding:.65rem .75rem; border-radius:8px; text-decoration:none; color:#1e293b; font-size:.82rem; margin-bottom:.3rem; border:1px solid transparent; transition:all .15s; }
    .lote-item:hover { background:#f1f5f9; border-color:#e2e8f0; }
    .lote-item.active { background:var(--inst); color:#fff; border-color:var(--inst); }
    .lote-item .nm { font-weight:600; display:block; }
    .lote-item .sub { font-size:.7rem; opacity:.7; }

    .summary-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:.8rem; margin-bottom:1.2rem; }
    .summary-card { background:#fff; border-radius:10px; padding:.8rem 1rem; box-shadow:0 2px 6px rgba(0,0,0,.06); border-left:4px solid #94a3b8; }
    .summary-card .num { font-size:1.4rem; font-weight:800; color:#1e293b; }
    .summary-card .lbl { font-size:.7rem; text-transform:uppercase; color:#64748b; letter-spacing:.04em; }
    .summary-card.in   { border-color:#22c55e; }
    .summary-card.out  { border-color:#ef4444; }
    .summary-card.adj  { border-color:#f59e0b; }
    .summary-card.bal  { border-color:#2e3a75; }

    .timeline { position:relative; padding-left:2rem; }
    .timeline::before { content:''; position:absolute; left:9px; top:0; bottom:0; width:3px; background:linear-gradient(to bottom,#2e3a75,#7c3aed); border-radius:2px; }
    .tl-item { position:relative; padding-bottom:1.4rem; }
    .tl-dot { position:absolute; left:-1.85rem; top:.4rem; width:18px; height:18px; border-radius:50%; border:3px solid #fff; box-shadow:0 0 0 2px #cbd5e1; }
    .tl-card { background:#fff; border-radius:10px; padding:.85rem 1.1rem; box-shadow:0 2px 6px rgba(0,0,0,.06); border-left:4px solid #94a3b8; }
    .tl-card .head { display:flex; justify-content:space-between; align-items:center; gap:.6rem; flex-wrap:wrap; margin-bottom:.4rem; }
    .tl-card .pill { display:inline-block; padding:.2rem .6rem; border-radius:14px; font-size:.7rem; font-weight:700; color:#fff; }
    .tl-card .meta { font-size:.78rem; color:#64748b; }
    .tl-card .qty { font-weight:800; font-size:1rem; }
    .tl-card .qty.in { color:#16a34a; }
    .tl-card .qty.out { color:#dc2626; }
    .empty { padding:2rem; text-align:center; color:#94a3b8; font-style:italic; background:#f8fafc; border-radius:10px; }

    .lote-info { background:linear-gradient(135deg,#f0f9ff 0%,#fae8ff 100%); border:1px solid #e2e8f0; border-radius:12px; padding:1rem 1.2rem; margin-bottom:1.2rem; }
    .lote-info h2 { margin:0 0 .4rem; color:var(--inst); font-size:1.15rem; }
    .lote-info .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(140px,1fr)); gap:.6rem; font-size:.82rem; }
    .lote-info .grid div b { display:block; color:#64748b; font-size:.7rem; text-transform:uppercase; letter-spacing:.04em; font-weight:600; }
</style>

<div class="page-hero">
    <h1>🧬 Trazabilidad por Lote</h1>
    <p>Línea de tiempo completa de cada lote — útil para auditorías INVIMA y respuesta a recalls.</p>
</div>

<div class="tab-bar">
    <a href="{{ route('admin.kardex.index') }}">Kardex General</a>
    <a href="{{ route('admin.kardex.lote') }}" class="active">Trazabilidad por Lote</a>
    <a href="{{ route('admin.kardex.analytics') }}">Dashboard Analítico</a>
</div>

<div class="layout">
    <div class="panel">
        <h3>Lotes</h3>
        <input type="text" class="lote-search" placeholder="Buscar lote o medicamento…" oninput="filterLotes(this.value)">
        <div class="lote-list" id="loteList">
            @foreach($lotes as $l)
            <a href="{{ route('admin.kardex.lote', ['lote_id' => $l->id]) }}"
               class="lote-item {{ $loteSeleccionado && $loteSeleccionado->id === $l->id ? 'active' : '' }}"
               data-search="{{ strtolower(($l->medicamento->nombre ?? '').' '.$l->lote) }}">
                <span class="nm">{{ $l->lote }}</span>
                <span class="sub">{{ $l->medicamento->nombre ?? '—' }} · Stock {{ rtrim(rtrim(number_format($l->cantidad_actual,2,'.',''), '0'), '.') }}</span>
            </a>
            @endforeach
        </div>
    </div>

    <div>
        @if(!$loteSeleccionado)
            <div class="panel">
                <div class="empty">Selecciona un lote del panel izquierdo para ver su línea de tiempo de trazabilidad.</div>
            </div>
        @else
            <div class="lote-info">
                <h2>{{ $loteSeleccionado->medicamento->nombre ?? 'Sin medicamento' }} — Lote <code style="background:#fff;padding:.1rem .5rem;border-radius:5px">{{ $loteSeleccionado->lote }}</code></h2>
                <div class="grid">
                    <div><b>Presentación</b>{{ $loteSeleccionado->presentacion->nombre ?? '—' }}</div>
                    <div><b>Vencimiento</b>{{ $loteSeleccionado->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</div>
                    <div><b>Proveedor</b>{{ $loteSeleccionado->proveedor->nombre ?? '—' }}</div>
                    <div><b>Costo unitario</b>$ {{ number_format($loteSeleccionado->costo_unitario, 0, ',', '.') }}</div>
                    <div><b>Ubicación</b>{{ $loteSeleccionado->ubicacion ?? '—' }}</div>
                </div>
            </div>

            <div class="summary-row">
                <div class="summary-card in">
                    <div class="num">+{{ number_format($resumen['entradas'], 0) }}</div>
                    <div class="lbl">Total Entradas</div>
                </div>
                <div class="summary-card out">
                    <div class="num">-{{ number_format($resumen['salidas'], 0) }}</div>
                    <div class="lbl">Total Salidas</div>
                </div>
                <div class="summary-card adj">
                    <div class="num">{{ $resumen['ajustes'] }}</div>
                    <div class="lbl">Ajustes</div>
                </div>
                <div class="summary-card bal">
                    <div class="num">{{ number_format($resumen['saldo'], 0) }}</div>
                    <div class="lbl">Saldo actual</div>
                </div>
                <div class="summary-card bal">
                    <div class="num">$ {{ number_format($resumen['valor'], 0, ',', '.') }}</div>
                    <div class="lbl">Valor lote</div>
                </div>
            </div>

            <h3 style="color:var(--inst);margin:0 0 1rem">📍 Línea de tiempo</h3>
            @if($movimientos->isEmpty())
                <div class="empty">Este lote aún no tiene movimientos registrados.</div>
            @else
            <div class="timeline">
                @foreach($movimientos as $m)
                <div class="tl-item">
                    <div class="tl-dot" style="background:{{ $m->tipo_color }}; box-shadow:0 0 0 2px {{ $m->tipo_color }}"></div>
                    <div class="tl-card" style="border-left-color:{{ $m->tipo_color }}">
                        <div class="head">
                            <div>
                                <span class="pill" style="background:{{ $m->tipo_color }}">{{ $m->tipo_label }}</span>
                                <span class="meta" style="margin-left:.5rem">{{ $m->fecha_movimiento?->format('d M Y · H:i') }}</span>
                            </div>
                            @if($m->cantidad > 0)
                                <span class="qty in">+{{ number_format($m->cantidad, 2) }}</span>
                            @else
                                <span class="qty out">{{ number_format($m->cantidad, 2) }}</span>
                            @endif
                        </div>
                        <div class="meta">
                            @if($m->usuario) 👤 {{ $m->usuario->name }} {{ $m->usuario->apellido1 }} · @endif
                            Saldo posterior: <b>{{ number_format((float)$m->stock_nuevo, 2) }}</b>
                            @if($m->bodegaOrigen) · Origen: {{ $m->bodegaOrigen->nombre }} @endif
                            @if($m->bodegaDestino) · Destino: {{ $m->bodegaDestino->nombre }} @endif
                        </div>
                        @if($m->observacion)
                            <div class="meta" style="margin-top:.4rem; font-style:italic;">💬 {{ $m->observacion }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        @endif
    </div>
</div>

<script>
function filterLotes(term) {
    term = term.toLowerCase();
    document.querySelectorAll('#loteList .lote-item').forEach(it => {
        it.style.display = it.dataset.search.includes(term) ? '' : 'none';
    });
}
</script>
</x-app-layout>
