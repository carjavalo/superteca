<x-app-layout>
    <x-slot name="header">
        <h2>Gestión de Roles</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }

        .st-alert { padding:12px 18px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-size:.92rem; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:4px solid #10b981; }
        .st-alert.error   { background:#fee2e2; color:#991b1b; border-left:4px solid #ef4444; }

        .toolbar { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px; }
        .search-wrap { position:relative; }
        .search-wrap input { padding:9px 14px 9px 38px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.9rem; width:300px; outline:none; transition:border .15s; }
        .search-wrap input:focus { border-color:var(--inst); }
        .search-wrap .ico-s { position:absolute; left:11px; top:50%; transform:translateY(-50%); color:#9ca3af; width:17px; height:17px; }
        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:9px 20px; border-radius:8px; font-weight:600; font-size:.9rem; display:flex; align-items:center; gap:7px; transition:background .15s; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; color:#fff; }

        .card-table { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.07); overflow:hidden; }
        .card-table table { width:100%; border-collapse:collapse; }
        .card-table thead tr { background:var(--inst); color:#fff; }
        .card-table thead th { padding:13px 14px; text-align:left; font-size:.78rem; text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }
        .card-table tbody tr { border-bottom:1px solid #f3f4f6; transition:background .12s; }
        .card-table tbody tr:last-child { border-bottom:none; }
        .card-table tbody tr:hover { background:#f0f3ff; }
        .card-table td { padding:12px 14px; font-size:.87rem; color:#374151; vertical-align:middle; }

        .role-icon { width:38px; height:38px; border-radius:9px; background:var(--inst); color:#fff; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
        .role-cell { display:flex; align-items:center; gap:12px; }
        .role-name { font-weight:700; color:#111827; font-size:.9rem; }
        .role-id   { font-size:.73rem; color:#9ca3af; }

        .actions { display:flex; gap:6px; align-items:center; justify-content:center; }
        .btn-icon { width:32px; height:32px; border-radius:7px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; }
        .btn-edit   { background:#eff6ff; color:#2563eb; }
        .btn-edit:hover   { background:#2563eb; color:#fff; }
        .btn-delete { background:#fef2f2; color:#dc2626; }
        .btn-delete:hover { background:#dc2626; color:#fff; }
        .btn-view   { background:#f0fdf4; color:#16a34a; }
        .btn-view:hover   { background:#16a34a; color:#fff; }

        .pagination-wrap { display:flex; align-items:center; justify-content:space-between; padding:14px 18px; border-top:1px solid #f3f4f6; flex-wrap:wrap; gap:10px; }
        .pagination-info { color:#6b7280; font-size:.85rem; }
        .pagination-links { display:flex; gap:4px; }
        .pagination-links a, .pagination-links span { padding:5px 11px; border-radius:6px; font-size:.85rem; border:1px solid #e5e7eb; color:#374151; text-decoration:none; transition:all .12s; }
        .pagination-links a:hover { background:var(--inst); color:#fff; border-color:var(--inst); }
        .pagination-links span.active { background:var(--inst); color:#fff; border-color:var(--inst); }

        /* Modal */
        .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:200; padding:16px; opacity:0; pointer-events:none; transition:opacity .2s; }
        .modal-overlay.open { opacity:1; pointer-events:all; }
        .modal-box { background:#fff; border-radius:14px; width:100%; max-width:480px; transform:translateY(20px); transition:transform .2s; box-shadow:0 25px 60px rgba(0,0,0,0.25); }
        .modal-overlay.open .modal-box { transform:translateY(0); }
        .modal-header { display:flex; align-items:center; justify-content:space-between; padding:18px 24px 14px; border-bottom:1px solid #e5e7eb; }
        .modal-header h3 { margin:0; color:var(--inst); font-size:1.05rem; }
        .modal-close { background:none; border:none; cursor:pointer; color:#6b7280; font-size:1.3rem; line-height:1; padding:2px; }
        .modal-close:hover { color:#dc2626; }
        .modal-body { padding:20px 24px; }
        .modal-footer { padding:12px 24px 18px; display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #f3f4f6; }

        .form-group { margin-bottom:16px; }
        .form-group:last-child { margin-bottom:0; }
        .form-group label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:5px; }
        .form-group input, .form-group textarea { width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:7px; font-size:.88rem; outline:none; transition:border .15s; box-sizing:border-box; font-family:inherit; }
        .form-group input:focus, .form-group textarea:focus { border-color:var(--inst); }
        .form-group .char-hint { font-size:.73rem; color:#9ca3af; margin-top:3px; text-align:right; }
        .form-group .err { color:#dc2626; font-size:.77rem; margin-top:3px; }

        .btn-cancel { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; padding:9px 20px; border-radius:8px; font-weight:600; cursor:pointer; font-size:.9rem; font-family:inherit; }
        .btn-cancel:hover { background:#e5e7eb; }

        /* Modal ver */
        .view-badge { display:inline-flex; align-items:center; justify-content:center; width:64px; height:64px; border-radius:14px; background:var(--inst); color:#fff; font-size:1.7rem; margin:0 auto 14px; }
        .vf-label { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:#9ca3af; display:block; margin-bottom:3px; }
        .vf-value { font-size:.95rem; color:#1f2937; font-weight:500; }

        /* Confirm */
        .confirm-box { background:#fff; border-radius:14px; width:100%; max-width:420px; padding:28px; text-align:center; transform:scale(.95); transition:transform .2s; box-shadow:0 25px 60px rgba(0,0,0,0.25); }
        .modal-overlay.open .confirm-box { transform:scale(1); }
        .confirm-icon { width:60px; height:60px; border-radius:50%; background:#fee2e2; color:#dc2626; display:flex; align-items:center; justify-content:center; font-size:1.6rem; margin:0 auto 16px; }
        .confirm-box h3 { color:#111827; margin:0 0 8px; }
        .confirm-box p { color:#6b7280; font-size:.9rem; margin:0 0 22px; }

        .empty-state { text-align:center; padding:48px 20px; color:#9ca3af; }
        .empty-state svg { width:52px; height:52px; margin:0 auto 12px; display:block; opacity:.4; }

        .total-badge { display:inline-flex; align-items:center; gap:6px; background:#eff6ff; color:var(--inst); font-size:.82rem; font-weight:600; padding:4px 12px; border-radius:20px; }
    </style>

    @if(session('success'))
        <div class="st-alert success">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="st-alert error">✗ {{ session('error') }}</div>
    @endif

    {{-- ── Toolbar ── --}}
    <div class="toolbar">
        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
            <form method="GET" action="{{ route('admin.roles.index') }}" class="search-wrap" id="searchForm">
                <svg class="ico-s" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/>
                </svg>
                <input type="text" name="search" id="searchInput" value="{{ $search }}"
                       placeholder="Buscar por nombre o descripción..."
                       oninput="debounceSearch()" autocomplete="off">
            </form>
            @if($search)
                <a href="{{ route('admin.roles.index') }}" style="font-size:.85rem; color:#6b7280; text-decoration:none;">✕ Limpiar</a>
            @endif
            <span class="total-badge">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                </svg>
                {{ $roles->total() }} rol{{ $roles->total() !== 1 ? 'es' : '' }}
            </span>
        </div>
        <button class="btn-primary" onclick="openModal('createModal')">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
            </svg>
            Nuevo rol
        </button>
    </div>

    {{-- ── Tabla ── --}}
    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Rol</th>
                    <th>Descripción</th>
                    <th>Creado</th>
                    <th style="text-align:center; width:120px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td style="color:#9ca3af; font-size:.78rem;">{{ $role->id }}</td>
                    <td>
                        <div class="role-cell">
                            <div class="role-icon">
                                {{ strtoupper(substr($role->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="role-name">{{ $role->name }}</div>
                                <div class="role-id">ID #{{ $role->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td style="color:#6b7280; font-size:.85rem; max-width:320px;">
                        {{ $role->descripcion ?: '—' }}
                    </td>
                    <td style="color:#9ca3af; font-size:.82rem; white-space:nowrap;">
                        {{ $role->created_at->format('d/m/Y') }}
                    </td>
                    <td>
                        <div class="actions">
                            <button class="btn-icon btn-view" title="Ver detalle"
                                    onclick="openView({{ json_encode($role) }})">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-edit" title="Editar"
                                    onclick="openEdit({{ json_encode($role) }})">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                                    <path stroke-linecap="round" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                </svg>
                            </button>
                            <button class="btn-icon btn-delete" title="Eliminar"
                                    onclick="openConfirm({{ $role->id }}, '{{ addslashes($role->name) }}')">
                                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <polyline stroke-linecap="round" points="3 6 5 6 21 6"/>
                                    <path stroke-linecap="round" d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/>
                                </svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5">
                        <div class="empty-state">
                            <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            No se encontraron roles.
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($roles->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-info">
                Mostrando {{ $roles->firstItem() }}–{{ $roles->lastItem() }} de {{ $roles->total() }}
            </div>
            <div class="pagination-links">
                @if($roles->onFirstPage())
                    <span>‹</span>
                @else
                    <a href="{{ $roles->previousPageUrl() }}">‹</a>
                @endif
                @foreach($roles->getUrlRange(1, $roles->lastPage()) as $page => $url)
                    @if($page == $roles->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
                @if($roles->hasMorePages())
                    <a href="{{ $roles->nextPageUrl() }}">›</a>
                @else
                    <span>›</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ════════════════════════════ --}}
    {{-- MODAL CREAR --}}
    {{-- ════════════════════════════ --}}
    <div class="modal-overlay" id="createModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>➕ Nuevo rol</h3>
                <button class="modal-close" onclick="closeModal('createModal')">✕</button>
            </div>
            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre del rol *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               maxlength="60" placeholder="Ej: Administrador, Farmacéutico..."
                               oninput="updateCount(this,'c_name_count')">
                        <div class="char-hint"><span id="c_name_count">0</span>/60</div>
                        @error('name') <div class="err">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" value="{{ old('descripcion') }}"
                               maxlength="100" placeholder="Breve descripción del rol..."
                               oninput="updateCount(this,'c_desc_count')">
                        <div class="char-hint"><span id="c_desc_count">0</span>/100</div>
                        @error('descripcion') <div class="err">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('createModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Crear rol</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════ --}}
    {{-- MODAL EDITAR --}}
    {{-- ════════════════════════════ --}}
    <div class="modal-overlay" id="editModal">
        <div class="modal-box">
            <div class="modal-header">
                <h3>✏️ Editar rol</h3>
                <button class="modal-close" onclick="closeModal('editModal')">✕</button>
            </div>
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label>Nombre del rol *</label>
                        <input type="text" name="name" id="e_name" required maxlength="60"
                               oninput="updateCount(this,'e_name_count')">
                        <div class="char-hint"><span id="e_name_count">0</span>/60</div>
                    </div>
                    <div class="form-group">
                        <label>Descripción</label>
                        <input type="text" name="descripcion" id="e_descripcion" maxlength="100"
                               oninput="updateCount(this,'e_desc_count')">
                        <div class="char-hint"><span id="e_desc_count">0</span>/100</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════ --}}
    {{-- MODAL VER --}}
    {{-- ════════════════════════════ --}}
    <div class="modal-overlay" id="viewModal">
        <div class="modal-box" style="max-width:400px;">
            <div class="modal-header">
                <h3>🔍 Detalle del rol</h3>
                <button class="modal-close" onclick="closeModal('viewModal')">✕</button>
            </div>
            <div class="modal-body" style="text-align:center;">
                <div style="display:flex; justify-content:center; margin-bottom:16px;">
                    <div style="width:64px;height:64px;border-radius:14px;background:var(--inst);color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.7rem;font-weight:700;" id="v_icon"></div>
                </div>
                <div style="text-align:left; background:#f9fafb; border-radius:10px; padding:14px 18px; display:flex; flex-direction:column; gap:12px;">
                    <div>
                        <span class="vf-label">ID</span>
                        <span class="vf-value" id="v_id">—</span>
                    </div>
                    <div>
                        <span class="vf-label">Nombre del rol</span>
                        <span class="vf-value" id="v_name">—</span>
                    </div>
                    <div>
                        <span class="vf-label">Descripción</span>
                        <span class="vf-value" id="v_desc">—</span>
                    </div>
                    <div>
                        <span class="vf-label">Fecha de creación</span>
                        <span class="vf-value" id="v_created">—</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('viewModal')">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════ --}}
    {{-- MODAL ELIMINAR --}}
    {{-- ════════════════════════════ --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="confirm-box">
            <div class="confirm-icon">🗑</div>
            <h3>¿Eliminar rol?</h3>
            <p id="deleteMsg">Esta acción es irreversible.</p>
            <div style="display:flex; gap:10px; justify-content:center;">
                <button class="btn-cancel" onclick="closeModal('deleteModal')">Cancelar</button>
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:#dc2626;color:#fff;border:none;padding:9px 22px;border-radius:8px;font-weight:600;cursor:pointer;font-size:.9rem;">
                        Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let debounceTimer;
        function debounceSearch() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => document.getElementById('searchForm').submit(), 450);
        }

        function openModal(id)  { document.getElementById(id).classList.add('open');    document.body.style.overflow='hidden'; }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
        document.querySelectorAll('.modal-overlay').forEach(el => {
            el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); });
        });

        function updateCount(input, spanId) {
            document.getElementById(spanId).textContent = input.value.length;
        }

        function openEdit(r) {
            const f = document.getElementById('editForm');
            f.action = '/admin/roles/' + r.id;
            const nameEl = document.getElementById('e_name');
            const descEl = document.getElementById('e_descripcion');
            nameEl.value = r.name ?? '';
            descEl.value = r.descripcion ?? '';
            document.getElementById('e_name_count').textContent = nameEl.value.length;
            document.getElementById('e_desc_count').textContent = descEl.value.length;
            openModal('editModal');
        }

        function openView(r) {
            document.getElementById('v_icon').textContent    = r.name ? r.name.charAt(0).toUpperCase() : '?';
            document.getElementById('v_id').textContent      = '#' + r.id;
            document.getElementById('v_name').textContent    = r.name || '—';
            document.getElementById('v_desc').textContent    = r.descripcion || '—';
            // Formatear fecha
            const d = new Date(r.created_at);
            document.getElementById('v_created').textContent = isNaN(d)
                ? '—'
                : d.toLocaleDateString('es-CO', { day:'2-digit', month:'2-digit', year:'numeric' });
            openModal('viewModal');
        }

        function openConfirm(id, name) {
            document.getElementById('deleteMsg').textContent  = '¿Deseas eliminar el rol "' + name + '"? Esta acción es irreversible.';
            document.getElementById('deleteForm').action      = '/admin/roles/' + id;
            openModal('deleteModal');
        }

        // Auto-dismiss alertas
        setTimeout(() => {
            document.querySelectorAll('.st-alert').forEach(el => {
                el.style.transition = 'opacity .5s';
                el.style.opacity    = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 3500);
    </script>
</x-app-layout>
