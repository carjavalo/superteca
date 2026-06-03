<style>
    :root { --calq:#0f766e; --calq2:#0d9488; }
    .page-header { background: linear-gradient(135deg, var(--calq2) 0%, var(--calq) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }

    .nav-tabs { display:flex; gap:.4rem; margin-bottom:1.2rem; flex-wrap:wrap; }
    .nav-tabs a { padding:.55rem 1rem; border-radius:8px; text-decoration:none; font-size:.82rem; font-weight:600; color:#475569; background:#fff; border:1px solid #e2e8f0; }
    .nav-tabs a.active { background:var(--calq2); color:#fff; border-color:var(--calq2); }

    .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:1rem; margin-bottom:1.2rem; }
    .kpi { background:#fff; border-radius:12px; padding:1rem 1.1rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid var(--calq2); }
    .kpi.k-ok { border-left-color:#22c55e; }
    .kpi.k-cond { border-left-color:#f59e0b; }
    .kpi.k-rech { border-left-color:#ef4444; }
    .kpi.k-pend { border-left-color:#6366f1; }
    .kpi.k-q { border-left-color:#a855f7; }
    .kpi.k-blk { border-left-color:#0f172a; }
    .kpi .lbl { font-size:.7rem; color:#94a3b8; text-transform:uppercase; font-weight:600; }
    .kpi .val { font-size:1.7rem; font-weight:700; color:#1e293b; margin-top:.2rem; }

    .card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1.2rem; }
    .card h3 { margin:0 0 .8rem; font-size:.95rem; color:#0f172a; }

    .grid-2 { display:grid; grid-template-columns: 1.4fr 1fr; gap:1.2rem; }
    @media(max-width:1100px){ .grid-2 { grid-template-columns:1fr; } }

    .sem-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:.9rem; }
    .sem-card { padding:.9rem 1rem; border-radius:10px; border:2px solid #e2e8f0; background:#fff; }
    .sem-card.s-VERDE { border-color:#22c55e; }
    .sem-card.s-AMARILLO { border-color:#facc15; }
    .sem-card.s-ROJO { border-color:#ef4444; background:#fef2f2; }
    .sem-card.s-GRIS { border-color:#cbd5e1; opacity:.75; }
    .sem-card .nom { font-weight:700; font-size:.9rem; color:#0f172a; }
    .sem-card .meta { font-size:.7rem; color:#64748b; margin-top:.15rem; }
    .sem-card .nums { display:flex; gap:.5rem; margin-top:.5rem; flex-wrap:wrap; font-size:.7rem; }
    .sem-card .nums span { padding:.1rem .4rem; border-radius:6px; background:#f1f5f9; color:#334155; font-weight:600; }
    .sem-card .nums .ok { background:#dcfce7; color:#166534; }
    .sem-card .nums .co { background:#fef3c7; color:#854d0e; }
    .sem-card .nums .re { background:#fee2e2; color:#991b1b; }
    .sem-card .nums .pe { background:#e0e7ff; color:#3730a3; }

    .dot { width:12px; height:12px; border-radius:50%; display:inline-block; margin-right:.3rem; vertical-align:middle; }
    .d-VERDE { background:#22c55e; }
    .d-AMARILLO { background:#facc15; }
    .d-ROJO { background:#ef4444; }
    .d-GRIS { background:#94a3b8; }

    .badge { display:inline-block; padding:.15rem .6rem; border-radius:8px; font-size:.66rem; font-weight:700; letter-spacing:.3px; }
    .b-APROBADO,.b-LIBERADO   { background:#dcfce7; color:#166534; }
    .b-CONDICIONAL,.b-CUARENTENA { background:#fef3c7; color:#854d0e; }
    .b-RECHAZADO,.b-BLOQUEADO { background:#fee2e2; color:#991b1b; }
    .b-PENDIENTE { background:#e0e7ff; color:#3730a3; }
    .b-DESECHADO { background:#1e293b; color:#fff; }
    .b-ABIERTA { background:#fee2e2; color:#991b1b; }
    .b-EN_PROCESO { background:#fef3c7; color:#854d0e; }
    .b-CERRADA { background:#dcfce7; color:#166534; }

    table.t { width:100%; border-collapse:collapse; font-size:.82rem; }
    table.t th { background:#f8fafc; text-align:left; padding:.55rem .6rem; border-bottom:2px solid #e2e8f0; font-weight:600; color:#475569; }
    table.t td { padding:.5rem .6rem; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    table.t tr:hover { background:#f8fafc; }

    .form-row { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:1rem; margin-bottom:1rem; }
    .form-group label { display:block; font-size:.78rem; font-weight:600; color:#334155; margin-bottom:.3rem; }
    .form-group input, .form-group select, .form-group textarea {
        width:100%; padding:.55rem .7rem; border:1px solid #e2e8f0; border-radius:8px; font-size:.85rem; font-family:inherit;
    }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline:none; border-color:var(--calq2); box-shadow:0 0 0 3px rgba(13,148,136,.15); }

    .btn { padding:.55rem 1rem; border-radius:8px; border:none; font-weight:600; cursor:pointer; font-size:.85rem; text-decoration:none; display:inline-flex; align-items:center; gap:.4rem; }
    .btn-primary { background:var(--calq2); color:#fff; }
    .btn-primary:hover { background:var(--calq); }
    .btn-secondary { background:#fff; color:#475569; border:1px solid #e2e8f0; }
    .btn-secondary:hover { background:#f1f5f9; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-mini { padding:.3rem .6rem; font-size:.72rem; border-radius:6px; }
    .btn-link { background:transparent; color:var(--calq); padding:0; }

    .timeline { position:relative; padding-left:1.5rem; }
    .timeline::before { content:''; position:absolute; left:.5rem; top:.4rem; bottom:.4rem; width:2px; background:#e2e8f0; }
    .timeline-item { position:relative; padding-bottom:1rem; }
    .timeline-item::before { content:''; position:absolute; left:-1.1rem; top:.4rem; width:14px; height:14px; border-radius:50%; background:var(--calq2); border:3px solid #fff; box-shadow:0 0 0 2px var(--calq2); }
    .timeline-item.r-RECHAZADO::before { background:#ef4444; box-shadow:0 0 0 2px #ef4444; }
    .timeline-item.r-CONDICIONAL::before { background:#f59e0b; box-shadow:0 0 0 2px #f59e0b; }
    .timeline-item.r-PENDIENTE::before { background:#6366f1; box-shadow:0 0 0 2px #6366f1; }
</style>
