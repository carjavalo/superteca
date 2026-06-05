<x-app-layout>
    <x-slot name="header">
        <h2>Gestión de Medicamentos</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }

        .st-alert { padding:12px 18px; border-radius:8px; margin-bottom:20px; display:flex; align-items:center; gap:10px; font-size:.92rem; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:4px solid #10b981; }

        .filter-card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.07); padding: 20px; margin-bottom: 20px; }
        .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px; }
        .filter-grid input, .filter-grid select { padding: 9px 12px; border: 1.5px solid #d1d5db; border-radius: 6px; font-size: .88rem; outline:none; }
        .filter-grid input:focus, .filter-grid select:focus { border-color: var(--inst); }
        .filter-actions { display: flex; justify-content: flex-end; gap: 10px; }

        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:9px 20px; border-radius:8px; font-weight:600; font-size:.9rem; display:flex; align-items:center; gap:7px; transition:background .15s; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; color:#fff; }
        .btn-outline { background:#f3f4f6; color:#374151; border:1px solid #e5e7eb; cursor:pointer; padding:9px 20px; border-radius:8px; font-weight:600; font-size:.9rem; transition:background .15s; text-decoration:none; }
        .btn-outline:hover { background:#e5e7eb; }

        .card-table { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.07); overflow-x:auto; }
        .card-table table { width:100%; border-collapse:collapse; min-width: 1000px; }
        .card-table thead tr { background:var(--inst); color:#fff; }
        .card-table thead th { padding:13px 14px; text-align:left; font-size:.78rem; text-transform:uppercase; letter-spacing:.06em; white-space:nowrap; }
        .card-table tbody tr { border-bottom:1px solid #f3f4f6; transition:background .12s; }
        .card-table tbody tr:hover { background:#f0f3ff; }
        .card-table td { padding:11px 14px; font-size:.87rem; color:#374151; vertical-align:middle; }

        .badge { display:inline-block; padding:3px 10px; border-radius:20px; font-size:.73rem; font-weight:600; }
        .badge-active   { background:#d1fae5; color:#065f46; }
        .badge-inactive { background:#f3f4f6; color:#6b7280; }

        .actions { display:flex; gap:6px; align-items:center; }
        .btn-icon { width:32px; height:32px; border-radius:7px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; }
        .btn-edit   { background:#eff6ff; color:#2563eb; }
        .btn-edit:hover   { background:#2563eb; color:#fff; }
        .btn-delete { background:#fef2f2; color:#dc2626; }
        .btn-delete:hover { background:#dc2626; color:#fff; }

        .pagination-wrap { display:flex; justify-content:center; padding:14px 18px; border-top:1px solid #f3f4f6; }

        /* Modal classes simplified */
        .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.5); display:flex; align-items:center; justify-content:center; z-index:200; opacity:0; pointer-events:none; transition:opacity .2s; }
        .modal-overlay.open { opacity:1; pointer-events:all; }
        .modal-box { background:#fff; border-radius:12px; width:100%; max-width:650px; max-height:90vh; overflow-y:auto; transform:translateY(20px); transition:transform .2s; }
        .modal-overlay.open .modal-box { transform:translateY(0); }
        .modal-header { padding:18px 24px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:#fff; z-index:1; }
        .modal-header h3 { margin:0; font-size:1.1rem; color:var(--inst); }
        .modal-close { background:none; border:none; font-size:1.5rem; cursor:pointer; color:#6b7280; }
        .modal-body { padding:24px; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-bottom:16px; }
        .form-group label { display:block; font-size:.85rem; font-weight:600; color:#374151; margin-bottom:6px; }
        .form-group input, .form-group select { width:100%; padding:9px 12px; border:1.5px solid #d1d5db; border-radius:6px; font-size:.9rem; outline:none; }
        .form-group input:focus, .form-group select:focus { border-color:var(--inst); }
        .modal-footer { padding:16px 24px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; }
    </style>

    @if(session('success'))
        <div class="st-alert success">✓ {{ session('success') }}</div>
    @endif

    <div class="filter-card">
        <form method="GET" action="{{ route('admin.medicamentos.index') }}">
            <div class="filter-grid">
                <input type="text" name="nombre" placeholder="Nombre o Genérico" value="{{ request('nombre') }}">
                <input type="text" name="codigo" placeholder="Código" value="{{ request('codigo') }}">
                <input type="text" name="registro_invima" placeholder="Registro INVIMA" value="{{ request('registro_invima') }}">
                <input type="text" name="laboratorio" placeholder="Laboratorio" value="{{ request('laboratorio') }}">
                <input type="text" name="forma_farmaceutica" placeholder="Forma Farmacéutica" value="{{ request('forma_farmaceutica') }}">
                <select name="estado">
                    <option value="">Todos los estados</option>
                    <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="filter-actions">
                <a href="{{ route('admin.medicamentos.index') }}" class="btn-outline">Limpiar</a>
                <button type="submit" class="btn-primary">Filtrar tabla</button>
            </div>
        </form>
    </div>

    <div style="margin-bottom: 20px; display:flex; justify-content: flex-end;">
        @puede('Medicamentos','Crear')
        <button class="btn-primary" onclick="openModal('modalCreate')">
            + Nuevo Medicamento
        </button>
        @endpuede
    </div>

    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Concentración</th>
                    <th>Unidad</th>
                    <th>Forma</th>
                    <th>Registro INVIMA</th>
                    <th>Laboratorio</th>
                    <th>Semáforo</th>
                    <th>Estado</th>
                    <th style="width: 100px;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($medicamentos as $med)
                    <tr>
                        <td>{{ $med->codigo ?: '-' }}</td>
                        <td>
                            <strong>{{ $med->nombre }}</strong>
                            @if($med->nombre_generico) <br><span style="font-size:0.75rem; color:#6b7280">{{ $med->nombre_generico }}</span> @endif
                        </td>
                        <td>{{ $med->concentracion ?: '-' }}</td>
                        <td>{{ $med->unidad_medida ?: '-' }}</td>
                        <td>{{ $med->forma_farmaceutica ?: '-' }}</td>
                        <td>{{ $med->registro_invima ?: '-' }}</td>
                        <td>{{ $med->laboratorio ?: '-' }}</td>
                        <td>
                            @if($med->semaforo_sanitario === 'verde')
                                <span title="Sin alertas">🟢</span>
                            @elseif($med->semaforo_sanitario === 'amarillo')
                                <span title="Próximo vencimiento">🟡</span>
                            @else
                                <span title="Retiro INVIMA">🔴</span>
                            @endif
                        </td>
                        <td>
                            @if($med->estado)
                                <span class="badge badge-active">Activo</span>
                            @else
                                <span class="badge badge-inactive">Inactivo</span>
                            @endif
                        </td>
                        <td>
                            <div class="actions">
                                @puede('Medicamentos','Editar')<button type="button" class="btn-icon btn-edit" onclick="openEditModal({{ $med->toJson() }})">✎</button>@endpuede
                                @puede('Medicamentos','Eliminar')<button type="button" class="btn-icon btn-delete" onclick="openDeleteModal({{ $med->id }}, '{{ $med->nombre }}')">🗑</button>@endpuede
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 40px; color: #9ca3af;">
                            No hay medicamentos registrados o no coinciden con los filtros.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination-wrap">
            {{ $medicamentos->links() }}
        </div>
    </div>

    {{-- MODAL CREATE --}}
    <div class="modal-overlay" id="modalCreate">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Crear Medicamento</h3>
                <button class="modal-close" onclick="closeModal('modalCreate')">×</button>
            </div>
            <form action="{{ route('admin.medicamentos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group"><label>Código</label><input type="text" name="codigo" value="{{ old('codigo') }}"></div>
                        <div class="form-group"><label>Nombre *</label><input type="text" name="nombre" required value="{{ old('nombre') }}"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Nombre genérico</label><input type="text" name="nombre_generico" value="{{ old('nombre_generico') }}"></div>
                        <div class="form-group"><label>Laboratorio</label><input type="text" name="laboratorio" value="{{ old('laboratorio') }}"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Concentración (Ej: 500)</label><input type="text" name="concentracion" value="{{ old('concentracion') }}"></div>
                        <div class="form-group"><label>Unidad medida (Ej: mg, ml)</label><input type="text" name="unidad_medida" value="{{ old('unidad_medida') }}"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Forma Farmacéutica</label><input type="text" name="forma_farmaceutica" value="{{ old('forma_farmaceutica') }}"></div>
                        <div class="form-group"><label>Vía administración</label><input type="text" name="via_administracion" value="{{ old('via_administracion') }}"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Registro INVIMA</label><input type="text" name="registro_invima" value="{{ old('registro_invima') }}"></div>
                        <div class="form-group"><label>Estabilidad horas</label><input type="number" name="estabilidad_horas" value="{{ old('estabilidad_horas') }}"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Requiere Refrigeración</label>
                            <select name="requiere_refrigeracion">
                                <option value="0">No</option><option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Requiere Fotoprotección</label>
                            <select name="fotoproteccion">
                                <option value="0">No</option><option value="1">Sí</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Medicamento Controlado</label>
                            <select name="controlado">
                                <option value="0">No</option><option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Semáforo Sanitario</label>
                            <select name="semaforo_sanitario">
                                <option value="verde">🟢 Sin alertas</option>
                                <option value="amarillo">🟡 Próximo vencimiento</option>
                                <option value="rojo">🔴 Retiro INVIMA</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Estado</label>
                            <select name="estado">
                                <option value="1">Activo</option><option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div class="form-group"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeModal('modalCreate')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT --}}
    <div class="modal-overlay" id="modalEdit">
        <div class="modal-box">
            <div class="modal-header">
                <h3>Editar Medicamento</h3>
                <button class="modal-close" onclick="closeModal('modalEdit')">×</button>
            </div>
            <form id="formEdit" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group"><label>Código</label><input type="text" name="codigo" id="e_codigo"></div>
                        <div class="form-group"><label>Nombre *</label><input type="text" name="nombre" id="e_nombre" required></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Nombre genérico</label><input type="text" name="nombre_generico" id="e_nombre_generico"></div>
                        <div class="form-group"><label>Laboratorio</label><input type="text" name="laboratorio" id="e_laboratorio"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Concentración</label><input type="text" name="concentracion" id="e_concentracion"></div>
                        <div class="form-group"><label>Unidad medida</label><input type="text" name="unidad_medida" id="e_unidad_medida"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Forma Farmacéutica</label><input type="text" name="forma_farmaceutica" id="e_forma_farmaceutica"></div>
                        <div class="form-group"><label>Vía administración</label><input type="text" name="via_administracion" id="e_via_administracion"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Registro INVIMA</label><input type="text" name="registro_invima" id="e_registro_invima"></div>
                        <div class="form-group"><label>Estabilidad horas</label><input type="number" name="estabilidad_horas" id="e_estabilidad_horas"></div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Requiere Refrigeración</label>
                            <select name="requiere_refrigeracion" id="e_requiere_refrigeracion">
                                <option value="0">No</option><option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Requiere Fotoprotección</label>
                            <select name="fotoproteccion" id="e_fotoproteccion">
                                <option value="0">No</option><option value="1">Sí</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Medicamento Controlado</label>
                            <select name="controlado" id="e_controlado">
                                <option value="0">No</option><option value="1">Sí</option>
                            </select>
                        </div>
                        <div class="form-group"><label>Semáforo Sanitario</label>
                            <select name="semaforo_sanitario" id="e_semaforo_sanitario">
                                <option value="verde">🟢 Sin alertas</option>
                                <option value="amarillo">🟡 Próximo vencimiento</option>
                                <option value="rojo">🔴 Retiro INVIMA</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group"><label>Estado</label>
                            <select name="estado" id="e_estado">
                                <option value="1">Activo</option><option value="0">Inactivo</option>
                            </select>
                        </div>
                        <div class="form-group"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeModal('modalEdit')">Cancelar</button>
                    <button type="submit" class="btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal-overlay" id="modalDelete">
        <div class="modal-box" style="max-width: 400px; padding: 24px; text-align: center;">
            <div style="font-size: 3rem; color: #dc2626; margin-bottom: 10px;">🗑</div>
            <h3 style="margin-top: 0;">Eliminar Medicamento</h3>
            <p style="color: #6b7280; font-size: .9rem;">¿Estás seguro de que deseas eliminar el medicamento <strong id="d_nombre"></strong>? Esta acción no se puede deshacer.</p>
            <form id="formDelete" method="POST" style="margin-top: 20px; display: flex; gap: 10px; justify-content: center;">
                @csrf @method('DELETE')
                <button type="button" class="btn-outline" onclick="closeModal('modalDelete')">Cancelar</button>
                <button type="submit" class="btn-primary" style="background:#dc2626;">Eliminar</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        function openEditModal(med) {
            document.getElementById('formEdit').action = '/admin/medicamentos/' + med.id;
            document.getElementById('e_codigo').value = med.codigo || '';
            document.getElementById('e_nombre').value = med.nombre || '';
            document.getElementById('e_nombre_generico').value = med.nombre_generico || '';
            document.getElementById('e_laboratorio').value = med.laboratorio || '';
            document.getElementById('e_concentracion').value = med.concentracion || '';
            document.getElementById('e_unidad_medida').value = med.unidad_medida || '';
            document.getElementById('e_forma_farmaceutica').value = med.forma_farmaceutica || '';
            document.getElementById('e_via_administracion').value = med.via_administracion || '';
            document.getElementById('e_registro_invima').value = med.registro_invima || '';
            document.getElementById('e_estabilidad_horas').value = med.estabilidad_horas || '';
            
            document.getElementById('e_requiere_refrigeracion').value = med.requiere_refrigeracion ? '1' : '0';
            document.getElementById('e_fotoproteccion').value = med.fotoproteccion ? '1' : '0';
            document.getElementById('e_controlado').value = med.controlado ? '1' : '0';
            document.getElementById('e_estado').value = med.estado ? '1' : '0';
            
            openModal('modalEdit');
        }

        function openDeleteModal(id, nombre) {
            document.getElementById('formDelete').action = '/admin/medicamentos/' + id;
            document.getElementById('d_nombre').textContent = nombre;
            openModal('modalDelete');
        }
    </script>
</x-app-layout>