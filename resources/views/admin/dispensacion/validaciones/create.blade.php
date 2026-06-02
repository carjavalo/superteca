<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #6366f1 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; }
    .page-header h1 { margin:0; font-size:1.3rem; }
    .card-box { background:#fff; padding:1.2rem 1.4rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .grid { display:grid; gap:.7rem; }
    .grid-2 { grid-template-columns:repeat(2,1fr); }
    .grid-3 { grid-template-columns:repeat(3,1fr); }
    .grid-4 { grid-template-columns:repeat(4,1fr); }
    @media (max-width:800px) { .grid-2,.grid-3,.grid-4 { grid-template-columns:1fr; } }
    label { font-size:.72rem; color:#475569; font-weight:600; display:block; margin-bottom:.25rem; }
    input,select,textarea { width:100%; padding:.55rem .7rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; box-sizing:border-box; }
    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; display:inline-block; }
    .btn-primary { background:#6366f1; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-sm { padding:.3rem .65rem; font-size:.75rem; }
    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:.55rem .7rem; font-size:.7rem; text-align:left; color:#475569; text-transform:uppercase; }
    td { padding:.5rem .7rem; font-size:.82rem; border-top:1px solid #f1f5f9; }
</style>

<div class="page-header">
    <h1>Nueva Validación Farmacéutica</h1>
</div>

@if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">
        <ul style="margin:0 0 0 1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.dispensacion.validaciones.store') }}">
    @csrf
    <div class="card-box">
        <h3 style="margin:0 0 .8rem;font-size:1rem">Origen</h3>
        <div class="grid grid-4">
            <div><label>Tipo de validación *</label>
                <select name="tipo_validacion" required>
                    @foreach(\App\Models\Validacion::TIPOS as $k=>$v)
                        <option value="{{ $k }}" {{ old('tipo_validacion','PRESCRIPCION')==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div><label>Prioridad *</label>
                <select name="prioridad" required>
                    @foreach(\App\Models\Validacion::PRIORIDADES as $k=>$v)
                        <option value="{{ $k }}" {{ old('prioridad','NORMAL')==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div><label>Paciente</label>
                <select name="paciente_id" id="pacSel">
                    <option value="">—</option>
                    @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" {{ old('paciente_id')==$p->id?'selected':'' }}>{{ $p->documento }} · {{ trim($p->apellidos.' '.$p->nombres) }}</option>
                    @endforeach
                </select>
            </div>
            <div><label>Prescripción origen</label>
                <select name="prescripcion_id" id="rxSel">
                    <option value="">—</option>
                    @foreach($prescripciones as $r)
                        <option value="{{ $r->id }}" data-paciente="{{ $r->paciente_id }}" {{ ($preselect ?? old('prescripcion_id'))==$r->id?'selected':'' }}>
                            {{ $r->codigo }} · {{ $r->paciente?->apellidos }} · {{ $r->detalles->count() }} ítems
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="margin-top:.7rem"><label>Observaciones</label><textarea name="observaciones" rows="2">{{ old('observaciones') }}</textarea></div>
    </div>

    <div class="card-box">
        <h3 style="margin:0 0 .8rem;font-size:1rem">Medicamentos a validar</h3>
        <table id="detTable">
            <thead>
                <tr>
                    <th style="width:25%">Medicamento *</th>
                    <th>Dosis prescrita</th>
                    <th>Dosis recomendada</th>
                    <th>Unidad</th>
                    <th>Vía</th>
                    <th>Observaciones</th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <button type="button" class="btn btn-secondary btn-sm" onclick="addRow()" style="margin-top:.6rem">+ Agregar medicamento</button>
    </div>

    <div style="display:flex;gap:.7rem;justify-content:flex-end">
        <a href="{{ route('admin.dispensacion.validaciones.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Crear y Analizar</button>
    </div>
</form>

@php
    $medsData = $medicamentos->map(fn($m) => ['id' => $m->id, 'nombre' => $m->nombre]);
    $unsData = $unidades->map(fn($u) => ['id' => $u->id, 'nombre' => $u->nombre]);
    $viasData = $vias->map(fn($v) => ['id' => $v->id, 'nombre' => $v->nombre]);
    $rxDataArr = $prescripciones->map(function($r) {
        return [
            'id' => $r->id,
            'detalles' => $r->detalles->map(function($d) {
                return [
                    'med' => $d->medicamento_id,
                    'dosis' => $d->dosis,
                    'um' => $d->unidad_medida_id,
                    'via' => $d->via_administracion_id
                ];
            })->values()->all()
        ];
    })->values()->all();
@endphp
<script>
    const meds = @json($medsData);
    const uns  = @json($unsData);
    const vias = @json($viasData);
    const rxData = @json($rxDataArr);
    let idx = 0;
    function addRow(pre = {}) {
        const i = idx++;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><select name="detalles[${i}][medicamento_id]" required><option value="">—</option>${meds.map(m=>`<option value="${m.id}" ${pre.med==m.id?'selected':''}>${m.nombre}</option>`).join('')}</select></td>
            <td><input type="number" step="0.0001" name="detalles[${i}][dosis_prescrita]" value="${pre.dosis ?? ''}"></td>
            <td><input type="number" step="0.0001" name="detalles[${i}][dosis_recomendada]"></td>
            <td><select name="detalles[${i}][unidad_medida_id]"><option value="">—</option>${uns.map(u=>`<option value="${u.id}" ${pre.um==u.id?'selected':''}>${u.nombre}</option>`).join('')}</select></td>
            <td><select name="detalles[${i}][via_administracion_id]"><option value="">—</option>${vias.map(v=>`<option value="${v.id}" ${pre.via==v.id?'selected':''}>${v.nombre}</option>`).join('')}</select></td>
            <td><input type="text" name="detalles[${i}][observaciones]"></td>
            <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">×</button></td>`;
        document.querySelector('#detTable tbody').appendChild(tr);
    }
    function loadRxDetalles() {
        const sel = document.querySelector('#rxSel');
        const id  = parseInt(sel.value || 0);
        if (!id) return;
        const opt = sel.options[sel.selectedIndex];
        const pacId = opt.dataset.paciente;
        if (pacId) document.querySelector('#pacSel').value = pacId;
        const rx = rxData.find(r => r.id === id);
        if (!rx) return;
        document.querySelector('#detTable tbody').innerHTML = '';
        idx = 0;
        rx.detalles.forEach(d => addRow(d));
    }
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelector('#rxSel').addEventListener('change', loadRxDetalles);
        if (document.querySelector('#rxSel').value) loadRxDetalles();
        else addRow();
    });
</script>
</x-app-layout>
