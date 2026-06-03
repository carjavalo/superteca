<style>
    :root { --inst:#0c4a6e; }
    .page-header { background: linear-gradient(135deg, #0ea5e9 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }
    .nav-tabs { display:flex; gap:.4rem; margin-bottom:1.2rem; flex-wrap:wrap; }
    .nav-tabs a { padding:.55rem 1rem; border-radius:8px; text-decoration:none; font-size:.82rem; font-weight:600; color:#475569; background:#fff; border:1px solid #e2e8f0; }
    .nav-tabs a.active { background:#0ea5e9; color:#fff; border-color:#0ea5e9; }
    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:#0ea5e9; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }
    .btn-mini { padding:.3rem .6rem; border-radius:6px; font-size:.72rem; font-weight:600; border:1px solid #e2e8f0; background:#fff; cursor:pointer; }
    .btn-mini.btn-del { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
    .alert-ok { background:#dcfce7; color:#166534; padding:.7rem 1rem; border-radius:8px; margin-bottom:1rem; }
    .alert-err { background:#fee2e2; color:#991b1b; padding:.7rem 1rem; border-radius:8px; margin-bottom:1rem; }
    .tabla { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .tabla table { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tabla th { background:#f1f5f9; padding:.6rem .8rem; text-align:left; font-size:.7rem; color:#475569; text-transform:uppercase; }
    .tabla td { padding:.55rem .8rem; border-top:1px solid #f1f5f9; }
    .badge { display:inline-block; padding:.15rem .55rem; border-radius:8px; font-size:.66rem; font-weight:700; }
    .badge-tipo { background:#e0f2fe; color:#075985; padding:.15rem .55rem; border-radius:8px; font-size:.7rem; font-weight:600; }
    .b-CRITICA { background:#fee2e2; color:#991b1b; }
    .b-ALTA { background:#ffedd5; color:#9a3412; }
    .b-MEDIA { background:#fef3c7; color:#92400e; }
    .b-BAJA { background:#dbeafe; color:#1e40af; }
    .b-ABIERTA { background:#fee2e2; color:#991b1b; }
    .b-INVESTIGACION { background:#fef3c7; color:#92400e; }
    .b-CERRADA { background:#dcfce7; color:#166534; }
    .b-LIBERADO { background:#dcfce7; color:#166534; }
    .b-CUARENTENA { background:#fef3c7; color:#92400e; }
    .b-BLOQUEADO { background:#fee2e2; color:#991b1b; }
    .b-DESECHADO { background:#1f2937; color:#fff; }
    .b-PENDIENTE_EVALUACION { background:#e0e7ff; color:#3730a3; }
    .dot { width:10px; height:10px; border-radius:50%; display:inline-block; margin-right:.4rem; vertical-align:middle; }
    .d-VERDE { background:#22c55e; }
    .d-AMARILLO { background:#facc15; }
    .d-ROJO { background:#ef4444; animation:pulse 1.5s infinite; }
    .d-GRIS { background:#94a3b8; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    .modal-bg { position:fixed; inset:0; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; z-index:1000; padding:1rem; }
    .modal { background:#fff; border-radius:12px; padding:1.5rem; max-width:760px; width:100%; max-height:90vh; overflow-y:auto; }
    .modal-h { display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem; }
    .modal-h h3 { margin:0; }
    .modal .x { background:none; border:none; font-size:1.5rem; cursor:pointer; color:#64748b; }
    .grid-2c { display:grid; grid-template-columns:1fr 1fr; gap:.7rem; }
    @media(max-width:600px){ .grid-2c{ grid-template-columns:1fr; } }
    .grid-2c label { font-size:.7rem; color:#475569; font-weight:600; display:block; margin-bottom:.2rem; }
    .grid-2c input, .grid-2c select, .grid-2c textarea { width:100%; padding:.5rem .7rem; border:1.5px solid #e2e8f0; border-radius:6px; font-size:.85rem; }
    .card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1.2rem; }
    .card h3 { margin:0 0 .8rem; font-size:.95rem; color:#0f172a; }
    .filters { background:#fff; padding:1rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); display:flex; gap:.6rem; align-items:end; flex-wrap:wrap; margin-bottom:1.2rem; }
    .filters label { font-size:.7rem; color:#475569; font-weight:600; display:block; margin-bottom:.2rem; }
    .filters input, .filters select { padding:.5rem .8rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; }
</style>
