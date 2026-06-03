<style>
:root{
    --tz:#7c3aed; --tz-d:#5b21b6; --tz-l:#a78bfa; --tz-soft:#ede9fe;
    --tz-bg:#faf5ff;
}
.tz-page{ padding:1.5rem; max-width:1500px; margin:0 auto; }

.tz-hdr{ display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem;
    background:linear-gradient(135deg,var(--tz) 0%, var(--tz-d) 100%); color:#fff;
    padding:1.5rem 2rem; border-radius:14px; margin-bottom:1.25rem;
    box-shadow:0 8px 25px rgba(124,58,237,.28);}
.tz-hdr h1{ margin:0; font-size:1.55rem; font-weight:800; display:flex; align-items:center; gap:.6rem;}
.tz-hdr p{ margin:.25rem 0 0; opacity:.92; font-size:.9rem;}
.tz-hdr-actions{ display:flex; gap:.5rem; flex-wrap:wrap;}

.tz-btn{ padding:.55rem 1rem; border-radius:8px; font-weight:600; border:0; cursor:pointer; text-decoration:none;
    display:inline-flex; align-items:center; gap:.4rem; font-size:.875rem; transition:.15s; white-space:nowrap;}
.tz-btn-primary{ background:#fff; color:var(--tz-d);}
.tz-btn-primary:hover{ background:#f3f4f6;}
.tz-btn-recall{ background:#dc2626; color:#fff;}
.tz-btn-recall:hover{ background:#b91c1c;}
.tz-btn-ghost{ background:rgba(255,255,255,.18); color:#fff;}
.tz-btn-ghost:hover{ background:rgba(255,255,255,.28);}
.tz-btn-out{ background:#fff; color:var(--tz); border:1px solid var(--tz);}
.tz-btn-out:hover{ background:var(--tz-soft);}

.tz-card{ background:#fff; border-radius:12px; padding:1.25rem; box-shadow:0 2px 6px rgba(0,0,0,.05); margin-bottom:1rem;}
.tz-card h3{ margin:0 0 .85rem; font-size:1rem; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:.5rem;}
.tz-card h3 .pill{ font-size:.65rem; background:var(--tz-soft); color:var(--tz-d); padding:.18rem .55rem; border-radius:999px; font-weight:700; letter-spacing:.3px; text-transform:uppercase;}
.tz-card h3 .icon{ width:28px; height:28px; border-radius:8px; background:var(--tz-soft); color:var(--tz-d); display:inline-flex; align-items:center; justify-content:center; font-size:.9rem;}

.tz-search{ background:linear-gradient(135deg,#fff 0%, var(--tz-bg) 100%);
    border:1px solid #e9d5ff; border-radius:14px; padding:1.5rem 1.75rem; margin-bottom:1rem;
    box-shadow:0 2px 8px rgba(124,58,237,.06);}
.tz-search-title{ font-weight:700; color:var(--tz-d); font-size:1rem; margin-bottom:.7rem; display:flex; align-items:center; gap:.6rem;}
.tz-search-title .ico{ width:34px;height:34px; border-radius:10px; background:var(--tz); color:#fff; display:inline-flex; align-items:center; justify-content:center; font-size:1.1rem;}
.tz-search form{ display:flex; gap:.6rem; align-items:stretch;}
.tz-search input[type=text]{ flex:1; padding:.85rem 1rem .85rem 2.6rem; border:2px solid #e9d5ff; border-radius:10px;
    font-size:.95rem; outline:none; transition:.15s;
    background:#fff url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='18' height='18' fill='none' stroke='%237c3aed' stroke-width='2' viewBox='0 0 24 24'><circle cx='11' cy='11' r='7'/><path d='M21 21l-4.3-4.3'/></svg>") no-repeat .85rem center;}
.tz-search input[type=text]:focus{ border-color:var(--tz); box-shadow:0 0 0 4px var(--tz-soft);}
.tz-search button{ padding:0 1.4rem; background:var(--tz); color:#fff; border:0; border-radius:10px; font-weight:700; cursor:pointer; font-size:.9rem;}
.tz-search button:hover{ background:var(--tz-d);}
.tz-search .hint{ font-size:.78rem; color:#6b7280; margin-top:.6rem;}
.tz-search .chips{ display:flex; gap:.4rem; flex-wrap:wrap; margin-top:.7rem;}
.tz-search .chip{ font-size:.72rem; background:#fff; border:1px solid #e9d5ff; color:var(--tz-d); padding:.25rem .65rem; border-radius:999px; font-weight:600;}

.tz-kpis{ display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:.85rem; margin-bottom:1rem;}
.tz-kpi{ background:#fff; border-radius:12px; padding:1rem 1.1rem; box-shadow:0 2px 6px rgba(0,0,0,.05);
    border-left:4px solid var(--tz); position:relative; overflow:hidden; transition:.15s;}
.tz-kpi:hover{ transform:translateY(-2px); box-shadow:0 6px 16px rgba(0,0,0,.08);}
.tz-kpi .lbl{ font-size:.7rem; color:#64748b; text-transform:uppercase; letter-spacing:.05em; font-weight:700;}
.tz-kpi .val{ font-size:1.6rem; font-weight:800; color:#0f172a; line-height:1.1; margin-top:.2rem; font-variant-numeric:tabular-nums;}
.tz-kpi .sub{ font-size:.7rem; color:#94a3b8; margin-top:.2rem; font-weight:500;}
.tz-kpi .ic{ position:absolute; right:.85rem; top:50%; transform:translateY(-50%); font-size:1.6rem; opacity:.15; color:var(--tz);}
.tz-kpi.ok{ border-left-color:#16a34a;} .tz-kpi.ok .ic{ color:#16a34a;}
.tz-kpi.cyan{ border-left-color:#06b6d4;} .tz-kpi.cyan .ic{ color:#06b6d4;}
.tz-kpi.amber{ border-left-color:#f59e0b;} .tz-kpi.amber .ic{ color:#f59e0b;}
.tz-kpi.pink{ border-left-color:#ec4899;} .tz-kpi.pink .ic{ color:#ec4899;}
.tz-kpi.indigo{ border-left-color:#6366f1;} .tz-kpi.indigo .ic{ color:#6366f1;}
.tz-kpi.crit{ border-left-color:#dc2626;} .tz-kpi.crit .ic{ color:#dc2626;} .tz-kpi.crit .val{ color:#dc2626;}
.tz-kpi.warn{ border-left-color:#f59e0b;} .tz-kpi.warn .val{ color:#d97706;}

.tz-row2{ display:grid; grid-template-columns:2fr 1fr; gap:1rem;}
.tz-row22{ display:grid; grid-template-columns:1fr 1fr; gap:1rem;}
.tz-row3{ display:grid; grid-template-columns:repeat(3,1fr); gap:1rem;}
@media(max-width:1000px){ .tz-row2,.tz-row22,.tz-row3{ grid-template-columns:1fr;} }

.tz-t{ width:100%; border-collapse:collapse; font-size:.82rem;}
.tz-t th,.tz-t td{ padding:.55rem .6rem; text-align:left; border-bottom:1px solid #e2e8f0; vertical-align:middle;}
.tz-t th{ background:#f8fafc; font-weight:700; color:#475569; font-size:.7rem; text-transform:uppercase; letter-spacing:.05em;}
.tz-t tbody tr:hover{ background:#faf5ff;}
.tz-t .num{ text-align:right; font-variant-numeric:tabular-nums;}
.tz-t a{ color:var(--tz); text-decoration:none; font-weight:600;}
.tz-t a:hover{ text-decoration:underline;}

.tz-pill{ display:inline-block; padding:.18rem .55rem; border-radius:999px; font-size:.68rem; font-weight:700; letter-spacing:.3px;}
.tz-pill.ok{ background:#dcfce7; color:#166534;}
.tz-pill.warn{ background:#fef3c7; color:#92400e;}
.tz-pill.crit{ background:#fee2e2; color:#991b1b;}
.tz-pill.info{ background:#dbeafe; color:#1e40af;}
.tz-pill.purple{ background:var(--tz-soft); color:var(--tz-d);}
.tz-pill.gray{ background:#f3f4f6; color:#374151;}

.tz-flow{ display:grid; grid-template-columns:repeat(4,1fr); gap:0; margin-top:.6rem;}
.tz-flow-step{ position:relative; padding:1rem .85rem; text-align:center;
    background:linear-gradient(135deg,#fff 0%, var(--tz-bg) 100%); border:1px solid #e9d5ff;}
.tz-flow-step:first-child{ border-radius:10px 0 0 10px;}
.tz-flow-step:last-child{ border-radius:0 10px 10px 0;}
.tz-flow-step:not(:first-child){ border-left:0;}
.tz-flow-step:not(:last-child)::after{
    content:''; position:absolute; right:-12px; top:50%; transform:translateY(-50%) rotate(45deg);
    width:22px; height:22px; background:var(--tz-bg); border-top:1px solid #e9d5ff; border-right:1px solid #e9d5ff; z-index:2;}
.tz-flow-step .ico{ width:38px; height:38px; border-radius:10px; background:var(--tz); color:#fff;
    display:inline-flex; align-items:center; justify-content:center; font-size:1.1rem; margin:0 auto .5rem;}
.tz-flow-step .lbl{ font-size:.7rem; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:.4px;}
.tz-flow-step .val{ font-size:1.7rem; font-weight:800; color:var(--tz-d); margin-top:.15rem; font-variant-numeric:tabular-nums;}
.tz-flow-step .sub{ font-size:.72rem; color:#64748b; margin-top:.1rem;}
@media(max-width:900px){ .tz-flow{ grid-template-columns:1fr 1fr;} .tz-flow-step::after{ display:none !important;} .tz-flow-step{ border-radius:10px !important; border:1px solid #e9d5ff !important;} }

.tz-bars{ display:flex; flex-direction:column; gap:.45rem;}
.tz-bar{ display:grid; grid-template-columns:200px 1fr 80px; align-items:center; gap:.6rem; font-size:.8rem;}
.tz-bar .nm{ overflow:hidden; text-overflow:ellipsis; white-space:nowrap; color:#0f172a; font-weight:600;}
.tz-bar .bg{ height:14px; background:#f3e8ff; border-radius:7px; overflow:hidden;}
.tz-bar .fg{ display:block; height:100%; background:linear-gradient(90deg,var(--tz-l),var(--tz)); border-radius:7px;}
.tz-bar .vl{ text-align:right; font-variant-numeric:tabular-nums; color:#0f172a; font-weight:700;}

.tz-spark{ display:flex; align-items:flex-end; gap:.3rem; height:140px; padding:1.4rem 0 .4rem;}
.tz-spark .b{ flex:1; background:linear-gradient(180deg,var(--tz-l),var(--tz-d)); border-radius:4px 4px 0 0; min-width:6px; position:relative; transition:.2s;}
.tz-spark .b:hover{ filter:brightness(1.15); cursor:pointer;}
.tz-spark .b .t{ position:absolute; bottom:100%; left:50%; transform:translateX(-50%) translateY(-4px);
    background:#0f172a; color:#fff; font-size:.65rem; padding:.2rem .4rem; border-radius:4px; white-space:nowrap; opacity:0; pointer-events:none;}
.tz-spark .b:hover .t{ opacity:1;}
.tz-spark-foot{ display:flex; justify-content:space-between; font-size:.7rem; color:#64748b; padding-top:.4rem; border-top:1px solid #e2e8f0;}

.tz-heat{ display:grid; grid-template-columns:repeat(auto-fit,minmax(130px,1fr)); gap:.5rem;}
.tz-heat-cell{ position:relative; padding:.85rem; border-radius:10px; text-align:center; transition:.15s;
    box-shadow:0 1px 3px rgba(0,0,0,.06);}
.tz-heat-cell:hover{ transform:translateY(-2px); box-shadow:0 4px 10px rgba(0,0,0,.12);}
.tz-heat-cell .nm{ font-size:.78rem; font-weight:700; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;}
.tz-heat-cell .v{ font-size:1.3rem; font-weight:800; margin-top:.25rem; line-height:1;}
.tz-heat-cell .sub{ font-size:.7rem; opacity:.85; margin-top:.15rem;}

.tz-tabs{ display:flex; gap:.3rem; border-bottom:2px solid #e5e7eb; margin-bottom:1rem; overflow-x:auto;}
.tz-tabs button{ background:transparent; border:0; padding:.65rem 1rem; cursor:pointer; font-size:.85rem; font-weight:600; color:#6b7280; border-bottom:2px solid transparent; margin-bottom:-2px; white-space:nowrap; display:inline-flex; align-items:center; gap:.4rem;}
.tz-tabs button .cnt{ background:#f3f4f6; color:#6b7280; padding:.05rem .45rem; border-radius:999px; font-size:.7rem; font-weight:700;}
.tz-tabs button.active{ color:var(--tz); border-bottom-color:var(--tz);}
.tz-tabs button.active .cnt{ background:var(--tz-soft); color:var(--tz-d);}
.tz-tab-pane{ display:none;}
.tz-tab-pane.active{ display:block;}

.tz-tree{ font-family:'Consolas','Courier New',monospace; font-size:.85rem; line-height:1.7; padding:1rem 1.2rem; background:#1e1b4b; color:#e0e7ff; border-radius:10px; overflow-x:auto;}
.tz-tree .node{ color:#fbbf24;}
.tz-tree .leaf{ color:#86efac;}
.tz-tree .meta{ color:#94a3b8;}
.tz-tree .num{ color:#f472b6; font-weight:700;}

.tz-fields{ display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem;}
.tz-field .lbl{ font-size:.7rem; color:#64748b; text-transform:uppercase; font-weight:700; letter-spacing:.4px;}
.tz-field .val{ font-size:.92rem; font-weight:600; color:#0f172a; margin-top:.2rem;}
.tz-field .val.mono{ font-family:'Consolas',monospace; color:var(--tz-d);}
.tz-field .val small{ display:block; font-size:.72rem; color:#94a3b8; font-weight:500; margin-top:.1rem;}

.tz-empty{ text-align:center; padding:2rem 1rem; color:#94a3b8; font-size:.88rem;}
.tz-empty::before{ content:'∅'; display:block; font-size:2rem; opacity:.3; margin-bottom:.4rem;}

.tz-crumbs{ font-size:.82rem; color:#64748b; margin-bottom:.8rem;}
.tz-crumbs a{ color:var(--tz); text-decoration:none; font-weight:600;}
.tz-crumbs a:hover{ text-decoration:underline;}

.tz-alert{ padding:.85rem 1.1rem; border-radius:10px; font-size:.88rem; margin-bottom:1rem; display:flex; align-items:center; gap:.6rem;}
.tz-alert.warn{ background:#fef3c7; border:1px solid #fbbf24; color:#92400e;}
.tz-alert.crit{ background:#fee2e2; border:1px solid #fca5a5; color:#991b1b;}

.tz-quick{ display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:.6rem;}
.tz-quick a{ display:flex; align-items:center; gap:.6rem; padding:.85rem 1rem; border-radius:10px;
    background:#fff; border:1px solid #e5e7eb; color:#374151; text-decoration:none; font-size:.85rem; font-weight:600; transition:.15s;}
.tz-quick a:hover{ border-color:var(--tz); color:var(--tz); transform:translateY(-2px); box-shadow:0 4px 10px rgba(124,58,237,.1);}
.tz-quick a .ic{ width:34px; height:34px; border-radius:9px; background:var(--tz-soft); color:var(--tz-d); display:inline-flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0;}
.tz-quick a .meta{ font-size:.7rem; color:#94a3b8; font-weight:500; display:block; margin-top:.15rem;}

.tz-donut{ display:flex; align-items:center; gap:1.2rem; flex-wrap:wrap;}
.tz-donut svg{ width:160px; height:160px; flex-shrink:0;}
.tz-donut .lg{ flex:1; min-width:140px;}
.tz-donut .lg div{ display:flex; align-items:center; gap:.5rem; padding:.3rem 0; font-size:.82rem;}
.tz-donut .lg .sw{ width:12px; height:12px; border-radius:3px;}
.tz-donut .lg b{ margin-left:auto; font-variant-numeric:tabular-nums;}
</style>