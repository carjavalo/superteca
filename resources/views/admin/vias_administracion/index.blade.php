<x-app-layout>
    <x-slot name="header">
        <h2>Maestros · Vías de Administración</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }

        .st-alert { padding:14px 18px; border-radius:10px; margin-bottom:20px; display:flex; align-items:center; gap:12px; font-size:.92rem; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:5px solid #10b981; }
        .st-alert.error   { background:#fee2e2; color:#991b1b; border-left:5px solid #ef4444; }

        .dash-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:16px; margin-bottom:24px; }
        .dash-card { background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 4px 12px rgba(0,0,0,.05); border-left:5px solid var(--inst); display:flex; align-items:center; justify-content:space-between; gap:14px; }
        .dash-card .label { color:#6b7280; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
        .dash-card .value { color:var(--inst); font-size:1.85rem; font-weight:800; line-height:1; margin-top:4px; }
        .dash-card .ic { font-size:1.6rem; }
        .dash-card.green { border-left-color:#10b981; }
        .dash-card.blue  { border-left-color:#3b82f6; }
        .dash-card.amber { border-left-color:#f59e0b; }
        .dash-card.red   { border-left-color:#ef4444; }

        .layout-catalog { display:grid; grid-template-columns:280px 1fr; gap:24px; align-items:start; }
        @media (max-width:900px) { .layout-catalog { grid-template-columns:1fr; } }

        .filter-card { background:rgba(255,255,255,.95); border-radius:14px; box-shadow:0 6px 16px rgba(0,0,0,.05); padding:22px; position:sticky; top:20px; }
        .filter-card h3 { font-size:1.1rem; color:var(--inst); margin:0 0 18px 0; font-weight:700; border-bottom:2px solid #f3f4f6; padding-bottom:10px; }
        .filter-group { margin-bottom:14px; }
        .filter-group label { display:block; font-size:.82rem; font-weight:600; color:#4b5563; margin-bottom:6px; }
        .filter-group input, .filter-group select { width:100%; padding:9px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; }
        .filter-group input:focus, .filter-group select:focus { border-color:var(--inst); box-shadow:0 0 0 3px rgba(46,58,117,.1); }
        .filter-actions { display:flex; flex-direction:column; gap:10px; margin-top:18px; }

        .catalog-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:15px; flex-wrap:wrap; }
        .catalog-top h2 { font-size:1.4rem; font-weight:800; color:var(--inst); margin:0; }
        .catalog-top p  { color:#6b7280; margin:4px 0 0 0; font-size:.92rem; }

        .card-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(290px,1fr)); gap:18px; }

        .va-card { background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 6px 14px rgba(0,0,0,.05); border:1px solid #f1f1f4; transition:all .25s; display:flex; flex-direction:column; }
        .va-card:hover { transform:translateY(-5px); box-shadow:0 16px 32px rgba(0,0,0,.1); }
        .va-card .accent { height:8px; width:100%; background:#94a3b8; }

        .va-head { padding:20px; display:flex; align-items:center; gap:14px; }
        .va-icon { width:64px; height:64px; border-radius:18px; background:#eef0f7; display:flex; align-items:center; justify-content:center; font-size:1.9rem; flex-shrink:0; }
        .va-title h3 { margin:0; font-size:1.15rem; font-weight:800; color:#1f2937; }
        .va-title .sub { color:#6b7280; font-size:.82rem; margin-top:2px; }

        .va-body { padding:0 20px 14px 20px; display:grid; grid-template-columns:1fr 1fr; gap:8px; font-size:.82rem; color:#4b5563; }
        .va-attr { display:flex; align-items:center; gap:6px; padding:6px 10px; background:#f9fafb; border-radius:8px; border:1px solid #f1f1f4; }
        .va-attr .yes { color:#059669; font-weight:700; }
        .va-attr .no  { color:#9ca3af; }

        .va-foot { padding:14px 20px; background:#f9fafb; border-top:1px solid #f1f1f4; display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; }

        .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:.72rem; font-weight:700; border:1px solid transparent; }
        .badge-tipo     { background:#f3f4f6; color:#374151; border-color:#e5e7eb; }
        .badge-active   { background:#d1fae5; color:#065f46; border-color:#a7f3d0; }
        .badge-inactive { background:#fee2e2; color:#991b1b; border-color:#fecaca; }

        .risk { display:inline-flex; align-items:center; gap:5px; font-size:.72rem; font-weight:700; padding:3px 9px; border-radius:20px; }
        .risk-BAJO    { background:#dcfce7; color:#065f46; border:1px solid #86efac; }
        .risk-MEDIO   { background:#fef9c3; color:#854d0e; border:1px solid #fde68a; }
        .risk-ALTO    { background:#ffedd5; color:#9a3412; border:1px solid #fdba74; }
        .risk-CRITICO { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }

        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:9px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; justify-content:center; gap:7px; transition:all .2s; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; transform:translateY(-2px); }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; cursor:pointer; padding:9px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; justify-content:center; gap:7px; text-decoration:none; }
        .btn-outline:hover { background:#f9fafb; }
        .btn-icon { width:34px; height:34px; border-radius:8px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; }
        .btn-view { background:#f5f3ff; color:#7c3aed; } .btn-view:hover { background:#7c3aed; color:#fff; }
        .btn-edit { background:#eff6ff; color:#2563eb; } .btn-edit:hover { background:#2563eb; color:#fff; }
        .btn-del  { background:#fee2e2; color:#dc2626; } .btn-del:hover  { background:#dc2626; color:#fff; }

        .empty-state { text-align:center; padding:3rem 2rem; background:rgba(255,255,255,.8); border-radius:16px; border:2px dashed #e5e7eb; grid-column:1/-1; }

        .modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,.65); backdrop-filter:blur(5px); display:flex; align-items:center; justify-content:center; z-index:200; opacity:0; pointer-events:none; transition:opacity .25s; }
        .modal-overlay.open { opacity:1; pointer-events:all; }
        .modal-box { background:#fff; border-radius:18px; width:100%; max-width:900px; max-height:92vh; overflow-y:auto; transform:scale(.96); transition:transform .25s; box-shadow:0 25px 50px -12px rgba(0,0,0,.3); }
        .modal-overlay.open .modal-box { transform:scale(1); }
        .modal-header { padding:18px 22px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:#fff; z-index:1; }
        .modal-header h3 { margin:0; font-size:1.25rem; color:var(--inst); font-weight:800; }
        .modal-close { background:none; border:none; font-size:1.5rem; cursor:pointer; color:#9ca3af; width:32px; height:32px; border-radius:8px; }
        .modal-body { padding:22px; }
        .modal-footer { padding:16px 22px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; background:#f9fafb; border-bottom-left-radius:18px; border-bottom-right-radius:18px; }

        .form-section { margin-bottom:18px; }
        .form-section h4 { font-size:.9rem; color:var(--inst); border-bottom:2px solid #f3f4f6; padding-bottom:6px; margin:0 0 12px 0; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:10px; }
        .form-row.cols-3 { grid-template-columns:1fr 1fr 1fr; }
        .form-row.cols-4 { grid-template-columns:repeat(4, 1fr); }
        .form-group label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:5px; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:9px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; font-family:inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--inst); box-shadow:0 0 0 3px rgba(46,58,117,.1); }
        .form-check { display:flex; align-items:center; gap:7px; padding:8px 10px; background:#f9fafb; border-radius:8px; border:1px solid #f1f1f4; }
        .form-check label { margin:0; font-weight:600; cursor:pointer; }
        @media (max-width:700px) { .form-row, .form-row.cols-3, .form-row.cols-4 { grid-template-columns:1fr; } }
    </style>

    @if(session('success'))
        <div class="st-alert success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="st-alert error">{{ session('error') }}</div>
    @endif

    <div class="dash-grid">
        <div class="dash-card blue">
            <div><div class="label">Total vías</div><div class="value">{{ $stats['total'] }}</div></div>
            <div class="ic">💉</div>
        </div>
        <div class="dash-card green">
            <div><div class="label">Parenterales</div><div class="value">{{ $stats['parenterales'] }}</div></div>
            <div class="ic">🩸</div>
        </div>
        <div class="dash-card amber">
            <div><div class="label">Estériles requeridas</div><div class="value">{{ $stats['esteriles'] }}</div></div>
            <div class="ic">🧪</div>
        </div>
        <div class="dash-card red">
            <div><div class="label">Riesgo crítico</div><div class="value">{{ $stats['criticas'] }}</div></div>
            <div class="ic">⚠️</div>
        </div>
    </div>

    <div class="layout-catalog">
        <aside>
            <div class="filter-card">
                <h3>Filtros del catálogo</h3>
                <form method="GET" action="{{ route('admin.vias_administracion.index') }}">
                    <div class="filter-group">
                        <label>Buscar (nombre o código)</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej: IV, Oral...">
                    </div>
                    <div class="filter-group">
                        <label>Tipo</label>
                        <select name="tipo">
                            <option value="">Todos</option>
                            @foreach(\App\Models\ViaAdministracion::TIPOS as $k => $l)
                                <option value="{{ $k }}" {{ request('tipo') == $k ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Riesgo clínico</label>
                        <select name="riesgo">
                            <option value="">Todos</option>
                            @foreach(\App\Models\ViaAdministracion::RIESGOS as $k => $l)
                                <option value="{{ $k }}" {{ request('riesgo') == $k ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Estéril requerido</label>
                        <select name="esteril">
                            <option value="">Indiferente</option>
                            <option value="1" {{ request('esteril') === '1' ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ request('esteril') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Requiere bomba</label>
                        <select name="bomba">
                            <option value="">Indiferente</option>
                            <option value="1" {{ request('bomba') === '1' ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ request('bomba') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-primary">Filtrar</button>
                        <a href="{{ route('admin.vias_administracion.index') }}" class="btn-outline" style="text-align:center;">Restablecer</a>
                    </div>
                </form>
            </div>
        </aside>

        <main>
            <div class="catalog-top">
                <div>
                    <h2>Catálogo Clínico Farmacéutico</h2>
                    <p>Define las vías de administración que controlan validaciones clínicas, preparación, seguridad y límites farmacológicos.</p>
                </div>
                <button class="btn-primary" onclick="openModal('modal-crear')">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nueva Vía
                </button>
            </div>

            <div class="card-grid">
                @forelse($vias as $via)
                    @php $color = $via->color_identificacion ?: '#94a3b8'; @endphp
                    <div class="va-card">
                        <div class="accent" style="background:{{ $color }};"></div>
                        <div class="va-head">
                            <div class="va-icon" style="background:{{ $color }}20;">
                                {{ $via->icono ?: '💉' }}
                            </div>
                            <div class="va-title">
                                <h3>{{ $via->nombre }}</h3>
                                <div class="sub">
                                    @if($via->codigo) <strong>{{ $via->codigo }}</strong> · @endif
                                    {{ $via->tipo_label }}
                                </div>
                                <div style="margin-top:6px; display:flex; gap:5px; flex-wrap:wrap;">
                                    @if($via->tipo)
                                        <span class="badge badge-tipo">{{ $via->tipo_label }}</span>
                                    @endif
                                    @if($via->riesgo_clinico)
                                        <span class="risk risk-{{ $via->riesgo_clinico }}">● {{ $via->riesgo_label }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="va-body">
                            <div class="va-attr">
                                <span>🧪</span>
                                <span>Estéril: <span class="{{ $via->esteril_requerido ? 'yes' : 'no' }}">{{ $via->esteril_requerido ? 'Sí' : 'No' }}</span></span>
                            </div>
                            <div class="va-attr">
                                <span>⏵</span>
                                <span>Bomba: <span class="{{ $via->requiere_bomba_infusion ? 'yes' : 'no' }}">{{ $via->requiere_bomba_infusion ? 'Sí' : 'No' }}</span></span>
                            </div>
                            <div class="va-attr">
                                <span>🧷</span>
                                <span>Filtro: <span class="{{ $via->requiere_filtro ? 'yes' : 'no' }}">{{ $via->requiere_filtro ? 'Sí' : 'No' }}</span></span>
                            </div>
                            <div class="va-attr">
                                <span>💧</span>
                                <span>Bolo: <span class="{{ $via->permite_bolo ? 'yes' : 'no' }}">{{ $via->permite_bolo ? 'Sí' : 'No' }}</span></span>
                            </div>
                            <div class="va-attr">
                                <span>📈</span>
                                <span>Infusión: <span class="{{ $via->permite_infusion_continua ? 'yes' : 'no' }}">{{ $via->permite_infusion_continua ? 'Sí' : 'No' }}</span></span>
                            </div>
                            <div class="va-attr">
                                <span>👁</span>
                                <span>Monitor: <span class="{{ $via->requiere_monitorizacion ? 'yes' : 'no' }}">{{ $via->requiere_monitorizacion ? 'Sí' : 'No' }}</span></span>
                            </div>
                        </div>

                        <div class="va-foot">
                            @if($via->estado)
                                <span class="badge badge-active">● Activo</span>
                            @else
                                <span class="badge badge-inactive">● Inactivo</span>
                            @endif
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('admin.vias_administracion.show', $via) }}" class="btn-icon btn-view" title="Ver detalle">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <button type="button" class="btn-icon btn-edit" title="Editar" onclick='editVia(@json($via))'>
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('admin.vias_administracion.destroy', $via) }}" method="POST" onsubmit="return confirm('¿Eliminar vía de administración?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon btn-del" title="Eliminar">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <h3 style="font-size:1.2rem; color:#4b5563;">Catálogo vacío</h3>
                        <p style="color:#6b7280;">No se encontraron vías con los filtros aplicados.</p>
                    </div>
                @endforelse
            </div>

            <div style="margin-top:22px;">{{ $vias->links() }}</div>
        </main>
    </div>

    {{-- Modal crear / editar --}}
    <div id="modal-crear" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modal-title">Nueva Vía de Administración</h3>
                <button type="button" class="modal-close" onclick="closeModal('modal-crear')">&times;</button>
            </div>
            <form id="form-via" action="{{ route('admin.vias_administracion.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div class="modal-body">
                    <div class="form-section">
                        <h4>Identificación</h4>
                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label>Código *</label>
                                <input type="text" name="codigo" id="f_codigo" maxlength="20" placeholder="Ej: IV">
                            </div>
                            <div class="form-group">
                                <label>Nombre *</label>
                                <input type="text" name="nombre" id="f_nombre" required>
                            </div>
                            <div class="form-group">
                                <label>Nombre corto</label>
                                <input type="text" name="nombre_corto" id="f_nombre_corto">
                            </div>
                        </div>
                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label>Tipo</label>
                                <select name="tipo" id="f_tipo">
                                    <option value="">— Seleccione —</option>
                                    @foreach(\App\Models\ViaAdministracion::TIPOS as $k => $l)
                                        <option value="{{ $k }}">{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Ícono (emoji)</label>
                                <input type="text" name="icono" id="f_icono" placeholder="💉 💊 🌬">
                            </div>
                            <div class="form-group">
                                <label>Color identificación</label>
                                <input type="color" name="color_identificacion" id="f_color_identificacion" value="#3b82f6">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descripción</label>
                            <textarea name="descripcion" id="f_descripcion" rows="2"></textarea>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Requisitos técnicos</h4>
                        <div class="form-row cols-4">
                            <div class="form-check">
                                <input type="hidden" name="esteril_requerido" value="0">
                                <input type="checkbox" name="esteril_requerido" id="f_esteril_requerido" value="1">
                                <label for="f_esteril_requerido">Estéril</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="requiere_bomba_infusion" value="0">
                                <input type="checkbox" name="requiere_bomba_infusion" id="f_requiere_bomba_infusion" value="1">
                                <label for="f_requiere_bomba_infusion">Bomba</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="requiere_filtro" value="0">
                                <input type="checkbox" name="requiere_filtro" id="f_requiere_filtro" value="1">
                                <label for="f_requiere_filtro">Filtro</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="permite_bolo" value="0">
                                <input type="checkbox" name="permite_bolo" id="f_permite_bolo" value="1">
                                <label for="f_permite_bolo">Permite bolo</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="permite_infusion_continua" value="0">
                                <input type="checkbox" name="permite_infusion_continua" id="f_permite_infusion_continua" value="1">
                                <label for="f_permite_infusion_continua">Infusión cont.</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="fotosensible" value="0">
                                <input type="checkbox" name="fotosensible" id="f_fotosensible" value="1">
                                <label for="f_fotosensible">Fotosensible</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="requiere_monitorizacion" value="0">
                                <input type="checkbox" name="requiere_monitorizacion" id="f_requiere_monitorizacion" value="1">
                                <label for="f_requiere_monitorizacion">Monitorizar</label>
                            </div>
                            <div class="form-check">
                                <input type="hidden" name="estado" value="0">
                                <input type="checkbox" name="estado" id="f_estado" value="1" checked>
                                <label for="f_estado">Activo</label>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Límites farmacológicos</h4>
                        <div class="form-row cols-4">
                            <div class="form-group">
                                <label>Vel. mín (ml/h)</label>
                                <input type="number" step="0.01" name="velocidad_min_ml_h" id="f_velocidad_min_ml_h" min="0">
                            </div>
                            <div class="form-group">
                                <label>Vel. máx (ml/h)</label>
                                <input type="number" step="0.01" name="velocidad_max_ml_h" id="f_velocidad_max_ml_h" min="0">
                            </div>
                            <div class="form-group">
                                <label>Osmolaridad máx (mOsm/L)</label>
                                <input type="number" step="0.01" name="osmolaridad_max" id="f_osmolaridad_max" min="0">
                            </div>
                            <div class="form-group">
                                <label>Riesgo clínico</label>
                                <select name="riesgo_clinico" id="f_riesgo_clinico">
                                    <option value="">—</option>
                                    @foreach(\App\Models\ViaAdministracion::RIESGOS as $k => $l)
                                        <option value="{{ $k }}">{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Observaciones</label>
                            <textarea name="observaciones" id="f_observaciones" rows="2"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeModal('modal-crear')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        const STORE_URL = "{{ route('admin.vias_administracion.store') }}";
        function updateUrl(id) { return "{{ url('admin/vias-administracion') }}/" + id; }

        function resetForm() {
            const f = document.getElementById('form-via');
            f.reset();
            f.action = STORE_URL;
            document.getElementById('form-method').value = 'POST';
            document.getElementById('modal-title').textContent = 'Nueva Vía de Administración';
            document.getElementById('f_color_identificacion').value = '#3b82f6';
            document.getElementById('f_estado').checked = true;
        }

        document.querySelector('[onclick="openModal(\'modal-crear\')"]').addEventListener('click', resetForm);

        function editVia(v) {
            resetForm();
            document.getElementById('modal-title').textContent = 'Editar Vía de Administración';
            document.getElementById('form-via').action = updateUrl(v.id);
            document.getElementById('form-method').value = 'PUT';

            ['codigo','nombre','nombre_corto','descripcion','tipo','icono','color_identificacion',
             'velocidad_min_ml_h','velocidad_max_ml_h','osmolaridad_max','riesgo_clinico','observaciones'
            ].forEach(f => {
                const el = document.getElementById('f_' + f);
                if (el) el.value = v[f] ?? (f === 'color_identificacion' ? '#3b82f6' : '');
            });

            ['esteril_requerido','requiere_bomba_infusion','requiere_filtro','permite_bolo',
             'permite_infusion_continua','fotosensible','requiere_monitorizacion','estado'
            ].forEach(f => {
                const el = document.getElementById('f_' + f);
                if (el) el.checked = !!v[f];
            });

            openModal('modal-crear');
        }

        @if($errors->any())
            openModal('modal-crear');
        @endif
    </script>
</x-app-layout>
