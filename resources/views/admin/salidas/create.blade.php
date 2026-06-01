<x-app-layout>
    <x-slot name="header">
        <h2>{{ $salida->exists ? 'Editar Salida · '.$salida->codigo : 'Nueva Salida de Inventario' }}</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; --salida: #b91c1c; }
        .st-alert { padding:14px 18px; border-radius:10px; margin-bottom:20px; }
        .st-alert.error { background:#fee2e2; color:#991b1b; border-left:5px solid #ef4444; }
        .st-alert.info  { background:#eef2ff; color:#3730a3; border-left:5px solid #6366f1; }

        .panel { background:#fff; border-radius:14px; padding:22px; box-shadow:0 6px 14px rgba(0,0,0,.04); margin-bottom:20px; }
        .panel h3 { margin:0 0 16px 0; font-size:1rem; color:var(--inst); border-bottom:2px solid #f3f4f6; padding-bottom:8px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; display:flex; align-items:center; gap:8px; }

        .form-row { display:grid; gap:14px; margin-bottom:10px; }
        .cols-2 { grid-template-columns:1fr 1fr; }
        .cols-3 { grid-template-columns:1fr 1fr 1fr; }
        .cols-4 { grid-template-columns:repeat(4,1fr); }
        @media (max-width:800px){ .cols-2,.cols-3,.cols-4 { grid-template-columns:1fr; } }

        .form-group label { display:block; font-size:.82rem; font-weight:600; color:#374151; margin-bottom:5px; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:9px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; font-family:inherit; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--salida); box-shadow:0 0 0 3px rgba(185,28,28,.1); }

        .items-table { width:100%; border-collapse:separate; border-spacing:0; }
        .items-table th { background:#f9fafb; color:#374151; font-weight:700; font-size:.74rem; text-transform:uppercase; letter-spacing:.04em; padding:10px 8px; text-align:left; border-bottom:2px solid #e5e7eb; }
        .items-table td { padding:8px; border-bottom:1px solid #f3f4f6; vertical-align:top; }
        .items-table input, .items-table select { width:100%; padding:7px 8px; border:1.5px solid #d1d5db; border-radius:6px; font-size:.84rem; outline:none; }
        .items-table input:focus, .items-table select:focus { border-color:var(--salida); }
        .col-mini { width:90px; }
        .col-med  { width:140px; }

        .semaforo { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:5px; vertical-align:middle; }
        .sem-green { background:#10b981; }
        .sem-amber { background:#f59e0b; }
        .sem-red   { background:#ef4444; }
        .sem-gray  { background:#9ca3af; }

        .stock-pill { display:inline-flex; align-items:center; gap:5px; padding:3px 9px; border-radius:14px; font-size:.78rem; font-weight:700; }
        .sp-ok { background:#d1fae5; color:#065f46; }
        .sp-bajo { background:#fef9c3; color:#854d0e; }
        .sp-critico { background:#fee2e2; color:#991b1b; }

        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:10px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; gap:7px; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; }
        .btn-success { background:#10b981; color:#fff; border:none; cursor:pointer; padding:10px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; gap:7px; text-decoration:none; }
        .btn-success:hover { background:#059669; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:10px 16px; border-radius:8px; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; cursor:pointer; }
        .btn-ghost-add { background:#fef2f2; color:var(--salida); border:1.5px dashed var(--salida); padding:10px 18px; border-radius:8px; font-weight:600; cursor:pointer; display:inline-flex; align-items:center; gap:7px; }
        .btn-row-del { background:#fee2e2; color:#dc2626; border:none; cursor:pointer; width:32px; height:32px; border-radius:8px; }
        .btn-row-del:hover { background:#dc2626; color:#fff; }

        .totals { background:#f9fafb; border-radius:12px; padding:18px; display:grid; gap:8px; }
        .totals .row { display:flex; justify-content:space-between; font-size:.95rem; }
        .totals .row.big { font-size:1.25rem; font-weight:800; color:var(--salida); border-top:2px solid #e5e7eb; padding-top:10px; margin-top:6px; }

        .actions { display:flex; justify-content:flex-end; gap:10px; margin-top:18px; flex-wrap:wrap; }
        .fefo-tag { background:#ecfeff; color:#0e7490; border:1px solid #a5f3fc; padding:2px 8px; border-radius:12px; font-size:.7rem; font-weight:700; }
    </style>

    @if ($errors->any())
        <div class="st-alert error">
            <strong>Hay errores en el formulario:</strong>
            <ul style="margin:6px 0 0 22px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="st-alert info">
        <strong>FEFO automático:</strong> al elegir un medicamento, el sistema sugiere el lote con vencimiento más próximo. Puede cambiarlo manualmente si lo requiere. <strong>Nunca descuenta stock vencido</strong> salvo en salidas tipo <em>Vencimiento</em>.
    </div>

    <form method="POST" id="form-salida"
          action="{{ $salida->exists ? route('admin.salidas.update', $salida) : route('admin.salidas.store') }}">
        @csrf
        @if($salida->exists) @method('PUT') @endif

        <div class="panel">
            <h3>📄 Información de la salida</h3>
            <div class="form-row cols-4">
                <div class="form-group">
                    <label>Código</label>
                    <input type="text" name="codigo" value="{{ old('codigo', $salida->codigo) }}" placeholder="Se generará automáticamente">
                </div>
                <div class="form-group">
                    <label>Tipo de salida *</label>
                    <select name="tipo_salida" required>
                        @foreach(\App\Models\Salida::TIPOS as $k => $l)
                            <option value="{{ $k }}" {{ old('tipo_salida', $salida->tipo_salida) == $k ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Fecha de salida *</label>
                    <input type="datetime-local" name="fecha_salida" required
                           value="{{ old('fecha_salida', optional($salida->fecha_salida)->format('Y-m-d\TH:i')) }}">
                </div>
                <div class="form-group">
                    <label>N° Documento</label>
                    <input type="text" name="numero_documento" value="{{ old('numero_documento', $salida->numero_documento) }}">
                </div>
            </div>
            <div class="form-row cols-4">
                <div class="form-group">
                    <label>Paciente (ID)</label>
                    <input type="number" name="paciente_id" value="{{ old('paciente_id', $salida->paciente_id) }}" placeholder="Opcional">
                </div>
                <div class="form-group">
                    <label>Servicio (ID)</label>
                    <input type="number" name="servicio_id" value="{{ old('servicio_id', $salida->servicio_id) }}" placeholder="Opcional">
                </div>
                <div class="form-group">
                    <label>Bodega origen (ID)</label>
                    <input type="number" name="bodega_origen_id" value="{{ old('bodega_origen_id', $salida->bodega_origen_id) }}" placeholder="Opcional">
                </div>
                <div class="form-group">
                    <label>Bodega destino (ID)</label>
                    <input type="number" name="bodega_destino_id" value="{{ old('bodega_destino_id', $salida->bodega_destino_id) }}" placeholder="Solo en traslados">
                </div>
            </div>
            <div class="form-row cols-2">
                <div class="form-group">
                    <label>Autorizado por</label>
                    <select name="autorizado_por">
                        <option value="">— Sin autorización —</option>
                        @foreach($usuarios as $u)
                            <option value="{{ $u->id }}" {{ old('autorizado_por', $salida->autorizado_por) == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Observaciones</label>
                    <textarea name="observaciones" rows="1">{{ old('observaciones', $salida->observaciones) }}</textarea>
                </div>
            </div>
        </div>

        <div class="panel">
            <h3>💊 Productos a egresar
                <button type="button" class="btn-ghost-add" style="margin-left:auto; font-size:.82rem;" onclick="addRow()">+ Agregar producto</button>
            </h3>
            <div style="overflow-x:auto;">
                <table class="items-table" id="items-table">
                    <thead>
                        <tr>
                            <th class="col-med">Medicamento *</th>
                            <th class="col-med">Presentación</th>
                            <th class="col-med">Lote (FEFO) *</th>
                            <th class="col-mini">Vence</th>
                            <th>Sem</th>
                            <th class="col-mini">Disponible</th>
                            <th class="col-mini">Cantidad *</th>
                            <th>Unidad</th>
                            <th class="col-mini">Costo unit.</th>
                            <th class="col-mini">Total</th>
                            <th>Motivo / Preparación</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div style="margin-top:18px; display:grid; grid-template-columns:1fr 320px; gap:18px;">
                <div></div>
                <div class="totals">
                    <div class="row"><span>Subtotal (costo)</span><strong id="t-subtotal">$ 0.00</strong></div>
                    <div class="row" style="display:flex; align-items:center; gap:10px;">
                        <span>Impuestos</span>
                        <input type="number" step="0.01" name="impuestos" id="f-impuestos" value="{{ old('impuestos', $salida->impuestos ?: 0) }}" style="max-width:120px; text-align:right; padding:6px 8px; border:1.5px solid #d1d5db; border-radius:8px;">
                    </div>
                    <div class="row big"><span>Total</span><strong id="t-total">$ 0.00</strong></div>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('admin.salidas.index') }}" class="btn-outline">← Cancelar</a>
            <button type="submit" class="btn-primary" name="confirmar" value="0">💾 Guardar borrador</button>
            <button type="submit" class="btn-success" name="confirmar" value="1" onclick="return confirm('Al confirmar se descontará el stock y se generará el kardex. ¿Continuar?')">✅ Guardar y confirmar</button>
        </div>
    </form>

    <script>
        const MEDICAMENTOS   = @json($medicamentosJson);
        const PRESENTACIONES = @json($presentacionesJson);
        const LOTES          = @json($lotesJson);
        const UNIDADES       = @json($unidadesJson);
        const EXISTING       = @json($existingJson);

        let rowIdx = 0;
        const tbody = document.querySelector('#items-table tbody');

        function semaforoVenc(fecha) {
            if (!fecha) return 'sem-gray';
            const hoy = new Date(); hoy.setHours(0,0,0,0);
            const v = new Date(fecha);
            const dias = Math.floor((v - hoy) / 86400000);
            if (dias < 0) return 'sem-red';
            if (dias <= 90) return 'sem-amber';
            return 'sem-green';
        }
        function pillStock(disp, ped) {
            const d = parseFloat(disp) || 0;
            const p = parseFloat(ped)  || 0;
            if (p > d) return ['sp-critico','🔴 insuficiente'];
            if (d <= 5 || (p > 0 && p > d * 0.7)) return ['sp-bajo','🟡 bajo'];
            return ['sp-ok','🟢 suficiente'];
        }

        function buildSelect(name, options, value, includeBlank = true, label = 'nombre') {
            const opts = (includeBlank ? '<option value="">—</option>' : '') +
                options.map(o => `<option value="${o.id}" ${String(value)===String(o.id)?'selected':''}>${o[label]}${o.abreviatura?' ('+o.abreviatura+')':''}</option>`).join('');
            return `<select name="${name}">${opts}</select>`;
        }

        function lotesPorMedPres(medId, presId) {
            return LOTES.filter(l =>
                String(l.medicamento_id) === String(medId) &&
                (!presId || String(l.presentacion_id) === String(presId))
            ); // Ya vienen ordenados FEFO desde el servidor
        }

        function addRow(prefill = {}) {
            const i = rowIdx++;
            const tr = document.createElement('tr');
            tr.dataset.idx = i;
            tr.innerHTML = `
                <td>${buildSelect(`__med_${i}`, MEDICAMENTOS, prefill.medicamento_id)}</td>
                <td><select data-role="presentacion"><option value="">—</option></select></td>
                <td>
                    <select name="items[${i}][inventario_lote_id]" data-role="lote" required></select>
                    <div style="margin-top:3px;"><span class="fefo-tag" data-role="fefo-tag" style="display:none;">⚡ FEFO sugerido</span></div>
                </td>
                <td><input type="text" data-role="venc" readonly style="background:#f9fafb;"></td>
                <td style="text-align:center;"><span class="semaforo sem-gray" data-role="sem"></span></td>
                <td style="text-align:right;"><span class="stock-pill sp-ok" data-role="stock">—</span></td>
                <td><input type="number" step="0.01" min="0.01" name="items[${i}][cantidad]" value="${prefill.cantidad||''}" required data-role="cant"></td>
                <td>${buildSelect(`items[${i}][unidad_medida_id]`, UNIDADES, prefill.unidad_medida_id, true, 'nombre')}</td>
                <td><input type="number" step="0.01" min="0" name="items[${i}][costo_unitario]" value="${prefill.costo_unitario||''}" data-role="costo"></td>
                <td style="text-align:right;"><strong data-role="subtotal">0.00</strong></td>
                <td>
                    <input type="text" name="items[${i}][motivo_salida]" value="${prefill.motivo_salida||''}" placeholder="Motivo">
                    <input type="text" name="items[${i}][numero_preparacion]" value="${prefill.numero_preparacion||''}" placeholder="N° preparación" style="margin-top:4px;">
                </td>
                <td><button type="button" class="btn-row-del" onclick="removeRow(this)">×</button></td>`;
            tbody.appendChild(tr);

            const medSel  = tr.querySelector(`[name="__med_${i}"]`);
            const preSel  = tr.querySelector('[data-role="presentacion"]');
            const loteSel = tr.querySelector('[data-role="lote"]');
            const venc    = tr.querySelector('[data-role="venc"]');
            const sem     = tr.querySelector('[data-role="sem"]');
            const stock   = tr.querySelector('[data-role="stock"]');
            const cant    = tr.querySelector('[data-role="cant"]');
            const costo   = tr.querySelector('[data-role="costo"]');
            const fefoTag = tr.querySelector('[data-role="fefo-tag"]');
            const uniSel  = tr.querySelector(`[name="items[${i}][unidad_medida_id]"]`);

            function refreshPresentaciones() {
                const medId = medSel.value;
                const pres = PRESENTACIONES.filter(p => String(p.medicamento_id) === String(medId));
                preSel.innerHTML = '<option value="">— Todas las presentaciones —</option>' + pres.map(p =>
                    `<option value="${p.id}" ${String(p.id)===String(prefill.presentacion_id)?'selected':''}>${p.nombre}</option>`).join('');
                refreshLotes(true);
            }

            function refreshLotes(autoFefo) {
                const medId  = medSel.value;
                const presId = preSel.value;
                const opts   = lotesPorMedPres(medId, presId);
                if (!opts.length) {
                    loteSel.innerHTML = '<option value="">Sin lotes con stock</option>';
                    venc.value = ''; sem.className = 'semaforo sem-gray';
                    stock.className = 'stock-pill sp-critico'; stock.textContent = 'sin stock';
                    fefoTag.style.display = 'none';
                    return;
                }
                loteSel.innerHTML = opts.map((l, idx) => {
                    const sel = prefill.inventario_lote_id
                        ? String(l.id) === String(prefill.inventario_lote_id)
                        : idx === 0;
                    return `<option value="${l.id}" ${sel?'selected':''}>${l.lote} · vence ${l.fecha_vencimiento||'—'} · disp ${l.cantidad_actual}</option>`;
                }).join('');
                onLoteChange();
                fefoTag.style.display = (autoFefo && !prefill.inventario_lote_id) ? 'inline-block' : 'none';
            }

            function onLoteChange() {
                const lote = LOTES.find(l => String(l.id) === String(loteSel.value));
                if (!lote) return;
                venc.value = lote.fecha_vencimiento || '—';
                sem.className = 'semaforo ' + semaforoVenc(lote.fecha_vencimiento);
                if (!costo.value) costo.value = lote.costo_unitario || 0;
                if (!uniSel.value && lote.unidad_medida_id) uniSel.value = lote.unidad_medida_id;
                refreshStockPill();
                recalc();
            }

            function refreshStockPill() {
                const lote = LOTES.find(l => String(l.id) === String(loteSel.value));
                if (!lote) { stock.className = 'stock-pill sp-critico'; stock.textContent = '—'; return; }
                const [cls, txt] = pillStock(lote.cantidad_actual, cant.value);
                stock.className = 'stock-pill ' + cls;
                stock.textContent = lote.cantidad_actual + ' · ' + txt;
            }

            function recalc() {
                const c = parseFloat(cant.value) || 0;
                const p = parseFloat(costo.value) || 0;
                tr.querySelector('[data-role="subtotal"]').textContent = (c*p).toFixed(2);
                refreshStockPill();
                updateTotals();
            }

            medSel.addEventListener('change', refreshPresentaciones);
            preSel.addEventListener('change', () => refreshLotes(true));
            loteSel.addEventListener('change', () => { fefoTag.style.display='none'; onLoteChange(); });
            [cant, costo].forEach(el => el.addEventListener('input', recalc));

            refreshPresentaciones();
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

        if (EXISTING.length) {
            EXISTING.forEach(d => addRow(d));
        } else {
            addRow();
        }
    </script>
</x-app-layout>
