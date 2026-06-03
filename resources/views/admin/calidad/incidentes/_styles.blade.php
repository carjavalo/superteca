{{-- Estilos compartidos del módulo Incidentes --}}
<style>
:root{
    --inc:#dc2626;
    --inc-d:#991b1b;
    --inc-l:#fee2e2;
    --gray:#64748b;
    --bg:#f8fafc;
}
.page-header{display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg,var(--inc) 0%,var(--inc-d) 100%);color:#fff;padding:1.5rem 2rem;border-radius:14px;margin-bottom:1.25rem;box-shadow:0 8px 25px rgba(220,38,38,.25);}
.page-header h1{margin:0;font-size:1.55rem;font-weight:800;}
.page-header p{margin:.25rem 0 0;opacity:.92;font-size:.9rem;}
.btn{padding:.55rem 1rem;border-radius:8px;font-weight:600;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;font-size:.875rem;}
.btn-primary{background:var(--inc);color:#fff;}
.btn-secondary{background:#e2e8f0;color:#0f172a;}
.btn-success{background:#16a34a;color:#fff;}
.btn-warning{background:#f59e0b;color:#fff;}
.btn-danger{background:#b91c1c;color:#fff;}
.btn-mini{padding:.3rem .65rem;font-size:.75rem;}
.tabs{display:flex;gap:.4rem;margin:1rem 0;flex-wrap:wrap;}
.tabs a{padding:.55rem 1rem;border-radius:8px;background:#f1f5f9;color:#334155;font-weight:600;text-decoration:none;font-size:.85rem;border:1px solid transparent;}
.tabs a.active{background:var(--inc);color:#fff;}
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:.85rem;margin-bottom:1rem;}
.kpi{background:#fff;border-radius:12px;padding:1rem;box-shadow:0 2px 6px rgba(0,0,0,.05);border-left:4px solid var(--gray);}
.kpi .lbl{font-size:.75rem;color:#64748b;text-transform:uppercase;letter-spacing:.05em;font-weight:600;}
.kpi .val{font-size:1.75rem;font-weight:800;color:#0f172a;line-height:1.1;margin-top:.25rem;}
.kpi.k-crit{border-left-color:#dc2626;}
.kpi.k-open{border-left-color:#f59e0b;}
.kpi.k-inv{border-left-color:#0ea5e9;}
.kpi.k-acc{border-left-color:#a855f7;}
.kpi.k-cls{border-left-color:#16a34a;}
.kpi.k-blk{border-left-color:#1e293b;}
.card{background:#fff;border-radius:12px;padding:1.25rem;box-shadow:0 2px 6px rgba(0,0,0,.05);margin-bottom:1rem;}
.card h3{margin:0 0 .85rem;font-size:1rem;font-weight:700;color:#0f172a;}
.t{width:100%;border-collapse:collapse;font-size:.85rem;}
.t th,.t td{padding:.6rem .65rem;text-align:left;border-bottom:1px solid #e2e8f0;vertical-align:middle;}
.t th{background:#f8fafc;font-weight:700;color:#475569;font-size:.75rem;text-transform:uppercase;letter-spacing:.05em;}
.t tbody tr:hover{background:#fafbfc;}
.badge{display:inline-block;padding:.2rem .55rem;border-radius:999px;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.04em;}
.b-ABIERTO{background:#fef3c7;color:#92400e;}
.b-INVESTIGACION{background:#dbeafe;color:#1e40af;}
.b-ACCION_CORRECTIVA{background:#ede9fe;color:#6d28d9;}
.b-CERRADO{background:#dcfce7;color:#166534;}
.b-BAJA{background:#dcfce7;color:#166534;}
.b-MEDIA{background:#fef3c7;color:#92400e;}
.b-ALTA{background:#ffedd5;color:#9a3412;}
.b-CRITICA{background:#fee2e2;color:#991b1b;}
.b-PENDIENTE{background:#f1f5f9;color:#334155;}
.b-EN_PROCESO{background:#dbeafe;color:#1e40af;}
.b-CERRADA{background:#dcfce7;color:#166534;}
.b-CORRECTIVA{background:#ede9fe;color:#6d28d9;}
.b-PREVENTIVA{background:#dbeafe;color:#1e40af;}

/* Semáforo */
.sem-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:.85rem;}
.sem-card{padding:.85rem 1rem;border-radius:10px;border-left:5px solid #cbd5e1;background:#f8fafc;}
.sem-card.s-VERDE{border-left-color:#16a34a;background:#f0fdf4;}
.sem-card.s-AMARILLO{border-left-color:#f59e0b;background:#fffbeb;}
.sem-card.s-ROJO{border-left-color:#dc2626;background:#fef2f2;}
.sem-card.s-GRIS{border-left-color:#94a3b8;background:#f8fafc;}
.sem-card .nom{display:flex;align-items:center;gap:.5rem;font-weight:700;color:#0f172a;}
.sem-card .meta{color:#64748b;font-size:.75rem;margin:.25rem 0 .4rem;}
.sem-card .nums{display:flex;gap:.7rem;font-size:.78rem;font-weight:600;}
.sem-card .nums .ok{color:#16a34a;}
.sem-card .nums .op{color:#f59e0b;}
.sem-card .nums .cr{color:#dc2626;}
.dot{width:10px;height:10px;border-radius:50%;display:inline-block;}
.d-VERDE{background:#16a34a;}.d-AMARILLO{background:#f59e0b;}.d-ROJO{background:#dc2626;}.d-GRIS{background:#94a3b8;}

/* Kanban */
.kanban{display:grid;grid-template-columns:repeat(4,1fr);gap:.75rem;}
.kb-col{background:#f8fafc;border-radius:10px;padding:.75rem;min-height:300px;}
.kb-col h4{margin:0 0 .65rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.06em;color:#475569;display:flex;justify-content:space-between;align-items:center;}
.kb-col h4 span{background:#fff;color:#0f172a;border-radius:999px;padding:.05rem .55rem;font-size:.7rem;font-weight:700;}
.kb-card{background:#fff;border-radius:8px;padding:.6rem .7rem;box-shadow:0 1px 3px rgba(0,0,0,.06);margin-bottom:.5rem;border-left:4px solid var(--gray);}
.kb-card.sev-CRITICA{border-left-color:#dc2626;}
.kb-card.sev-ALTA{border-left-color:#f97316;}
.kb-card.sev-MEDIA{border-left-color:#f59e0b;}
.kb-card.sev-BAJA{border-left-color:#16a34a;}
.kb-card .codigo{font-weight:700;font-size:.8rem;color:#0f172a;}
.kb-card .desc{font-size:.75rem;color:#475569;margin:.25rem 0;line-height:1.3;}
.kb-card .meta{font-size:.7rem;color:#64748b;display:flex;justify-content:space-between;}
.kb-card a{color:inherit;text-decoration:none;display:block;}

/* Matriz de riesgo */
.matrix{width:100%;border-collapse:separate;border-spacing:4px;font-size:.78rem;}
.matrix th,.matrix td{padding:.6rem;text-align:center;border-radius:6px;}
.matrix th{background:#f1f5f9;color:#334155;font-weight:700;font-size:.7rem;}
.matrix td{background:#f8fafc;font-weight:700;color:#0f172a;}
.matrix td.lvl-1{background:#dcfce7;color:#166534;}
.matrix td.lvl-2{background:#fef3c7;color:#92400e;}
.matrix td.lvl-3{background:#ffedd5;color:#9a3412;}
.matrix td.lvl-4{background:#fee2e2;color:#991b1b;}

/* Forms */
.form-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;}
.form-grid label{display:block;font-weight:600;font-size:.8rem;color:#334155;margin-bottom:.3rem;}
.form-grid input,.form-grid select,.form-grid textarea{width:100%;padding:.55rem .7rem;border:1px solid #cbd5e1;border-radius:8px;font-size:.85rem;background:#fff;}
.form-grid textarea{resize:vertical;min-height:80px;}
.full{grid-column:1/-1;}

/* Timeline */
.timeline{border-left:3px solid #e2e8f0;padding-left:1rem;margin-left:.5rem;}
.tl-item{margin-bottom:.85rem;position:relative;}
.tl-item::before{content:"";position:absolute;left:-1.4rem;top:.3rem;width:.7rem;height:.7rem;background:var(--inc);border-radius:50%;border:3px solid #fff;box-shadow:0 0 0 2px var(--inc);}
.tl-item .tl-meta{font-size:.7rem;color:#64748b;}
.tl-item .tl-text{font-size:.85rem;color:#0f172a;margin-top:.15rem;}

@media(max-width:900px){.kanban{grid-template-columns:1fr 1fr;}}
@media(max-width:600px){.kanban{grid-template-columns:1fr;}}
</style>
