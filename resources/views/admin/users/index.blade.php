<x-app-layout>
    <x-slot name="header">
        <h2>Gestión de Usuarios</h2>
    </x-slot>

    {{-- DataTables CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">

    <style>
        :root { --inst: #2e3a75; }

        /* ── Alertas ── */
        .st-alert {
            padding: 12px 18px; border-radius: 8px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px; font-size: .92rem;
        }
        .st-alert.success { background: #d1fae5; color: #065f46; border-left: 4px solid #10b981; }
        .st-alert.error   { background: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444; }

        /* ── Toolbar ── */
        .toolbar {
            display: flex; align-items: center; justify-content: space-between;
            flex-wrap: wrap; gap: 12px; margin-bottom: 20px;
        }
        .search-wrap { position: relative; }
        .search-wrap input {
            padding: 9px 14px 9px 38px; border: 1.5px solid #d1d5db;
            border-radius: 8px; font-size: .9rem; width: 280px; outline: none;
            transition: border .15s;
        }
        .search-wrap input:focus { border-color: var(--inst); }
        .search-wrap .icon-search {
            position: absolute; left: 11px; top: 50%; transform: translateY(-50%);
            color: #9ca3af; width: 18px; height: 18px;
        }
        .btn-primary {
            background: var(--inst); color: #fff; border: none; cursor: pointer;
            padding: 9px 20px; border-radius: 8px; font-weight: 600; font-size: .9rem;
            display: flex; align-items: center; gap: 7px; transition: background .15s;
            text-decoration: none;
        }
        .btn-primary:hover { background: #3b4a96; color: #fff; }

        /* ── Card tabla ── */
        .card-table {
            background: #fff; border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            overflow: hidden;
        }
        .card-table table { width: 100%; border-collapse: collapse; }
        .card-table thead tr { background: var(--inst); color: #fff; }
        .card-table thead th {
            padding: 13px 16px; text-align: left; font-size: .82rem;
            text-transform: uppercase; letter-spacing: .06em; white-space: nowrap;
        }
        .card-table tbody tr { border-bottom: 1px solid #f3f4f6; transition: background .12s; }
        .card-table tbody tr:last-child { border-bottom: none; }
        .card-table tbody tr:hover { background: #f0f3ff; }
        .card-table td { padding: 12px 16px; font-size: .88rem; color: #374151; vertical-align: middle; }

        /* ── Avatar mini ── */
        .mini-avatar {
            width: 34px; height: 34px; border-radius: 50%; background: var(--inst);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: .85rem; overflow: hidden; flex-shrink: 0;
        }
        .mini-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-cell { display: flex; align-items: center; gap: 10px; }
        .user-cell .user-info .name { font-weight: 600; color: #111827; font-size: .9rem; }
        .user-cell .user-info .email { color: #6b7280; font-size: .78rem; }

        /* ── Badges ── */
        .badge {
            display: inline-block; padding: 3px 10px; border-radius: 20px;
            font-size: .75rem; font-weight: 600;
        }
        .badge-active { background: #d1fae5; color: #065f46; }

        /* ── Acciones ── */
        .actions { display: flex; gap: 6px; align-items: center; }
        .btn-icon {
            width: 32px; height: 32px; border-radius: 7px; border: none; cursor: pointer;
            display: flex; align-items: center; justify-content: center; transition: all .15s;
        }
        .btn-edit   { background: #eff6ff; color: #2563eb; }
        .btn-edit:hover   { background: #2563eb; color: #fff; }
        .btn-delete { background: #fef2f2; color: #dc2626; }
        .btn-delete:hover { background: #dc2626; color: #fff; }
        .btn-view   { background: #f0fdf4; color: #16a34a; }
        .btn-view:hover   { background: #16a34a; color: #fff; }

        /* ── Paginación ── */
        .pagination-wrap {
            display: flex; align-items: center; justify-content: space-between;
            padding: 14px 18px; border-top: 1px solid #f3f4f6;
            flex-wrap: wrap; gap: 10px;
        }
        .pagination-info { color: #6b7280; font-size: .85rem; }
        .pagination-links { display: flex; gap: 4px; }
        .pagination-links a, .pagination-links span {
            padding: 5px 11px; border-radius: 6px; font-size: .85rem;
            border: 1px solid #e5e7eb; color: #374151; text-decoration: none;
            transition: all .12s;
        }
        .pagination-links a:hover { background: var(--inst); color: #fff; border-color: var(--inst); }
        .pagination-links span.active { background: var(--inst); color: #fff; border-color: var(--inst); }

        /* ── Modal ── */
        .modal-overlay {
            position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            display: flex; align-items: center; justify-content: center;
            z-index: 200; padding: 16px;
            opacity: 0; pointer-events: none; transition: opacity .2s;
        }
        .modal-overlay.open { opacity: 1; pointer-events: all; }
        .modal-box {
            background: #fff; border-radius: 14px; width: 100%; max-width: 620px;
            max-height: 90vh; overflow-y: auto;
            transform: translateY(20px); transition: transform .2s;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
        }
        .modal-overlay.open .modal-box { transform: translateY(0); }
        .modal-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 20px 24px 16px; border-bottom: 1px solid #e5e7eb;
        }
        .modal-header h3 { margin: 0; color: var(--inst); font-size: 1.1rem; }
        .modal-close {
            background: none; border: none; cursor: pointer; color: #6b7280;
            font-size: 1.3rem; line-height: 1; padding: 2px;
        }
        .modal-close:hover { color: #dc2626; }
        .modal-body { padding: 20px 24px; }
        .modal-footer { padding: 14px 24px 20px; display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #f3f4f6; }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px; }
        .form-row.col1 { grid-template-columns: 1fr; }
        .form-group label { display: block; font-size: .83rem; font-weight: 600; color: #374151; margin-bottom: 5px; }
        .form-group input {
            width: 100%; padding: 9px 12px; border: 1.5px solid #d1d5db; border-radius: 7px;
            font-size: .9rem; outline: none; transition: border .15s; box-sizing: border-box;
        }
        .form-group input:focus { border-color: var(--inst); }
        .form-group .err { color: #dc2626; font-size: .78rem; margin-top: 3px; }
        .form-divider { height: 1px; background: #e5e7eb; margin: 16px 0; }

        .btn-cancel {
            background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb;
            padding: 9px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: .9rem;
        }
        .btn-cancel:hover { background: #e5e7eb; }

        /* ── Modal confirmación ── */
        .confirm-box {
            background: #fff; border-radius: 14px; width: 100%; max-width: 420px;
            padding: 28px; text-align: center;
            transform: scale(.95); transition: transform .2s;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
        }
        .modal-overlay.open .confirm-box { transform: scale(1); }
        .confirm-icon {
            width: 60px; height: 60px; border-radius: 50%; background: #fee2e2;
            color: #dc2626; display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; margin: 0 auto 16px;
        }
        .confirm-box h3 { color: #111827; margin: 0 0 8px; }
        .confirm-box p { color: #6b7280; font-size: .9rem; margin: 0 0 22px; }

        /* ── Modal ver usuario ── */
        .view-avatar {
            width: 70px; height: 70px; border-radius: 50%; background: var(--inst);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-size: 1.8rem; font-weight: 700; overflow: hidden; margin: 0 auto 14px;
            border: 3px solid var(--inst);
        }
        .view-avatar img { width: 100%; height: 100%; object-fit: cover; }
        .view-field { display: flex; flex-direction: column; gap: 2px; margin-bottom: 12px; }
        .view-field .vf-label { font-size: .76rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; }
        .view-field .vf-value { font-size: .95rem; color: #1f2937; font-weight: 500; }
        .view-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 20px; }

        /* ── Empty state ── */
        .empty-state { text-align: center; padding: 48px 20px; color: #9ca3af; }
        .empty-state svg { width: 56px; height: 56px; margin: 0 auto 12px; display: block; opacity: .5; }

        /* DataTables override */
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate { display: none; }
    </style>

    {{-- ── Alertas ── --}}
    @if(session('success'))
        <div class="st-alert success">✓ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="st-alert error">✗ {{ session('error') }}</div>
    @endif

    {{-- ── Toolbar ── --}}
    <div class="toolbar">
        <form method="GET" action="{{ route('admin.users.index') }}" class="search-wrap" id="searchForm">
            <svg class="icon-search" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/>
            </svg>
            <input type="text" name="search" id="searchInput" value="{{ $search }}"
                   placeholder="Buscar por nombre, correo, cédula..."
                   oninput="debounceSearch()" autocomplete="off">
        </form>

        <div style="display:flex; gap:10px; align-items:center;">
            @if($search)
                <a href="{{ route('admin.users.index') }}" style="font-size:.85rem; color:#6b7280; text-decoration:none;">✕ Limpiar</a>
            @endif
            @puede('Gestión de usuarios','Crear')
            <button class="btn-primary" onclick="openModal('createModal')">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" d="M12 5v14M5 12h14"/>
                </svg>
                Nuevo usuario
            </button>
            @endpuede
        </div>
    </div>

    {{-- Contador --}}
    <div style="margin-bottom:12px; color:#6b7280; font-size:.85rem;">
        {{ $users->total() }} usuario{{ $users->total() !== 1 ? 's' : '' }} encontrado{{ $users->total() !== 1 ? 's' : '' }}
        @if($search) &nbsp;·&nbsp; Filtro: <strong>"{{ $search }}"</strong> @endif
    </div>

    {{-- ── Tabla ── --}}
    <div class="card-table">
        <table id="usersTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Apellidos</th>
                    <th>Cédula</th>
                    <th>Contacto</th>
                    <th>Rol</th>
                    <th>Registrado</th>
                    <th style="text-align:center;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td style="color:#9ca3af; font-size:.8rem;">{{ $u->id }}</td>
                        <td>
                            <div class="user-cell">
                                <div class="mini-avatar">
                                    @if($u->profile_image)
                                        <img src="{{ asset('storage/'.$u->profile_image) }}" alt="">
                                    @else
                                        {{ strtoupper(substr($u->name,0,1)) }}
                                    @endif
                                </div>
                                <div class="user-info">
                                    <div class="name">{{ $u->name }}</div>
                                    <div class="email">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>{{ trim($u->apellido1.' '.$u->apellido2) ?: '—' }}</td>
                        <td>{{ $u->cedula ?: '—' }}</td>
                        <td>{{ $u->contacto ?: '—' }}</td>
                        <td>
                            @if($u->role)
                                <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:.73rem;font-weight:600;background:#eff6ff;color:#2e3a75;">{{ $u->role->name }}</span>
                            @else
                                <span style="color:#d1d5db;font-size:.82rem;">—</span>
                            @endif
                        </td>
                        <td style="white-space:nowrap;">{{ $u->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="actions" style="justify-content:center;">
                                {{-- Ver --}}
                                <button class="btn-icon btn-view" title="Ver detalle"
                                    onclick="openView({{ json_encode($u) }})">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                        <circle cx="12" cy="12" r="3"/>
                                    </svg>
                                </button>
                                {{-- Editar --}}
                                @puede('Gestión de usuarios','Editar')
                                <button class="btn-icon btn-edit" title="Editar"
                                    onclick="openEdit({{ json_encode($u) }})">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5"/>
                                        <path stroke-linecap="round" d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                @endpuede
                                {{-- Eliminar --}}
                                @if($u->id !== auth()->id())
                                @puede('Gestión de usuarios','Eliminar')
                                <button class="btn-icon btn-delete" title="Eliminar"
                                    onclick="openConfirm({{ $u->id }}, '{{ addslashes($u->name.' '.($u->apellido1??'')) }}')">
                                    <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline stroke-linecap="round" points="3 6 5 6 21 6"/>
                                        <path stroke-linecap="round" d="M19 6l-1 14H6L5 6"/>
                                        <path stroke-linecap="round" d="M10 11v6M14 11v6M9 6V4h6v2"/>
                                    </svg>
                                </button>
                                @endpuede
                                @else
                                <span style="width:32px; display:inline-block;"></span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                    <circle cx="9" cy="7" r="4"/>
                                    <path stroke-linecap="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                                </svg>
                                No se encontraron usuarios.
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Paginación --}}
        @if($users->hasPages())
        <div class="pagination-wrap">
            <div class="pagination-info">
                Mostrando {{ $users->firstItem() }}–{{ $users->lastItem() }} de {{ $users->total() }}
            </div>
            <div class="pagination-links">
                @if($users->onFirstPage())
                    <span>‹</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}">‹</a>
                @endif

                @foreach($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                    @if($page == $users->currentPage())
                        <span class="active">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}">›</a>
                @else
                    <span>›</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- MODAL CREAR USUARIO --}}
    {{-- ════════════════════════════════════════ --}}
    <div class="modal-overlay" id="createModal">
        <div class="modal-box" style="max-width:860px; background:#f4f5f7;">
            <div class="modal-header" style="background:#fff;">
                <h3>➕ Nuevo usuario</h3>
                <button class="modal-close" onclick="closeModal('createModal')">✕</button>
            </div>
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body" style="padding: 24px; display: flex; flex-direction: column; gap: 24px;">

                    {{-- ── Información personal ── --}}
                    <div style="background:#fff; padding:28px; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
                        <h3 style="color:#2e3a75; margin-top:0; border-bottom:2px solid #e5e7eb; padding-bottom:10px; margin-bottom: 22px;">Información personal</h3>

                        {{-- Avatar --}}
                        <div style="display:flex; align-items:center; gap:18px; margin-bottom:22px;">
                            <div style="width:88px; height:88px; border-radius:50%; overflow:hidden; background:#2e3a75; color:#fff; display:flex; align-items:center; justify-content:center; font-size:2.5rem; font-weight:700; border:3px solid #2e3a75; flex-shrink:0;">
                                U
                            </div>
                            <div>
                                <label style="display:block; font-size:.85rem; font-weight:600; color:#374151; margin-bottom:6px;">Foto de perfil</label>
                                <input id="profile_image" name="profile_image" type="file" accept="image/*" style="font-size: .85rem; color:#6b7280;">
                                @error('profile_image') <div class="err" style="color: #dc2626; font-size:.78rem; margin-top:3px;">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        {{-- Nombre y apellidos --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
                            <div class="form-group">
                                <label>Nombre *</label>
                                <input type="text" name="name" value="{{ old('name') }}" required>
                                @error('name') <div class="err">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label>Primer apellido</label>
                                <input type="text" name="apellido1" value="{{ old('apellido1') }}">
                                @error('apellido1') <div class="err">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label>Segundo apellido</label>
                                <input type="text" name="apellido2" value="{{ old('apellido2') }}">
                            </div>
                        </div>

                        {{-- Cédula y contacto --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-top:14px;">
                            <div class="form-group">
                                <label>Cédula</label>
                                <input type="text" name="cedula" value="{{ old('cedula') }}">
                                @error('cedula') <div class="err">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label>Contacto (teléfono)</label>
                                <input type="text" name="contacto" value="{{ old('contacto') }}">
                            </div>
                        </div>

                        <div style="height:1px; background:#e5e7eb; margin:24px 0;"></div>

                        {{-- Correo y Rol --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <div class="form-group">
                                <label>Correo electrónico *</label>
                                <input type="email" name="email" value="{{ old('email') }}" required>
                                @error('email') <div class="err">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label>Rol</label>
                                <select name="role_id" style="width: 100%; padding: 9px 12px; border: 1.5px solid #d1d5db; border-radius: 7px; font-size: .9rem; outline: none;">
                                    <option value="">— Ninguno —</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}" {{ old('role_id') == $rol->id ? 'selected' : '' }}>{{ $rol->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ── Seguridad ── --}}
                    <div style="background:#fff; padding:28px; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
                        <h3 style="color:#2e3a75; margin-top:0; border-bottom:2px solid #e5e7eb; padding-bottom:10px; margin-bottom: 22px;">Seguridad</h3>
                        <p style="color:#6b7280; font-size:.9rem; margin-top:0; margin-bottom: 18px;">Asigna la contraseña inicial para la cuenta.</p>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <div class="form-group">
                                <label>Contraseña *</label>
                                <input type="password" name="password" required>
                                @error('password') <div class="err">{{ $message }}</div> @enderror
                            </div>
                            <div class="form-group">
                                <label>Confirmar contraseña *</label>
                                <input type="password" name="password_confirmation" required>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="background:#fff;">
                    <button type="button" class="btn-cancel" onclick="closeModal('createModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- MODAL EDITAR USUARIO --}}
    {{-- ════════════════════════════════════════ --}}
    <div class="modal-overlay" id="editModal">
        <div class="modal-box" style="max-width:860px; background:#f4f5f7;">
            <div class="modal-header" style="background:#fff;">
                <h3>✏️ Editar usuario</h3>
                <button class="modal-close" onclick="closeModal('editModal')">✕</button>
            </div>
            <form method="POST" id="editForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body" style="padding: 24px; display: flex; flex-direction: column; gap: 24px;">

                    {{-- ── Información personal ── --}}
                    <div style="background:#fff; padding:28px; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
                        <h3 style="color:#2e3a75; margin-top:0; border-bottom:2px solid #e5e7eb; padding-bottom:10px; margin-bottom: 22px;">Información personal</h3>

                        {{-- Avatar --}}
                        <div style="display:flex; align-items:center; gap:18px; margin-bottom:22px;">
                            <div id="editAvatarPreview" style="width:88px; height:88px; border-radius:50%; overflow:hidden; background:#2e3a75; color:#fff; display:flex; align-items:center; justify-content:center; font-size:2.5rem; font-weight:700; border:3px solid #2e3a75; flex-shrink:0;">
                                U
                            </div>
                            <div>
                                <label style="display:block; font-size:.85rem; font-weight:600; color:#374151; margin-bottom:6px;">Foto de perfil</label>
                                <input id="edit_profile_image" name="profile_image" type="file" accept="image/*" style="font-size: .85rem; color:#6b7280;">
                                <div style="font-size: .8rem; color: #9ca3af; margin-top: 4px;">Sube una imagen para reemplazar la actual.</div>
                            </div>
                        </div>

                        {{-- Nombre y apellidos --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px;">
                            <div class="form-group">
                                <label>Nombre *</label>
                                <input type="text" name="name" id="edit_name" required>
                            </div>
                            <div class="form-group">
                                <label>Primer apellido</label>
                                <input type="text" name="apellido1" id="edit_apellido1">
                            </div>
                            <div class="form-group">
                                <label>Segundo apellido</label>
                                <input type="text" name="apellido2" id="edit_apellido2">
                            </div>
                        </div>

                        {{-- Cédula y contacto --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-top:14px;">
                            <div class="form-group">
                                <label>Cédula</label>
                                <input type="text" name="cedula" id="edit_cedula">
                            </div>
                            <div class="form-group">
                                <label>Contacto (teléfono)</label>
                                <input type="text" name="contacto" id="edit_contacto">
                            </div>
                        </div>

                        <div style="height:1px; background:#e5e7eb; margin:24px 0;"></div>

                        {{-- Correo y Rol --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <div class="form-group">
                                <label>Correo electrónico *</label>
                                <input type="email" name="email" id="edit_email" required>
                            </div>
                            <div class="form-group">
                                <label>Rol</label>
                                <select name="role_id" id="edit_role_id" style="width: 100%; padding: 9px 12px; border: 1.5px solid #d1d5db; border-radius: 7px; font-size: .9rem; outline: none;">
                                    <option value="">— Ninguno —</option>
                                    @foreach($roles as $rol)
                                        <option value="{{ $rol->id }}">{{ $rol->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- ── Seguridad ── --}}
                    <div style="background:#fff; padding:28px; border-radius:10px; box-shadow:0 1px 4px rgba(0,0,0,0.07);">
                        <h3 style="color:#2e3a75; margin-top:0; border-bottom:2px solid #e5e7eb; padding-bottom:10px; margin-bottom: 22px;">Seguridad</h3>
                        <p style="color:#6b7280; font-size:.9rem; margin-top:0; margin-bottom: 18px;">Deja en blanco si no deseas cambiar la contraseña.</p>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
                            <div class="form-group">
                                <label>Nueva contraseña</label>
                                <input type="password" name="password" placeholder="Opcional">
                            </div>
                            <div class="form-group">
                                <label>Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" placeholder="Repetir">
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer" style="background:#fff;">
                    <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- MODAL VER USUARIO --}}
    {{-- ════════════════════════════════════════ --}}
    <div class="modal-overlay" id="viewModal">
        <div class="modal-box" style="max-width:460px;">
            <div class="modal-header">
                <h3>👤 Detalle de usuario</h3>
                <button class="modal-close" onclick="closeModal('viewModal')">✕</button>
            </div>
            <div class="modal-body" style="text-align:center; padding-bottom:6px;">
                <div class="view-avatar" id="view_avatar"></div>
                <div style="font-size:1.1rem; font-weight:700; color:#111827;" id="view_fullname"></div>
                <div style="color:#6b7280; font-size:.85rem; margin-bottom:18px;" id="view_email_top"></div>
                <div class="form-divider" style="margin:0 0 16px;"></div>
                <div class="view-grid" style="text-align:left;">
                    <div class="view-field">
                        <span class="vf-label">Cédula</span>
                        <span class="vf-value" id="view_cedula">—</span>
                    </div>
                    <div class="view-field">
                        <span class="vf-label">Contacto</span>
                        <span class="vf-value" id="view_contacto">—</span>
                    </div>
                    <div class="view-field">
                        <span class="vf-label">Correo</span>
                        <span class="vf-value" id="view_email">—</span>
                    </div>
                    <div class="view-field">
                        <span class="vf-label">Rol</span>
                        <span class="vf-value" id="view_rol">—</span>
                    </div>
                    <div class="view-field">
                        <span class="vf-label">Registrado</span>
                        <span class="vf-value" id="view_date">—</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal('viewModal')">Cerrar</button>
            </div>
        </div>
    </div>

    {{-- ════════════════════════════════════════ --}}
    {{-- MODAL CONFIRMAR ELIMINAR --}}
    {{-- ════════════════════════════════════════ --}}
    <div class="modal-overlay" id="deleteModal">
        <div class="confirm-box">
            <div class="confirm-icon">🗑</div>
            <h3>¿Eliminar usuario?</h3>
            <p id="deleteMsg">Esta acción es irreversible.</p>
            <div style="display:flex; gap:10px; justify-content:center;">
                <button class="btn-cancel" onclick="closeModal('deleteModal')">Cancelar</button>
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="background:#dc2626; color:#fff; border:none; padding:9px 22px; border-radius:8px; font-weight:600; cursor:pointer; font-size:.9rem;">
                        Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ── Búsqueda con debounce ──
        let debounceTimer;
        function debounceSearch() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                document.getElementById('searchForm').submit();
            }, 450);
        }

        // ── Modal helpers ──
        function openModal(id) {
            document.getElementById(id).classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
            document.body.style.overflow = '';
        }
        document.querySelectorAll('.modal-overlay').forEach(el => {
            el.addEventListener('click', function(e) {
                if (e.target === el) closeModal(el.id);
            });
        });

        // ── Abrir edición ──
        function openEdit(u) {
            document.getElementById('edit_name').value      = u.name ?? '';
            document.getElementById('edit_apellido1').value = u.apellido1 ?? '';
            document.getElementById('edit_apellido2').value = u.apellido2 ?? '';
            document.getElementById('edit_cedula').value    = u.cedula ?? '';
            document.getElementById('edit_contacto').value  = u.contacto ?? '';
            document.getElementById('edit_email').value     = u.email ?? '';
            
            const avatarEdit = document.getElementById('editAvatarPreview');
            if(u.profile_image) {
                avatarEdit.innerHTML = '<img src="/storage/' + u.profile_image + '" alt="" style="width:100%;height:100%;object-fit:cover;">';
            } else {
                avatarEdit.innerHTML = (u.name || '?').charAt(0).toUpperCase();
            }

            const roleSelect = document.getElementById('edit_role_id');
            roleSelect.value = u.role_id ?? '';
            document.getElementById('editForm').action      = '/admin/usuarios/' + u.id;
            openModal('editModal');
        }

        // ── Abrir vista detalle ──
        function openView(u) {
            const initials = (u.name || '?').charAt(0).toUpperCase();
            const avatarEl = document.getElementById('view_avatar');
            if (u.profile_image) {
                avatarEl.innerHTML = '<img src="/storage/' + u.profile_image + '" alt="">';
            } else {
                avatarEl.textContent = initials;
            }
            const full = [u.name, u.apellido1, u.apellido2].filter(Boolean).join(' ');
            document.getElementById('view_fullname').textContent = full || '—';
            document.getElementById('view_email_top').textContent = u.email || '—';
            document.getElementById('view_cedula').textContent   = u.cedula    || '—';
            document.getElementById('view_contacto').textContent = u.contacto  || '—';
            document.getElementById('view_email').textContent    = u.email     || '—';
            document.getElementById('view_rol').innerHTML = u.role
                ? '<span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:.8rem;font-weight:600;background:#eff6ff;color:#2e3a75;">' + u.role.name + '</span>'
                : '—';
            // Format date
            if (u.created_at) {
                const d = new Date(u.created_at);
                document.getElementById('view_date').textContent =
                    d.toLocaleDateString('es-CO', {day:'2-digit', month:'2-digit', year:'numeric'});
            }
            openModal('viewModal');
        }

        // ── Abrir confirmación eliminar ──
        function openConfirm(id, name) {
            document.getElementById('deleteMsg').textContent =
                '¿Deseas eliminar al usuario "' + name.trim() + '"? Esta acción es irreversible.';
            document.getElementById('deleteForm').action = '/admin/usuarios/' + id;
            openModal('deleteModal');
        }

        // ── Abrir modal crear si hubo errores de validación ──
        @if($errors->any() && !old('_edit_flag'))
            window.addEventListener('DOMContentLoaded', () => openModal('createModal'));
        @endif
        @if($errors->any() && old('_edit_flag'))
            window.addEventListener('DOMContentLoaded', () => openModal('editModal'));
        @endif

        // ── Cerrar alertas automáticamente ──
        setTimeout(() => {
            document.querySelectorAll('.st-alert').forEach(el => {
                el.style.transition = 'opacity .5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 3500);
    </script>
</x-app-layout>
