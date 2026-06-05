<x-app-layout>
    <x-slot name="header">
        Catálogo Institucional de Laboratorios Farmacéuticos
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }

        .st-alert { padding:14px 18px; border-radius:10px; margin-bottom:24px; display:flex; align-items:center; gap:12px; font-size:.92rem; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:5px solid #10b981; }

        .layout-catalog {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 25px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .layout-catalog {
                grid-template-columns: 1fr;
            }
        }

        .filter-card { background:rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-radius:14px; box-shadow:0 8px 20px rgba(0,0,0,0.06); padding: 25px; position: sticky; top: 20px;}
        .filter-card h3 { font-size: 1.15rem; color: var(--inst); margin-top: 0; margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid #f3f4f6; padding-bottom: 12px;}
        .filter-group { margin-bottom: 18px; }
        .filter-group label { display: block; font-size: .85rem; font-weight: 600; color: #4b5563; margin-bottom: 8px; }
        .filter-group input, .filter-group select { width: 100%; padding: 10px 12px; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: .9rem; outline:none; transition: all .2s; }
        .filter-group input:focus, .filter-group select:focus { border-color: var(--inst); box-shadow: 0 0 0 3px rgba(46,58,117,0.1); }
        .filter-actions { display: flex; flex-direction: column; gap: 12px; margin-top: 25px; }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .lab-card { background: rgba(255,255,255,0.95); border-radius: 16px; box-shadow: 0 6px 15px rgba(0,0,0,0.04); border: 1px solid rgba(255,255,255,0.8); overflow: hidden; transition: all .2s cubic-bezier(0.4, 0, 0.2, 1); display: flex; flex-direction: column; position: relative;}
        .lab-card:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08); border-color: #e5e7eb;}
        
        .lab-card-header { padding: 20px; border-bottom: 1px solid #f3f4f6; display: flex; align-items: flex-start; justify-content: space-between; gap: 10px;}
        .lab-title-group { flex: 1; }
        .lab-name { font-size: 1.15rem; font-weight: 700; color: #1f2937; margin: 0 0 4px 0; }
        .lab-code { font-size: 0.8rem; color: #6b7280; font-family: monospace; background: #f3f4f6; padding: 2px 6px; border-radius: 4px; display: inline-block;}
        
        .lab-card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; gap: 12px;}
        .lab-info-item { display: flex; align-items: flex-start; gap: 10px; font-size: 0.9rem; color: #4b5563;}
        .lab-info-item svg { width: 18px; height: 18px; color: #9ca3af; flex-shrink: 0; margin-top: 2px;}
        
        .lab-card-footer { padding: 15px 20px; background: #f9fafb; border-top: 1px solid #f3f4f6; display: flex; justify-content: space-between; align-items: center;}
        
        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:10px 20px; border-radius:8px; font-weight:600; font-size:.95rem; display:inline-flex; align-items:center; justify-content:center; gap:7px; transition:all .2s; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; transform: translateY(-2px); box-shadow: 0 4px 10px rgba(46,58,117,0.25); }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; cursor:pointer; padding:10px 20px; border-radius:8px; font-weight:600; font-size:.95rem; display:inline-flex; align-items:center; justify-content:center; gap:7px; transition:all .2s; text-decoration:none; }
        .btn-outline:hover { background:#f9fafb; border-color: #9ca3af; }
        
        .btn-icon { width:34px; height:34px; border-radius:8px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; }
        .btn-edit   { background:#eff6ff; color:#2563eb; }
        .btn-edit:hover   { background:#2563eb; color:#fff; }

        .badge { display:inline-flex; align-items: center; gap:4px; padding:4px 10px; border-radius:20px; font-size:.75rem; font-weight:700; }
        .badge-active   { background:#d1fae5; color:#065f46; border: 1px solid #a7f3d0;}
        .badge-inactive { background:#fee2e2; color:#991b1b; border: 1px solid #fecaca;}
        .badge-frio     { background:#eff6ff; color:#1e40af; border: 1px solid #bfdbfe;}

        /* Head area */
        .catalog-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;}
        .catalog-title h2 { font-size: 1.5rem; font-weight: 800; color: var(--inst); margin: 0; }
        .catalog-title p { color: #6b7280; margin: 4px 0 0 0; font-size: 0.95rem; }

        /* Modals */
        .modal-overlay { position:fixed; inset:0; background:rgba(15, 23, 42, 0.65); backdrop-filter: blur(5px); display:flex; align-items:center; justify-content:center; z-index:200; opacity:0; pointer-events:none; transition:opacity .3s ease-out; }
        .modal-overlay.open { opacity:1; pointer-events:all; }
        .modal-box { background:#fff; border-radius:20px; width:100%; max-width:800px; max-height:90vh; overflow-y:auto; transform:scale(0.95) translateY(20px); transition:all .3s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.3); }
        .modal-overlay.open .modal-box { transform:scale(1) translateY(0); }
        .modal-header { padding:20px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:rgba(255,255,255,0.95); backdrop-filter: blur(8px); z-index:1; }
        .modal-header h3 { margin:0; font-size:1.35rem; color:var(--inst); font-weight: 800;}
        .modal-close { background:none; border:none; font-size:1.5rem; cursor:pointer; color:#9ca3af; transition: color .2s; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px;}
        .modal-close:hover { color:#111827; background: #f3f4f6; }
        .modal-body { padding:24px; }
        .form-section { margin-bottom: 24px; }
        .form-section h4 { font-size: 1rem; color: var(--inst); border-bottom: 2px solid #f3f4f6; padding-bottom: 8px; margin-top: 0; margin-bottom: 16px; font-weight: 700;}
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:.85rem; font-weight:600; color:#374151; margin-bottom:6px; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:10px 12px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.9rem; outline:none; transition: border-color .2s; font-family: inherit;}
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--inst); box-shadow: 0 0 0 3px rgba(46,58,117,0.1); }
        .modal-footer { padding:20px 24px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:12px; background: #f9fafb; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px;}
    
        .empty-state { text-align: center; padding: 4rem 2rem; background: rgba(255,255,255,0.8); border-radius: 16px; border: 2px dashed #e5e7eb; grid-column: 1 / -1;}
    </style>

    @if(session('success'))
        <div class="st-alert success">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="22" height="22"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="layout-catalog">
        
        <aside>
            <div class="filter-card">
                <h3>Búsqueda Rápida</h3>
                <form method="GET" action="{{ route('admin.laboratorios.index') }}">
                    
                    <div class="filter-group">
                        <label>Buscar (Nombre, NIT, Código)</label>
                        <input type="text" name="search" placeholder="Ej: Bayer, 900123" value="{{ request('search') }}">
                    </div>

                    <div class="filter-group">
                        <label>País de Origen</label>
                        <select name="pais_origen">
                            <option value="">Todos los países</option>
                            @foreach($paises as $pais)
                                <option value="{{ $pais }}" {{ request('pais_origen') == $pais ? 'selected' : '' }}>{{ $pais }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Estado de Vinculación</label>
                        <select name="estado">
                            <option value="">Todos los estados</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-primary">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Buscar Laboratorios
                        </button>
                        <a href="{{ route('admin.laboratorios.index') }}" class="btn-outline" style="text-align: center;">
                            Restablecer
                        </a>
                    </div>
                </form>
            </div>
        </aside>

        <main>
            <div class="catalog-top">
                <div class="catalog-title">
                    <h2>Directorio de Fabricantes</h2>
                    <p>Catálogo centralizado de laboratorios y proveedores farmacéuticos.</p>
                </div>
                @puede('Laboratorios','Crear')
                <button class="btn-primary" onclick="openModal('modal-crear')">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                    Añadir Laboratorio
                </button>
                @endpuede
            </div>

            <div class="card-grid">
                @forelse($laboratorios as $lab)
                    <div class="lab-card">
                        <div class="lab-card-header">
                            <div class="lab-title-group">
                                <h3 class="lab-name">{{ $lab->nombre }}</h3>
                                @if($lab->codigo) <span class="lab-code">Cód: {{ $lab->codigo }}</span> @endif
                            </div>
                            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:6px;">
                                @if($lab->semaforo_sanitario === 'verde')
                                    <span class="badge" style="background:#dcfce7; color:#065f46; border:1px solid #6ee7b7;" title="Sin alertas">🟢 Sin alertas</span>
                                @elseif($lab->semaforo_sanitario === 'amarillo')
                                    <span class="badge" style="background:#fef9c3; color:#854d0e; border:1px solid #fde047;" title="Próximo vencimiento">🟡 Próximo Venc.</span>
                                @else
                                    <span class="badge" style="background:#fee2e2; color:#991b1b; border:1px solid #fca5a5;" title="Retiro INVIMA">🔴 Retiro INVIMA</span>
                                @endif

                                @if($lab->estado)
                                    <span class="badge badge-active" title="Registrado y Activo">Activo</span>
                                @else
                                    <span class="badge badge-inactive" title="Alianza Suspendida o Inactiva">Inactivo</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="lab-card-body">
                            @if($lab->nit)
                            <div class="lab-info-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                                <span><strong>NIT/RUT:</strong> {{ $lab->nit }}</span>
                            </div>
                            @endif

                            @if($lab->pais_origen || $lab->ciudad)
                            <div class="lab-info-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $lab->ciudad ? $lab->ciudad.', ' : '' }}{{ $lab->pais_origen ?: 'Ubicación no especificada' }}</span>
                            </div>
                            @endif

                            @if($lab->contacto_comercial || $lab->telefono)
                            <div class="lab-info-item">
                                <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.896-1.596-5.48-4.18-7.076-7.076l1.293-.97c.362-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"></path></svg>
                                <div>
                                    @if($lab->contacto_comercial)<div style="color:#111827;">{{ $lab->contacto_comercial }}</div>@endif
                                    @if($lab->telefono)<div>{{ $lab->telefono }}</div>@endif
                                </div>
                            </div>
                            @endif

                             @if($lab->requiere_cadena_frio)
                            <div class="lab-info-item" style="margin-top:auto;">
                                <span class="badge badge-frio">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v18M8 7l4-4 4 4m0 10l-4 4-4-4"/></svg>
                                    Logística Fría (2-8°C)
                                </span>
                            </div>
                            @endif
                        </div>

                        <div class="lab-card-footer">
                            <span style="font-size: 0.8rem; color: #9ca3af;">
                                Vinculado el {{ $lab->created_at ? $lab->created_at->format('d/m/Y') : '' }}
                            </span>
                            @puede('Laboratorios','Editar')
                            <button type="button" class="btn-icon btn-edit" title="Editar Ficha" onclick='editLaboratorio(@json($lab))'>
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            @endpuede
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <svg fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24" width="60" height="60" style="margin: 0 auto 15px; color: #d1d5db; display: block;"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <h3 style="font-size: 1.25rem; color: #4b5563; margin-bottom: 8px;">Catálogo Vacío</h3>
                        <p style="color: #6b7280; font-size: 0.95rem;">No se encontraron laboratorios que coincidan con los filtros aplicados.</p>
                    </div>
                @endforelse
            </div>
            
            <div style="margin-top: 25px;">
                {{ $laboratorios->links() }}
            </div>
        </main>
    </div>

    <!-- Modal Crear -->
    <div id="modal-crear" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Nuevo Laboratorio</h3>
                <button type="button" class="modal-close" onclick="closeModal('modal-crear')">&times;</button>
            </div>
            <form action="{{ route('admin.laboratorios.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    
                    <div class="form-section">
                        <h4>Identificación Corporativa</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Razón Social <span style="color:#ef4444">*</span></label>
                                <input type="text" name="nombre" required placeholder="Ej: Laboratorios Bayer S.A.S">
                            </div>
                            <div class="form-group">
                                <label>NIT o Rut Corporativo</label>
                                <input type="text" name="nit" placeholder="Ej: 900.123.456-7">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Código Institucional (Opcional)</label>
                                <input type="text" name="codigo" placeholder="Ej: LAB-01">
                            </div>
                            <div class="form-group">
                                <label>Registro INVIMA Base</label>
                                <input type="text" name="registro_invima" placeholder="Nro de certificación">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Localización y Contacto</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>País de Origen</label>
                                <input type="text" name="pais_origen" placeholder="Ej: Alemania, Colombia...">
                            </div>
                            <div class="form-group">
                                <label>Ciudad / Estado</label>
                                <input type="text" name="ciudad" placeholder="Ciudad sede">
                            </div>
                        </div>
                        <div class="form-row" style="grid-template-columns: 1fr;">
                            <div class="form-group">
                                <label>Dirección Física Principal</label>
                                <input type="text" name="direccion" placeholder="Calle, Avenida, Edificio...">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Teléfono(s) Corporativo(s)</label>
                                <input type="text" name="telefono" placeholder="+57 300 000 0000">
                            </div>
                            <div class="form-group">
                                <label>Sitio Web Oficial</label>
                                <input type="url" name="sitio_web" placeholder="https://...">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Contacto Comercial (Representante)</label>
                                <input type="text" name="contacto_comercial" placeholder="Nombre completo">
                            </div>
                            <div class="form-group">
                                <label>Contacto Farmacovigilancia</label>
                                <input type="text" name="contacto_farmacovigilancia" placeholder="Nombre o email exclusivo">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Ajustes Operativos y Sanitarios</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Semáforo Sanitario (Alertas)</label>
                                <select name="semaforo_sanitario" required>
                                    <option value="verde">🟢 Sin alertas (Normal)</option>
                                    <option value="amarillo">🟡 Próximo vencimiento (Alerta)</option>
                                    <option value="rojo">🔴 Retiro INVIMA (Crítico)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row" style="align-items: center; margin-top: 10px;">
                            <div class="form-group mb-0" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0; font-size: .95rem;">
                                    <input type="checkbox" name="requiere_cadena_frio" value="1" style="width: 20px; height: 20px; cursor:pointer; accent-color: var(--inst);">
                                    Certificado en Logística Fría
                                </label>
                            </div>
                            <div class="form-group mb-0" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0; font-size: .95rem;">
                                    <input type="checkbox" name="estado" value="1" checked style="width: 20px; height: 20px; cursor:pointer; accent-color: var(--inst);">
                                    Proveedor Activo
                                </label>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label>Observaciones o Novedades Comerciales</label>
                            <textarea name="observaciones" rows="3" placeholder="Restricciones, acuerdos corporativos, plazos..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeModal('modal-crear')">Cancelar</button>
                    <button type="submit" class="btn-primary">Registrar en Catálogo</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar -->
    <div id="modal-editar" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Editar Ficha del Laboratorio</h3>
                <button type="button" class="modal-close" onclick="closeModal('modal-editar')">&times;</button>
            </div>
            <form id="form-editar" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    
                    <div class="form-section">
                        <h4>Identificación Corporativa</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Razón Social <span style="color:#ef4444">*</span></label>
                                <input type="text" name="nombre" id="edit_nombre" required>
                            </div>
                            <div class="form-group">
                                <label>NIT o Rut Corporativo</label>
                                <input type="text" name="nit" id="edit_nit">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Código Institucional (Opcional)</label>
                                <input type="text" name="codigo" id="edit_codigo">
                            </div>
                            <div class="form-group">
                                <label>Registro INVIMA Base</label>
                                <input type="text" name="registro_invima" id="edit_registro_invima">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Localización y Contacto</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>País de Origen</label>
                                <input type="text" name="pais_origen" id="edit_pais_origen">
                            </div>
                            <div class="form-group">
                                <label>Ciudad / Estado</label>
                                <input type="text" name="ciudad" id="edit_ciudad">
                            </div>
                        </div>
                        <div class="form-row" style="grid-template-columns: 1fr;">
                            <div class="form-group">
                                <label>Dirección Física Principal</label>
                                <input type="text" name="direccion" id="edit_direccion">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Teléfono(s) Corporativo(s)</label>
                                <input type="text" name="telefono" id="edit_telefono">
                            </div>
                            <div class="form-group">
                                <label>Sitio Web Oficial</label>
                                <input type="url" name="sitio_web" id="edit_sitio_web">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Contacto Comercial (Representante)</label>
                                <input type="text" name="contacto_comercial" id="edit_contacto_comercial">
                            </div>
                            <div class="form-group">
                                <label>Contacto Farmacovigilancia</label>
                                <input type="text" name="contacto_farmacovigilancia" id="edit_contacto_farmacovigilancia">
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Ajustes Operativos y Sanitarios</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Semáforo Sanitario (Alertas)</label>
                                <select name="semaforo_sanitario" id="edit_semaforo_sanitario" required>
                                    <option value="verde">🟢 Sin alertas (Normal)</option>
                                    <option value="amarillo">🟡 Próximo vencimiento (Alerta)</option>
                                    <option value="rojo">🔴 Retiro INVIMA (Crítico)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row" style="align-items: center; margin-top: 10px;">
                            <div class="form-group mb-0" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0; font-size: .95rem;">
                                    <input type="checkbox" name="requiere_cadena_frio" id="edit_requiere_cadena_frio" value="1" style="width: 20px; height: 20px; cursor:pointer; accent-color: var(--inst);">
                                    Certificado en Logística Fría
                                </label>
                            </div>
                            <div class="form-group mb-0" style="margin-bottom: 0;">
                                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; margin-bottom: 0; font-size: .95rem;">
                                    <input type="checkbox" name="estado" id="edit_estado" value="1" style="width: 20px; height: 20px; cursor:pointer; accent-color: var(--inst);">
                                    Proveedor Activo
                                </label>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 20px;">
                            <label>Observaciones o Novedades Comerciales</label>
                            <textarea name="observaciones" id="edit_observaciones" rows="3"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="justify-content: space-between;">
                    <button type="button" class="btn-outline" style="color: #dc2626; border-color: #fecaca; background: #fef2f2;" onclick="deleteLaboratorio()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Baja del Sistema
                    </button>
                    <div style="display:flex; gap:12px;">
                        <button type="button" class="btn-outline" onclick="closeModal('modal-editar')">Cancelar</button>
                        <button type="submit" class="btn-primary">Guardar Cambios</button>
                    </div>
                </div>
            </form>

            <form id="form-delete" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
            document.body.style.overflow = '';
        }

        function editLaboratorio(lab) {
            let form = document.getElementById('form-editar');
            form.action = `/admin/laboratorios/` + lab.id;
            
            document.getElementById('edit_nombre').value = lab.nombre || '';
            document.getElementById('edit_nit').value = lab.nit || '';
            document.getElementById('edit_codigo').value = lab.codigo || '';
            document.getElementById('edit_registro_invima').value = lab.registro_invima || '';
            document.getElementById('edit_pais_origen').value = lab.pais_origen || '';
            document.getElementById('edit_ciudad').value = lab.ciudad || '';
            document.getElementById('edit_direccion').value = lab.direccion || '';
            document.getElementById('edit_telefono').value = lab.telefono || '';
            document.getElementById('edit_sitio_web').value = lab.sitio_web || '';
            document.getElementById('edit_contacto_comercial').value = lab.contacto_comercial || '';
            document.getElementById('edit_contacto_farmacovigilancia').value = lab.contacto_farmacovigilancia || '';
            document.getElementById('edit_semaforo_sanitario').value = lab.semaforo_sanitario || 'verde';
            document.getElementById('edit_observaciones').value = lab.observaciones || '';
            
            document.getElementById('edit_requiere_cadena_frio').checked = lab.requiere_cadena_frio == 1;
            document.getElementById('edit_estado').checked = lab.estado == 1;

            let formDel = document.getElementById('form-delete');
            formDel.action = `/admin/laboratorios/` + lab.id;

            openModal('modal-editar');
        }

        function deleteLaboratorio() {
            appConfirm('¿Está seguro de eliminar o dar de baja este laboratorio del catálogo? Esta acción no se puede deshacer.').then(function (ok) {
                if (ok) document.getElementById('form-delete').submit();
            });
        }
    </script>
</x-app-layout>