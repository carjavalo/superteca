<x-app-layout>
    <x-slot name="header">
        <h2>Configuración · Gestor de Tipos de Entrada</h2>
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
        .dash-card.amber { border-left-color:#f59e0b; }

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
        .te-card .accent { height:8px; width:100%; background:linear-gradient(90deg, var(--inst), #5b6bb5); }

        .te-head { padding:18px 20px 10px; display:flex; align-items:flex-start; gap:14px; }
        .te-codigo { flex-shrink:0; min-width:54px; height:54px; padding:0 10px; border-radius:14px; background:#eef0f7; color:var(--inst); display:flex; flex-direction:column; align-items:center; justify-content:center; font-weight:800; line-height:1; }
        .te-codigo small { font-size:.58rem; font-weight:700; letter-spacing:.06em; opacity:.7; text-transform:uppercase; margin-bottom:2px; }
        .te-codigo span { font-size:1.15rem; }
        .te-title h3 { margin:0; font-size:1.18rem; font-weight:800; color:#1f2937; }
        .te-title .clave { margin-top:3px; font-size:.72rem; font-weight:700; color:#6b7280; font-family:ui-monospace, SFMono-Regular, Menlo, monospace; }

        .te-body { padding:4px 20px 16px; flex:1; }
        .te-body .obs { font-size:.86rem; color:#4b5563; line-height:1.5; }
        .te-body .obs.empty { color:#9ca3af; font-style:italic; }

        .te-foot { padding:13px 20px; background:#f9fafb; border-top:1px solid #f1f1f4; display:flex; justify-content:space-between; align-items:center; gap:8px; flex-wrap:wrap; }

        .badge-uso { display:inline-flex; align-items:center; gap:5px; font-size:.72rem; font-weight:700; padding:4px 10px; border-radius:20px; }
        .uso-yes { background:#dcfce7; color:#065f46; border:1px solid #a7f3d0; }
        .uso-no  { background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; }

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
        .form-group input, .form-group textarea { width:100%; padding:10px 12px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.9rem; outline:none; font-family:inherit; }
        .form-group input:focus, .form-group textarea:focus { border-color:var(--inst); box-shadow:0 0 0 3px rgba(46,58,117,.1); }
        .form-group input[readonly] { background:#f3f4f6; color:#6b7280; cursor:not-allowed; }
        .counter { text-align:right; font-size:.74rem; color:#9ca3af; margin-top:4px; }

        .codigo-box { display:flex; gap:12px; }
        .codigo-box > div:first-child { flex:0 0 130px; }
        .clave-preview { background:#eef0f7; border:1.5px dashed #c7cce0; border-radius:8px; padding:10px 12px; font-size:.82rem; color:var(--inst); display:flex; align-items:center; }
        .clave-preview code { font-family:ui-monospace, SFMono-Regular, Menlo, monospace; font-weight:800; letter-spacing:.03em; }

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
        <span>Los tipos que crees aquí alimentan automáticamente el selector <strong>«Tipo de entrada»</strong> al registrar una <a href="{{ route('admin.entradas.create') }}" style="color:var(--inst); font-weight:700;">nueva entrada de inventario</a>. El <strong>Código</strong> se asigna solo y la <strong>Observación</strong> explica para qué sirve cada tipo.</span>
    </div>

    <div class="dash-grid">
        <div class="dash-card blue">
            <div><div class="label">Tipos registrados</div><div class="value">{{ $stats['total'] }}</div></div>
            <div class="ic">🏷️</div>
        </div>
        <div class="dash-card green">
            <div><div class="label">En uso</div><div class="value">{{ $stats['en_uso'] }}</div></div>
            <div class="ic">✅</div>
        </div>
        <div class="dash-card amber">
            <div><div class="label">Sin uso</div><div class="value">{{ $stats['sin_uso'] }}</div></div>
            <div class="ic">🕓</div>
        </div>
        <div class="dash-card">
            <div><div class="label">Entradas asociadas</div><div class="value">{{ number_format($stats['entradas']) }}</div></div>
            <div class="ic">📥</div>
        </div>
    </div>

    <div class="layout-catalog">
        <aside>
            <div class="filter-card">
                <h3>Filtros</h3>
                <form method="GET" action="{{ route('admin.tipos_entrada.index') }}">
                    <div class="filter-group">
                        <label>Buscar (detalle u observación)</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Ej: Compra, Donación...">
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
                        <a href="{{ route('admin.tipos_entrada.index') }}" class="btn-outline" style="text-align:center;">Restablecer</a>
                    </div>
                </form>
            </div>
        </aside>

        <main>
            <div class="catalog-top">
                <div>
                    <h2>Catálogo de Tipos de Entrada</h2>
                    <p>Administra los tipos que clasifican cada ingreso de inventario. Crea, edita o elimina según las necesidades del servicio farmacéutico.</p>
                </div>
                <button class="btn-primary" onclick="openCrear()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nuevo tipo
                </button>
            </div>

            <div class="card-grid">
                @forelse($tipos as $tipo)
                    @php $uso = $usoPorCodigo[$tipo->codigo] ?? 0; @endphp
                    <div class="te-card">
                        <div class="accent"></div>
                        <div class="te-head">
                            <div class="te-codigo">
                                <small>Cód.</small>
                                <span>{{ $tipo->id }}</span>
                            </div>
                            <div class="te-title">
                                <h3>{{ $tipo->Detalle }}</h3>
                                <div class="clave">🔑 {{ $tipo->codigo }}</div>
                            </div>
                        </div>
                        <div class="te-body">
                            <div class="obs {{ $tipo->Observacion ? '' : 'empty' }}">
                                {{ $tipo->Observacion ?: 'Sin observación registrada.' }}
                            </div>
                        </div>
                        <div class="te-foot">
                            @if($uso > 0)
                                <span class="badge-uso uso-yes">● {{ $uso }} entrada{{ $uso == 1 ? '' : 's' }}</span>
                            @else
                                <span class="badge-uso uso-no">● Sin uso</span>
                            @endif
                            <div style="display:flex; gap:6px;">
                                <button type="button" class="btn-icon btn-edit" title="Editar" onclick='editTipo(@json($tipo))'>
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <form action="{{ route('admin.tipos_entrada.destroy', $tipo) }}" method="POST"
                                      onsubmit="return confirm('¿Eliminar el tipo de entrada «{{ $tipo->Detalle }}»? Esta acción no se puede deshacer.')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-icon btn-del" title="{{ $uso > 0 ? 'No se puede eliminar: está en uso' : 'Eliminar' }}" {{ $uso > 0 ? 'disabled' : '' }}>
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <h3 style="font-size:1.2rem; color:#4b5563;">Sin tipos de entrada</h3>
                        <p style="color:#6b7280;">No se encontraron resultados con los filtros aplicados. Crea el primer tipo con el botón «Nuevo tipo».</p>
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
                <h3 id="modal-title">Nuevo tipo de entrada</h3>
                <button type="button" class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <form id="form-tipo" action="{{ route('admin.tipos_entrada.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">

                <div class="modal-body">
                    <div class="codigo-box">
                        <div class="form-group" style="margin-bottom:0;">
                            <label>Código</label>
                            <input type="text" id="f_codigo" value="Automático" readonly>
                        </div>
                        <div class="form-group" style="flex:1; margin-bottom:0;">
                            <label>Clave interna <span class="hint">(se genera del detalle)</span></label>
                            <div class="clave-preview"><code id="clave_preview">—</code></div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-top:16px;">
                        <label>Detalle <span class="hint">— nombre del tipo de entrada *</span></label>
                        <input type="text" name="Detalle" id="f_detalle" maxlength="120" required
                               placeholder="Ej: Compra, Donación, Devolución..."
                               value="{{ old('Detalle') }}" oninput="updateClave()">
                    </div>

                    <div class="form-group">
                        <label>Observación <span class="hint">— ¿con qué objetivo se crea este tipo?</span></label>
                        <textarea name="Observacion" id="f_observacion" rows="4" maxlength="300"
                                  placeholder="Explica para qué se utiliza este tipo de entrada..."
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
        const STORE_URL = "{{ route('admin.tipos_entrada.index') }}";

        function openModal()  { document.getElementById('modal-tipo').classList.add('open'); }
        function closeModal() { document.getElementById('modal-tipo').classList.remove('open'); }

        // Deriva la clave interna igual que el backend (MAYÚSCULAS, sin acentos, A-Z0-9_).
        function deriveCode(s) {
            return (s || '')
                .normalize('NFD').replace(/[̀-ͯ]/g, '')
                .toUpperCase()
                .replace(/[^A-Z0-9]+/g, '_')
                .replace(/^_+|_+$/g, '');
        }
        function updateClave() {
            const code = deriveCode(document.getElementById('f_detalle').value);
            document.getElementById('clave_preview').textContent = code || '—';
        }
        function updateCounter() {
            document.getElementById('obs_count').textContent = document.getElementById('f_observacion').value.length;
        }

        function resetForm() {
            const f = document.getElementById('form-tipo');
            f.reset();
            f.action = "{{ route('admin.tipos_entrada.store') }}";
            document.getElementById('form-method').value = 'POST';
            document.getElementById('modal-title').textContent = 'Nuevo tipo de entrada';
            document.getElementById('f_codigo').value = 'Automático';
            updateClave();
            updateCounter();
        }

        function openCrear() {
            resetForm();
            openModal();
            setTimeout(() => document.getElementById('f_detalle').focus(), 100);
        }

        function editTipo(t) {
            resetForm();
            document.getElementById('modal-title').textContent = 'Editar tipo de entrada';
            document.getElementById('form-tipo').action = "{{ url('admin/tipos-entrada') }}/" + t.id;
            document.getElementById('form-method').value = 'PUT';
            document.getElementById('f_codigo').value = t.id;
            document.getElementById('f_detalle').value = t.Detalle ?? '';
            document.getElementById('f_observacion').value = t.Observacion ?? '';
            updateClave();
            updateCounter();
            openModal();
        }

        // Reabrir el modal si hubo errores de validación.
        @if($errors->any())
            window.addEventListener('DOMContentLoaded', () => {
                @if(old('_method') === 'PUT')
                    document.getElementById('modal-title').textContent = 'Editar tipo de entrada';
                @endif
                updateClave();
                updateCounter();
                openModal();
            });
        @endif
    </script>
</x-app-layout>
