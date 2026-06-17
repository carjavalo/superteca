<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #6d28d9 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.5rem; }
    .page-header h1 { font-size:1.4rem; margin:0; }
    .page-header p { opacity:.85; font-size:.82rem; margin:0; }
    .card { background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:1.2rem; }
    .card h3 { margin:0 0 1rem 0; font-size:1rem; color:var(--inst); border-left:4px solid #6d28d9; padding-left:.6rem; }
    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
    .field { display:flex; flex-direction:column; gap:.3rem; }
    .field label { font-size:.78rem; color:#475569; font-weight:600; }
    .field input, .field select, .field textarea { border:1.5px solid #e2e8f0; border-radius:7px; padding:.5rem .75rem; font-size:.9rem; }
    .row-detalle { display:grid; grid-template-columns:3fr 1fr 1.5fr 2fr auto; gap:.5rem; align-items:end; padding:.7rem; background:#f8fafc; border-radius:8px; margin-bottom:.5rem; }
    .row-detalle .field label { font-size:.7rem; }
    .btn-del { background:#ef4444; color:#fff; border:none; border-radius:6px; padding:.4rem .7rem; cursor:pointer; height:35px; }
    .btn-add { background:#22c55e; color:#fff; border:none; border-radius:7px; padding:.5rem 1rem; cursor:pointer; font-size:.85rem; }
    .actions { display:flex; gap:.7rem; justify-content:flex-end; }
    .btn { padding:.6rem 1.4rem; border-radius:8px; border:none; font-size:.9rem; font-weight:600; cursor:pointer; text-decoration:none; }
    .btn-primary { background:var(--inst); color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }
    .alert-error { background:#fee2e2; color:#991b1b; padding:.7rem 1rem; border-radius:8px; margin-bottom:1rem; }
    @media (max-width:900px) { .grid-2,.grid-3 { grid-template-columns:1fr; } .row-detalle { grid-template-columns:1fr; } }
</style>

<div class="page-header">
    <h1>&#9879; Programar Nueva Mezcla</h1>
    <p>Define los componentes que serán elaborados en la central de mezclas</p>
</div>

@if($errors->any())<div class="alert-error">@foreach($errors->all() as $e)<div>&#9888; {{ $e }}</div>@endforeach</div>@endif

<form method="POST" action="{{ route('admin.mezclas.store') }}">
    @csrf

    <div class="card">
        <h3>1. Datos generales</h3>
        <div class="grid-3">
            <div class="field">
                <label>Fórmula base (opcional)</label>
                <select name="formula_id" id="formula_id" onchange="cargarFormula()">
                    <option value="">— Mezcla manual —</option>
                    @foreach($formulas as $f)
                        <option value="{{ $f->id }}" data-tipo="{{ $f->tipo_formula }}" data-volumen="{{ $f->volumen_final }}" data-detalles="{{ json_encode($f->detalles->map(fn($d) => ['medicamento_id' => $d->medicamento_id, 'dosis' => $d->dosis, 'unidad' => $d->unidad_medida_id, 'nombre' => $d->medicamento->nombre ?? ''])) }}">{{ $f->codigo }} — {{ $f->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Tipo de mezcla *</label>
                <select name="tipo_mezcla" id="tipo_mezcla" required>
                    @foreach(\App\Models\Mezcla::TIPOS as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Fecha programada *</label>
                <input type="datetime-local" name="fecha_programada" required value="{{ now()->format('Y-m-d\TH:i') }}">
            </div>
            <div class="field">
                <label>Volumen programado</label>
                <input type="number" step="0.01" name="volumen_programado" id="volumen_programado">
            </div>
            <div class="field">
                <label>Unidad de volumen</label>
                <select name="unidad_volumen_id">
                    <option value="">—</option>
                    @foreach($unidades as $u)<option value="{{ $u->id }}">{{ $u->nombre }}</option>@endforeach
                </select>
            </div>
            <div class="field">
                <label>Cantidad de preparaciones *</label>
                <input type="number" name="cantidad_preparaciones" required min="1" value="1">
            </div>
        </div>
        <div class="field" style="margin-top:1rem">
            <label>Observaciones</label>
            <textarea name="observaciones" rows="2"></textarea>
        </div>
    </div>

    <div class="card">
        <h3>2. Componentes Programados</h3>
        <div id="detalles-container"></div>
        <button type="button" class="btn-add" onclick="agregarDetalle()">&#43; Agregar componente</button>
    </div>

    <div class="actions">
        <a href="{{ route('admin.mezclas.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">&#10003; Programar Mezcla</button>
    </div>
</form>

<script>
const medicamentos = @json($medicamentos->map(fn($m) => ['id'=>$m->id,'nombre'=>$m->nombre]));
const unidades = @json($unidades->map(fn($u) => ['id'=>$u->id,'nombre'=>$u->nombre]));

let idx = 0;
function agregarDetalle(med = null, dosis = '', unidad = null) {
    const i = idx++;
    const optMed = medicamentos.map(m => `<option value="${m.id}" ${med == m.id ? 'selected' : ''}>${m.nombre}</option>`).join('');
    const optUni = '<option value="">—</option>' + unidades.map(u => `<option value="${u.id}" ${unidad == u.id ? 'selected' : ''}>${u.nombre}</option>`).join('');
    const html = `
    <div class="row-detalle" id="row-${i}">
        <div class="field"><label>Medicamento *</label><select name="detalles[${i}][medicamento_id]" required>${optMed}</select></div>
        <div class="field"><label>Dosis *</label><input type="number" step="0.0001" name="detalles[${i}][dosis_requerida]" value="${dosis}" required></div>
        <div class="field"><label>Unidad</label><select name="detalles[${i}][unidad_medida_id]">${optUni}</select></div>
        <div class="field"><label>Observación</label><input type="text" name="detalles[${i}][observaciones]"></div>
        <button type="button" class="btn-del" onclick="document.getElementById('row-${i}').remove()">&#10005;</button>
    </div>`;
    document.getElementById('detalles-container').insertAdjacentHTML('beforeend', html);
}

function cargarFormula() {
    const sel = document.getElementById('formula_id');
    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;
    document.getElementById('tipo_mezcla').value = (opt.dataset.tipo || '').replace('PEDIATRIA','PEDIATRICA');
    document.getElementById('volumen_programado').value = opt.dataset.volumen || '';
    document.getElementById('detalles-container').innerHTML = '';
    idx = 0;
    const dets = JSON.parse(opt.dataset.detalles || '[]');
    if (dets.length === 0) { agregarDetalle(); return; }
    dets.forEach(d => agregarDetalle(d.medicamento_id, d.dosis, d.unidad));
}

agregarDetalle();
</script>
</x-app-layout>
