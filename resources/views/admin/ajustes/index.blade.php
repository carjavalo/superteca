<x-app-layout>
    <x-slot name="header"><h2>Inventario · Ajustes</h2></x-slot>

    <style>
        :root { --inst:#2e3a75; --ajuste:#7c3aed; }
        .st-alert { padding:14px 18px; border-radius:10px; margin-bottom:20px; font-size:.92rem; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:5px solid #10b981; }
        .st-alert.error   { background:#fee2e2; color:#991b1b; border-left:5px solid #ef4444; }

        .page-hero { background:linear-gradient(135deg,#2e3a75 0%, #7c3aed 100%); color:#fff; border-radius:16px; padding:24px 28px; margin-bottom:22px; display:flex; align-items:center; gap:18px; box-shadow:0 10px 25px rgba(46,58,117,.18); }
        .page-hero .ic { font-size:3rem; }
        .page-hero h1 { margin:0; font-size:1.5rem; font-weight:800; }
        .page-hero p { margin:4px 0 0; opacity:.85; font-size:.92rem; }

        .dash-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:24px; }
        .dash-card { background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 4px 12px rgba(0,0,0,.05); border-left:5px solid var(--inst); display:flex; align-items:center; justify-content:space-between; gap:14px; }
        .dash-card .label { color:#6b7280; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
        .dash-card .value { color:var(--inst); font-size:1.85rem; font-weight:800; line-height:1; margin-top:4px; }
        .dash-card .ic { font-size:1.6rem; }
        .dash-card.green { border-left-color:#10b981; } .dash-card.green .value{color:#065f46;}
        .dash-card.amber { border-left-color:#f59e0b; } .dash-card.amber .value{color:#92400e;}
        .dash-card.red   { border-left-color:#ef4444; } .dash-card.red .value{color:#991b1b;}
        .dash-card.violet{ border-left-color:#7c3aed; } .dash-card.violet .value{color:#6b21a8;}

        .toolbar { background:#fff; border-radius:14px; padding:16px 20px; box-shadow:0 4px 12px rgba(0,0,0,.04); display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:18px; }
        .toolbar form { display:flex; gap:10px; flex-wrap:wrap; align-items:center; flex:1; }
        .toolbar input, .toolbar select { padding:8px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; }
        .toolbar input:focus, .toolbar select:focus { border-color:var(--ajuste); }

        .btn-primary { background:var(--ajuste); color:#fff; border:none; cursor:pointer; padding:10px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; gap:7px; text-decoration:none; }
        .btn-primary:hover { background:#6b21a8; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:10px 16px; border-radius:8px; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; cursor:pointer; }
        .btn-icon { width:32px; height:32px; border-radius:8px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; }
        .btn-view { background:#f5f3ff; color:#7c3aed; } .btn-view:hover { background:#7c3aed; color:#fff; }
        .btn-del  { background:#fee2e2; color:#dc2626; } .btn-del:hover  { background:#dc2626; color:#fff; }

        .table-wrap { background:#fff; border-radius:14px; box-shadow:0 6px 14px rgba(0,0,0,.04); overflow:hidden; }
        table { width:100%; border-collapse:collapse; font-size:.92rem; }
        th { text-align:left; padding:13px 14px; background:#f9fafb; color:#374151; font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid #e5e7eb; }
        td { padding:14px; border-top:1px solid #f3f4f6; color:#4b5563; }
        tr:hover td { background:#fafbff; }

        .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; border:1px solid transparent; }
        .b-POSITIVO { background:#d1fae5; color:#065f46; border-color:#a7f3d0; }
        .b-NEGATIVO { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }

        .est { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
        .est-BORRADOR { background:#fef9c3; color:#854d0e; border:1px solid #fde68a; }
        .est-APROBADO { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
        .est-ANULADO  { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }

        .sem { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:6px; }
        .sem-green  { background:#10b981; }
        .sem-amber  { background:#f59e0b; }
        .sem-red    { background:#ef4444; }
    </style>

    @if(session('success')) <div class="st-alert success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="st-alert error">{{ session('error') }}</div> @endif

    <div class="page-hero">
        <div class="ic">⚖️</div>
        <div>
            <h1>Ajustes de inventario auditados</h1>
            <p>Corrige diferencias entre stock físico y sistema · Cada ajuste genera Kardex y queda firmado por solicitante y aprobador.</p>
        </div>
    </div>

    <div class="dash-grid">
        <div class="dash-card violet"><div><div class="label">Ajustes hoy</div><div class="value">{{ $stats['hoy'] }}</div></div><div class="ic">📊</div></div>
        <div class="dash-card green"><div><div class="label">Positivos (mes)</div><div class="value">{{ $stats['positivos'] }}</div></div><div class="ic">➕</div></div>
        <div class="dash-card red"><div><div class="label">Negativos (mes)</div><div class="value">{{ $stats['negativos'] }}</div></div><div class="ic">➖</div></div>
        <div class="dash-card amber"><div><div class="label">Pendientes aprobación</div><div class="value">{{ $stats['pendientes'] }}</div></div><div class="ic">⏳</div></div>
        <div class="dash-card"><div><div class="label">Valor ajustado (mes)</div><div class="value">$ {{ number_format($stats['valor_mes'], 0) }}</div></div><div class="ic">💰</div></div>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.ajustes.index') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar código u observación...">
            <select name="tipo">
                <option value="">Todos los tipos</option>
                @foreach(\App\Models\AjusteInventario::TIPOS as $k => $l)
                    <option value="{{ $k }}" {{ request('tipo')==$k?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <select name="estado">
                <option value="">Todos los estados</option>
                @foreach(\App\Models\AjusteInventario::ESTADOS as $k => $l)
                    <option value="{{ $k }}" {{ request('estado')==$k?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <input type="date" name="desde" value="{{ request('desde') }}">
            <input type="date" name="hasta" value="{{ request('hasta') }}">
            <button type="submit" class="btn-outline">Filtrar</button>
            <a href="{{ route('admin.ajustes.index') }}" class="btn-outline">Limpiar</a>
        </form>
        <a href="{{ route('admin.ajustes.create') }}" class="btn-primary">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nuevo Ajuste
        </a>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Fecha</th>
                    <th>Motivo</th>
                    <th>Solicitante</th>
                    <th style="text-align:center;">Ítems</th>
                    <th style="text-align:right;">Valor</th>
                    <th>Estado</th>
                    <th style="text-align:right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ajustes as $a)
                    @php
                        $sem = $a->valor_total < 100000 ? 'sem-green' : ($a->valor_total < 1000000 ? 'sem-amber' : 'sem-red');
                    @endphp
                    <tr>
                        <td><strong style="color:var(--ajuste);">{{ $a->codigo }}</strong></td>
                        <td><span class="badge b-{{ $a->tipo_ajuste }}">{{ $a->tipo_ajuste }}</span></td>
                        <td>{{ optional($a->fecha_ajuste)->format('d/m/Y H:i') }}</td>
                        <td>{{ $a->motivo->nombre ?? '—' }}</td>
                        <td>{{ $a->solicitante->name ?? '—' }}</td>
                        <td style="text-align:center;"><strong>{{ $a->detalles_count }}</strong></td>
                        <td style="text-align:right;"><span class="sem {{ $sem }}"></span><strong>$ {{ number_format($a->valor_total, 0) }}</strong></td>
                        <td><span class="est est-{{ $a->estado }}">● {{ $a->estado_label }}</span></td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:6px;">
                                <a href="{{ route('admin.ajustes.show', $a) }}" class="btn-icon btn-view" title="Ver">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($a->estado === 'BORRADOR')
                                    <form action="{{ route('admin.ajustes.destroy', $a) }}" method="POST" onsubmit="return confirm('¿Eliminar este ajuste en borrador?')" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon btn-del" title="Eliminar">
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center; padding:3rem; color:#6b7280;">No hay ajustes registrados con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">{{ $ajustes->links() }}</div>
</x-app-layout>
