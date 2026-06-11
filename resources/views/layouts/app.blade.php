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
                @php
                    $isSuperAdmin = optional(auth()->user()->role)->name === 'Super Admin';
                    // Helper de permisos: $P('Vista') => ¿el rol actual puede Ver esa vista?
                    $P = fn ($vista, $accion = 'Ver') => \App\Support\Permisos::puede($vista, $accion);
                @endphp
                @php $gReportes = $isSuperAdmin || $P('Consumos generales') || $P('Trazabilidad de lotes'); @endphp
                @if($gReportes)
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
                        @endif
                        @if($P('Consumos generales'))
                            <a href="{{ route('admin.reportes.insumos') }}" class="{{ request()->routeIs('admin.reportes.insumos*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7 14l4-4 4 4 5-7"/>
                                </svg>
                                Consumos
                            </a>
                        @endif
                        @if($P('Trazabilidad de lotes'))
                            <a href="{{ route('admin.reportes.trazabilidad') }}" class="{{ request()->routeIs('admin.reportes.trazabilidad*') ? 'active' : '' }}">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7M15 15l3 3 5-5"/>
                                </svg>
                                Trazabilidad
                            </a>
                        @endif
                    </div>
                </div>
                @endif

                @php $gMaestros = $isSuperAdmin || $P('Medicamentos') || $P('Presentaciones') || $P('Laboratorios') || $P('Proveedores') || $P('Formas farmacéuticas') || $P('Vías de administración') || $P('Unidades de medida'); @endphp
                @if($gMaestros)
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
                        @if($P('Medicamentos'))
                        <a href="{{ route('admin.medicamentos.index') }}" class="{{ request()->routeIs('admin.medicamentos.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2V8l-5-5z"/>
                                <path stroke-linecap="round" d="M9 3v5h10"/>
                                <path stroke-linecap="round" d="M12 12v4M10 14h4"/>
                            </svg>
                            Gestión Medicamentos
                        </a>
                        @endif
                        @if($P('Presentaciones'))
                        <a href="{{ route('admin.presentaciones.index') }}" class="{{ request()->routeIs('admin.presentaciones.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Presentaciones
                        </a>
                        @endif
                        @if($P('Laboratorios'))
                        <a href="{{ route('admin.laboratorios.index') }}" class="{{ request()->routeIs('admin.laboratorios.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Laboratorios
                        </a>
                        @endif
                        @if($P('Proveedores'))
                        <a href="{{ route('admin.proveedores.index') }}" class="{{ request()->routeIs('admin.proveedores.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18M6 3v18M18 3v18"/>
                            </svg>
                            Proveedores
                        </a>
                        @endif
                        @if($P('Formas farmacéuticas'))
                        <a href="{{ route('admin.formas_farmaceuticas.index') }}" class="{{ request()->routeIs('admin.formas_farmaceuticas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Formas Farmacéuticas
                        </a>
                        @endif
                        @if($P('Vías de administración'))
                        <a href="{{ route('admin.vias_administracion.index') }}" class="{{ request()->routeIs('admin.vias_administracion.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Vías de Administración
                        </a>
                        @endif
                        @if($P('Unidades de medida'))
                        <a href="{{ route('admin.unidades_medida.index') }}" class="{{ request()->routeIs('admin.unidades_medida.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2v18H3V3zm4 6h2v12H7V9zm4-4h2v16h-2V5zm4 8h2v8h-2v-8zm4-6h2v14h-2V7z"/>
                            </svg>
                            Unidades de Medida
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                @php $gInventario = $isSuperAdmin || $P('Entradas') || $P('Salidas') || $P('Inventario por lotes') || $P('Ajustes') || $P('Traslados') || $P('Kardex'); @endphp
                @if($gInventario)
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
                        @if($P('Entradas'))
                        <a href="{{ route('admin.entradas.index') }}" class="{{ request()->routeIs('admin.entradas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3"/>
                            </svg>
                            Entradas
                        </a>
                        @endif
                        @if($P('Salidas'))
                        <a href="{{ route('admin.salidas.index') }}" class="{{ request()->routeIs('admin.salidas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 8v-2a2 2 0 00-2-2H6a2 2 0 00-2 2v2m16 0l-5 5m5-5h-5M4 8l5 5m-5-5h5m-5 8v2a2 2 0 002 2h12a2 2 0 002-2v-2"/>
                            </svg>
                            Salidas
                        </a>
                        @endif
                        @if($P('Inventario por lotes'))
                        <a href="{{ route('admin.inventarios.lotes') }}" class="{{ request()->routeIs('admin.inventarios.lotes') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Inventario por Lotes
                        </a>
                        @endif
                        @if($P('Ajustes'))
                        <a href="{{ route('admin.ajustes.index') }}" class="{{ request()->routeIs('admin.ajustes.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317a1 1 0 011.35-.936l1.715.572a8 8 0 003.49.27l1.81-.226a1 1 0 011.084 1.345l-.69 1.69a8 8 0 000 3.876l.69 1.69a1 1 0 01-1.084 1.346l-1.81-.226a8 8 0 00-3.49.27l-1.715.572a1 1 0 01-1.35-.936V4.317z"/>
                                <circle cx="12" cy="12" r="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Ajustes
                        </a>
                        @endif
                        @if($P('Traslados'))
                        <a href="{{ route('admin.traslados.index') }}" class="{{ request()->routeIs('admin.traslados.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m-4 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Traslados
                        </a>
                        @endif
                        @if($P('Kardex'))
                        <a href="{{ route('admin.kardex.index') }}" class="{{ request()->routeIs('admin.kardex.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2a4 4 0 014-4h6m-3-3l3 3-3 3M3 4h12a2 2 0 012 2v3M3 4v16h10"/>
                            </svg>
                            Kardex
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Producción --}}
                @php $gProduccion = $isSuperAdmin || $P('Preparaciones') || $P('Fórmulas magistrales') || $P('Mezclas IV') || $P('Reempaques'); @endphp
                @if($gProduccion)
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
                        @if($P('Preparaciones'))
                        <a href="{{ route('admin.preparaciones.index') }}" class="{{ request()->routeIs('admin.preparaciones.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                            </svg>
                            Preparaciones
                        </a>
                        @endif
                        @if($P('Fórmulas magistrales'))
                        <a href="{{ route('admin.formulas.index') }}" class="{{ request()->routeIs('admin.formulas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2M12 11h4M12 15h4M8 11h.01M8 15h.01"/>
                            </svg>
                            Fórmulas
                        </a>
                        @endif
                        @if($P('Mezclas IV'))
                        <a href="{{ route('admin.mezclas.index') }}" class="{{ request()->routeIs('admin.mezclas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 17H5a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v2M9 7h6m4 6h-6a2 2 0 00-2 2v4a2 2 0 002 2h6a2 2 0 002-2v-4a2 2 0 00-2-2zM12 17h.01"/>
                            </svg>
                            Mezclas
                        </a>
                        @endif
                        @if($P('Reempaques'))
                        <a href="{{ route('admin.reempaques.index') }}" class="{{ request()->routeIs('admin.reempaques.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            Reempaque
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Dispensación --}}
                @php $gDispensacion = $isSuperAdmin || $P('Entregas') || $P('Historial paciente') || $P('Validación farmacéutica'); @endphp
                @if($gDispensacion)
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
                        @if($P('Entregas'))
                        <a href="{{ route('admin.dispensacion.entregas.index') }}" class="{{ request()->routeIs('admin.dispensacion.entregas.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            Entregas
                        </a>
                        @endif
                        @if($P('Historial paciente'))
                        <a href="{{ route('admin.dispensacion.pacientes.index') }}" class="{{ request()->routeIs('admin.dispensacion.pacientes.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 14a4 4 0 10-8 0m8 0a4 4 0 11-8 0m8 0v.5A4.5 4.5 0 0120 19v2H4v-2a4.5 4.5 0 014-4.5V14m4-6a3 3 0 100-6 3 3 0 000 6z"/>
                            </svg>
                            Pacientes
                        </a>
                        @endif
                        @if($P('Validación farmacéutica'))
                        <a href="{{ route('admin.dispensacion.validaciones.index') }}" class="{{ request()->routeIs('admin.dispensacion.validaciones.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Validación
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Calidad --}}
                @php $gCalidad = $isSuperAdmin || $P('Cadena de frío') || $P('Controles de calidad') || $P('Incidentes'); @endphp
                @if($gCalidad)
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
                        @if($P('Cadena de frío'))
                        <a href="{{ route('admin.calidad.cadena-frio.index') }}" class="{{ request()->routeIs('admin.calidad.cadena-frio.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18m-9-9h18M5.6 5.6l12.8 12.8M18.4 5.6L5.6 18.4"/>
                            </svg>
                            Cadena de Frío
                        </a>
                        @endif
                        @if($P('Controles de calidad'))
                        <a href="{{ route('admin.calidad.controles.index') }}" class="{{ request()->routeIs('admin.calidad.controles.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M21 12c0 4.97-4.03 9-9 9s-9-4.03-9-9 4.03-9 9-9 9 4.03 9 9z"/>
                            </svg>
                            Controles
                        </a>
                        @endif
                        @if($P('Incidentes'))
                        <a href="{{ route('admin.calidad.incidentes.index') }}" class="{{ request()->routeIs('admin.calidad.incidentes.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                            </svg>
                            Incidentes
                        </a>
                        @endif
                    </div>
                </div>
                @endif

                {{-- Configuración con submenú --}}
                @php
                    $verUsuarios          = \App\Support\Permisos::puede('Gestión de usuarios');
                    $verRoles             = \App\Support\Permisos::puede('Gestión de roles');
                    $verTiposEntrada      = \App\Support\Permisos::puede('Gestor Tipos de Entrada');
                    $verTiposAdmin        = \App\Support\Permisos::puede('Gestor Tipos de Administración');
                    $verServicios         = \App\Support\Permisos::puede('Gestor de Servicios');
                    $verEps               = \App\Support\Permisos::puede('Gestor de EPS');
                    $verConfig            = $isSuperAdmin || $verUsuarios || $verRoles || $verTiposEntrada || $verTiposAdmin || $verServicios || $verEps;
                @endphp
                @if($verConfig)
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
                        @if($verUsuarios)
                        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path stroke-linecap="round" d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                            </svg>
                            Gestión usuarios
                        </a>
                        @endif
                        @if($verRoles)
                        <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                            Gestión de Roles
                        </a>
                        @endif
                        @if($verTiposEntrada)
                        <a href="{{ route('admin.tipos_entrada.index') }}" class="{{ request()->routeIs('admin.tipos_entrada.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                            </svg>
                            Gestor Tipos de Entrada
                        </a>
                        @endif
                        @if($verTiposAdmin)
                        <a href="{{ route('admin.tipos_administracion.index') }}" class="{{ request()->routeIs('admin.tipos_administracion.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Gestor Tipos de Administración
                        </a>
                        @endif
                        @if($verServicios)
                        <a href="{{ route('admin.tipos_servicios.index') }}" class="{{ request()->routeIs('admin.tipos_servicios.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0H5m14 0h2M5 21H3m9-14h.01M12 11h.01M12 15h.01" />
                            </svg>
                            Gestor de Servicios
                        </a>
                        @endif
                        @if($verEps)
                        <a href="{{ route('admin.eps.index') }}" class="{{ request()->routeIs('admin.eps.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Gestor de EPS
                        </a>
                        @endif
                        @if($isSuperAdmin)
                        <a href="{{ route('admin.permisos.index') }}" class="{{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Gestión de Permisos
                        </a>
                        @endif
                    </div>
                </div>
                @endif
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
                            @if(Auth::user()->profile_image && \Illuminate\Support\Facades\Storage::disk('public')->exists(Auth::user()->profile_image))
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

        {{-- ===================== MODAL DE CONFIRMACIÓN GLOBAL ===================== --}}
        <div id="appConfirmOverlay" class="app-confirm-overlay" aria-hidden="true">
            <div class="app-confirm-box" role="dialog" aria-modal="true" aria-labelledby="appConfirmTitle">
                <div class="app-confirm-icon" id="appConfirmIcon">
                    <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                        <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </div>
                <h3 class="app-confirm-title" id="appConfirmTitle">Confirmar acción</h3>
                <p class="app-confirm-message" id="appConfirmMessage"></p>
                <div class="app-confirm-actions">
                    <button type="button" class="app-confirm-btn app-confirm-cancel" id="appConfirmCancel">Cancelar</button>
                    <button type="button" class="app-confirm-btn app-confirm-ok" id="appConfirmOk">Aceptar</button>
                </div>
            </div>
        </div>

        <style>
            .app-confirm-overlay{
                position:fixed; inset:0; background:rgba(15,23,42,.55); backdrop-filter:blur(2px);
                display:none; align-items:center; justify-content:center; z-index:9999;
                opacity:0; transition:opacity .15s ease;
            }
            .app-confirm-overlay.show{ display:flex; opacity:1; }
            .app-confirm-box{
                background:#fff; width:100%; max-width:420px; margin:1rem; border-radius:16px;
                padding:1.6rem 1.6rem 1.3rem; box-shadow:0 20px 50px rgba(0,0,0,.3);
                text-align:center; transform:translateY(8px) scale(.98); transition:transform .18s ease;
                font-family:'Figtree', sans-serif;
            }
            .app-confirm-overlay.show .app-confirm-box{ transform:translateY(0) scale(1); }
            .app-confirm-icon{
                width:58px; height:58px; border-radius:50%; margin:0 auto .9rem;
                display:flex; align-items:center; justify-content:center;
                background:#eef0f7; color:var(--institucional);
            }
            .app-confirm-box.danger .app-confirm-icon{ background:#fee2e2; color:#dc2626; }
            .app-confirm-title{ margin:0 0 .4rem; font-size:1.18rem; font-weight:700; color:#0f172a; }
            .app-confirm-message{ margin:0 0 1.4rem; font-size:.95rem; color:#475569; line-height:1.5; }
            .app-confirm-actions{ display:flex; gap:.6rem; justify-content:center; }
            .app-confirm-btn{
                flex:1; max-width:160px; padding:.65rem 1rem; border-radius:10px; font-size:.92rem;
                font-weight:600; cursor:pointer; border:0; transition:.15s; font-family:inherit;
            }
            .app-confirm-cancel{ background:#eef0f7; color:#334155; }
            .app-confirm-cancel:hover{ background:#e2e6f1; }
            .app-confirm-ok{ background:var(--institucional); color:#fff; }
            .app-confirm-ok:hover{ background:var(--institucional-hover); }
            .app-confirm-box.danger .app-confirm-ok{ background:#dc2626; }
            .app-confirm-box.danger .app-confirm-ok:hover{ background:#b91c1c; }
        </style>

        <script>
            /* ============ Sistema global de confirmación con estilo del aplicativo ============ */
            (function () {
                const overlay = document.getElementById('appConfirmOverlay');
                const box     = overlay.querySelector('.app-confirm-box');
                const titleEl = document.getElementById('appConfirmTitle');
                const msgEl   = document.getElementById('appConfirmMessage');
                const okBtn   = document.getElementById('appConfirmOk');
                const cancelBtn = document.getElementById('appConfirmCancel');
                let resolver = null;

                function close(result) {
                    overlay.classList.remove('show');
                    overlay.setAttribute('aria-hidden', 'true');
                    const r = resolver; resolver = null;
                    if (r) r(result);
                }

                function looksDestructive(text) {
                    return /elimin|anular|cancelar|rechaz|baja|borrar|revertir|destru/i.test(text || '');
                }

                // API pública: window.appConfirm(mensaje[, {titulo, okText, cancelText}]) -> Promise<bool>
                window.appConfirm = function (message, opts) {
                    opts = opts || {};
                    msgEl.textContent = message || '¿Desea continuar con esta acción?';
                    const destructive = looksDestructive(message);
                    box.classList.toggle('danger', destructive);
                    titleEl.textContent = opts.titulo || (destructive ? 'Confirmar acción' : 'Confirmar');
                    okBtn.textContent = opts.okText || 'Aceptar';
                    cancelBtn.textContent = opts.cancelText || 'Cancelar';
                    overlay.classList.add('show');
                    overlay.setAttribute('aria-hidden', 'false');
                    okBtn.focus();
                    return new Promise(function (resolve) { resolver = resolve; });
                };

                okBtn.addEventListener('click', function () { close(true); });
                cancelBtn.addEventListener('click', function () { close(false); });
                overlay.addEventListener('click', function (e) { if (e.target === overlay) close(false); });
                document.addEventListener('keydown', function (e) {
                    if (!overlay.classList.contains('show')) return;
                    if (e.key === 'Escape') close(false);
                    if (e.key === 'Enter') { e.preventDefault(); close(true); }
                });

                // Extrae el mensaje de un atributo inline: ...confirm('mensaje')...
                function extractMessage(attr) {
                    const m = attr.match(/confirm\(\s*(['"`])([\s\S]*?)\1\s*\)/);
                    if (!m) return '¿Desea continuar con esta acción?';
                    return m[2].replace(/\\(['"`])/g, '$1').replace(/\\n/g, ' ');
                }

                // Convierte los confirm() inline en atributos data-confirm para interceptarlos
                function scan(root) {
                    (root || document).querySelectorAll('form[onsubmit]').forEach(function (f) {
                        const a = f.getAttribute('onsubmit');
                        if (a && a.indexOf('confirm(') !== -1) {
                            f.dataset.confirmMessage = extractMessage(a);
                            f.removeAttribute('onsubmit');
                            f.onsubmit = null;
                        }
                    });
                    (root || document).querySelectorAll('[onclick]').forEach(function (el) {
                        const a = el.getAttribute('onclick');
                        if (a && a.indexOf('confirm(') !== -1) {
                            el.dataset.confirmMessage = extractMessage(a);
                            el.removeAttribute('onclick');
                            el.onclick = null;
                        }
                    });
                }

                document.addEventListener('DOMContentLoaded', function () { scan(document); });

                // Intercepta el envío de formularios con confirmación
                document.addEventListener('submit', function (e) {
                    const form = e.target;
                    if (!form.matches || !form.matches('form[data-confirm-message]')) return;
                    if (form.__confirmed) { form.__confirmed = false; return; }
                    e.preventDefault();
                    appConfirm(form.dataset.confirmMessage).then(function (ok) {
                        if (ok) { form.__confirmed = true; form.requestSubmit ? form.requestSubmit() : form.submit(); }
                    });
                }, true);

                // Intercepta clics en botones/enlaces con confirmación
                document.addEventListener('click', function (e) {
                    const el = e.target.closest && e.target.closest('[data-confirm-message]');
                    if (!el || el.tagName === 'FORM') return;
                    e.preventDefault();
                    e.stopPropagation();
                    const form = el.closest('form');
                    appConfirm(el.dataset.confirmMessage).then(function (ok) {
                        if (!ok) return;
                        if (form) {
                            if (form.requestSubmit && el.tagName === 'BUTTON') { form.requestSubmit(el); }
                            else { form.submit(); }
                        } else if (el.tagName === 'A' && el.href) {
                            window.location.href = el.href;
                        }
                    });
                }, true);
            })();
        </script>

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
