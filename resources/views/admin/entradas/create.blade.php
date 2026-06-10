<x-app-layout>
    <x-slot name="header">
        <h2>{{ $entrada->exists ? 'Editar Entrada · '.$entrada->codigo : 'Nueva Entrada de Inventario' }}</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }
        .st-alert { padding:14px 18px; border-radius:10px; margin-bottom:20px; }
        .st-alert.error { background:#fee2e2; color:#991b1b; border-left:5px solid #ef4444; }
        .panel { background:#fff; border-radius:14px; padding:22px; box-shadow:0 6px 14px rgba(0,0,0,.04); margin-bottom:20px; }
        .panel h3 { margin:0 0 16px 0; font-size:1rem; color:var(--inst); border-bottom:2px solid #f3f4f6; padding-bottom:8px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; display:flex; align-items:center; gap:8px; }

        .form-row { display:grid; gap:14px; margin-bottom:10px; }
        .cols-2 { grid-template-columns:1fr 1fr; }
        .cols-3 { grid-template-columns:1fr 1fr 1fr; }
        .cols-4 { grid-template-columns:repeat(4,1fr); }
        .cols-6 { grid-template-columns:repeat(6,1fr); }
        @media (max-width:1100px){ .cols-6 { grid-template-columns:repeat(3,1fr); } }
        @media (max-width:800px){ .cols-2,.cols-3,.cols-4,.cols-6 { grid-template-columns:1fr; } }

        .form-group label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:5px; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:9px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; font-family:inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--inst); box-shadow:0 0 0 3px rgba(46,58,117,.1); }

        .items-table { width:100%; border-collapse:separate; border-spacing:0; }
        .items-table th { background:#f9fafb; color:#374151; font-weight:700; font-size:.74rem; text-transform:uppercase; letter-spacing:.04em; padding:10px 8px; text-align:left; border-bottom:2px solid #e5e7eb; }
        .items-table td { padding:8px; border-bottom:1px solid #f3f4f6; vertical-align:top; }
        .items-table input, .items-table select { width:100%; padding:7px 8px; border:1.5px solid #d1d5db; border-radius:6px; font-size:.84rem; outline:none; }
        .items-table input:focus, .items-table select:focus { border-color:var(--inst); }
        .col-mini { width:90px; }
        .col-med  { width:140px; }

        .semaforo { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:5px; vertical-align:middle; }
        .sem-green { background:#10b981; }
        .sem-amber { background:#f59e0b; }
        .sem-red   { background:#ef4444; }
        .sem-gray  { background:#9ca3af; }

        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:10px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; gap:7px; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; }
        .btn-success { background:#10b981; color:#fff; border:none; cursor:pointer; padding:10px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; gap:7px; text-decoration:none; }
        .btn-success:hover { background:#059669; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:10px 16px; border-radius:8px; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; cursor:pointer; }
        .btn-ghost-add { background:#eef0f7; color:var(--inst); border:1.5px dashed var(--inst); padding:10px 18px; border-radius:8px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; }
        .btn-row-del { background:#fee2e2; color:#dc2626; border:none; cursor:pointer; width:32px; height:32px; border-radius:8px; }
        .btn-row-del:hover { background:#dc2626; color:#fff; }

        .totals { background:#f9fafb; border-radius:12px; padding:18px; display:grid; gap:8px; }
        .totals .row { display:flex; justify-content:space-between; font-size:.95rem; }
        .totals .row.big { font-size:1.25rem; font-weight:800; color:var(--inst); border-top:2px solid #e5e7eb; padding-top:10px; margin-top:6px; }

        .actions { display:flex; justify-content:flex-end; gap:10px; margin-top:18px; flex-wrap:wrap; }
    </style>

    @if ($errors->any())
        <div class="st-alert error">
            <strong>Hay errores en el formulario:</strong>
            <ul style="margin:6px 0 0 22px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" id="form-entrada"
          action="{{ $entrada->exists ? route('admin.entradas.update', $entrada) : route('admin.entradas.store') }}">
        @csrf
        @if($entrada->exists) @method('PUT') @endif

        <div class="panel">
            <h3>📄 Documento de entrada</h3>
            <div class="form-row cols-4">
                <div class="form-group">
                    <label>Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo', $entrada->codigo) }}" placeholder="Se generará automáticamente">
                </div>
                <div class="form-group">
                    <label>Tipo de entrada *</label>
                    <select name="tipo_entrada" required>
                        @foreach($tiposEntrada as $t)
                            <option value="{{ $t->codigo }}" {{ old('tipo_entrada', $entrada->tipo_entrada) == $t->codigo ? 'selected' : '' }}>{{ $t->Detalle }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha de entrada *</label>
                    <input type="datetime-local" name="fecha_entrada" required
                           value="{{ old('fecha_entrada', optional($entrada->fecha_entrada)->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="form-group">
                    <label>Fecha del documento</label>
                    <input type="date" name="fecha_documento" value="{{ old('fecha_documento', optional($entrada->fecha_documento)->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="form-row cols-6">
                <div class="form-group">
                    <label>Proveedor</label>
                    <select name="proveedor_id">
                        <option value="">— Sin proveedor —</option>
                        @foreach($proveedores as $p)
                            <option value="{{ $p->id }}" {{ old('proveedor_id', $entrada->proveedor_id) == $p->id ? 'selected' : '' }}>{{ $p->razon_social }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>N° Factura</label>
                    <input type="text" name="numero_factura" value="{{ old('numero_factura', $entrada->numero_factura) }}">
                </div>
                <div class="form-group">
                    <label>N° Remisión</label>
                    <input type="text" name="numero_remision" value="{{ old('numero_remision', $entrada->numero_remision) }}">
                </div>
                {{-- Asignación a paciente (inline): visibles solo si Tipo de entrada = Asignación --}}
                <div class="form-group asig-field" style="display:none;">
                    <label>N° Identificación *</label>
                    <input type="text" id="asig_doc" name="_asig_doc" autocomplete="off" placeholder="Documento"
                           value="{{ old('_asig_doc', optional($entrada->paciente)->documento) }}">
                    <small id="asig_msg" style="font-size:.74rem; display:block; margin-top:3px;"></small>
                    <a href="{{ route('admin.dispensacion.pacientes.create') }}" target="_blank"
                       style="font-size:.74rem; color:var(--inst); text-decoration:none; font-weight:600;">➕ Crear paciente</a>
                </div>
                <div class="form-group asig-field" style="display:none;">
                    <label>Paciente</label>
                    <input type="text" id="asig_nombre" readonly placeholder="—" style="background:#f9fafb;"
                           value="{{ optional($entrada->paciente)->nombre_completo }}">
                    <input type="hidden" name="paciente_id" id="asig_paciente_id" value="{{ old('paciente_id', $entrada->paciente_id) }}">
                </div>
                <div class="form-group asig-field" style="display:none;">
                    <label>EPS</label>
                    <input type="text" id="asig_eps" readonly placeholder="—" style="background:#f9fafb;"
                           value="{{ optional($entrada->paciente)->eps }}">
                </div>
            </div>
            <div class="form-group">
                <label>Observaciones</label>
                <textarea name="observaciones" rows="2">{{ old('observaciones', $entrada->observaciones) }}</textarea>
            </div>
        </div>

        <div class="panel">
            <h3>📦 Productos recibidos
                <button type="button" class="btn-ghost-add" style="margin-left:auto; font-size:.82rem;" onclick="addRow()">+ Agregar producto</button>
            </h3>
            <div style="overflow-x:auto;">
                <table class="items-table" id="items-table">
                    <thead>
                        <tr>
                            <th class="col-med">Medicamento *</th>
                            <th class="col-med">Presentación *</th>
                            <th class="col-mini">Lote *</th>
                            <th class="col-mini">Vence</th>
                            <th>Sem</th>
                            <th class="col-mini">Cantidad *</th>
                            <th>Unidad</th>
                            <th class="col-mini">Costo unit.</th>
                            <th class="col-mini">Total</th>
                            <th>INVIMA</th>
                            <th>Ubic.</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div style="margin-top:18px; display:grid; grid-template-columns:1fr 320px; gap:18px;">
                <div></div>
                <div class="totals">
                    <div class="row"><span>Subtotal</span><strong id="t-subtotal">$ 0.00</strong></div>
                    <div class="row" style="display:flex; align-items:center; gap:10px;">
                        <span>Impuestos</span>
                        <input type="number" step="0.01" name="impuestos" id="f-impuestos" value="{{ old('impuestos', $entrada->impuestos ?: 0) }}" style="max-width:120px; text-align:right; padding:6px 8px; border:1.5px solid #d1d5db; border-radius:8px;">
                    </div>
                    <div class="row big"><span>Total</span><strong id="t-total">$ 0.00</strong></div>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('admin.entradas.index') }}" class="btn-outline">← Cancelar</a>
            <button type="submit" class="btn-primary" name="confirmar" value="0">💾 Guardar borrador</button>
            <button type="submit" class="btn-success" name="confirmar" value="1" onclick="return confirm('Al confirmar se actualizará el stock y se generará el kardex. ¿Continuar?')">✅ Guardar y confirmar</button>
        </div>
    </form>

    <script>
        const MEDICAMENTOS   = @json($medicamentosJson);
        const PRESENTACIONES = @json($presentacionesJson);
        const LABORATORIOS   = @json($laboratoriosJson);
        const UNIDADES       = @json($unidadesJson);
        const EXISTING       = @json($existingJson);

        let rowIdx = 0;
        const tbody = document.querySelector('#items-table tbody');

        function semaforo(fecha) {
            if (!fecha) return 'sem-gray';
            const hoy = new Date(); hoy.setHours(0,0,0,0);
            const v = new Date(fecha);
            const dias = Math.floor((v - hoy) / 86400000);
            if (dias < 0) return 'sem-red';
            if (dias <= 90) return 'sem-amber';
            return 'sem-green';
        }

        function buildSelect(name, options, value, includeBlank = true, label = 'nombre') {
            const opts = (includeBlank ? '<option value="">—</option>' : '') +
                options.map(o => `<option value="${o.id}" ${String(value)===String(o.id)?'selected':''}>${o[label]}${o.abreviatura?' ('+o.abreviatura+')':''}</option>`).join('');
            return `<select name="${name}">${opts}</select>`;
        }

        function addRow(prefill = {}) {
            const i = rowIdx++;
            const tr = document.createElement('tr');
            tr.dataset.idx = i;
            tr.innerHTML = `
                <td>${buildSelect(`items[${i}][medicamento_id]`, MEDICAMENTOS, prefill.medicamento_id)}</td>
                <td><select name="items[${i}][presentacion_id]" data-role="presentacion"><option value="">—</option></select></td>
                <td><input type="text" name="items[${i}][lote]" value="${prefill.lote||''}" required></td>
                <td><input type="date" name="items[${i}][fecha_vencimiento]" value="${prefill.fecha_vencimiento||''}" data-role="venc"></td>
                <td style="text-align:center;"><span class="semaforo sem-gray" data-role="sem"></span></td>
                <td><input type="number" step="0.01" min="0" name="items[${i}][cantidad]" value="${prefill.cantidad||''}" required data-role="cant"></td>
                <td>${buildSelect(`items[${i}][unidad_medida_id]`, UNIDADES, prefill.unidad_medida_id, true, 'nombre')}</td>
                <td><input type="number" step="0.01" min="0" name="items[${i}][costo_unitario]" value="${prefill.costo_unitario||0}" data-role="costo"></td>
                <td style="text-align:right;"><strong data-role="subtotal">0.00</strong></td>
                <td><input type="text" name="items[${i}][registro_invima]" value="${prefill.registro_invima||''}" placeholder="INVIMA"></td>
                <td><input type="text" name="items[${i}][ubicacion]" value="${prefill.ubicacion||''}" placeholder="Estante"></td>
                <td><button type="button" class="btn-row-del" onclick="removeRow(this)">×</button>
                    <input type="hidden" name="items[${i}][laboratorio_id]" value="${prefill.laboratorio_id||''}"></td>`;
            tbody.appendChild(tr);

            // Wire events
            const medSel = tr.querySelector(`[name="items[${i}][medicamento_id]"]`);
            const preSel = tr.querySelector('[data-role="presentacion"]');
            preSel.name = `items[${i}][presentacion_id]`;
            const uniSel = tr.querySelector(`[name="items[${i}][unidad_medida_id]"]`);
            const cant   = tr.querySelector('[data-role="cant"]');
            const costo  = tr.querySelector('[data-role="costo"]');
            const venc   = tr.querySelector('[data-role="venc"]');
            const sem    = tr.querySelector('[data-role="sem"]');

            function refreshPresentaciones() {
                const medId = medSel.value;
                const filt  = PRESENTACIONES.filter(p => String(p.medicamento_id) === String(medId));
                preSel.innerHTML = '<option value="">—</option>' + filt.map(p => `<option value="${p.id}" data-unidad="${p.unidad_medida_id||''}" ${String(p.id)===String(prefill.presentacion_id)?'selected':''}>${p.nombre}</option>`).join('');
                preSel.dispatchEvent(new Event('change'));
                // auto INVIMA
                const med = MEDICAMENTOS.find(m => String(m.id)===String(medId));
                const invInput = tr.querySelector(`[name="items[${i}][registro_invima]"]`);
                if (med && med.registro_invima && !invInput.value) invInput.value = med.registro_invima;
            }
            preSel.addEventListener('change', () => {
                const opt = preSel.selectedOptions[0];
                if (opt && opt.dataset.unidad && !uniSel.value) uniSel.value = opt.dataset.unidad;
            });
            medSel.addEventListener('change', refreshPresentaciones);
            refreshPresentaciones();

            function recalc() {
                const c = parseFloat(cant.value) || 0;
                const p = parseFloat(costo.value) || 0;
                tr.querySelector('[data-role="subtotal"]').textContent = (c*p).toFixed(2);
                updateTotals();
            }
            [cant, costo].forEach(el => el.addEventListener('input', recalc));
            venc.addEventListener('input', () => {
                sem.className = 'semaforo ' + semaforo(venc.value);
            });
            sem.className = 'semaforo ' + semaforo(venc.value);
            recalc();
        }

        function removeRow(btn) {
            btn.closest('tr').remove();
            updateTotals();
        }

        function updateTotals() {
            let sub = 0;
            document.querySelectorAll('#items-table tbody tr').forEach(tr => {
                sub += parseFloat(tr.querySelector('[data-role="subtotal"]').textContent) || 0;
            });
            const imp = parseFloat(document.getElementById('f-impuestos').value) || 0;
            document.getElementById('t-subtotal').textContent = '$ ' + sub.toFixed(2);
            document.getElementById('t-total').textContent    = '$ ' + (sub + imp).toFixed(2);
        }
        document.getElementById('f-impuestos').addEventListener('input', updateTotals);

        // Cargar filas existentes o una vacía
        if (EXISTING.length) {
            EXISTING.forEach(d => addRow(d));
        } else {
            addRow();
        }

        // ---- Asignación a paciente (Tipo de entrada = ASIGNACION) ----
        const tipoEntradaSel = document.querySelector('select[name="tipo_entrada"]');
        const asigFields     = document.querySelectorAll('.asig-field');
        const asigDoc        = document.getElementById('asig_doc');
        const asigNombre     = document.getElementById('asig_nombre');
        const asigEps        = document.getElementById('asig_eps');
        const asigPacienteId = document.getElementById('asig_paciente_id');
        const asigMsg        = document.getElementById('asig_msg');
        let   asigTimer;

        function toggleAsignacion() {
            const esAsig = tipoEntradaSel.value === 'ASIGNACION';
            asigFields.forEach(el => el.style.display = esAsig ? '' : 'none');
            if (!esAsig) {           // si no aplica, no se envía paciente
                asigDoc.value = ''; asigNombre.value = ''; asigEps.value = '';
                asigPacienteId.value = ''; asigMsg.textContent = '';
            }
        }

        function buscarPaciente() {
            const doc = asigDoc.value.trim();
            asigPacienteId.value = ''; asigNombre.value = ''; asigEps.value = '';
            if (!doc) { asigMsg.textContent = ''; return; }
            asigMsg.style.color = '#6b7280'; asigMsg.textContent = 'Buscando…';
            fetch("{{ route('admin.entradas.buscar_paciente') }}?documento=" + encodeURIComponent(doc),
                  { headers: { 'Accept': 'application/json' } })
                .then(r => r.ok ? r.json() : Promise.reject(r))
                .then(p => {
                    asigNombre.value     = p.nombre || '';
                    asigEps.value        = p.eps || '';
                    asigPacienteId.value = p.id;
                    asigMsg.style.color  = '#059669';
                    asigMsg.textContent  = '✓ Paciente encontrado';
                })
                .catch(() => {
                    asigMsg.style.color = '#dc2626';
                    asigMsg.innerHTML   = '✗ No existe un paciente con ese documento. Usa «Crear paciente».';
                });
        }

        tipoEntradaSel.addEventListener('change', toggleAsignacion);
        asigDoc.addEventListener('input', () => { clearTimeout(asigTimer); asigTimer = setTimeout(buscarPaciente, 500); });

        toggleAsignacion();  // estado inicial
        if (tipoEntradaSel.value === 'ASIGNACION' && asigDoc.value.trim()) {
            buscarPaciente(); // re-cargar datos del paciente (edición o tras error de validación)
        }
    </script>
</x-app-layout>
