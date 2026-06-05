<x-app-layout>
    <x-slot name="header">
        <h2>Gestión de Permisos</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }

        .pm-alert { padding:12px 18px; border-radius:8px; margin-bottom:18px; display:flex; align-items:center; gap:10px; font-size:.92rem; }
        .pm-alert.success { background:#d1fae5; color:#065f46; border-left:4px solid #10b981; }
        .pm-alert.error   { background:#fee2e2; color:#991b1b; border-left:4px solid #ef4444; }
        .pm-alert.warn    { background:#fef3c7; color:#92400e; border-left:4px solid #f59e0b; }

        /* KPIs */
        .pm-kpis { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:14px; margin-bottom:20px; }
        .pm-kpi { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:16px 18px; display:flex; align-items:center; gap:13px; }
        .pm-kpi .ic { width:42px; height:42px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1.25rem; flex-shrink:0; }
        .pm-kpi .val { font-size:1.5rem; font-weight:800; color:#111827; line-height:1; }
        .pm-kpi .lbl { font-size:.74rem; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; margin-top:4px; }

        /* Toolbar */
        .pm-toolbar { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:14px 18px; display:flex; align-items:flex-end; justify-content:space-between; flex-wrap:wrap; gap:14px; margin-bottom:18px; }
        .pm-field { display:flex; flex-direction:column; gap:5px; }
        .pm-field label { font-size:.72rem; font-weight:700; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; }
        .pm-field select, .pm-field input { padding:9px 12px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; transition:border .15s; background:#fff; font-family:inherit; }
        .pm-field select:focus, .pm-field input:focus { border-color:var(--inst); }
        .pm-field select.rol { font-weight:700; color:var(--inst); min-width:230px; }
        .pm-actions { display:flex; gap:8px; flex-wrap:wrap; }
        .pm-btn { border:none; cursor:pointer; padding:9px 16px; border-radius:8px; font-weight:600; font-size:.86rem; display:inline-flex; align-items:center; gap:7px; text-decoration:none; transition:all .15s; font-family:inherit; }
        .pm-btn.primary { background:var(--inst); color:#fff; } .pm-btn.primary:hover { background:#3b4a96; }
        .pm-btn.ghost   { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; } .pm-btn.ghost:hover { background:#e5e7eb; }

        /* Layout principal */
        .pm-layout { display:grid; grid-template-columns:1fr 290px; gap:18px; align-items:start; }
        @media (max-width:1100px){ .pm-layout { grid-template-columns:1fr; } }

        .pm-legend { display:flex; gap:16px; flex-wrap:wrap; font-size:.8rem; color:#6b7280; margin-bottom:14px; align-items:center; }
        .pm-legend .dot { width:11px; height:11px; border-radius:50%; display:inline-block; margin-right:5px; vertical-align:middle; }

        /* Módulos */
        .pm-module { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:14px; overflow:hidden; }
        .pm-mod-head { display:flex; align-items:center; gap:12px; padding:13px 18px; cursor:pointer; user-select:none; border-left:4px solid var(--inst); }
        .pm-mod-head:hover { background:#f8f9ff; }
        .pm-mod-ic { font-size:1.3rem; }
        .pm-mod-title { font-weight:700; color:#1f2937; font-size:.98rem; }
        .pm-mod-count { font-size:.74rem; color:#6b7280; background:#eef2ff; color:var(--inst); padding:3px 9px; border-radius:20px; font-weight:700; }
        .pm-mod-tools { margin-left:auto; display:flex; gap:6px; align-items:center; }
        .pm-mini { background:#f3f4f6; border:1px solid #e5e7eb; color:#4b5563; border-radius:6px; padding:4px 9px; font-size:.74rem; cursor:pointer; font-weight:600; font-family:inherit; }
        .pm-mini:hover { background:var(--inst); color:#fff; border-color:var(--inst); }
        .pm-chev { transition:transform .2s; color:#9ca3af; }
        .pm-module.collapsed .pm-chev { transform:rotate(-90deg); }
        .pm-module.collapsed .pm-mod-body { display:none; }

        .pm-mod-body { overflow-x:auto; }
        table.pm-matrix { width:100%; border-collapse:collapse; }
        table.pm-matrix thead th { background:#f9fafb; padding:9px 10px; font-size:.7rem; text-transform:uppercase; letter-spacing:.03em; color:#6b7280; border-bottom:1.5px solid #eef0f4; white-space:nowrap; text-align:center; }
        table.pm-matrix thead th.vcol { text-align:left; padding-left:18px; min-width:220px; }
        .pm-col-toggle { background:none; border:none; cursor:pointer; font:inherit; color:#6b7280; font-size:.7rem; text-transform:uppercase; letter-spacing:.03em; font-weight:700; padding:2px 4px; border-radius:4px; }
        .pm-col-toggle:hover { color:var(--inst); background:#eef2ff; }
        table.pm-matrix tbody tr { border-bottom:1px solid #f3f4f6; }
        table.pm-matrix tbody tr:last-child { border-bottom:none; }
        table.pm-matrix tbody tr:hover { background:#f0f3ff; }
        table.pm-matrix td { padding:8px 10px; text-align:center; vertical-align:middle; }
        table.pm-matrix td.vcol { text-align:left; padding-left:18px; }
        .pm-vista-name { font-size:.87rem; color:#374151; font-weight:500; }
        .pm-vista-count { font-size:.7rem; color:#9ca3af; margin-left:8px; }

        /* Switch */
        .switch { position:relative; display:inline-block; width:38px; height:21px; }
        .switch input { opacity:0; width:0; height:0; }
        .switch .slider { position:absolute; cursor:pointer; inset:0; background:#d1d5db; transition:.2s; border-radius:21px; }
        .switch .slider:before { content:""; position:absolute; height:15px; width:15px; left:3px; bottom:3px; background:#fff; transition:.2s; border-radius:50%; box-shadow:0 1px 2px rgba(0,0,0,.3); }
        .switch input:checked + .slider { background:#10b981; }
        .switch input:checked + .slider:before { transform:translateX(17px); }

        /* Panel lateral del rol */
        .pm-side { position:sticky; top:16px; display:flex; flex-direction:column; gap:14px; }
        .pm-rolecard { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:18px; }
        .pm-rolecard .ravatar { width:54px; height:54px; border-radius:13px; background:var(--inst); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:800; margin-bottom:12px; }
        .pm-rolecard .rname { font-size:1.1rem; font-weight:800; color:#111827; }
        .pm-rolecard .rlbl { font-size:.74rem; color:#9ca3af; text-transform:uppercase; letter-spacing:.04em; }
        .pm-stat { display:flex; justify-content:space-between; align-items:center; padding:9px 0; border-top:1px solid #f3f4f6; font-size:.86rem; }
        .pm-stat .k { color:#6b7280; } .pm-stat .v { font-weight:700; color:#1f2937; }
        .pm-bar { height:8px; background:#eef0f4; border-radius:6px; overflow:hidden; margin-top:6px; }
        .pm-bar > span { display:block; height:100%; background:linear-gradient(90deg,#10b981,#34d399); }

        /* Barra de guardado fija */
        .pm-savebar { position:sticky; bottom:0; background:#fff; border-top:2px solid #eef0f4; border-radius:12px 12px 0 0; box-shadow:0 -4px 14px rgba(0,0,0,.07); padding:13px 18px; display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top:6px; z-index:5; }
        .pm-savebar .info { font-size:.85rem; color:#6b7280; }
        .pm-savebar .info b { color:var(--inst); }

        /* Comparación */
        .pm-compare { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.06); padding:18px; margin-bottom:18px; }
        .pm-compare h3 { margin:0 0 12px; color:var(--inst); font-size:1rem; }
        .pm-compare table { width:100%; border-collapse:collapse; }
        .pm-compare th, .pm-compare td { padding:8px 12px; font-size:.84rem; text-align:left; border-bottom:1px solid #f3f4f6; }
        .pm-compare thead th { background:var(--inst); color:#fff; font-size:.74rem; text-transform:uppercase; }
        .pm-compare td.num { text-align:center; font-weight:700; }
        .pm-compare tr.diff { background:#fff7ed; }
        .pm-chip { display:inline-block; padding:2px 9px; border-radius:20px; font-size:.74rem; font-weight:700; }
        .pm-chip.full { background:#d1fae5; color:#065f46; } .pm-chip.zero { background:#fee2e2; color:#991b1b; } .pm-chip.part { background:#fef3c7; color:#92400e; }

        /* Modal */
        .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; z-index:200; padding:16px; opacity:0; pointer-events:none; transition:opacity .2s; }
        .modal-overlay.open { opacity:1; pointer-events:all; }
        .modal-box { background:#fff; border-radius:14px; width:100%; max-width:460px; transform:translateY(20px); transition:transform .2s; box-shadow:0 25px 60px rgba(0,0,0,.25); }
        .modal-overlay.open .modal-box { transform:translateY(0); }
        .modal-header { display:flex; align-items:center; justify-content:space-between; padding:18px 24px 14px; border-bottom:1px solid #e5e7eb; }
        .modal-header h3 { margin:0; color:var(--inst); font-size:1.05rem; }
        .modal-close { background:none; border:none; cursor:pointer; color:#6b7280; font-size:1.3rem; line-height:1; }
        .modal-body { padding:20px 24px; display:flex; flex-direction:column; gap:14px; }
        .modal-footer { padding:12px 24px 18px; display:flex; justify-content:flex-end; gap:10px; }
        .modal-body label { font-size:.82rem; font-weight:600; color:#374151; display:block; margin-bottom:5px; }
        .modal-body select { width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; font-family:inherit; }
        .pm-hint { font-size:.8rem; color:#6b7280; background:#f9fafb; border-radius:8px; padding:10px 12px; }

        .pm-empty { text-align:center; padding:40px; color:#9ca3af; background:#fff; border-radius:12px; }
    </style>

    @if(session('success')) <div class="pm-alert success">✓ {{ session('success') }}</div> @endif
    @if(session('error'))   <div class="pm-alert error">✗ {{ session('error') }}</div> @endif
    @if($errors->any())     <div class="pm-alert error">✗ {{ $errors->first() }}</div> @endif

    {{-- ─────────── KPIs ─────────── --}}
    <div class="pm-kpis">
        <div class="pm-kpi"><div class="ic" style="background:#eef2ff;color:#4f46e5;">👥</div><div><div class="val">{{ $kpis['usuarios'] }}</div><div class="lbl">Usuarios</div></div></div>
        <div class="pm-kpi"><div class="ic" style="background:#ecfeff;color:#0891b2;">🛡️</div><div><div class="val">{{ $kpis['roles'] }}</div><div class="lbl">Roles</div></div></div>
        <div class="pm-kpi"><div class="ic" style="background:#f0fdf4;color:#16a34a;">📄</div><div><div class="val">{{ $kpis['vistas'] }}</div><div class="lbl">Vistas</div></div></div>
        <div class="pm-kpi"><div class="ic" style="background:#fef9c3;color:#ca8a04;">⚡</div><div><div class="val">{{ $kpis['acciones'] }}</div><div class="lbl">Acciones</div></div></div>
        <div class="pm-kpi"><div class="ic" style="background:#dcfce7;color:#059669;">✓</div><div><div class="val">{{ number_format($kpis['asignaciones']) }}</div><div class="lbl">Asignaciones</div></div></div>
        <div class="pm-kpi"><div class="ic" style="background:#fae8ff;color:#a21caf;">★</div><div><div class="val">{{ $kpis['especiales'] }}</div><div class="lbl">Especiales</div></div></div>
    </div>

    {{-- ─────────── Toolbar ─────────── --}}
    <div class="pm-toolbar">
        <form method="GET" action="{{ route('admin.permisos.index') }}" id="rolForm" style="display:flex; gap:14px; flex-wrap:wrap; align-items:flex-end;">
            <div class="pm-field">
                <label>Rol / Perfil</label>
                <select name="rol" class="rol" onchange="document.getElementById('rolForm').submit()">
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}" @selected($rolSeleccionado && $r->id === $rolSeleccionado->id)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pm-field">
                <label>Módulo</label>
                <select id="filtroModulo" onchange="pmFilter()">
                    <option value="">Todos los módulos</option>
                    @foreach($modulosCatalogo as $k => $m)
                        <option value="{{ $k }}" @selected($filtroModulo === $k)>{{ $m['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pm-field">
                <label>Buscar vista</label>
                <input type="text" id="buscarVista" placeholder="Ej: Entradas, Mezclas..." value="{{ $busqueda }}" oninput="pmFilter()" autocomplete="off">
            </div>
        </form>

        <div class="pm-actions">
            <button type="button" class="pm-btn ghost" onclick="openModal('cmpModal')">⚖️ Comparar</button>
            <button type="button" class="pm-btn ghost" onclick="openModal('dupModal')">📑 Duplicar</button>
            @if($rolSeleccionado)
                <a class="pm-btn ghost" href="{{ route('admin.permisos.exportar', ['rol' => $rolSeleccionado->id]) }}">⬇️ Exportar</a>
            @endif
        </div>
    </div>

    {{-- ─────────── Comparación ─────────── --}}
    @if($comparacion)
        <div class="pm-compare">
            <h3>⚖️ Comparación: {{ $comparacion['rolA']->name }} vs {{ $comparacion['rolB']->name }}</h3>
            @if(empty($comparacion['filas']))
                <p style="color:#9ca3af;">Ninguno de los dos roles tiene permisos asignados.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Módulo</th><th>Vista</th>
                            <th style="text-align:center;">{{ $comparacion['rolA']->name }}</th>
                            <th style="text-align:center;">{{ $comparacion['rolB']->name }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($comparacion['filas'] as $f)
                            <tr class="{{ $f['difiere'] ? 'diff' : '' }}">
                                <td style="color:#6b7280;">{{ $f['modulo'] }}</td>
                                <td>{{ $f['vista'] }}</td>
                                <td class="num"><span class="pm-chip {{ $f['a'] === 0 ? 'zero' : ($f['a'] >= $f['total'] ? 'full' : 'part') }}">{{ $f['a'] }}/{{ $f['total'] }}</span></td>
                                <td class="num"><span class="pm-chip {{ $f['b'] === 0 ? 'zero' : ($f['b'] >= $f['total'] ? 'full' : 'part') }}">{{ $f['b'] }}/{{ $f['total'] }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <p style="font-size:.78rem; color:#9ca3af; margin-top:10px;">Las filas resaltadas indican diferencias entre ambos roles.</p>
            @endif
        </div>
    @endif

    <div class="pm-legend">
        <span><span class="dot" style="background:#10b981;"></span>Permitido</span>
        <span><span class="dot" style="background:#d1d5db;"></span>Denegado</span>
        <span style="margin-left:auto; color:#9ca3af;">Activa cada acción con su interruptor y pulsa <b>Guardar cambios</b>.</span>
    </div>

    @if($rolInfo && $rolInfo['super'])
        <div class="pm-alert warn">⚠️ El rol <b>Super Admin</b> tiene acceso total al sistema por diseño; sus asignaciones no son necesarias.</div>
    @endif

    {{-- ─────────── Layout matriz + panel ─────────── --}}
    <div class="pm-layout">
        <div>
            @if($rolSeleccionado)
            <form method="POST" action="{{ route('admin.permisos.update') }}" id="permForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="rol_id" value="{{ $rolSeleccionado->id }}">

                <div id="modulesWrap">
                    @foreach($modulos as $mod)
                        <div class="pm-module" data-mod="{{ $mod['key'] }}" data-label="{{ mb_strtolower($mod['label']) }}">
                            <div class="pm-mod-head" onclick="toggleModule(this)">
                                <span class="pm-mod-ic">{{ $mod['icono'] }}</span>
                                <span class="pm-mod-title">{{ $mod['label'] }}</span>
                                <span class="pm-mod-count" data-count="{{ $mod['key'] }}">{{ $mod['activos'] }}/{{ $mod['total'] }}</span>
                                <span class="pm-mod-tools" onclick="event.stopPropagation()">
                                    <button type="button" class="pm-mini" onclick="markModule('{{ $mod['key'] }}', true)">Marcar todo</button>
                                    <button type="button" class="pm-mini" onclick="markModule('{{ $mod['key'] }}', false)">Limpiar</button>
                                    <svg class="pm-chev" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M19 9l-7 7-7-7"/></svg>
                                </span>
                            </div>
                            <div class="pm-mod-body">
                                <table class="pm-matrix">
                                    <thead>
                                        <tr>
                                            <th class="vcol">Vista</th>
                                            @foreach($mod['acciones'] as $col)
                                                <th><button type="button" class="pm-col-toggle" onclick="toggleColumn('{{ $mod['key'] }}', {{ $col['id'] }})" title="Marcar/limpiar columna">{{ $col['nombre'] }}</button></th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($mod['vistas'] as $v)
                                            <tr class="pm-row" data-vista="{{ mb_strtolower($v['vista']) }}">
                                                <td class="vcol">
                                                    <span class="pm-vista-name">{{ $v['vista'] }}</span>
                                                    @if($v['descripcion'])<span class="pm-vista-count">· {{ $v['descripcion'] }}</span>@endif
                                                </td>
                                                @foreach($mod['acciones'] as $col)
                                                    <td>
                                                        <label class="switch">
                                                            <input type="checkbox" class="pm-check" data-mod="{{ $mod['key'] }}" data-aid="{{ $col['id'] }}"
                                                                   name="perm[{{ $v['id'] }}][{{ $col['id'] }}]" value="1" @checked($v['estados'][$col['id']])>
                                                            <span class="slider"></span>
                                                        </label>
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @empty
                                            <tr><td colspan="{{ count($mod['acciones']) + 1 }}" style="padding:18px; color:#9ca3af;">Sin vistas en este módulo.</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pm-savebar">
                    <div class="info">Editando rol <b>{{ $rolSeleccionado->name }}</b> · <span id="totalOn">{{ $rolInfo['activos'] }}</span> acciones permitidas</div>
                    <button type="submit" class="pm-btn primary">💾 Guardar cambios</button>
                </div>
            </form>
            @else
                <div class="pm-empty">No hay roles disponibles. Crea un rol en <b>Configuración → Gestión de Roles</b>.</div>
            @endif
        </div>

        {{-- Panel lateral --}}
        <div class="pm-side">
            @if($rolInfo)
                <div class="pm-rolecard">
                    <div class="ravatar">{{ strtoupper(substr($rolInfo['nombre'], 0, 1)) }}</div>
                    <div class="rlbl">Rol seleccionado</div>
                    <div class="rname">{{ $rolInfo['nombre'] }}</div>
                    <div class="pm-stat"><span class="k">Usuarios con el rol</span><span class="v">{{ $rolInfo['usuarios'] }}</span></div>
                    <div class="pm-stat"><span class="k">Acciones permitidas</span><span class="v" id="sideActivos">{{ $rolInfo['activos'] }}</span></div>
                    <div class="pm-stat"><span class="k">Asignables totales</span><span class="v">{{ $rolInfo['total'] }}</span></div>
                    <div class="pm-bar"><span id="sideBar" style="width:{{ $rolInfo['total'] ? round($rolInfo['activos']/$rolInfo['total']*100) : 0 }}%"></span></div>
                </div>
                <div class="pm-rolecard" style="font-size:.82rem; color:#6b7280;">
                    <b style="color:#374151;">💡 Atajos</b>
                    <p style="margin:8px 0 0;">Usa los botones <b>Marcar todo</b> / <b>Limpiar</b> de cada módulo, o haz clic en el nombre de una columna para activar/desactivar esa acción en todo el módulo.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- ─────────── Modal Duplicar ─────────── --}}
    <div class="modal-overlay" id="dupModal">
        <div class="modal-box">
            <div class="modal-header"><h3>📑 Duplicar permisos</h3><button class="modal-close" onclick="closeModal('dupModal')">✕</button></div>
            <form method="POST" action="{{ route('admin.permisos.duplicar') }}">
                @csrf
                <div class="modal-body">
                    <div class="pm-hint">Copia <b>todos</b> los permisos de un rol origen hacia un rol destino. Los permisos actuales del destino serán reemplazados.</div>
                    <div>
                        <label>Copiar permisos de (origen)</label>
                        <select name="origen_id" required>
                            @foreach($roles as $r)<option value="{{ $r->id }}" @selected($rolSeleccionado && $r->id === $rolSeleccionado->id)>{{ $r->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label>Aplicar a (destino)</label>
                        <select name="destino_id" required>
                            @foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="pm-btn ghost" onclick="closeModal('dupModal')">Cancelar</button>
                    <button type="submit" class="pm-btn primary">Duplicar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ─────────── Modal Comparar ─────────── --}}
    <div class="modal-overlay" id="cmpModal">
        <div class="modal-box">
            <div class="modal-header"><h3>⚖️ Comparar roles</h3><button class="modal-close" onclick="closeModal('cmpModal')">✕</button></div>
            <form method="GET" action="{{ route('admin.permisos.index') }}">
                @if($rolSeleccionado)<input type="hidden" name="rol" value="{{ $rolSeleccionado->id }}">@endif
                <div class="modal-body">
                    <div class="pm-hint">Muestra una tabla comparativa de los permisos concedidos a dos roles, vista por vista.</div>
                    <div>
                        <label>Rol A</label>
                        <select name="cmp_a" required>
                            @foreach($roles as $r)<option value="{{ $r->id }}" @selected($rolSeleccionado && $r->id === $rolSeleccionado->id)>{{ $r->name }}</option>@endforeach
                        </select>
                    </div>
                    <div>
                        <label>Rol B</label>
                        <select name="cmp_b" required>
                            @foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="pm-btn ghost" onclick="closeModal('cmpModal')">Cancelar</button>
                    <button type="submit" class="pm-btn primary">Comparar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleModule(head) { head.closest('.pm-module').classList.toggle('collapsed'); }

        function openModal(id)  { document.getElementById(id).classList.add('open');    document.body.style.overflow='hidden'; }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
        document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); }));

        function markModule(key, on) {
            document.querySelectorAll('.pm-check[data-mod="' + key + '"]').forEach(c => c.checked = on);
            recalc();
        }
        function toggleColumn(key, aid) {
            const boxes = document.querySelectorAll('.pm-check[data-mod="' + key + '"][data-aid="' + aid + '"]');
            const allOn = Array.from(boxes).every(c => c.checked);
            boxes.forEach(c => c.checked = !allOn);
            recalc();
        }

        function recalc() {
            let total = 0;
            document.querySelectorAll('.pm-module').forEach(mod => {
                const key = mod.dataset.mod;
                const boxes = mod.querySelectorAll('.pm-check');
                const on = Array.from(boxes).filter(c => c.checked).length;
                total += on;
                const badge = mod.querySelector('[data-count="' + key + '"]');
                if (badge) badge.textContent = on + '/' + boxes.length;
            });
            const totalEl = document.getElementById('totalOn');   if (totalEl) totalEl.textContent = total;
            const sideEl  = document.getElementById('sideActivos'); if (sideEl) sideEl.textContent = total;
            const bar = document.getElementById('sideBar');
            const grand = document.querySelectorAll('.pm-check').length;
            if (bar && grand) bar.style.width = Math.round(total / grand * 100) + '%';
        }
        document.addEventListener('change', e => { if (e.target.classList && e.target.classList.contains('pm-check')) recalc(); });

        // Filtro cliente: módulo + búsqueda de vista
        function pmFilter() {
            const mod = (document.getElementById('filtroModulo')?.value || '').toLowerCase();
            const q   = (document.getElementById('buscarVista')?.value || '').trim().toLowerCase();
            document.querySelectorAll('.pm-module').forEach(card => {
                const okMod = !mod || card.dataset.mod === mod;
                let visibles = 0;
                card.querySelectorAll('.pm-row').forEach(row => {
                    const okQ = !q || row.dataset.vista.includes(q);
                    row.style.display = okQ ? '' : 'none';
                    if (okQ) visibles++;
                });
                card.style.display = (okMod && (visibles > 0 || !q)) ? '' : 'none';
            });
        }
        // Aplica filtro inicial si venía precargado.
        if (document.getElementById('buscarVista')?.value || document.getElementById('filtroModulo')?.value) pmFilter();

        setTimeout(() => document.querySelectorAll('.pm-alert.success, .pm-alert.error').forEach(el => {
            el.style.transition = 'opacity .5s'; el.style.opacity = '0'; setTimeout(() => el.remove(), 500);
        }), 4000);
    </script>
</x-app-layout>
