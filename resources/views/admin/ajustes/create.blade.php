<x-app-layout>
    <x-slot name="header"><h2>Nuevo ajuste de inventario</h2></x-slot>

    <style>
        :root { --inst:#2e3a75; --ajuste:#7c3aed; }
        .st-alert.error { background:#fee2e2; color:#991b1b; border-left:5px solid #ef4444; padding:14px 18px; border-radius:10px; margin-bottom:20px; }
        .card { background:#fff; border-radius:14px; padding:22px 24px; box-shadow:0 4px 12px rgba(0,0,0,.05); margin-bottom:18px; }
        .card h3 { margin:0 0 14px; color:var(--inst); font-size:1.05rem; font-weight:800; border-bottom:2px solid #f3f4f6; padding-bottom:8px; }
        .form-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:14px; }
        label { font-size:.78rem; font-weight:700; color:#374151; text-transform:uppercase; letter-spacing:.04em; display:block; margin-bottom:5px; }
        input, select, textarea { width:100%; padding:9px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.9rem; outline:none; }
        input:focus, select:focus, textarea:focus { border-color:var(--ajuste); }
        .row-motivo { display:flex; gap:8px; align-items:end; }
        .row-motivo > div { flex:1; }
        .btn-add { background:#ecfeff; color:#0e7490; border:1.5px dashed #06b6d4; padding:9px 13px; border-radius:8px; font-weight:700; cursor:pointer; white-space:nowrap; }
        .btn-add:hover { background:#06b6d4; color:#fff; }

        table { width:100%; border-collapse:collapse; }
        th { background:#f9fafb; color:#374151; font-size:.74rem; padding:10px; text-align:left; text-transform:uppercase; }
        td { padding:8px 10px; border-top:1px solid #f3f4f6; vertical-align:middle; }
        td input, td select { padding:7px 9px; font-size:.85rem; }
        .diff-pos { color:#059669; font-weight:700; }
        .diff-neg { color:#dc2626; font-weight:700; }
        .diff-zero{ color:#6b7280; font-weight:600; }

        .footer-actions { display:flex; gap:10px; justify-content:flex-end; margin-top:22px; }
        .btn-primary { background:var(--ajuste); color:#fff; border:none; padding:11px 22px; border-radius:8px; font-weight:700; cursor:pointer; }
        .btn-primary:hover { background:#6b21a8; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:11px 22px; border-radius:8px; font-weight:600; text-decoration:none; }

        .totals { display:flex; gap:18px; justify-content:flex-end; margin-top:14px; padding:12px 18px; background:#faf5ff; border-radius:10px; border:1px solid #e9d5ff; }
        .totals strong { color:var(--ajuste); font-size:1.05rem; }

        /* Modal motivo */
        .modal-bg { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center; }
        .modal-bg.show { display:flex; }
        .modal { background:#fff; border-radius:14px; padding:24px; width:420px; max-width:92%; box-shadow:0 20px 50px rgba(0,0,0,.25); }
        .modal h4 { margin:0 0 14px; color:var(--ajuste); font-size:1.1rem; font-weight:800; }
        .modal label { margin-top:10px; }
    </style>

    @if($errors->any())
        <div class="st-alert error">
            <strong>Revisa los siguientes campos:</strong>
            <ul style="margin:6px 0 0 18px;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif
    @if(session('error')) <div class="st-alert error">{{ session('error') }}</div> @endif

    <form method="POST" action="{{ route('admin.ajustes.store') }}" id="formAjuste">
        @csrf

        <div class="card">
            <h3>📋 Encabezado del ajuste</h3>
            <div class="form-grid">
                <div>
                    <label>Tipo de ajuste *</label>
                    <select name="tipo_ajuste" id="tipoAjuste" required>
                        <option value="">Seleccione…</option>
                        @foreach(\App\Models\AjusteInventario::TIPOS as $k=>$v)
                            <option value="{{ $k }}" {{ old('tipo_ajuste')==$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Fecha del ajuste *</label>
                    <input type="datetime-local" name="fecha_ajuste" value="{{ old('fecha_ajuste', now()->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div style="grid-column: span 2;">
                    <label>Motivo *</label>
                    <div class="row-motivo">
                        <div>
                            <select name="motivo_ajuste_id" id="motivoSelect" required>
                                <option value="">Seleccione un motivo…</option>
                                @foreach($motivos as $m)
                                    <option value="{{ $m->id }}" data-tipo="{{ $m->tipo }}" {{ old('motivo_ajuste_id')==$m->id?'selected':'' }}>
                                        {{ $m->codigo }} · {{ $m->nombre }} ({{ $m->tipo }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn-add" onclick="abrirModalMotivo()">+ Nuevo motivo</button>
                    </div>
                </div>
                <div style="grid-column: 1 / -1;">
                    <label>Observaciones generales</label>
                    <textarea name="observaciones" rows="2" placeholder="Comentario, soporte, hallazgo de auditoría…">{{ old('observaciones') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>📦 Detalle de lotes ajustados</h3>
            <table id="detalleTable">
                <thead>
                    <tr>
                        <th style="width:32%;">Lote / Medicamento</th>
                        <th style="width:13%;">Sistema</th>
                        <th style="width:13%;">Físico</th>
                        <th style="width:12%;">Diferencia</th>
                        <th style="width:13%;">Costo unit.</th>
                        <th style="width:12%;">Valor</th>
                        <th style="width:5%;"></th>
                    </tr>
                </thead>
                <tbody id="detalleBody"></tbody>
            </table>

            <div style="margin-top:12px;">
                <button type="button" class="btn-add" onclick="agregarFila()">+ Agregar lote</button>
            </div>

            <div class="totals">
                <span>Total ítems: <strong id="totalItems">0</strong></span>
                <span>Valor del ajuste: <strong>$ <span id="totalValor">0</span></strong></span>
            </div>
        </div>

        <div class="footer-actions">
            <a href="{{ route('admin.ajustes.index') }}" class="btn-outline">Cancelar</a>
            <button type="submit" class="btn-primary">💾 Guardar ajuste (Borrador)</button>
        </div>
    </form>

    <!-- Modal Nuevo Motivo -->
    <div class="modal-bg" id="modalMotivo">
        <div class="modal">
            <h4>✨ Crear nuevo motivo</h4>
            <label>Nombre *</label>
            <input type="text" id="m_nombre" placeholder="Ej. Robo identificado en auditoría">
            <label>Tipo aplicable *</label>
            <select id="m_tipo">
                <option value="AMBOS">Ambos</option>
                <option value="POSITIVO">Solo Positivo</option>
                <option value="NEGATIVO">Solo Negativo</option>
            </select>
            <div style="display:flex; gap:14px; margin-top:14px;">
                <label style="display:flex; align-items:center; gap:6px; text-transform:none;">
                    <input type="checkbox" id="m_obs" checked style="width:auto;"> Requiere observación
                </label>
                <label style="display:flex; align-items:center; gap:6px; text-transform:none;">
                    <input type="checkbox" id="m_apr" checked style="width:auto;"> Requiere aprobación
                </label>
            </div>
            <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:18px;">
                <button type="button" class="btn-outline" onclick="cerrarModalMotivo()">Cancelar</button>
                <button type="button" class="btn-primary" onclick="guardarMotivo()">Crear</button>
            </div>
        </div>
    </div>

    <script>
        @php
            $lotesData = $lotes->map(function ($l) {
                return [
                    'id'          => $l->id,
                    'lote'        => $l->lote,
                    'medicamento' => $l->medicamento->nombre ?? 'Sin nombre',
                    'cantidad'    => (float) $l->cantidad_actual,
                    'costo'       => (float) ($l->costo_unitario ?? 0),
                    'venc'        => $l->fecha_vencimiento,
                ];
            })->values();
        @endphp
        const LOTES = {!! $lotesData->toJson() !!};

        let filaIdx = 0;

        function fmt(n){ return Number(n||0).toLocaleString('es-CO',{maximumFractionDigits:2}); }

        function recalcular() {
            let totalValor = 0, totalItems = 0;
            document.querySelectorAll('#detalleBody tr').forEach(tr=>{
                const sis = parseFloat(tr.querySelector('.f-sistema').value) || 0;
                const fis = parseFloat(tr.querySelector('.f-fisico').value) || 0;
                const cos = parseFloat(tr.querySelector('.f-costo').value) || 0;
                const diff = fis - sis;
                const valor = Math.abs(diff) * cos;
                const cell = tr.querySelector('.f-diff');
                cell.textContent = (diff>0?'+':'') + fmt(diff);
                cell.className = 'f-diff ' + (diff>0?'diff-pos':(diff<0?'diff-neg':'diff-zero'));
                tr.querySelector('.f-valor').textContent = '$ ' + fmt(valor);
                totalValor += valor;
                totalItems++;
            });
            document.getElementById('totalValor').textContent = fmt(totalValor);
            document.getElementById('totalItems').textContent = totalItems;
        }

        function agregarFila() {
            const idx = filaIdx++;
            const opts = LOTES.map(l=>`<option value="${l.id}" data-cant="${l.cantidad}" data-costo="${l.costo}">${l.medicamento} · Lote ${l.lote} (Stock: ${l.cantidad})</option>`).join('');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <select name="detalles[${idx}][inventario_lote_id]" class="f-lote" required onchange="seleccionarLote(this)">
                        <option value="">Seleccione lote…</option>${opts}
                    </select>
                    <input type="text" name="detalles[${idx}][observacion]" placeholder="Observación del ítem…" style="margin-top:4px;">
                </td>
                <td><input type="number" step="0.01" class="f-sistema" readonly></td>
                <td><input type="number" step="0.01" name="detalles[${idx}][stock_fisico]" class="f-fisico" required oninput="recalcular()"></td>
                <td class="f-diff diff-zero">0</td>
                <td><input type="number" step="0.01" class="f-costo" readonly></td>
                <td class="f-valor">$ 0</td>
                <td><button type="button" class="btn-add" style="background:#fee2e2;color:#dc2626;border-color:#fca5a5;padding:5px 9px;" onclick="this.closest('tr').remove();recalcular();">×</button></td>
            `;
            document.getElementById('detalleBody').appendChild(tr);
        }

        function seleccionarLote(sel) {
            const opt = sel.options[sel.selectedIndex];
            const tr = sel.closest('tr');
            tr.querySelector('.f-sistema').value = opt.dataset.cant || 0;
            tr.querySelector('.f-costo').value   = opt.dataset.costo || 0;
            recalcular();
        }

        function abrirModalMotivo(){ document.getElementById('modalMotivo').classList.add('show'); }
        function cerrarModalMotivo(){ document.getElementById('modalMotivo').classList.remove('show'); }

        async function guardarMotivo() {
            const nombre = document.getElementById('m_nombre').value.trim();
            const tipo   = document.getElementById('m_tipo').value;
            const obs    = document.getElementById('m_obs').checked;
            const apr    = document.getElementById('m_apr').checked;
            if (!nombre) { alert('Escribe un nombre.'); return; }

            const resp = await fetch('{{ route('admin.ajustes.motivos.store') }}', {
                method:'POST',
                headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}','Accept':'application/json'},
                body: JSON.stringify({ nombre, tipo, requiere_observacion: obs?1:0, requiere_aprobacion: apr?1:0 })
            });
            const data = await resp.json();
            if (!resp.ok || !data.success) {
                alert('Error: ' + (data.message || 'No se pudo crear.'));
                return;
            }
            const sel = document.getElementById('motivoSelect');
            const opt = new Option(`${data.motivo.codigo} · ${data.motivo.nombre} (${data.motivo.tipo})`, data.motivo.id, true, true);
            opt.dataset.tipo = data.motivo.tipo;
            sel.add(opt);
            cerrarModalMotivo();
            document.getElementById('m_nombre').value = '';
        }

        // Iniciar con una fila
        agregarFila();
    </script>
</x-app-layout>
