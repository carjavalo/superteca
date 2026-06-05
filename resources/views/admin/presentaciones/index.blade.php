<x-app-layout>
    <x-slot name="header">
        Gestión de Presentaciones
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }

        .st-alert { padding:12px 18px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-size:.92rem; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:4px solid #10b981; }

        .layout-split {
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 25px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .layout-split {
                grid-template-columns: 1fr;
            }
        }

        .filter-card { background:rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.05); padding: 25px; position: sticky; top: 20px;}
        .filter-card h3 { font-size: 1.15rem; color: var(--inst); margin-top: 0; margin-bottom: 20px; font-weight: 700; border-bottom: 2px solid #f3f4f6; padding-bottom: 10px;}
        .filter-group { margin-bottom: 15px; }
        .filter-group label { display: block; font-size: .85rem; font-weight: 600; color: #4b5563; margin-bottom: 6px; }
        .filter-group input, .filter-group select { width: 100%; padding: 10px 12px; border: 1.5px solid #d1d5db; border-radius: 8px; font-size: .9rem; outline:none; transition: all .2s; }
        .filter-group input:focus, .filter-group select:focus { border-color: var(--inst); box-shadow: 0 0 0 3px rgba(46,58,117,0.1); }
        .filter-actions { display: flex; flex-direction: column; gap: 10px; margin-top: 25px; }

        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:10px 20px; border-radius:8px; font-weight:600; font-size:.95rem; display:flex; align-items:center; justify-content:center; gap:7px; transition:all .2s; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; transform: translateY(-1px); box-shadow: 0 4px 6px rgba(46,58,117,0.2); }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #e5e7eb; cursor:pointer; padding:10px 20px; border-radius:8px; font-weight:600; font-size:.95rem; display:flex; align-items:center; justify-content:center; gap:7px; transition:all .2s; text-decoration:none; }
        .btn-outline:hover { background:#f9fafb; border-color: #d1d5db; }

        .card-table-wrapper { background:rgba(255,255,255,0.9); backdrop-filter: blur(10px); border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.05); padding: 25px; border: 1px solid rgba(255,255,255,0.5);}
        .card-table-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;}
        .card-table-title { font-size: 1.25rem; font-weight: 700; color: #1f2937; margin: 0; }
        .card-table-desc { font-size: 0.9rem; color: #6b7280; margin-top: 5px; }
        
        .card-table { overflow-x:auto; }
        .card-table table { width:100%; border-collapse: separate; border-spacing: 0; min-width: 800px; }
        .card-table thead tr th { background:rgba(243,244,246,0.8); color:#4b5563; font-weight: 700; border-bottom: 2px solid #e5e7eb; text-transform: uppercase; letter-spacing: 0.05em; font-size: 0.75rem; padding: 14px 16px; text-align: left;}
        .card-table tbody tr { transition:all .15s; }
        .card-table tbody tr:hover { background:rgba(240,243,255,0.6); transform: scale(1.002); box-shadow: 0 2px 5px rgba(0,0,0,0.03); border-radius: 8px;}
        .card-table tbody td { padding:14px 16px; font-size:.9rem; color:#374151; vertical-align:middle; border-bottom: 1px solid #f3f4f6;}
        
        .info-cell strong { color: var(--inst); font-weight: 700; display: block; font-size: 0.98rem; margin-bottom: 2px;}
        .info-cell span { font-size: 0.8rem; color: #4b5563; background: #e5e7eb; padding: 2px 6px; border-radius: 4px;}

        .badge { display:inline-block; padding:4px 10px; border-radius:20px; font-size:.75rem; font-weight:700; }
        .badge-active   { background:#d1fae5; color:#065f46; border: 1px solid #a7f3d0;}
        .badge-inactive { background:#fee2e2; color:#991b1b; border: 1px solid #fecaca;}
        .badge-warning  { background:#eff6ff; color:#1e40af; border: 1px solid #bfdbfe;}

        .actions { display:flex; gap:6px; align-items:center; }
        .btn-icon { width:32px; height:32px; border-radius:8px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; }
        .btn-edit   { background:#eff6ff; color:#2563eb; }
        .btn-edit:hover   { background:#2563eb; color:#fff; }
        .btn-delete { background:#fef2f2; color:#dc2626; }
        .btn-delete:hover { background:#dc2626; color:#fff; }

        .pagination-wrap { display:flex; justify-content:center; padding:18px 0 0 0; }

        /* Modals */
        .modal-overlay { position:fixed; inset:0; background:rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); display:flex; align-items:center; justify-content:center; z-index:200; opacity:0; pointer-events:none; transition:opacity .25s ease-out; }
        .modal-overlay.open { opacity:1; pointer-events:all; }
        .modal-box { background:#fff; border-radius:16px; width:100%; max-width:650px; max-height:90vh; overflow-y:auto; transform:scale(0.95) translateY(20px); transition:all .25s cubic-bezier(0.34, 1.56, 0.64, 1); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); }
        .modal-overlay.open .modal-box { transform:scale(1) translateY(0); }
        .modal-header { padding:20px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:rgba(255,255,255,0.95); backdrop-filter: blur(8px); z-index:1; }
        .modal-header h3 { margin:0; font-size:1.25rem; color:var(--inst); font-weight: 700;}
        .modal-close { background:none; border:none; font-size:1.5rem; cursor:pointer; color:#9ca3af; transition: color .2s; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px;}
        .modal-close:hover { color:#111827; background: #f3f4f6; }
        .modal-body { padding:24px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:.85rem; font-weight:600; color:#374151; margin-bottom:6px; }
        .form-group input, .form-group select { width:100%; padding:10px 12px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.9rem; outline:none; transition: border-color .2s; }
        .form-group input:focus, .form-group select:focus { border-color:var(--inst); box-shadow: 0 0 0 3px rgba(46,58,117,0.1); }
        .modal-footer { padding:20px 24px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:12px; background: #f9fafb; border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;}
    </style>

    @if(session('success'))
        <div class="st-alert success">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="layout-split">
        
        <!-- SIDEBAR DE FILTROS -->
        <aside>
            <div class="filter-card">
                <h3>Búsqueda y Filtros</h3>
                <form method="GET" action="{{ route('admin.presentaciones.index') }}">
                    
                    <div class="filter-group">
                        <label>Buscar (Medicamento o Código)</label>
                        <input type="text" name="search" placeholder="Ej: Amoxicilina" value="{{ request('search') }}">
                    </div>

                    <div class="filter-group">
                        <label>Forma Farmacéutica</label>
                        <select name="forma_farmaceutica">
                            <option value="">Todas las formas</option>
                            @foreach($formas as $forma)
                                <option value="{{ $forma }}" {{ request('forma_farmaceutica') == $forma ? 'selected' : '' }}>{{ $forma }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Vía de Administración</label>
                        <select name="via_administracion">
                            <option value="">Todas las vías</option>
                            @foreach($vias as $via)
                                <option value="{{ $via }}" {{ request('via_administracion') == $via ? 'selected' : '' }}>{{ $via }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Refrigeración</label>
                        <select name="refrigeracion">
                            <option value="">Cualquiera</option>
                            <option value="1" {{ request('refrigeracion') === '1' ? 'selected' : '' }}>Requiere Refrigeración</option>
                            <option value="0" {{ request('refrigeracion') === '0' ? 'selected' : '' }}>No Requiere</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="">Todos los estados</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activo</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button type="submit" class="btn-primary">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Aplicar Filtros
                        </button>
                        <a href="{{ route('admin.presentaciones.index') }}" class="btn-outline">
                            Limpiar Filtros
                        </a>
                    </div>
                </form>
            </div>
        </aside>

        <!-- AREA DE DATOS VISUAL -->
        <main>
            <div class="card-table-wrapper">
                <div class="card-table-header">
                    <div>
                        <h2 class="card-table-title">Directorio de Presentaciones</h2>
                        <p class="card-table-desc">Consulta visual interactiva de presentaciones registradas.</p>
                    </div>
                    @puede('Presentaciones','Crear')
                    <button class="btn-primary" onclick="openModal('modal-crear')">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="20" height="20"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
                        Nueva Presentación
                    </button>
                    @endpuede
                </div>

                <div class="card-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Medicamento & Presentación</th>
                                <th>Composición</th>
                                <th>Cualidades Farmacéuticas</th>
                                <th>Estado</th>
                                <th style="width: 90px; text-align: right;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($presentaciones as $pre)
                            <tr>
                                <td class="info-cell">
                                    <strong>{{ $pre->medicamento->nombre }}</strong>
                                    {{ $pre->nombre }}<br>
                                    @if($pre->codigo) <span style="display:inline-block; margin-top:4px;">Cód: {{ $pre->codigo }}</span> @endif
                                </td>
                                <td>
                                    @if($pre->concentracion)
                                        <div style="font-weight: 600; color: #374151;">Dosis: {{ $pre->concentracion }} {{ $pre->unidad_concentracion }}</div>
                                    @endif
                                    @if($pre->volumen)
                                        <div style="font-size: 0.85rem; color: #6b7280;">Vol Total: {{ $pre->volumen }} {{ $pre->unidad_volumen }}</div>
                                    @endif
                                </td>
                                <td>
                                    <div style="margin-bottom: 4px;">{{ $pre->forma_farmaceutica ?: '-' }}</div>
                                    <div style="font-size: 0.85rem; color: #6b7280; margin-bottom: 4px;">{{ $pre->via_administracion ?: '-' }}</div>
                                    @if($pre->requiere_refrigeracion)
                                        <div class="badge badge-warning" style="font-size: 0.65rem;">❄️ REFRIGERADO</div>
                                    @endif
                                </td>
                                <td>
                                    @if($pre->estado)
                                        <span class="badge badge-active">Activo</span>
                                    @else
                                        <span class="badge badge-inactive">Inactivo</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="actions" style="justify-content: flex-end;">
                                        @puede('Presentaciones','Editar')
                                        <button type="button" class="btn-icon btn-edit" title="Editar" onclick='editPresentacion(@json($pre))'>
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        @endpuede
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 3rem; color: #6b7280;">
                                    <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" width="48" height="48" style="margin: 0 auto 10px; color: #d1d5db; display: block;"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                    No hay presentaciones registradas o no coinciden con los filtros.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <div class="pagination-wrap">
                        {{ $presentaciones->links() }}
                    </div>
                </div>
            </div>
        </main>

    </div>

    <!-- Modal Crear -->
    <div id="modal-crear" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Nueva Presentación</h3>
                <button type="button" class="modal-close" onclick="closeModal('modal-crear')">&times;</button>
            </div>
            <form action="{{ route('admin.presentaciones.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label>Medicamento Base <span style="color:#ef4444">*</span></label>
                        <select name="medicamento_id" required>
                            <option value="">Seleccione...</option>
                            @foreach($medicamentos as $med)
                                <option value="{{ $med->id }}">{{ $med->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre Presentación <span style="color:#ef4444">*</span></label>
                            <input type="text" name="nombre" required placeholder="Ej: Caja x 30 Tab">
                        </div>
                        <div class="form-group">
                            <label>Código Interno</label>
                            <input type="text" name="codigo" placeholder="Opcional">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Concentración</label>
                            <input type="number" step="0.001" name="concentracion" placeholder="Ej: 500">
                        </div>
                        <div class="form-group">
                            <label>Und. Concentración</label>
                            <input type="text" name="unidad_concentracion" placeholder="Ej: mg, g">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Volumen Total</label>
                            <input type="number" step="0.001" name="volumen" placeholder="Ej: 100">
                        </div>
                        <div class="form-group">
                            <label>Und. Volumen</label>
                            <input type="text" name="unidad_volumen" placeholder="Ej: ml, cc">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Forma Farmacéutica</label>
                            <input type="text" name="forma_farmaceutica" placeholder="Ej: Tableta, Jarabe">
                        </div>
                        <div class="form-group">
                            <label>Vía de Admin.</label>
                            <input type="text" name="via_administracion" placeholder="Ej: Oral">
                        </div>
                    </div>

                    <div class="form-row" style="align-items: center; margin-top: 20px;">
                        <div class="form-group mb-0" style="margin-bottom: 0;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-bottom: 0; font-size: .95rem;">
                                <input type="checkbox" name="requiere_refrigeracion" value="1" style="width: 20px; height: 20px; cursor:pointer;">
                                ¿Requiere Refrigeración?
                            </label>
                        </div>
                        <div class="form-group mb-0" style="margin-bottom: 0;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-bottom: 0; font-size: .95rem;">
                                <input type="checkbox" name="estado" value="1" checked style="width: 20px; height: 20px; cursor:pointer;">
                                Activo
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeModal('modal-crear')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar Presentación</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Editar -->
    <div id="modal-editar" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Editar Presentación</h3>
                <button type="button" class="modal-close" onclick="closeModal('modal-editar')">&times;</button>
            </div>
            <form id="form-editar" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label>Medicamento Base <span style="color:#ef4444">*</span></label>
                        <select name="medicamento_id" id="edit_medicamento_id" required>
                            <option value="">Seleccione...</option>
                            @foreach($medicamentos as $med)
                                <option value="{{ $med->id }}">{{ $med->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre Presentación <span style="color:#ef4444">*</span></label>
                            <input type="text" name="nombre" id="edit_nombre" required>
                        </div>
                        <div class="form-group">
                            <label>Código Interno</label>
                            <input type="text" name="codigo" id="edit_codigo">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Concentración</label>
                            <input type="number" step="0.001" name="concentracion" id="edit_concentracion">
                        </div>
                        <div class="form-group">
                            <label>Und. Concentración</label>
                            <input type="text" name="unidad_concentracion" id="edit_unidad_concentracion">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Volumen Total</label>
                            <input type="number" step="0.001" name="volumen" id="edit_volumen">
                        </div>
                        <div class="form-group">
                            <label>Und. Volumen</label>
                            <input type="text" name="unidad_volumen" id="edit_unidad_volumen">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Forma Farmacéutica</label>
                            <input type="text" name="forma_farmaceutica" id="edit_forma_farmaceutica">
                        </div>
                        <div class="form-group">
                            <label>Vía de Admin.</label>
                            <input type="text" name="via_administracion" id="edit_via_administracion">
                        </div>
                    </div>

                    <div class="form-row" style="align-items: center; margin-top: 20px;">
                        <div class="form-group mb-0" style="margin-bottom: 0;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-bottom: 0; font-size: .95rem;">
                                <input type="checkbox" name="requiere_refrigeracion" id="edit_refrigeracion" value="1" style="width: 20px; height: 20px; cursor:pointer;">
                                ¿Requiere Refrigeración?
                            </label>
                        </div>
                        <div class="form-group mb-0" style="margin-bottom: 0;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; margin-bottom: 0; font-size: .95rem;">
                                <input type="checkbox" name="estado" id="edit_estado" value="1" style="width: 20px; height: 20px; cursor:pointer;">
                                Activo
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" style="justify-content: space-between;">
                    <button type="button" class="btn-outline" style="color: #dc2626; border-color: #fecaca; background: #fef2f2;" onclick="deletePresentacion()">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Eliminar
                    </button>
                    <div style="display:flex; gap:10px;">
                        <button type="button" class="btn-outline" onclick="closeModal('modal-editar')">Cancelar</button>
                        <button type="submit" class="btn-primary">Actualizar</button>
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

        function editPresentacion(pre) {
            let form = document.getElementById('form-editar');
            form.action = `/admin/presentaciones/` + pre.id;
            
            document.getElementById('edit_medicamento_id').value = pre.medicamento_id || '';
            document.getElementById('edit_nombre').value = pre.nombre || '';
            document.getElementById('edit_codigo').value = pre.codigo || '';
            document.getElementById('edit_concentracion').value = pre.concentracion || '';
            document.getElementById('edit_unidad_concentracion').value = pre.unidad_concentracion || '';
            document.getElementById('edit_volumen').value = pre.volumen || '';
            document.getElementById('edit_unidad_volumen').value = pre.unidad_volumen || '';
            document.getElementById('edit_forma_farmaceutica').value = pre.forma_farmaceutica || '';
            document.getElementById('edit_via_administracion').value = pre.via_administracion || '';
            
            document.getElementById('edit_refrigeracion').checked = pre.requiere_refrigeracion == 1;
            document.getElementById('edit_estado').checked = pre.estado == 1;

            let formDel = document.getElementById('form-delete');
            formDel.action = `/admin/presentaciones/` + pre.id;

            openModal('modal-editar');
        }

        function deletePresentacion() {
            appConfirm('¿Está seguro de eliminar de forma permanente esta presentación?').then(function (ok) {
                if (ok) document.getElementById('form-delete').submit();
            });
        }
    </script>
</x-app-layout>