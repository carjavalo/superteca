<x-app-layout>
<style>
    :root { --inst:#2e3a75; --teal:#14b8a6; }

    .split-layout { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:1.2rem; align-items:start; }
    @media (max-width:1150px) { .split-layout { grid-template-columns:1fr; } }

    .col-header { color:#fff; padding:1rem 1.4rem; border-radius:12px; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .col-header h1 { margin:0; font-size:1.15rem; }
    .col-header .sub { font-size:.78rem; opacity:.9; }
    .col-header.form { background:linear-gradient(135deg, var(--teal) 0%, var(--inst) 100%); }
    .col-header.list { background:linear-gradient(135deg, var(--inst) 0%, #3b4a96 100%); }
    .col-header .count { background:rgba(255,255,255,.2); padding:.25rem .7rem; border-radius:20px; font-size:.8rem; font-weight:700; white-space:nowrap; }

    .card-box { background:#fff; padding:1.1rem 1.2rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .card-box h2 { margin:0 0 .9rem 0; font-size:.92rem; color:var(--inst); border-bottom:2px solid #f1f5f9; padding-bottom:.5rem; }
    .grid { display:grid; gap:.7rem; }
    .grid-2 { grid-template-columns:repeat(2,1fr); }
    @media (max-width:480px) { .grid-2 { grid-template-columns:1fr; } }
    label { font-size:.72rem; color:#475569; font-weight:600; display:block; margin-bottom:.25rem; }
    input,select,textarea { width:100%; padding:.55rem .7rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; box-sizing:border-box; outline:none; font-family:inherit; }
    input:focus,select:focus,textarea:focus { border-color:var(--teal); box-shadow:0 0 0 3px rgba(20,184,166,.12); }

    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:var(--teal); color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }

    /* ---- Datatable lateral ---- */
    .list-tools { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:.6rem; }
    .list-tools .search { flex:1 1 140px; min-width:110px; padding:.5rem .6rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.82rem; box-sizing:border-box; }
    .list-tools select { flex:0 1 145px; max-width:155px; padding:.5rem .45rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.8rem; box-sizing:border-box; }
    .list-tools .search:focus, .list-tools select:focus { border-color:var(--teal); box-shadow:0 0 0 3px rgba(20,184,166,.12); outline:none; }

    .list-actions { display:flex; gap:.5rem; justify-content:flex-end; align-items:center; margin-bottom:.8rem; flex-wrap:wrap; }
    .btn-sm { padding:.45rem .85rem; border-radius:8px; border:none; cursor:pointer; font-size:.78rem; font-weight:600; display:inline-flex; align-items:center; gap:.35rem; }
    .btn-print { background:#e2e8f0; color:#1e293b; }
    .btn-print:hover { background:#cbd5e1; }
    .btn-excel { background:#16a34a; color:#fff; }
    .btn-excel:hover { background:#15803d; }

    .tabla-wrap { max-height:64vh; overflow-y:auto; border:1px solid #f1f5f9; border-radius:10px; }
    table.dt { width:100%; border-collapse:collapse; }
    table.dt th { position:sticky; top:0; background:#f1f5f9; padding:.6rem .7rem; text-align:left; font-size:.68rem; color:#475569; text-transform:uppercase; z-index:1; }
    table.dt td { padding:.55rem .7rem; font-size:.82rem; border-top:1px solid #f1f5f9; vertical-align:middle; }
    table.dt tr:hover td { background:#f8fafc; cursor:pointer; }
    table.dt td small { color:#94a3b8; font-size:.72rem; }

    .badge { display:inline-block; padding:.16rem .55rem; border-radius:10px; font-size:.68rem; font-weight:600; white-space:nowrap; }
    .b-act { background:#dcfce7; color:#166534; }
    .b-egr { background:#e0e7ff; color:#3730a3; }
    .b-fal { background:#fee2e2; color:#991b1b; }
    .cama-tag { background:#f1f5f9; color:#475569; padding:.1rem .45rem; border-radius:6px; font-family:monospace; font-size:.72rem; }
    .empty-row td { text-align:center; padding:2rem; color:#94a3b8; }
</style>

@if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">
        <ul style="margin:0 0 0 1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="split-layout">

    {{-- ===================== IZQUIERDA · FORMULARIO ===================== --}}
    <form method="POST" action="{{ route('admin.dispensacion.pacientes.store') }}">
        @csrf
        <div class="col-header form">
            <h1>Registrar Paciente</h1>
            <span class="sub">Formulario de ingreso</span>
        </div>

        <div class="card-box">
            <h2>Identificación</h2>
            <div class="grid grid-2">
                <div>
                    <label>Tipo doc. *</label>
                    <select name="tipo_documento" required>
                        @foreach(['CC'=>'Cédula','TI'=>'T. Identidad','RC'=>'R. Civil','CE'=>'Extranjería','PA'=>'Pasaporte'] as $k=>$v)
                            <option value="{{ $k }}" {{ old('tipo_documento','CC')==$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Documento *</label><input type="text" name="documento" required value="{{ old('documento') }}"></div>
                <div><label>Nombres *</label><input type="text" name="nombres" required value="{{ old('nombres') }}"></div>
                <div><label>Apellidos *</label><input type="text" name="apellidos" required value="{{ old('apellidos') }}"></div>
            </div>
        </div>

        <div class="card-box">
            <h2>Datos clínicos</h2>
            <div class="grid grid-2">
                <div><label>Fecha nacimiento</label><input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"></div>
                <div><label>Sexo</label>
                    <select name="sexo">
                        <option value="">—</option>
                        <option value="M" {{ old('sexo')=='M'?'selected':'' }}>Masculino</option>
                        <option value="F" {{ old('sexo')=='F'?'selected':'' }}>Femenino</option>
                        <option value="O" {{ old('sexo')=='O'?'selected':'' }}>Otro</option>
                    </select>
                </div>
                <div><label>Peso (kg)</label><input type="number" step="0.01" name="peso" value="{{ old('peso') }}"></div>
                <div><label>Talla (cm)</label><input type="number" step="0.01" name="talla" value="{{ old('talla') }}"></div>
                <div><label>EPS</label>
                    <select name="eps">
                        <option value="">—</option>
                        @foreach($eps as $e)
                            <option value="{{ $e->Detalle }}" {{ old('eps')==$e->Detalle?'selected':'' }}>{{ $e->Detalle }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Servicio</label>
                    <select name="servicio_id">
                        <option value="">—</option>
                        @foreach($servicios as $s)
                            <option value="{{ $s->id }}" {{ old('servicio_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Cama</label><input type="text" name="cama" value="{{ old('cama') }}"></div>
                <div><label>Estado clínico *</label>
                    <select name="estado_clinico" required>
                        @foreach(\App\Models\Paciente::ESTADOS_CLINICOS as $k=>$v)
                            <option value="{{ $k }}" {{ old('estado_clinico','ACTIVO')==$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Fecha ingreso</label><input type="datetime-local" name="fecha_ingreso" value="{{ old('fecha_ingreso', now()->format('Y-m-d\TH:i')) }}"></div>
            </div>
        </div>

        <div class="card-box">
            <h2>Contacto</h2>
            <div class="grid grid-2">
                <div><label>Teléfono</label><input type="text" name="telefono" value="{{ old('telefono') }}"></div>
                <div><label>Correo</label><input type="email" name="correo" value="{{ old('correo') }}"></div>
            </div>
            <div style="margin-top:.7rem"><label>Dirección</label><input type="text" name="direccion" value="{{ old('direccion') }}"></div>
            <div style="margin-top:.7rem">
                <label>Observaciones</label>
                <textarea name="observaciones" rows="2">{{ old('observaciones') }}</textarea>
            </div>
        </div>

        <div style="display:flex;gap:.7rem;justify-content:flex-end">
            <a href="{{ route('admin.dispensacion.pacientes.index') }}" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-primary">Registrar</button>
        </div>
    </form>

    {{-- ===================== DERECHA · GRILLA DE CONSULTA ===================== --}}
    <div>
        <div class="col-header list">
            <h1>Pacientes registrados</h1>
            <span class="count"><span id="dt-count">{{ $pacientes->count() }}</span> de {{ $pacientes->count() }}</span>
        </div>

        <div class="card-box">
            <div class="list-tools">
                <input type="text" id="dt-doc" class="search" placeholder="🔍 Identificación...">
                <input type="text" id="dt-nombre" class="search" placeholder="🔍 Nombre...">
                <select id="dt-estado">
                    <option value="">Todos los estados</option>
                    @foreach(\App\Models\Paciente::ESTADOS_CLINICOS as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
                <select id="dt-servicio">
                    <option value="">Todos los servicios</option>
                    @foreach($servicios as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="list-actions">
                <button type="button" class="btn-sm btn-print" id="btn-print">🖨 Imprimir</button>
                <button type="button" class="btn-sm btn-excel" id="btn-excel">📊 Exportar Excel</button>
            </div>

            <div class="tabla-wrap">
                <table class="dt">
                    <thead>
                        <tr>
                            <th>Identificación</th>
                            <th>Paciente</th>
                            <th>Servicio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="dt-body">
                        @forelse($pacientes as $p)
                            @php
                                $bclass = ['ACTIVO'=>'b-act','EGRESADO'=>'b-egr','FALLECIDO'=>'b-fal'][$p->estado_clinico ?? 'ACTIVO'] ?? 'b-act';
                                $estLabel = \App\Models\Paciente::ESTADOS_CLINICOS[$p->estado_clinico ?? 'ACTIVO'] ?? 'Activo';
                                $nombreFull = trim($p->apellidos.' '.$p->nombres);
                            @endphp
                            <tr class="dt-row"
                                data-doc="{{ $p->documento }}"
                                data-nombre="{{ $nombreFull }}"
                                data-estado="{{ $p->estado_clinico ?? 'ACTIVO' }}"
                                data-servicio="{{ $p->servicio_id ?? '' }}"
                                data-eps="{{ $p->eps }}"
                                data-servnom="{{ $p->servicio->nombre ?? '' }}"
                                data-cama="{{ $p->cama }}"
                                data-estlabel="{{ $estLabel }}"
                                data-tipodoc="{{ $p->tipo_documento }}"
                                onclick="window.location='{{ route('admin.dispensacion.pacientes.show', $p) }}'">
                                <td style="font-family:monospace">{{ $p->documento }}</td>
                                <td>
                                    <strong>{{ $nombreFull }}</strong>
                                    @if($p->cama)<br><small>Cama <span class="cama-tag">{{ $p->cama }}</span></small>@endif
                                </td>
                                <td>{{ $p->servicio->nombre ?? '—' }}</td>
                                <td><span class="badge {{ $bclass }}">{{ $estLabel }}</span></td>
                            </tr>
                        @empty
                            <tr class="empty-row"><td colspan="4">Aún no hay pacientes registrados.</td></tr>
                        @endforelse
                        <tr id="dt-noresults" style="display:none"><td colspan="4" style="text-align:center;padding:2rem;color:#94a3b8">Sin coincidencias con la búsqueda.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const fDoc    = document.getElementById('dt-doc');
        const fNom    = document.getElementById('dt-nombre');
        const fEstado = document.getElementById('dt-estado');
        const fServ   = document.getElementById('dt-servicio');
        const rows    = Array.from(document.querySelectorAll('.dt-row'));
        const noRes   = document.getElementById('dt-noresults');
        const countEl = document.getElementById('dt-count');

        // Normaliza: minúsculas y sin acentos (búsqueda flexible).
        const norm = s => (s || '').toString().toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');

        function visibles() { return rows.filter(r => r.style.display !== 'none'); }

        function applyFilters() {
            const qd  = norm(fDoc.value.trim());
            const qn  = norm(fNom.value.trim());
            const est = fEstado.value;
            const srv = fServ.value;
            let visible = 0;

            rows.forEach(tr => {
                const okD = !qd  || norm(tr.dataset.doc).includes(qd);
                const okN = !qn  || norm(tr.dataset.nombre).includes(qn);
                const okE = !est || tr.dataset.estado === est;
                const okS = !srv || tr.dataset.servicio === srv;
                const show = okD && okN && okE && okS;
                tr.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (countEl) countEl.textContent = visible;
            if (noRes) noRes.style.display = (visible === 0 && rows.length > 0) ? '' : 'none';
        }

        [fDoc, fNom, fEstado, fServ].forEach(el => {
            if (!el) return;
            el.addEventListener('input', applyFilters);
            el.addEventListener('change', applyFilters);
        });

        // ---- Datos para imprimir / exportar (sólo filas visibles) ----
        const COLS = ['Identificación', 'Tipo doc.', 'Apellidos y Nombres', 'EPS', 'Servicio', 'Cama', 'Estado'];
        function rowData(tr) {
            return [
                tr.dataset.doc || '',
                tr.dataset.tipodoc || '',
                tr.dataset.nombre || '',
                tr.dataset.eps || '',
                tr.dataset.servnom || '',
                tr.dataset.cama || '',
                tr.dataset.estlabel || ''
            ];
        }
        function esc(s) { return (s || '').toString().replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
        function stamp() {
            const d = new Date();
            const p = n => String(n).padStart(2, '0');
            return p(d.getDate())+'/'+p(d.getMonth()+1)+'/'+d.getFullYear()+' '+p(d.getHours())+':'+p(d.getMinutes());
        }

        function buildTable(forExcel) {
            let h = '<table border="1" cellspacing="0" cellpadding="4"><thead><tr>';
            COLS.forEach(c => h += '<th>' + esc(c) + '</th>');
            h += '</tr></thead><tbody>';
            visibles().forEach(tr => {
                const d = rowData(tr);
                h += '<tr>';
                d.forEach((v, i) => {
                    // Forzar la identificación como texto en Excel (evita notación científica).
                    const style = (forExcel && i === 0) ? ' style="mso-number-format:\'\\@\'"' : '';
                    h += '<td' + style + '>' + esc(v) + '</td>';
                });
                h += '</tr>';
            });
            h += '</tbody></table>';
            return h;
        }

        // ---- Imprimir ----
        document.getElementById('btn-print').addEventListener('click', function () {
            const n = visibles().length;
            if (n === 0) { alert('No hay resultados para imprimir.'); return; }
            const w = window.open('', '_blank');
            w.document.write(
                '<html><head><title>Pacientes</title><meta charset="UTF-8"><style>' +
                'body{font-family:Arial,Helvetica,sans-serif;color:#1e293b;padding:22px}' +
                'h2{color:#2e3a75;margin:0 0 2px}.meta{color:#64748b;font-size:12px;margin-bottom:14px}' +
                'table{width:100%;border-collapse:collapse}th{background:#2e3a75;color:#fff;text-align:left;padding:6px 8px;font-size:11px;border:1px solid #2e3a75}' +
                'td{padding:5px 8px;border:1px solid #e2e8f0;font-size:11px}tr:nth-child(even) td{background:#f8fafc}' +
                '</style></head><body>' +
                '<h2>Listado de Pacientes</h2><div class="meta">Resultados: ' + n + ' &middot; Generado: ' + stamp() + '</div>' +
                buildTable(false) +
                '</body></html>'
            );
            w.document.close(); w.focus();
            setTimeout(() => { w.print(); }, 250);
        });

        // ---- Exportar a Excel ----
        document.getElementById('btn-excel').addEventListener('click', function () {
            const n = visibles().length;
            if (n === 0) { alert('No hay resultados para exportar.'); return; }
            const html =
                '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' +
                '<head><meta charset="UTF-8"></head><body>' + buildTable(true) + '</body></html>';
            const blob = new Blob(['﻿', html], { type: 'application/vnd.ms-excel;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            const d = new Date(), p = x => String(x).padStart(2, '0');
            a.href = url;
            a.download = 'pacientes_' + d.getFullYear() + p(d.getMonth()+1) + p(d.getDate()) + '_' + p(d.getHours()) + p(d.getMinutes()) + '.xls';
            document.body.appendChild(a); a.click(); document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    })();
</script>
</x-app-layout>
