<x-app-layout>
    <x-slot name="header"><h2>Reportes · Registro de actividad</h2></x-slot>

    <style>
        :root { --inst:#2e3a75; --rep:#0f766e; }
        .page-hero { background:linear-gradient(135deg,#2e3a75 0%, #0f766e 100%); color:#fff; border-radius:16px; padding:24px 28px; margin-bottom:22px; display:flex; align-items:center; gap:18px; box-shadow:0 10px 25px rgba(46,58,117,.18); }
        .page-hero .ic { font-size:3rem; }
        .page-hero h1 { margin:0; font-size:1.5rem; font-weight:800; }
        .page-hero p  { margin:4px 0 0; opacity:.85; font-size:.92rem; }

        .dash-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:16px; margin-bottom:24px; }
        .dash-card { background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 4px 12px rgba(0,0,0,.05); border-left:5px solid var(--inst); display:flex; align-items:center; justify-content:space-between; gap:14px; }
        .dash-card .label { color:#6b7280; font-size:.74rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
        .dash-card .value { color:var(--inst); font-size:1.7rem; font-weight:800; line-height:1; margin-top:4px; }
        .dash-card .ic { font-size:1.5rem; }
        .dash-card.green { border-left-color:#10b981; } .dash-card.green .value { color:#065f46; }
        .dash-card.amber { border-left-color:#f59e0b; } .dash-card.amber .value { color:#92400e; }
        .dash-card.blue  { border-left-color:#3b82f6; } .dash-card.blue  .value { color:#1e40af; }
        .dash-card.violet{ border-left-color:#7c3aed; } .dash-card.violet .value { color:#6b21a8; }

        .toolbar { background:#fff; border-radius:14px; padding:14px 18px; box-shadow:0 4px 12px rgba(0,0,0,.04); display:flex; gap:10px; flex-wrap:wrap; align-items:center; margin-bottom:18px; }
        .toolbar input, .toolbar select { padding:8px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; }
        .toolbar input:focus, .toolbar select:focus { border-color:var(--rep); }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:9px 14px; border-radius:8px; font-weight:600; font-size:.88rem; text-decoration:none; cursor:pointer; }

        .timeline { position:relative; padding:6px 0 0 28px; }
        .timeline::before { content:""; position:absolute; left:8px; top:0; bottom:0; width:3px; background:linear-gradient(180deg, #cbd5e1 0%, transparent 100%); border-radius:3px; }
        .ev { position:relative; background:#fff; border-radius:14px; padding:14px 18px; box-shadow:0 3px 10px rgba(0,0,0,.04); margin-bottom:14px; cursor:pointer; transition:transform .12s, box-shadow .12s; }
        .ev:hover { transform:translateY(-1px); box-shadow:0 8px 22px rgba(0,0,0,.08); }
        .ev::before { content:""; position:absolute; left:-25px; top:18px; width:14px; height:14px; border-radius:50%; background:var(--rep); border:3px solid #fff; box-shadow:0 0 0 2px var(--rep); }
        .ev.LOGIN::before    { background:#3b82f6; box-shadow:0 0 0 2px #3b82f6; }
        .ev.LOGOUT::before   { background:#94a3b8; box-shadow:0 0 0 2px #94a3b8; }
        .ev.CREATED::before  { background:#10b981; box-shadow:0 0 0 2px #10b981; }
        .ev.UPDATED::before  { background:#f59e0b; box-shadow:0 0 0 2px #f59e0b; }
        .ev.DELETED::before  { background:#ef4444; box-shadow:0 0 0 2px #ef4444; }

        .ev-head { display:flex; flex-wrap:wrap; align-items:center; gap:10px; margin-bottom:6px; }
        .badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:.7rem; font-weight:800; letter-spacing:.04em; text-transform:uppercase; border:1px solid transparent; }
        .b-LOGIN   { background:#dbeafe; color:#1e40af; border-color:#bfdbfe; }
        .b-LOGOUT  { background:#f1f5f9; color:#475569; border-color:#cbd5e1; }
        .b-CREATED { background:#d1fae5; color:#065f46; border-color:#a7f3d0; }
        .b-UPDATED { background:#fef3c7; color:#92400e; border-color:#fde68a; }
        .b-DELETED { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }

        .ev-meta { color:#64748b; font-size:.78rem; }
        .ev-desc { color:#0f172a; font-weight:600; font-size:.95rem; }
        .ev-user { display:inline-flex; align-items:center; gap:6px; color:#334155; font-size:.82rem; }
        .ev-user .av { width:24px; height:24px; border-radius:50%; background:linear-gradient(135deg,var(--rep),var(--inst)); color:#fff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:.7rem; }

        .empty { text-align:center; padding:40px; background:#fff; border-radius:14px; color:#6b7280; }

        /* Modal */
        .ovly { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:90; align-items:center; justify-content:center; padding:20px; }
        .ovly.open { display:flex; }
        .modal { background:#fff; max-width:780px; width:100%; max-height:86vh; overflow:auto; border-radius:16px; padding:24px 28px; box-shadow:0 20px 60px rgba(0,0,0,.3); }
        .modal h3 { margin:0 0 12px; color:var(--inst); font-size:1.1rem; }
        .kv { display:grid; grid-template-columns:160px 1fr; gap:6px 14px; font-size:.86rem; padding:8px 0; border-bottom:1px dashed #e5e7eb; }
        .kv b { color:#374151; font-weight:600; }
        pre { background:#0f172a; color:#a7f3d0; padding:14px; border-radius:8px; font-size:.78rem; overflow:auto; max-height:300px; }
    </style>

    <div class="page-hero">
        <div class="ic">🛡️</div>
        <div>
            <h1>Bitácora de actividad del sistema</h1>
            <p>Registro inmutable de inicios de sesión y todas las creaciones, ediciones y eliminaciones realizadas por los usuarios. <strong>Excluye Super Admin.</strong></p>
        </div>
    </div>

    <div class="dash-grid">
        <div class="dash-card violet"><div><div class="label">Total registros</div><div class="value">{{ number_format($stats['total']) }}</div></div><div class="ic">📚</div></div>
        <div class="dash-card blue"><div><div class="label">Eventos hoy</div><div class="value">{{ $stats['hoy'] }}</div></div><div class="ic">⏱️</div></div>
        <div class="dash-card green"><div><div class="label">Logins hoy</div><div class="value">{{ $stats['logins'] }}</div></div><div class="ic">🔓</div></div>
        <div class="dash-card amber"><div><div class="label">Cambios hoy</div><div class="value">{{ $stats['cambios'] }}</div></div><div class="ic">✏️</div></div>
        <div class="dash-card"><div><div class="label">Usuarios activos hoy</div><div class="value">{{ $stats['usuarios'] }}</div></div><div class="ic">👥</div></div>
    </div>

    <form method="GET" class="toolbar">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar acción, usuario, ruta..." style="flex:1; min-width:220px;">
        <select name="event">
            <option value="">Todos los eventos</option>
            @foreach(\App\Models\ActivityLog::EVENTOS as $k => $l)
                <option value="{{ $k }}" {{ request('event')==$k?'selected':'' }}>{{ $l }}</option>
            @endforeach
        </select>
        <select name="user_id">
            <option value="">Todos los usuarios</option>
            @foreach($usuarios as $u)
                <option value="{{ $u->id }}" {{ request('user_id')==$u->id?'selected':'' }}>{{ $u->name }}</option>
            @endforeach
        </select>
        <input type="date" name="desde" value="{{ request('desde') }}">
        <input type="date" name="hasta" value="{{ request('hasta') }}">
        <button type="submit" class="btn-outline">Filtrar</button>
        <a href="{{ route('admin.reportes.registros') }}" class="btn-outline">Limpiar</a>
    </form>

    @if($logs->count() === 0)
        <div class="empty">No hay actividad registrada con esos filtros.</div>
    @else
        <div class="timeline">
            @foreach($logs as $l)
                <div class="ev {{ $l->event }}" onclick="abrirDetalle({{ $l->id }})">
                    <div class="ev-head">
                        <span class="badge b-{{ $l->event }}">{{ $l->evento_label }}</span>
                        @if($l->model_short)
                            <span style="font-size:.78rem; color:#64748b;">📦 {{ $l->model_short }}#{{ $l->model_id }}</span>
                        @endif
                        <span class="ev-meta">{{ optional($l->created_at)->format('d/m/Y H:i:s') }} · {{ $l->ip ?? '—' }}</span>
                    </div>
                    <div class="ev-desc">{{ $l->description ?? '(sin descripción)' }}</div>
                    <div style="margin-top:6px; display:flex; flex-wrap:wrap; gap:14px;">
                        <div class="ev-user">
                            <span class="av">{{ strtoupper(substr($l->user_name ?? '?', 0, 2)) }}</span>
                            <span><strong>{{ $l->user_name ?? 'Sistema' }}</strong> @if($l->role_name) · <span style="color:#64748b;">{{ $l->role_name }}</span> @endif</span>
                        </div>
                        @if($l->method || $l->route)
                            <span class="ev-meta">⚙️ {{ $l->method }} · {{ $l->route ?? '—' }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div style="margin-top:18px;">{{ $logs->links() }}</div>
    @endif

    <div class="ovly" id="ovly" onclick="if(event.target===this)cerrarDetalle()">
        <div class="modal" id="detalleModal">
            <h3 id="detTitle">Detalle del evento</h3>
            <div id="detBody"></div>
            <div style="text-align:right; margin-top:14px;">
                <button class="btn-outline" onclick="cerrarDetalle()">Cerrar</button>
            </div>
        </div>
    </div>

    <script>
        async function abrirDetalle(id) {
            try {
                const res = await fetch(`{{ url('admin/reportes/registros') }}/${id}`, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) throw new Error('No se pudo cargar');
                const log = await res.json();
                const body = document.getElementById('detBody');
                let html = '';
                html += `<div class="kv"><b>Evento</b><span>${log.event} - ${log.description ?? ''}</span></div>`;
                html += `<div class="kv"><b>Usuario</b><span>${log.user_name ?? '—'} (#${log.user_id ?? '—'}) · ${log.role_name ?? ''}</span></div>`;
                html += `<div class="kv"><b>Modelo</b><span>${log.model_type ?? '—'} #${log.model_id ?? ''}</span></div>`;
                html += `<div class="kv"><b>Ruta</b><span>${log.method ?? ''} ${log.route ?? ''}</span></div>`;
                html += `<div class="kv"><b>URL</b><span style="word-break:break-all;">${log.url ?? '—'}</span></div>`;
                html += `<div class="kv"><b>IP / Navegador</b><span>${log.ip ?? '—'} · ${log.user_agent ?? ''}</span></div>`;
                html += `<div class="kv"><b>Fecha</b><span>${log.created_at ?? ''}</span></div>`;
                if (log.changes) {
                    html += `<h4 style="margin:14px 0 8px; color:#0f172a;">Cambios</h4>`;
                    html += `<pre>${JSON.stringify(log.changes, null, 2)}</pre>`;
                }
                body.innerHTML = html;
                document.getElementById('ovly').classList.add('open');
            } catch (e) {
                alert('No se pudo cargar el detalle.');
            }
        }
        function cerrarDetalle(){ document.getElementById('ovly').classList.remove('open'); }
    </script>
</x-app-layout>
