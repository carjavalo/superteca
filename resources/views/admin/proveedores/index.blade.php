<x-app-layout>
    <x-slot name="header">
        <h2>Maestros · Proveedores</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }

        .st-alert { padding:14px 18px; border-radius:10px; margin-bottom:20px; display:flex; align-items:center; gap:12px; font-size:.92rem; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:5px solid #10b981; }
        .st-alert.error   { background:#fee2e2; color:#991b1b; border-left:5px solid #ef4444; }

        /* Dashboard superior */
        .dash-grid { display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:16px; margin-bottom:24px; }
        .dash-card { background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 4px 12px rgba(0,0,0,.05); border-left:5px solid var(--inst); display:flex; align-items:center; justify-content:space-between; gap:14px; }
        .dash-card .label { color:#6b7280; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
        .dash-card .value { color:var(--inst); font-size:1.85rem; font-weight:800; line-height:1; margin-top:4px; }
        .dash-card .ic { width:46px; height:46px; border-radius:12px; background:rgba(46,58,117,.08); color:var(--inst); display:flex; align-items:center; justify-content:center; }
        .dash-card.green  { border-left-color:#10b981; } .dash-card.green .ic  { background:rgba(16,185,129,.1); color:#10b981; }
        .dash-card.blue   { border-left-color:#3b82f6; } .dash-card.blue .ic   { background:rgba(59,130,246,.1); color:#3b82f6; }
        .dash-card.amber  { border-left-color:#f59e0b; } .dash-card.amber .ic  { background:rgba(245,158,11,.1); color:#f59e0b; }
        .dash-card.purple { border-left-color:#8b5cf6; } .dash-card.purple .ic { background:rgba(139,92,246,.1); color:#8b5cf6; }

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

        .card-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(310px,1fr)); gap:18px; }

        .prov-card { background:#fff; border-radius:16px; box-shadow:0 6px 14px rgba(0,0,0,.04); border:1px solid #f1f1f4; overflow:hidden; transition:all .2s; display:flex; flex-direction:column; }
        .prov-card:hover { transform:translateY(-4px); box-shadow:0 14px 28px rgba(0,0,0,.08); }

        .prov-head { padding:18px 20px; display:flex; align-items:center; gap:14px; border-bottom:1px solid #f3f4f6; }
        .prov-logo { width:54px; height:54px; border-radius:12px; background:#eef0f7; color:var(--inst); display:flex; align-items:center; justify-content:center; font-weight:800; font-size:1.1rem; overflow:hidden; flex-shrink:0; }
        .prov-logo img { width:100%; height:100%; object-fit:cover; }
        .prov-title { flex:1; min-width:0; }
        .prov-title h3 { margin:0; font-size:1.05rem; font-weight:700; color:#1f2937; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .prov-title .sub { color:#6b7280; font-size:.8rem; margin-top:2px; }

        .prov-body { padding:16px 20px; display:flex; flex-direction:column; gap:8px; font-size:.88rem; color:#4b5563; flex:1; }
        .prov-body .row { display:flex; align-items:center; gap:8px; }
        .prov-body .row svg { width:16px; height:16px; color:#9ca3af; flex-shrink:0; }

        .prov-footer { padding:12px 18px; background:#f9fafb; border-top:1px solid #f1f1f4; display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; }

        .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:.72rem; font-weight:700; border:1px solid transparent; }
        .badge-active   { background:#d1fae5; color:#065f46; border-color:#a7f3d0; }
        .badge-inactive { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
        .badge-frio     { background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
        .badge-tipo     { background:#f3f4f6; color:#374151; border-color:#e5e7eb; }
        .badge-credit   { background:#fef9c3; color:#854d0e; border-color:#fde68a; }

        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:9px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; justify-content:center; gap:7px; transition:all .2s; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; transform:translateY(-2px); }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; cursor:pointer; padding:9px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; justify-content:center; gap:7px; text-decoration:none; }
        .btn-outline:hover { background:#f9fafb; }
        .btn-icon { width:34px; height:34px; border-radius:8px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; }
        .btn-view { background:#f5f3ff; color:#7c3aed; } .btn-view:hover { background:#7c3aed; color:#fff; }
        .btn-edit { background:#eff6ff; color:#2563eb; } .btn-edit:hover { background:#2563eb; color:#fff; }
        .btn-del  { background:#fee2e2; color:#dc2626; } .btn-del:hover  { background:#dc2626; color:#fff; }

        .empty-state { text-align:center; padding:3rem 2rem; background:rgba(255,255,255,.8); border-radius:16px; border:2px dashed #e5e7eb; grid-column:1/-1; }

        /* Modal */
        .modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,.65); backdrop-filter:blur(5px); display:flex; align-items:center; justify-content:center; z-index:200; opacity:0; pointer-events:none; transition:opacity .25s; }
        .modal-overlay.open { opacity:1; pointer-events:all; }
        .modal-box { background:#fff; border-radius:18px; width:100%; max-width:900px; max-height:92vh; overflow-y:auto; transform:scale(.96); transition:transform .25s; box-shadow:0 25px 50px -12px rgba(0,0,0,.3); }
        .modal-overlay.open .modal-box { transform:scale(1); }
        .modal-header { padding:18px 22px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:#fff; z-index:1; }
        .modal-header h3 { margin:0; font-size:1.25rem; color:var(--inst); font-weight:800; }
        .modal-close { background:none; border:none; font-size:1.5rem; cursor:pointer; color:#9ca3af; width:32px; height:32px; border-radius:8px; }
        .modal-close:hover { color:#111827; background:#f3f4f6; }
        .modal-body { padding:22px; }
        .modal-footer { padding:16px 22px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; background:#f9fafb; border-bottom-left-radius:18px; border-bottom-right-radius:18px; }

        .form-section { margin-bottom:20px; }
        .form-section h4 { font-size:.95rem; color:var(--inst); border-bottom:2px solid #f3f4f6; padding-bottom:6px; margin:0 0 14px 0; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:12px; }
        .form-row.cols-3 { grid-template-columns:1fr 1fr 1fr; }
        .form-group label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:5px; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:9px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; font-family:inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--inst); box-shadow:0 0 0 3px rgba(46,58,117,.1); }
        .form-check { display:flex; align-items:center; gap:8px; margin-top:24px; }
        @media (max-width:700px) { .form-row, .form-row.cols-3 { grid-template-columns:1fr; } }
    </style>

    @if(session('success'))
        <div class="st-alert success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="st-alert error">{{ session('error') }}</div>
    @endif

    {{-- Dashboard --}}
    <div class="dash-grid">
        <div class="dash-card green">
            <div>
                <div class="label">Proveedores activos</div>
                <div class="value">{{ $stats['activos'] }}</div>
            </div>
            <div class="ic">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
        <div class="dash-card blue">
            <div>
                <div class="label">Con cadena de frío</div>
                <div class="value">{{ $stats['cadena_frio'] }}</div>
            </div>
            <div class="ic">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M8 7l4-4 4 4m0 10l-4 4-4-4"/></svg>
            </div>
        </div>
        <div class="dash-card purple">
            <div>
                <div class="label">Distribuidores</div>
                <div class="value">{{ $stats['distribuidores'] }}</div>
            </div>
            <div class="ic">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zm10 0a2 2 0 11-4 0 2 2 0 014 0zM13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10h10zm0 0h6l-3-7h-3"/></svg>
            </div>
        </div>
        <div class="dash-card amber">
            <div>
                <div class="label">Total registrados</div>
                <div class="value">{{ $stats['total'] }}</div>
            </div>
            <div class="ic">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H3v-2a4 4 0 014-4h0M16 3.13a4 4 0 010 7.75M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>
            </div>
        </div>
    </div>

    <div class="layout-catalog">
        {{-- Filtros --}}
        <aside>
            <div class="filter-card">
                <h3>Búsqueda Rápida</h3>
                <form method="GET" action="{{ route('admin.proveedores.index') }}">
                    <div class="filter-group">
                        <label>Razón social, NIT o código</label>
                        <input type="text" name="search" placeholder="Ej: Audifarma..." value="{{ request('search') }}">
                    </div>
                    <div class="filter-group">
                        <label>Tipo de proveedor</label>
                        <select name="tipo_proveedor">
                            <option value="">Todos</option>
                            @foreach(\App\Models\Proveedor::TIPOS as $key => $label)
                                <option value="{{ $key }}" {{ request('tipo_proveedor') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Ciudad</label>
                        <select name="ciudad">
                            <option value="">Todas</option>
                            @foreach($ciudades as $c)
                                <option value="{{ $c }}" {{ request('ciudad') == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Cadena de frío</label>
                        <select name="cadena_frio">
                            <option value="">Indiferente</option>
                            <option value="1" {{ request('cadena_frio') === '1' ? 'selected' : '' }}>Sí maneja</option>
                            <option value="0" {{ request('cadena_frio') === '0' ? 'selected' : '' }}>No maneja</option>
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
                        <a href="{{ route('admin.proveedores.index') }}" class="btn-outline" style="text-align:center;">Restablecer</a>
                    </div>
                </form>
            </div>
        </aside>

        {{-- Listado --}}
        <main>
            <div class="catalog-top">
                <div>
                    <h2>Directorio de Proveedores</h2>
                    <p>Gestión integral de proveedores, distribuidores, droguerías y laboratorios.</p>
                </div>
                <button class="btn-primary" onclick="openModal('modal-crear')">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nuevo Proveedor
                </button>
            </div>

            <div class="card-grid">
                @forelse($proveedores as $prov)
                    <div class="prov-card">
                        <div class="prov-head">
                            <div class="prov-logo">
                                @if($prov->logo)
                                    <img src="{{ asset('storage/'.$prov->logo) }}" alt="logo">
                                @else
                                    {{ strtoupper(substr($prov->razon_social, 0, 2)) }}
                                @endif
                            </div>
                            <div class="prov-title">
                                <h3 title="{{ $prov->razon_social }}">{{ $prov->nombre_comercial ?: $prov->razon_social }}</h3>
                                <div class="sub">
                                    @if($prov->codigo) <strong>{{ $prov->codigo }}</strong> · @endif
                                    NIT {{ $prov->nit_completo }}
                                </div>
                            </div>
                        </div>

                        <div class="prov-body">
                            @if($prov->tipo_proveedor)
                                <div>
                                    <span class="badge badge-tipo">{{ $prov->tipo_label }}</span>
                                    @if($prov->maneja_cadena_frio)
                                        <span class="badge badge-frio">❄ Cadena frío</span>
                                    @endif
                                    @if(!is_null($prov->dias_credito))
                                        <span class="badge badge-credit">Crédito {{ $prov->dias_credito }}d</span>
                                    @endif
                                </div>
                            @endif
                            @if($prov->ciudad || $prov->pais)
                                <div class="row">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.244-4.243a8 8 0 1111.314 0z"/><circle cx="12" cy="11" r="3"/></svg>
                                    <span>{{ $prov->ciudad }}{{ $prov->ciudad && $prov->pais ? ' - ' : '' }}{{ $prov->pais }}</span>
                                </div>
                            @endif
                            @if($prov->telefono || $prov->celular)
                                <div class="row">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h2.28a2 2 0 011.94 1.515l.7 2.8a2 2 0 01-.45 1.95l-1.27 1.27a16 16 0 006.586 6.586l1.27-1.27a2 2 0 011.95-.45l2.8.7A2 2 0 0121 18.72V21a2 2 0 01-2 2A18 18 0 013 5z"/></svg>
                                    <span>{{ $prov->telefono ?: $prov->celular }}</span>
                                </div>
                            @endif
                            @if($prov->email)
                                <div class="row">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $prov->email }}</span>
                                </div>
                            @endif
                            @if($prov->tiempo_entrega_horas)
                                <div class="row">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" d="M12 7v5l3 2"/></svg>
                                    <span>Entrega en {{ $prov->tiempo_entrega_horas }}h</span>
                                </div>
                            @endif
                        </div>

                        <div class="prov-footer">
                            @if($prov->estado)
                                <span class="badge badge-active">● Activo</span>
                            @else
                                <span class="badge badge-inactive">● Inactivo</span>
                            @endif
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('admin.proveedores.show', $prov) }}" class="btn-icon btn-view" title="Ver detalle">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <button type="button" class="btn-icon btn-edit" title="Editar" onclick='editProveedor(@json($prov))'>
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('admin.proveedores.destroy', $prov) }}" method="POST" onsubmit="return confirm('¿Eliminar proveedor?')" style="display:inline;">
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
                        <h3 style="font-size:1.2rem; color:#4b5563;">Sin proveedores</h3>
                        <p style="color:#6b7280;">No se encontraron proveedores con los filtros aplicados.</p>
                    </div>
                @endforelse
            </div>

            <div style="margin-top:22px;">
                {{ $proveedores->links() }}
            </div>
        </main>
    </div>

    {{-- Modal Crear / Editar (compartido) --}}
    <div id="modal-crear" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modal-title">Nuevo Proveedor</h3>
                <button type="button" class="modal-close" onclick="closeModal('modal-crear')">&times;</button>
            </div>
            <form id="form-proveedor" action="{{ route('admin.proveedores.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div class="modal-body">
                    <div class="form-section">
                        <h4>Identificación</h4>
                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label>Código</label>
                                <input type="text" name="codigo" id="f_codigo">
                            </div>
                            <div class="form-group">
                                <label>Tipo de proveedor</label>
                                <select name="tipo_proveedor" id="f_tipo_proveedor">
                                    <option value="">— Seleccione —</option>
                                    @foreach(\App\Models\Proveedor::TIPOS as $k => $l)
                                        <option value="{{ $k }}">{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Estado</label>
                                <select name="estado" id="f_estado">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Razón social *</label>
                                <input type="text" name="razon_social" id="f_razon_social" required>
                            </div>
                            <div class="form-group">
                                <label>Nombre comercial</label>
                                <input type="text" name="nombre_comercial" id="f_nombre_comercial">
                            </div>
                        </div>
                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label>NIT *</label>
                                <input type="text" name="nit" id="f_nit" required>
                            </div>
                            <div class="form-group">
                                <label>Dígito verificación</label>
                                <input type="text" name="digito_verificacion" id="f_digito_verificacion" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label>Logo</label>
                                <input type="file" name="logo" accept="image/*">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Regulatorio</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Registro INVIMA</label>
                                <input type="text" name="registro_invima" id="f_registro_invima">
                            </div>
                            <div class="form-group">
                                <label>Habilitación en Salud</label>
                                <input type="text" name="habilitacion_salud" id="f_habilitacion_salud">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Ubicación</h4>
                        <div class="form-row">
                            <div class="form-group" style="grid-column:span 2;">
                                <label>Dirección</label>
                                <input type="text" name="direccion" id="f_direccion">
                            </div>
                        </div>
                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label>Ciudad</label>
                                <input type="text" name="ciudad" id="f_ciudad">
                            </div>
                            <div class="form-group">
                                <label>Departamento</label>
                                <input type="text" name="departamento" id="f_departamento">
                            </div>
                            <div class="form-group">
                                <label>País</label>
                                <input type="text" name="pais" id="f_pais" value="Colombia">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Contacto general</h4>
                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label>Teléfono</label>
                                <input type="text" name="telefono" id="f_telefono">
                            </div>
                            <div class="form-group">
                                <label>Celular</label>
                                <input type="text" name="celular" id="f_celular">
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" name="email" id="f_email">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Sitio web</label>
                                <input type="text" name="sitio_web" id="f_sitio_web" placeholder="https://...">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Contactos especializados</h4>
                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label>Contacto comercial</label>
                                <input type="text" name="contacto_comercial" id="f_contacto_comercial">
                            </div>
                            <div class="form-group">
                                <label>Tel. contacto</label>
                                <input type="text" name="telefono_contacto" id="f_telefono_contacto">
                            </div>
                            <div class="form-group">
                                <label>Email contacto</label>
                                <input type="email" name="email_contacto" id="f_email_contacto">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Contacto farmacovigilancia</label>
                                <input type="text" name="contacto_farmacovigilancia" id="f_contacto_farmacovigilancia">
                            </div>
                            <div class="form-group">
                                <label>Contacto logística</label>
                                <input type="text" name="contacto_logistica" id="f_contacto_logistica">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Comercial y logística</h4>
                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label>Condiciones de pago</label>
                                <input type="text" name="condiciones_pago" id="f_condiciones_pago" placeholder="Ej: Contado / 30 días">
                            </div>
                            <div class="form-group">
                                <label>Días de crédito</label>
                                <input type="number" name="dias_credito" id="f_dias_credito" min="0" max="365">
                            </div>
                            <div class="form-group">
                                <label>Tiempo de entrega (horas)</label>
                                <input type="number" name="tiempo_entrega_horas" id="f_tiempo_entrega_horas" min="0">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Horario de entrega</label>
                                <input type="text" name="horario_entrega" id="f_horario_entrega" placeholder="Ej: Lun-Vie 8:00-17:00">
                            </div>
                        </div>
                        <div class="form-row cols-3">
                            <div class="form-group form-check">
                                <input type="hidden" name="maneja_cadena_frio" value="0">
                                <input type="checkbox" name="maneja_cadena_frio" id="f_maneja_cadena_frio" value="1">
                                <label for="f_maneja_cadena_frio" style="margin:0;">Maneja cadena de frío</label>
                            </div>
                            <div class="form-group">
                                <label>Temp. mín (°C)</label>
                                <input type="number" step="0.01" name="temperatura_min" id="f_temperatura_min">
                            </div>
                            <div class="form-group">
                                <label>Temp. máx (°C)</label>
                                <input type="number" step="0.01" name="temperatura_max" id="f_temperatura_max">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Información adicional</h4>
                        <div class="form-group">
                            <label>Certificaciones</label>
                            <textarea name="certificaciones" id="f_certificaciones" rows="2" placeholder="Ej: ISO 9001, BPM, BPA..."></textarea>
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

        const STORE_URL = "{{ route('admin.proveedores.store') }}";
        function updateUrl(id) { return "{{ url('admin/proveedores') }}/" + id; }

        function resetForm() {
            const f = document.getElementById('form-proveedor');
            f.reset();
            f.action = STORE_URL;
            document.getElementById('form-method').value = 'POST';
            document.getElementById('modal-title').textContent = 'Nuevo Proveedor';
        }

        document.querySelector('[onclick="openModal(\'modal-crear\')"]').addEventListener('click', resetForm);

        function editProveedor(p) {
            resetForm();
            document.getElementById('modal-title').textContent = 'Editar Proveedor';
            document.getElementById('form-proveedor').action = updateUrl(p.id);
            document.getElementById('form-method').value = 'PUT';

            const fields = ['codigo','tipo_proveedor','razon_social','nombre_comercial','nit','digito_verificacion',
                'registro_invima','habilitacion_salud','direccion','ciudad','departamento','pais',
                'telefono','celular','email','sitio_web','contacto_comercial','telefono_contacto','email_contacto',
                'contacto_farmacovigilancia','contacto_logistica','condiciones_pago','dias_credito',
                'tiempo_entrega_horas','horario_entrega','temperatura_min','temperatura_max',
                'certificaciones','observaciones'];
            fields.forEach(f => {
                const el = document.getElementById('f_' + f);
                if (el) el.value = p[f] ?? '';
            });
            document.getElementById('f_estado').value = p.estado ? '1' : '0';
            document.getElementById('f_maneja_cadena_frio').checked = !!p.maneja_cadena_frio;

            openModal('modal-crear');
        }

        @if($errors->any())
            openModal('modal-crear');
        @endif
    </script>
</x-app-layout>
