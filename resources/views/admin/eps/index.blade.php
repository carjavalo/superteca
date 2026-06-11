<x-app-layout>
    <x-slot name="header">
        <h2>Configuración · Gestor de EPS</h2>
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
        .dash-card.gray  { border-left-color:#9ca3af; }

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
        .catalog-top p  { color:#6b7280; margin:4px 0 0 0; font-size:.92rem; max-width:640px; }

        .card-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(320px,1fr)); gap:18px; }

        .te-card { background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 6px 14px rgba(0,0,0,.05); border:1px solid #f1f1f4; transition:all .25s; display:flex; flex-direction:column; }
        .te-card:hover { transform:translateY(-5px); box-shadow:0 16px 32px rgba(0,0,0,.1); }
        .te-card.off { opacity:.7; }
        .te-card .accent { height:8px; width:100%; background:linear-gradient(90deg, var(--inst), #5b6bb5); }
        .te-card.off .accent { background:#cbd5e1; }

        .te-head { padding:18px 20px 10px; display:flex; align-items:flex-start; gap:14px; }
        .te-codigo { flex-shrink:0; min-width:54px; height:54px; padding:0 10px; border-radius:14px; background:#eef0f7; color:var(--inst); display:flex; flex-direction:column; align-items:center; justify-content:center; font-weight:800; line-height:1; }
        .te-codigo small { font-size:.58rem; font-weight:700; letter-spacing:.06em; opacity:.7; text-transform:uppercase; margin-bottom:2px; }
        .te-codigo span { font-size:1.15rem; }
        .te-title h3 { margin:0; font-size:1.18rem; font-weight:800; color:#1f2937; }

        .te-body { padding:8px 20px 16px; flex:1; }
        .te-body .obs { font-size:.86rem; color:#4b5563; line-height:1.5; }
        .te-body .obs.empty { color:#9ca3af; font-style:italic; }

        .te-foot { padding:13px 20px; background:#f9fafb; border-top:1px solid #f1f1f4; display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; }

        .badge-estado { display:inline-flex; align-items:center; gap:5px; font-size:.72rem; font-weight:700; padding:4px 10px; border-radius:20px; }
        .est-on  { background:#dcfce7; color:#065f46; border:1px solid #a7f3d0; }
        .est-off { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
        .badge-uso { display:inline-flex; align-items:center; gap:5px; font-size:.72rem; font-weight:700; padding:4px 10px; border-radius:20px; background:#eef2ff; color:#3730a3; border:1px solid #c7d2fe; }

        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:9px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; justify-content:center; gap:7px; transition:all .2s; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; transform:translateY(-2px); }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; cursor:pointer; padding:9px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; justify-content:center; gap:7px; text-decoration:none; }
        .btn-outline:hover { background:#f9fafb; }
        .btn-icon { width:34px; height:34px; border-radius:8px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; }
        .btn-edit { background:#eff6ff; color:#2563eb; } .btn-edit:hover { background:#2563eb; color:#fff; }
        .btn-del  { background:#fee2e2; color:#dc2626; } .btn-del:hover  { background:#dc2626; color:#fff; }
        .btn-del:disabled { opacity:.4; cursor:not-allowed; }

        .empty-state { text-align:center; padding:3rem 2rem; background:rgba(255,255,255,.8); border-radius:16px; border:2px dashed #e5e7eb; grid-column:1/-1; }

        .modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,.65); backdrop-filter:blur(5px); display:flex; align-items:center; justify-content:center; z-index:200; opacity:0; pointer-events:none; transition:opacity .25s; }
        .modal-overlay.open { opacity:1; pointer-events:all; }
        .modal-box { background:#fff; border-radius:18px; width:100%; max-width:560px; max-height:92vh; overflow-y:auto; transform:scale(.96); transition:transform .25s; box-shadow:0 25px 50px -12px rgba(0,0,0,.3); }
        .modal-overlay.open .modal-box { transform:scale(1); }
        .modal-header { padding:18px 22px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:#fff; z-index:1; }
        .modal-header h3 { margin:0; font-size:1.25rem; color:var(--inst); font-weight:800; }
        .modal-close { background:none; border:none; font-size:1.5rem; cursor:pointer; color:#9ca3af; width:32px; height:32px; border-radius:8px; }
        .modal-body { padding:22px; }
        .modal-footer { padding:16px 22px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; background:#f9fafb; border-bottom-left-radius:18px; border-bottom-right-radius:18px; }

        .form-group { margin-bottom:16px; }
        .form-group label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:5px; }
        .form-group .hint { font-weight:400; color:#9ca3af; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:10px 12px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.9rem; outline:none; font-family:inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--inst); box-shadow:0 0 0 3px rgba(46,58,117,.1); }
        .form-group input[readonly] { background:#f3f4f6; color:#6b7280; cursor:not-allowed; }
        .form-row2 { display:flex; gap:12px; }
        .form-row2 > div { flex:1; }
        .counter { text-align:right; font-size:.74rem; color:#9ca3af; margin-top:4px; }

        .info-banner { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; border-radius:12px; padding:13px 16px; font-size:.86rem; display:flex; gap:10px; align-items:flex-start; margin-bottom:20px; }
    </style>

    @if(session('success'))
        <div class="st-alert success">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="st-alert error">⚠️ {{ session('error') }}</div>
    @endif

    <div class="info-banner">
        <span style="font-size:1.1rem;">💡</span>
        <span>Las EPS que crees aquí alimentan automáticamente el campo <strong>«EPS»</strong> al registrar un <a href="{{ route('admin.dispensacion.pacientes.create') }}" style="color:var(--inst); font-weight:700;">paciente</a> y en todas las vistas del sistema donde se diligencia la EPS. Las marcadas como <strong>Inactivas</strong> dejan de aparecer en esos selectores.</span>
    </div>

    <div class="dash-grid">
        <div class="dash-card blue">
            <div><div class="label">EPS registradas</div><div class="value">{{ $stats['total'] }}</div></div>
            <div class="ic">🏥</div>
        </div>
        <div class="dash-card green">
            <div><div class="label">Activas</div><div class="value">{{ $stats['activas'] }}</div></div>
            <div class="ic">✅</div>
        </div>
        <div class="dash-card gray">
            <div><div class="label">Inactivas</div><div class="value">{{ $stats['inactivas'] }}</div></div>
            <div class="ic">🚫</div>
        </div>
        <div class="dash-card">
            <div><div class="label">Pacientes asociados</div><div class="value">{{ number_format($stats['pacientes']) }}</div></div>
            <div class="ic">🧑‍⚕️</div>
        </div>
    </div>

    <div class="layout-catalog">
        <aside>
            <div class="filter-card">
                <h3>Filtros</h3>
                <form method="GET" action="{{ route('admin.eps.index') }}">
                    <div class="filter-group">
                        <label>Buscar (detalle u observación)</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej: Nueva EPS, Sura...">
                    </div>
                    <div class="filter-group">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="">Todas</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activas</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivas</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Ordenar por</label>
                        <select name="orden">
                            <option value="id"      {{ request('orden') !== 'detalle' ? 'selected' : '' }}>Código</option>
                            <option value="detalle" {{ request('orden') === 'detalle' ? 'selected' : '' }}>Detalle (A–Z)</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Dirección</label>
                        <select name="dir">
                            <option value="asc"  {{ request('dir') !== 'desc' ? 'selected' : '' }}>Ascendente</option>
                            <option value="desc" {{ request('dir') === 'desc' ? 'selected' : '' }}>Descendente</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-primary">Aplicar filtros</button>
                        <a href="{{ route('admin.eps.index') }}" class="btn-outline" style="text-align:center;">Restablecer</a>
                    </div>
                </form>
            </div>
        </aside>

        <main>
            <div class="catalog-top">
                <div>
                    <h2>Catálogo de EPS</h2>
                    <p>Administra las Entidades Promotoras de Salud. Las EPS inactivas se conservan pero no se ofrecen para diligenciar en el sistema.</p>
                </div>
                @puede('Gestor de EPS','Crear')
                <button class="btn-primary" onclick="openCrear()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nueva EPS
                </button>
                @endpuede
            </div>

            <div class="card-grid">
                @forelse($tipos as $tipo)
                    @php $uso = $usoPorNombre[$tipo->Detalle] ?? 0; @endphp
                    <div class="te-card {{ $tipo->Estado ? '' : 'off' }}">
                        <div class="accent"></div>
                        <div class="te-head">
                            <div class="te-codigo">
                                <small>Cód.</small>
                                <span>{{ $tipo->id }}</span>
                            </div>
                            <div class="te-title">
                                <h3>{{ $tipo->Detalle }}</h3>
                            </div>
                        </div>
                        <div class="te-body">
                            <div class="obs {{ $tipo->Observacion ? '' : 'empty' }}">
                                {{ $tipo->Observacion ?: 'Sin observación registrada.' }}
                            </div>
                        </div>
                        <div class="te-foot">
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                @if($tipo->Estado)
                                    <span class="badge-estado est-on">● Activa</span>
                                @else
                                    <span class="badge-estado est-off">● Inactiva</span>
                                @endif
                                @if($uso > 0)
                                    <span class="badge-uso">{{ $uso }} paciente{{ $uso == 1 ? '' : 's' }}</span>
                                @endif
                            </div>
                            <div style="display:flex; gap:6px;">
                                @puede('Gestor de EPS','Editar')
                                <button type="button" class="btn-icon btn-edit" title="Editar" onclick='editTipo(@json($tipo))'>
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                @endpuede
                                @puede('Gestor de EPS','Eliminar')
                                <form action="{{ route('admin.eps.destroy', $tipo) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar la EPS «{{ $tipo->Detalle }}»? Esta acción no se puede deshacer.')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon btn-del" title="{{ $uso > 0 ? 'No se puede eliminar: está en uso' : 'Eliminar' }}" {{ $uso > 0 ? 'disabled' : '' }}>
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                    </button>
                                </form>
                                @endpuede
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <h3 style="font-size:1.2rem; color:#4b5563;">Sin EPS</h3>
                        <p style="color:#6b7280;">No se encontraron resultados con los filtros aplicados. Crea la primera con el botón «Nueva EPS».</p>
                    </div>
                @endforelse
            </div>

            <div style="margin-top:22px;">{{ $tipos->links() }}</div>
        </main>
    </div>

    {{-- Modal crear / editar --}}
    <div id="modal-tipo" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modal-title">Nueva EPS</h3>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="form-tipo" action="{{ route('admin.eps.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div class="modal-body">
                    <div class="form-row2">
                        <div class="form-group" style="flex:0 0 130px;">
                            <label>Código <span class="hint">(auto)</span></label>
                            <input type="text" id="f_codigo" value="Automático" readonly>
                        </div>
                        <div class="form-group">
                            <label>Estado *</label>
                            <select name="Estado" id="f_estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Detalle <span class="hint">— nombre de la EPS *</span></label>
                        <input type="text" name="Detalle" id="f_detalle" maxlength="120" required
                               placeholder="Ej: Nueva EPS, EPS Sura..."
                               value="{{ old('Detalle') }}">
                    </div>

                    <div class="form-group">
                        <label>Observación <span class="hint">— nota u objetivo (opcional)</span></label>
                        <textarea name="Observacion" id="f_observacion" rows="4" maxlength="300"
                                  placeholder="Información adicional sobre la EPS..."
                                  oninput="updateCounter()">{{ old('Observacion') }}</textarea>
                        <div class="counter"><span id="obs_count">0</span> / 300</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeModal()">Cancelar</button>
                    <button type="submit" class="btn-primary">💾 Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal()  { document.getElementById('modal-tipo').classList.add('open'); }
        function closeModal() { document.getElementById('modal-tipo').classList.remove('open'); }

        function updateCounter() {
            document.getElementById('obs_count').textContent = document.getElementById('f_observacion').value.length;
        }

        function resetForm() {
            const f = document.getElementById('form-tipo');
            f.reset();
            f.action = "{{ route('admin.eps.store') }}";
            document.getElementById('form-method').value = 'POST';
            document.getElementById('modal-title').textContent = 'Nueva EPS';
            document.getElementById('f_codigo').value = 'Automático';
            document.getElementById('f_estado').value = '1';
            updateCounter();
        }

        function openCrear() {
            resetForm();
            openModal();
            setTimeout(() => document.getElementById('f_detalle').focus(), 100);
        }

        function editTipo(t) {
            resetForm();
            document.getElementById('modal-title').textContent = 'Editar EPS';
            document.getElementById('form-tipo').action = "{{ url('admin/eps') }}/" + t.id;
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('f_codigo').value = t.id;
            document.getElementById('f_detalle').value = t.Detalle ?? '';
            document.getElementById('f_estado').value = t.Estado ? '1' : '0';
            document.getElementById('f_observacion').value = t.Observacion ?? '';
            updateCounter();
            openModal();
        }

        // Reabrir el modal si hubo errores de validación.
        @if($errors->any())
            window.addEventListener('DOMContentLoaded', () => {
                @if(old('_method') === 'PUT')
                    document.getElementById('modal-title').textContent = 'Editar EPS';
                @endif
                @if(old('Estado') !== null)
                    document.getElementById('f_estado').value = "{{ old('Estado') }}";
                @endif
                updateCounter();
                openModal();
            });
        @endif
    </script>
</x-app-layout>
