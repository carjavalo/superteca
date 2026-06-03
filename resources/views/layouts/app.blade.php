<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Superteca') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            :root {
                --institucional: #2e3a75;
                --institucional-hover: #3b4a96;
            }
            body {
                font-family: 'Figtree', sans-serif;
                margin: 0;
                background-color: #eef0f7;
            }
            .sidebar {
                background: var(--institucional);
                color: #fff;
                width: 250px;
                min-height: 100vh;
                position: fixed;
                top: 0; left: 0;
                display: flex; flex-direction: column;
                box-shadow: 2px 0 8px rgba(0,0,0,0.08);
                z-index: 30;
            }
            .sidebar .brand {
                padding: 22px 20px;
                font-size: 1.35rem;
                font-weight: 700;
                letter-spacing: 1px;
                border-bottom: 1px solid rgba(255,255,255,0.15);
                text-align: center;
            }
            .sidebar nav { flex: 1; padding: 16px 0; }
            .sidebar nav a {
                display: flex; align-items: center; gap: 12px;
                padding: 12px 22px;
                color: #e5e7eb;
                text-decoration: none;
                font-weight: 500;
                border-left: 4px solid transparent;
                transition: all .15s;
            }
            .sidebar nav a:hover,
            .sidebar nav a.active {
                background: rgba(255,255,255,0.10);
                color: #fff;
                border-left-color: #fff;
            }
            .sidebar nav a svg { width: 20px; height: 20px; flex-shrink: 0; }
            .sidebar .footer-side {
                padding: 14px 20px;
                font-size: .8rem;
                color: rgba(255,255,255,0.7);
                border-top: 1px solid rgba(255,255,255,0.15);
                text-align: center;
            }

            /* ── Submenú sidebar ── */
            .nav-group { display: flex; flex-direction: column; }
            .nav-group-btn {
                display: flex; align-items: center; justify-content: space-between;
                padding: 12px 22px;
                color: #e5e7eb;
                background: transparent; border: none; cursor: pointer;
                font-weight: 500; font-size: .95rem; border-left: 4px solid transparent;
                transition: all .15s; width: 100%; font-family: inherit;
            }
            .nav-group-btn:hover, .nav-group-btn.active {
                background: rgba(255,255,255,0.10); color: #fff; border-left-color: #fff;
            }
            .nav-group-btn .chevron { transition: transform .25s; flex-shrink:0; }
            .nav-group-btn.open .chevron { transform: rotate(90deg); }
            .nav-sub {
                max-height: 0; overflow: hidden;
                transition: max-height .3s ease;
                background: rgba(0,0,0,0.15);
            }
            .nav-sub.open { max-height: 300px; }
            .nav-sub a {
                display: flex; align-items: center; gap: 10px;
                padding: 10px 22px 10px 40px;
                color: #cbd5e1; text-decoration: none; font-size: .88rem;
                border-left: 4px solid transparent; transition: all .12s;
            }
            .nav-sub a:hover, .nav-sub a.active {
                background: rgba(255,255,255,0.08); color: #fff; border-left-color: rgba(255,255,255,0.5);
            }

            .main-wrapper { margin-left: 250px; min-height: 100vh; display:flex; flex-direction:column; }
            .topbar {
                background: #fff;
                height: 64px;
                display: flex; align-items: center; justify-content: flex-end;
                padding: 0 24px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.06);
                position: sticky; top: 0; z-index: 20;
            }
            .user-menu { position: relative; }
            .user-menu-button {
                display: flex; align-items: center; gap: 10px;
                background: transparent; border: none; cursor: pointer;
                padding: 6px 10px; border-radius: 8px;
            }
            .user-menu-button:hover { background: #f3f4f6; }
            .user-avatar {
                width: 38px; height: 38px; border-radius: 50%;
                background: var(--institucional); color: #fff;
                display: flex; align-items: center; justify-content: center;
                font-weight: 600; overflow: hidden;
                border: 2px solid var(--institucional);
            }
            .user-avatar img { width: 100%; height: 100%; object-fit: cover; }
            .user-name { color: #1f2937; font-weight: 600; font-size: .95rem; }
            .user-dropdown {
                position: absolute; right: 0; top: 110%;
                background: #fff; min-width: 220px;
                border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.15);
                overflow: hidden; display: none;
                border: 1px solid #e5e7eb;
            }
            .user-dropdown.show { display: block; }
            .user-dropdown a, .user-dropdown button {
                display: flex; align-items: center; gap: 10px;
                width: 100%; text-align: left;
                padding: 11px 16px;
                color: #1f2937; text-decoration: none; font-size: .9rem;
                background: transparent; border: none; cursor: pointer;
                font-family: inherit;
            }
            .user-dropdown a:hover, .user-dropdown button:hover {
                background: #f3f4f6; color: var(--institucional);
            }
            .user-dropdown .divider { height: 1px; background: #e5e7eb; margin: 4px 0; }

            .page-header {
                background: #fff;
                padding: 18px 28px;
                border-bottom: 1px solid #e5e7eb;
            }
            .page-header h2 { color: var(--institucional); font-weight: 700; margin:0; }
            .content-area { padding: 28px; flex: 1; }

            .menu-toggle {
                display: none;
                margin-right: auto;
                background: var(--institucional); color: #fff;
                border: none; padding: 8px 12px; border-radius: 6px; cursor: pointer;
            }
            @media (max-width: 768px) {
                .sidebar { transform: translateX(-100%); transition: transform .25s; }
                .sidebar.open { transform: translateX(0); }
                .main-wrapper { margin-left: 0; }
                .menu-toggle { display: inline-flex; }
            }
        </style>
    </head>
    <body>
        <div style="position:fixed;inset:0;z-index:-1;background-image:url('{{ asset("img/nuevologo.jpg") }}');background-size:cover;background-position:center top;background-attachment:fixed;opacity:0.07;pointer-events:none;"></div>
        <aside class="sidebar" id="sidebar">
            <div class="brand">{{ config('app.name', 'Superteca') }}</div>
            <nav>
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h14V10"/></svg>
                    Inicio
                </a>
                @php $isSuperAdmin = optional(auth()->user()->role)->name === 'Super Admin'; @endphp
                <div class="nav-group">
                    <button class="nav-group-btn {{ request()->routeIs('admin.reportes.*') ? 'active' : '' }}"
                            onclick="toggleGroup('groupReportes')" id="btnGroupReportes">
                        <span style="display:flex;align-items:center;gap:12px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h6m-3-3l3 3-3 3"/></svg>
                            Reportes
                        </span>
                        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div class="nav-sub" id="groupReportes">
                        @if($isSuperAdmin)
                            <a href="{{ route('admin.reportes.registros') }}" class="{{ request()->routeIs('admin.reportes.registros*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M12 11h4M12 15h4M8 11h.01M8 15h.01"/>
                                </svg>
                                Registros
                            </a>
                        @else
                            <span style="display:block; padding:10px 14px; color:#94a3b8; font-size:.82rem; font-style:italic;">Sin reportes disponibles</span>
                        @endif
                    </div>
                </div>

                <div class="nav-group">
                    <button class="nav-group-btn {{ request()->routeIs('admin.medicamentos.*') || request()->routeIs('admin.laboratorios.*') || request()->routeIs('admin.proveedores.*') || request()->routeIs('admin.formas_farmaceuticas.*') || request()->routeIs('admin.vias_administracion.*') || request()->routeIs('admin.unidades_medida.*') ? 'active' : '' }}"
                            onclick="toggleGroup('groupMaestros')" id="btnGroupMaestros">
                        <span style="display:flex;align-items:center;gap:12px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Maestros
                        </span>
                        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div class="nav-sub" id="groupMaestros">
                        <a href="{{ route('admin.medicamentos.index') }}" class="{{ request()->routeIs('admin.medicamentos.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V8l-5-5z"/>
                                <path stroke-linecap="round" d="M9 3v5h10"/>
                                <path stroke-linecap="round" d="M12 12v4M10 14h4"/>
                            </svg>
                            Gestión Medicamentos
                        </a>
                        <a href="{{ route('admin.presentaciones.index') }}" class="{{ request()->routeIs('admin.presentaciones.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Presentaciones
                        </a>
                        <a href="{{ route('admin.laboratorios.index') }}" class="{{ request()->routeIs('admin.laboratorios.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Laboratorios
                        </a>
                        <a href="{{ route('admin.proveedores.index') }}" class="{{ request()->routeIs('admin.proveedores.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18M6 3v18M18 3v18"/>
                            </svg>
                            Proveedores
                        </a>
                        <a href="{{ route('admin.formas_farmaceuticas.index') }}" class="{{ request()->routeIs('admin.formas_farmaceuticas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Formas Farmacéuticas
                        </a>
                        <a href="{{ route('admin.vias_administracion.index') }}" class="{{ request()->routeIs('admin.vias_administracion.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Vías de Administración
                        </a>
                        <a href="{{ route('admin.unidades_medida.index') }}" class="{{ request()->routeIs('admin.unidades_medida.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2v18H3V3zm4 6h2v12H7V9zm4-4h2v16h-2V5zm4 8h2v8h-2v-8zm4-6h2v14h-2V7z"/>
                            </svg>
                            Unidades de Medida
                        </a>
                    </div>
                </div>

                <div class="nav-group">
                    <button class="nav-group-btn {{ request()->routeIs('admin.inventarios.*') || request()->routeIs('admin.entradas.*') || request()->routeIs('admin.salidas.*') || request()->routeIs('admin.ajustes.*') ? 'active' : '' }}"
                            onclick="toggleGroup('groupInventario')" id="btnGroupInventario">
                        <span style="display:flex;align-items:center;gap:12px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Inventario
                        </span>
                        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div class="nav-sub" id="groupInventario">
                        <a href="{{ route('admin.entradas.index') }}" class="{{ request()->routeIs('admin.entradas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                            </svg>
                            Entradas
                        </a>
                        <a href="{{ route('admin.salidas.index') }}" class="{{ request()->routeIs('admin.salidas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 8v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2m16 0l-5 5m5-5h-5M4 8l5 5m-5-5h5m-5 8v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                            </svg>
                            Salidas
                        </a>
                        <a href="{{ route('admin.inventarios.lotes') }}" class="{{ request()->routeIs('admin.inventarios.lotes') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Inventario por Lotes
                        </a>
                        <a href="{{ route('admin.ajustes.index') }}" class="{{ request()->routeIs('admin.ajustes.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317a1 1 0 011.35-.936l1.715.572a8 8 0 003.49.27l1.81-.226a1 1 0 011.084 1.345l-.69 1.69a8 8 0 000 3.876l.69 1.69a1 1 0 01-1.084 1.346l-1.81-.226a8 8 0 00-3.49.27l-1.715.572a1 1 0 01-1.35-.936V4.317z"/>
                                <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Ajustes
                        </a>
                        <a href="{{ route('admin.traslados.index') }}" class="{{ request()->routeIs('admin.traslados.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m-4 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Traslados
                        </a>
                        <a href="{{ route('admin.kardex.index') }}" class="{{ request()->routeIs('admin.kardex.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h6m-3-3l3 3-3 3M3 4h12a2 2 0 012 2v3M3 4v16h10"/>
                            </svg>
                            Kardex
                        </a>
                    </div>
                </div>

                {{-- Producción --}}
                <div class="nav-group">
                    <button class="nav-group-btn {{ request()->routeIs('admin.preparaciones.*') || request()->routeIs('admin.formulas.*') || request()->routeIs('admin.mezclas.*') || request()->routeIs('admin.reempaques.*') ? 'active' : '' }}"
                            onclick="toggleGroup('groupProduccion')" id="btnGroupProduccion">
                        <span style="display:flex;align-items:center;gap:12px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Producción
                        </span>
                        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div class="nav-sub" id="groupProduccion">
                        <a href="{{ route('admin.preparaciones.index') }}" class="{{ request()->routeIs('admin.preparaciones.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Preparaciones
                        </a>
                        <a href="{{ route('admin.formulas.index') }}" class="{{ request()->routeIs('admin.formulas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M12 11h4M12 15h4M8 11h.01M8 15h.01"/>
                            </svg>
                            Fórmulas
                        </a>
                        <a href="{{ route('admin.mezclas.index') }}" class="{{ request()->routeIs('admin.mezclas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17H5a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v2M9 7h6m4 6h-6a2 2 0 00-2 2v4a2 2 0 002 2h6a2 2 0 002-2v-4a2 2 0 00-2-2zM12 17h.01"/>
                            </svg>
                            Mezclas
                        </a>
                        <a href="{{ route('admin.reempaques.index') }}" class="{{ request()->routeIs('admin.reempaques.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Reempaque
                        </a>
                    </div>
                </div>

                {{-- Dispensación --}}
                <div class="nav-group">
                    <button class="nav-group-btn {{ request()->routeIs('admin.dispensacion.*') ? 'active' : '' }}"
                            onclick="toggleGroup('groupDispensacion')" id="btnGroupDispensacion">
                        <span style="display:flex;align-items:center;gap:12px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Dispensación
                        </span>
                        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div class="nav-sub" id="groupDispensacion">
                        <a href="{{ route('admin.dispensacion.entregas.index') }}" class="{{ request()->routeIs('admin.dispensacion.entregas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Entregas
                        </a>
                        <a href="{{ route('admin.dispensacion.pacientes.index') }}" class="{{ request()->routeIs('admin.dispensacion.pacientes.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 14a4 4 0 10-8 0m8 0a4 4 0 11-8 0m8 0v.5A4.5 4.5 0 0120 19v2H4v-2a4.5 4.5 0 014-4.5V14m4-6a3 3 0 100-6 3 3 0 000 6z"/>
                            </svg>
                            Pacientes
                        </a>
                        <a href="{{ route('admin.dispensacion.validaciones.index') }}" class="{{ request()->routeIs('admin.dispensacion.validaciones.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Validación
                        </a>
                    </div>
                </div>

                {{-- Calidad --}}
                <div class="nav-group">
                    <button class="nav-group-btn {{ request()->routeIs('admin.calidad.*') ? 'active' : '' }}"
                            onclick="toggleGroup('groupCalidad')" id="btnGroupCalidad">
                        <span style="display:flex;align-items:center;gap:12px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Calidad
                        </span>
                        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div class="nav-sub" id="groupCalidad">
                        <a href="{{ route('admin.calidad.cadena-frio.index') }}" class="{{ request()->routeIs('admin.calidad.cadena-frio.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-9-9h18M5.6 5.6l12.8 12.8M18.4 5.6L5.6 18.4"/>
                            </svg>
                            Cadena de Frío
                        </a>
                    </div>
                </div>

                {{-- Configuración con submenú --}}
                <div class="nav-group">
                    <button class="nav-group-btn {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                            onclick="toggleGroup('groupConfig')" id="btnGroupConfig">
                        <span style="display:flex;align-items:center;gap:12px;">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20">
                                <path stroke-linecap="round" d="M10.325 4.317a1 1 0 011.35-.936l1.715.572a8 8 0 003.49.27l1.81-.226a1 1 0 011.084 1.345l-.69 1.69a8 8 0 000 3.876l.69 1.69a1 1 0 01-1.084 1.346l-1.81-.226a8 8 0 00-3.49.27l-1.715.572a1 1 0 01-1.35-.936V4.317z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            Configuración
                        </span>
                        <svg class="chevron" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" width="14" height="14">
                            <path stroke-linecap="round" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                    <div class="nav-sub" id="groupConfig">
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path stroke-linecap="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                            </svg>
                            Gestión usuarios
                        </a>
                        <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Gestión de Roles
                        </a>
                    </div>
                </div>
            </nav>
            <div class="footer-side">© {{ date('Y') }} Superteca</div>
        </aside>

        <div class="main-wrapper">
            <header class="topbar">
                <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
                <div class="user-menu">
                    <button class="user-menu-button" onclick="document.getElementById('userDropdown').classList.toggle('show')">
                        <span class="user-name">{{ Auth::user()->name }} {{ Auth::user()->apellido1 }}</span>
                        <span class="user-avatar">
                            @if(Auth::user()->profile_image)
                                <img src="{{ asset('storage/'.Auth::user()->profile_image) }}" alt="avatar">
                            @else
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            @endif
                        </span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="{{ route('profile.edit') }}">👤 Mi cuenta</a>
                        <a href="{{ route('password.change') }}">🔒 Cambiar contraseña</a>
                        <div class="divider"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">🚪 Cerrar sesión</button>
                        </form>
                    </div>
                </div>
            </header>

            @isset($header)
                <div class="page-header">{{ $header }}</div>
            @endisset

            <main class="content-area">
                {{ $slot }}
            </main>
        </div>

        <script>
            document.addEventListener('click', function (e) {
                const menu = document.querySelector('.user-menu');
                const dd = document.getElementById('userDropdown');
                if (menu && dd && !menu.contains(e.target)) dd.classList.remove('show');
            });

            function toggleGroup(id) {
                const sub = document.getElementById(id);
                const btn = document.getElementById('btn' + id.charAt(0).toUpperCase() + id.slice(1));
                sub.classList.toggle('open');
                btn.classList.toggle('open');
            }

            // Auto-abrir submenú si hay una ruta activa dentro
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.nav-sub a.active').forEach(function (a) {
                    const sub = a.closest('.nav-sub');
                    if (sub) {
                        sub.classList.add('open');
                        const btn = sub.previousElementSibling;
                        if (btn) btn.classList.add('open');
                    }
                });
            });
        </script>
    </body>
</html>
