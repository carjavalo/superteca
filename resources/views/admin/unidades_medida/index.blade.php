<x-app-layout>
    <x-slot name="header">
        <h2>Maestros · Unidades de Medida</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }
        .st-alert { padding:14px 18px; border-radius:10px; margin-bottom:20px; display:flex; align-items:center; gap:12px; font-size:.92rem; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:5px solid #10b981; }
        .st-alert.error   { background:#fee2e2; color:#991b1b; border-left:5px solid #ef4444; }

        .dash-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(200px,1fr)); gap:16px; margin-bottom:24px; }
        .dash-card { background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 4px 12px rgba(0,0,0,.05); border-left:5px solid var(--inst); display:flex; align-items:center; justify-content:space-between; gap:14px; }
        .dash-card .label { color:#6b7280; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
        .dash-card .value { color:var(--inst); font-size:1.85rem; font-weight:800; line-height:1; margin-top:4px; }
        .dash-card .ic { font-size:1.6rem; }
        .dash-card.green { border-left-color:#10b981; }
        .dash-card.blue  { border-left-color:#3b82f6; }
        .dash-card.amber { border-left-color:#f59e0b; }
        .dash-card.purple{ border-left-color:#a855f7; }

        .calc-card { background:linear-gradient(135deg, #2e3a75 0%, #4c5fb8 100%); color:#fff; border-radius:16px; padding:22px; margin-bottom:24px; box-shadow:0 10px 24px rgba(46,58,117,.25); }
        .calc-card h3 { margin:0 0 14px 0; font-size:1.1rem; display:flex; align-items:center; gap:8px; }
        .calc-row { display:grid; grid-template-columns:1fr auto 1fr auto 1.4fr; gap:10px; align-items:end; }
        @media (max-width:800px){ .calc-row { grid-template-columns:1fr; } }
        .calc-row label { display:block; font-size:.78rem; opacity:.85; margin-bottom:5px; font-weight:600; }
        .calc-row input, .calc-row select { width:100%; padding:9px 11px; border-radius:8px; border:none; font-size:.92rem; outline:none; color:#1f2937; }
        .calc-arrow { font-size:1.4rem; text-align:center; padding-bottom:9px; }
        .calc-result { background:rgba(255,255,255,.18); border-radius:10px; padding:10px 14px; font-weight:700; font-size:1.1rem; min-height:44px; display:flex; align-items:center; }

        .layout-catalog { display:grid; grid-template-columns:280px 1fr; gap:24px; align-items:start; }
        @media (max-width:900px){ .layout-catalog { grid-template-columns:1fr; } }

        .filter-card { background:#fff; border-radius:14px; box-shadow:0 6px 16px rgba(0,0,0,.05); padding:22px; position:sticky; top:20px; }
        .filter-card h3 { font-size:1.05rem; color:var(--inst); margin:0 0 18px 0; font-weight:700; border-bottom:2px solid #f3f4f6; padding-bottom:10px; }
        .filter-group { margin-bottom:14px; }
        .filter-group label { display:block; font-size:.8rem; font-weight:600; color:#4b5563; margin-bottom:6px; }
        .filter-group input, .filter-group select { width:100%; padding:9px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; }
        .filter-actions { display:flex; flex-direction:column; gap:10px; margin-top:18px; }

        .catalog-top { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:15px; flex-wrap:wrap; }
        .catalog-top h2 { font-size:1.4rem; font-weight:800; color:var(--inst); margin:0; }
        .catalog-top p  { color:#6b7280; margin:4px 0 0 0; font-size:.92rem; }

        .group-block { margin-bottom:30px; }
        .group-block h3 { font-size:.95rem; color:var(--inst); font-weight:700; text-transform:uppercase; letter-spacing:.06em; margin:0 0 12px 0; display:flex; align-items:center; gap:8px; }
        .group-block h3 .pill { background:#eef0f7; color:var(--inst); font-size:.72rem; padding:2px 9px; border-radius:20px; }

        .card-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(240px,1fr)); gap:16px; }

        .u-card { background:#fff; border-radius:16px; box-shadow:0 6px 14px rgba(0,0,0,.05); border:1px solid #f1f1f4; transition:all .2s; overflow:hidden; display:flex; flex-direction:column; }
        .u-card:hover { transform:translateY(-4px); box-shadow:0 14px 28px rgba(0,0,0,.1); }
        .u-card .accent { height:6px; }
        .u-head { padding:18px; display:flex; align-items:center; gap:12px; }
        .u-icon { width:54px; height:54px; border-radius:14px; display:flex; align-items:center; justify-content:center; font-size:1.6rem; flex-shrink:0; }
        .u-title h4 { margin:0; font-size:1.05rem; font-weight:800; color:#1f2937; }
        .u-title .sub { color:#6b7280; font-size:.78rem; margin-top:2px; }
        .u-body { padding:0 18px 12px 18px; font-size:.82rem; color:#4b5563; }
        .u-body .kv { display:flex; justify-content:space-between; padding:5px 0; border-top:1px dashed #f1f1f4; }
        .u-body .kv:first-child { border-top:none; }
        .u-body .kv b { color:#1f2937; font-weight:700; }
        .u-foot { padding:12px 18px; background:#f9fafb; border-top:1px solid #f1f1f4; display:flex; justify-content:space-between; align-items:center; }

        .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 9px; border-radius:20px; font-size:.7rem; font-weight:700; border:1px solid transparent; }
        .badge-active   { background:#d1fae5; color:#065f46; border-color:#a7f3d0; }
        .badge-inactive { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
        .badge-base     { background:#eef0f7; color:var(--inst); border-color:#dde0ee; }

        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:9px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; justify-content:center; gap:7px; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; cursor:pointer; padding:9px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; gap:7px; text-decoration:none; }
        .btn-icon { width:32px; height:32px; border-radius:8px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; }
        .btn-view { background:#f5f3ff; color:#7c3aed; } .btn-view:hover { background:#7c3aed; color:#fff; }
        .btn-edit { background:#eff6ff; color:#2563eb; } .btn-edit:hover { background:#2563eb; color:#fff; }
        .btn-del  { background:#fee2e2; color:#dc2626; } .btn-del:hover  { background:#dc2626; color:#fff; }

        .modal-overlay { position:fixed; inset:0; background:rgba(15,23,42,.65); backdrop-filter:blur(5px); display:flex; align-items:center; justify-content:center; z-index:200; opacity:0; pointer-events:none; transition:opacity .25s; }
        .modal-overlay.open { opacity:1; pointer-events:all; }
        .modal-box { background:#fff; border-radius:18px; width:100%; max-width:820px; max-height:92vh; overflow-y:auto; }
        .modal-header { padding:18px 22px; border-bottom:1px solid #e5e7eb; display:flex; justify-content:space-between; align-items:center; position:sticky; top:0; background:#fff; }
        .modal-header h3 { margin:0; color:var(--inst); font-weight:800; }
        .modal-close { background:none; border:none; font-size:1.5rem; cursor:pointer; color:#9ca3af; }
        .modal-body { padding:22px; }
        .modal-footer { padding:16px 22px; border-top:1px solid #e5e7eb; display:flex; justify-content:flex-end; gap:10px; background:#f9fafb; border-bottom-left-radius:18px; border-bottom-right-radius:18px; }
        .form-section { margin-bottom:18px; }
        .form-section h4 { font-size:.85rem; color:var(--inst); border-bottom:2px solid #f3f4f6; padding-bottom:6px; margin:0 0 12px 0; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
        .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:10px; }
        .form-row.cols-3 { grid-template-columns:1fr 1fr 1fr; }
        .form-row.cols-4 { grid-template-columns:repeat(4,1fr); }
        .form-group label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:5px; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:9px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; font-family:inherit; }
        .form-check { display:flex; align-items:center; gap:7px; padding:8px 10px; background:#f9fafb; border-radius:8px; border:1px solid #f1f1f4; }
        @media (max-width:700px){ .form-row, .form-row.cols-3, .form-row.cols-4 { grid-template-columns:1fr; } }
    </style>

    @if(session('success')) <div class="st-alert success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="st-alert error">{{ session('error') }}</div> @endif

    <div class="dash-grid">
        <div class="dash-card blue"><div><div class="label">Total unidades</div><div class="value">{{ $stats['total'] }}</div></div><div class="ic">📏</div></div>
        <div class="dash-card green"><div><div class="label">Tipos clínicos</div><div class="value">{{ $stats['tipos'] }}</div></div><div class="ic">🧬</div></div>
        <div class="dash-card amber"><div><div class="label">Activas en cálculos</div><div class="value">{{ $stats['calculo'] }}</div></div><div class="ic">🧮</div></div>
        <div class="dash-card purple"><div><div class="label">Vigentes</div><div class="value">{{ $stats['activas'] }}</div></div><div class="ic">✅</div></div>
    </div>

    {{-- Calculadora integrada --}}
    <div class="calc-card">
        <h3>🧮 Calculadora de conversiones</h3>
        <div class="calc-row">
            <div>
                <label>Valor</label>
                <input type="number" id="calc-val" step="any" value="500">
            </div>
            <div class="calc-arrow">⇄</div>
            <div>
                <label>Desde</label>
                <select id="calc-from"></select>
            </div>
            <div class="calc-arrow">→</div>
            <div>
                <label>Hacia</label>
                <select id="calc-to"></select>
            </div>
        </div>
        <div style="margin-top:14px;">
            <label style="display:block; font-size:.78rem; opacity:.85; margin-bottom:5px; font-weight:600;">Resultado</label>
            <div class="calc-result" id="calc-result">—</div>
        </div>
    </div>

    <div class="layout-catalog">
        <aside>
            <div class="filter-card">
                <h3>Filtros</h3>
                <form method="GET" action="{{ route('admin.unidades_medida.index') }}">
                    <div class="filter-group">
                        <label>Buscar</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="mg, mL, MG...">
                    </div>
                    <div class="filter-group">
                        <label>Tipo</label>
                        <select name="tipo">
                            <option value="">Todos</option>
                            @foreach(\App\Models\UnidadMedida::TIPOS as $k => $l)
                                <option value="{{ $k }}" {{ request('tipo') == $k ? 'selected' : '' }}>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Cálculos clínicos</label>
                        <select name="calculos">
                            <option value="">Indiferente</option>
                            <option value="1" {{ request('calculos') === '1' ? 'selected' : '' }}>Sí</option>
                            <option value="0" {{ request('calculos') === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label>Estado</label>
                        <select name="estado">
                            <option value="">Todos</option>
                            <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activas</option>
                            <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactivas</option>
                        </select>
                    </div>
                    <div class="filter-actions">
                        <button type="submit" class="btn-primary">Filtrar</button>
                        <a href="{{ route('admin.unidades_medida.index') }}" class="btn-outline" style="text-align:center;">Restablecer</a>
                    </div>
                </form>
            </div>
        </aside>

        <main>
            <div class="catalog-top">
                <div>
                    <h2>Catálogo técnico de unidades clínicas</h2>
                    <p>Define las unidades de medida que controlan validaciones, conversiones automáticas y cálculos farmacéuticos.</p>
                </div>
                <button class="btn-primary" onclick="openCreate()">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Nueva Unidad
                </button>
            </div>

            @if(! request()->hasAny(['search','tipo','calculos','estado']))
                {{-- Vista agrupada por tipo --}}
                @foreach($unidadesPorTipo as $tipo => $items)
                    @php $color = \App\Models\UnidadMedida::COLOR_TIPO[$tipo] ?? '#94a3b8'; $icono = \App\Models\UnidadMedida::ICONO_TIPO[$tipo] ?? '🔧'; @endphp
                    <div class="group-block">
                        <h3>
                            <span style="display:inline-flex;width:24px;height:24px;border-radius:8px;background:{{ $color }}22;align-items:center;justify-content:center;">{{ $icono }}</span>
                            {{ \App\Models\UnidadMedida::TIPOS[$tipo] ?? $tipo }}
                            <span class="pill">{{ $items->count() }}</span>
                        </h3>
                        <div class="card-grid">
                            @foreach($items as $u)
                                @include('admin.unidades_medida._card', ['u' => $u])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                {{-- Vista filtrada plana paginada --}}
                <div class="card-grid">
                    @forelse($unidades as $u)
                        @include('admin.unidades_medida._card', ['u' => $u])
                    @empty
                        <div style="grid-column:1/-1; text-align:center; padding:2.5rem; background:#fff; border:2px dashed #e5e7eb; border-radius:14px;">
                            <h3 style="color:#4b5563;">Sin resultados</h3>
                        </div>
                    @endforelse
                </div>
                <div style="margin-top:20px;">{{ $unidades->links() }}</div>
            @endif
        </main>
    </div>

    {{-- Modal --}}
    <div id="modal-crear" class="modal-overlay">
        <div class="modal-box">
            <div class="modal-header">
                <h3 id="modal-title">Nueva Unidad de Medida</h3>
                <button type="button" class="modal-close" onclick="closeModal('modal-crear')">&times;</button>
            </div>
            <form id="form-unidad" action="{{ route('admin.unidades_medida.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <div class="modal-body">
                    <div class="form-section">
                        <h4>Identificación</h4>
                        <div class="form-row cols-4">
                            <div class="form-group"><label>Código *</label><input type="text" name="codigo" id="f_codigo" maxlength="20" required placeholder="MG"></div>
                            <div class="form-group"><label>Nombre *</label><input type="text" name="nombre" id="f_nombre" required></div>
                            <div class="form-group"><label>Abreviatura *</label><input type="text" name="abreviatura" id="f_abreviatura" required></div>
                            <div class="form-group"><label>Símbolo</label><input type="text" name="simbolo" id="f_simbolo"></div>
                        </div>
                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label>Tipo *</label>
                                <select name="tipo" id="f_tipo">
                                    <option value="">— Seleccione —</option>
                                    @foreach(\App\Models\UnidadMedida::TIPOS as $k => $l)
                                        <option value="{{ $k }}">{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Ícono</label><input type="text" name="icono" id="f_icono" placeholder="⚖️"></div>
                            <div class="form-group"><label>Color</label><input type="color" name="color_identificacion" id="f_color_identificacion" value="#3b82f6"></div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h4>Conversión y precisión</h4>
                        <div class="form-row cols-3">
                            <div class="form-group">
                                <label>Unidad base</label>
                                <select name="unidad_base_id" id="f_unidad_base_id">
                                    <option value="">— Independiente —</option>
                                    @foreach(\App\Models\UnidadMedida::orderBy('tipo')->orderBy('nombre')->get() as $b)
                                        <option value="{{ $b->id }}">[{{ $b->tipo_label }}] {{ $b->nombre }} ({{ $b->abreviatura }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group"><label>Factor de conversión</label><input type="number" step="0.00000001" name="factor_conversion" id="f_factor_conversion" value="1"></div>
                            <div class="form-group"><label>Precisión decimal</label><input type="number" min="0" max="10" name="precision_decimal" id="f_precision_decimal" value="2"></div>
                        </div>
                        <div class="form-row cols-3">
                            <div class="form-check"><input type="hidden" name="permite_fracciones" value="0"><input type="checkbox" name="permite_fracciones" id="f_permite_fracciones" value="1" checked><label for="f_permite_fracciones">Permite fracciones</label></div>
                            <div class="form-check"><input type="hidden" name="activa_calculos" value="0"><input type="checkbox" name="activa_calculos" id="f_activa_calculos" value="1" checked><label for="f_activa_calculos">Activa en cálculos clínicos</label></div>
                            <div class="form-check"><input type="hidden" name="estado" value="0"><input type="checkbox" name="estado" id="f_estado" value="1" checked><label for="f_estado">Activa</label></div>
                        </div>
                        <div class="form-group"><label>Observaciones</label><textarea name="observaciones" id="f_observaciones" rows="2"></textarea></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-outline" onclick="closeModal('modal-crear')">Cancelar</button>
                    <button type="submit" class="btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) { document.getElementById(id).classList.add('open'); }
        function closeModal(id) { document.getElementById(id).classList.remove('open'); }

        const STORE_URL = "{{ route('admin.unidades_medida.store') }}";
        function updateUrl(id) { return "{{ url('admin/unidades-medida') }}/" + id; }

        function openCreate() {
            const f = document.getElementById('form-unidad');
            f.reset();
            f.action = STORE_URL;
            document.getElementById('form-method').value = 'POST';
            document.getElementById('modal-title').textContent = 'Nueva Unidad de Medida';
            document.getElementById('f_color_identificacion').value = '#3b82f6';
            ['f_permite_fracciones','f_activa_calculos','f_estado'].forEach(id => document.getElementById(id).checked = true);
            openModal('modal-crear');
        }

        function editUnidad(u) {
            openCreate();
            document.getElementById('modal-title').textContent = 'Editar Unidad de Medida';
            document.getElementById('form-unidad').action = updateUrl(u.id);
            document.getElementById('form-method').value = 'PUT';
            ['codigo','nombre','abreviatura','simbolo','tipo','icono','color_identificacion','unidad_base_id','factor_conversion','precision_decimal','observaciones'].forEach(f => {
                const el = document.getElementById('f_'+f);
                if (el) el.value = u[f] ?? (f === 'color_identificacion' ? '#3b82f6' : '');
            });
            ['permite_fracciones','activa_calculos','estado'].forEach(f => {
                const el = document.getElementById('f_'+f);
                if (el) el.checked = !!u[f];
            });
        }

        @if($errors->any())
            openModal('modal-crear');
        @endif

        // ------- Calculadora -------
        const UNIDADES = @json($unidadesJson);

        function buildOptions(select, exclude = null) {
            select.innerHTML = '';
            const byTipo = {};
            UNIDADES.forEach(u => {
                if (!u.factor_conversion) return;
                (byTipo[u.tipo] ||= []).push(u);
            });
            Object.entries(byTipo).forEach(([tipo, items]) => {
                const g = document.createElement('optgroup');
                g.label = tipo;
                items.forEach(u => {
                    const o = document.createElement('option');
                    o.value = u.id;
                    o.dataset.tipo = u.tipo;
                    o.dataset.factor = u.factor_conversion;
                    o.dataset.precision = u.precision_decimal;
                    o.dataset.abrev = u.abreviatura;
                    o.textContent = `${u.nombre} (${u.abreviatura})`;
                    g.appendChild(o);
                });
                select.appendChild(g);
            });
        }

        const fromSel = document.getElementById('calc-from');
        const toSel   = document.getElementById('calc-to');
        const valIn   = document.getElementById('calc-val');
        const result  = document.getElementById('calc-result');
        buildOptions(fromSel);
        buildOptions(toSel);
        // por defecto mg → g
        const idMg = UNIDADES.find(u => u.codigo === 'MG')?.id;
        const idG  = UNIDADES.find(u => u.codigo === 'G')?.id;
        if (idMg) fromSel.value = idMg;
        if (idG)  toSel.value   = idG;

        function calcular() {
            const f = fromSel.selectedOptions[0];
            const t = toSel.selectedOptions[0];
            if (!f || !t) { result.textContent = '—'; return; }
            if (f.dataset.tipo !== t.dataset.tipo) {
                result.innerHTML = '⚠️ Tipos incompatibles (' + f.dataset.tipo + ' vs ' + t.dataset.tipo + ')';
                return;
            }
            const v = parseFloat(valIn.value);
            if (isNaN(v)) { result.textContent = '—'; return; }
            const base = v * parseFloat(f.dataset.factor);
            const out  = base / parseFloat(t.dataset.factor);
            const p    = parseInt(t.dataset.precision) || 2;
            result.innerHTML = `<span>${v} ${f.dataset.abrev}</span><span style="opacity:.6;margin:0 10px;">=</span><span>${out.toFixed(p)} ${t.dataset.abrev}</span>`;
        }
        [valIn, fromSel, toSel].forEach(el => el.addEventListener('input', calcular));
        calcular();
    </script>
</x-app-layout>
