<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, var(--inst) 0%, #1a6ba3 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.5rem; }
    .page-header h1 { font-size:1.4rem; margin:0; }
    .page-header p { opacity:.85; font-size:.82rem; margin:0; }

    .card { background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 10px rgba(0,0,0,.06); margin-bottom:1.2rem; }
    .card h3 { margin:0 0 1rem 0; font-size:1rem; color:var(--inst); border-left:4px solid var(--inst); padding-left:.6rem; }

    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    .grid-3 { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
    .field { display:flex; flex-direction:column; gap:.3rem; }
    .field label { font-size:.78rem; color:#475569; font-weight:600; }
    .field input, .field select, .field textarea { border:1.5px solid #e2e8f0; border-radius:7px; padding:.5rem .75rem; font-size:.9rem; }
    .field input:focus, .field select:focus, .field textarea:focus { outline:none; border-color:var(--inst); }

    .row-detalle { display:grid; grid-template-columns:3fr 2fr 1fr 1.5fr 2fr auto; gap:.5rem; align-items:end; padding:.7rem; background:#f8fafc; border-radius:8px; margin-bottom:.5rem; }
    .row-detalle .field label { font-size:.7rem; }
    .btn-del { background:#ef4444; color:#fff; border:none; border-radius:6px; padding:.4rem .7rem; cursor:pointer; height:35px; }
    .btn-add { background:#22c55e; color:#fff; border:none; border-radius:7px; padding:.5rem 1rem; cursor:pointer; font-size:.85rem; }

    .actions { display:flex; gap:.7rem; justify-content:flex-end; }
    .btn { padding:.6rem 1.4rem; border-radius:8px; border:none; font-size:.9rem; font-weight:600; cursor:pointer; text-decoration:none; }
    .btn-primary { background:var(--inst); color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }

    .alert-error { background:#fee2e2; color:#991b1b; padding:.7rem 1rem; border-radius:8px; margin-bottom:1rem; }

    @media (max-width: 900px) {
        .grid-2, .grid-3 { grid-template-columns:1fr; }
        .row-detalle { grid-template-columns:1fr; }
    }
</style>

<div class="page-header">
    <h1>&#9881; Constructor de Fórmula</h1>
    <p>Define una receta maestra reutilizable y sus componentes</p>
</div>

@if($errors->any())
<div class="alert-error">
    @foreach($errors->all() as $e)<div>&#9888; {{ $e }}</div>@endforeach
</div>
@endif

<form method="POST" action="{{ route('admin.formulas.store') }}" id="formFormula">
    @csrf

    <div class="card">
        <h3>1. Información General</h3>
        <div class="grid-2">
            <div class="field">
                <label>Nombre de la Fórmula *</label>
                <input type="text" name="nombre" required value="{{ old('nombre') }}" placeholder="Ej: Vancomicina IV 1g">
            </div>
            <div class="field">
                <label>Tipo de Fórmula *</label>
                <select name="tipo_formula" required>
                    @foreach(\App\Models\Formula::TIPOS as $k => $v)
                        <option value="{{ $k }}" @selected(old('tipo_formula') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="field" style="margin-top:1rem">
            <label>Descripción</label>
            <textarea name="descripcion" rows="2" placeholder="Indicación clínica, uso recomendado…">{{ old('descripcion') }}</textarea>
        </div>
    </div>

    <div class="card">
        <h3>2. Parámetros de Estabilidad</h3>
        <div class="grid-3">
            <div class="field">
                <label>Volumen Final (mL)</label>
                <input type="number" step="0.01" name="volumen_final" value="{{ old('volumen_final') }}">
            </div>
            <div class="field">
                <label>Unidad de Volumen</label>
                <select name="unidad_volumen_id">
                    <option value="">—</option>
                    @foreach($unidades as $u)
                        <option value="{{ $u->id }}" @selected(old('unidad_volumen_id') == $u->id)>{{ $u->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Estabilidad (horas)</label>
                <input type="number" name="tiempo_estabilidad_horas" value="{{ old('tiempo_estabilidad_horas') }}">
            </div>
            <div class="field">
                <label>Temperatura mínima (°C)</label>
                <input type="number" step="0.1" name="temperatura_min" value="{{ old('temperatura_min') }}">
            </div>
            <div class="field">
                <label>Temperatura máxima (°C)</label>
                <input type="number" step="0.1" name="temperatura_max" value="{{ old('temperatura_max') }}">
            </div>
            <div class="field">
                <label>Conservación</label>
                <label style="font-weight:normal; padding-top:.5rem"><input type="checkbox" name="requiere_refrigeracion" value="1"> Requiere refrigeración</label>
            </div>
        </div>
    </div>

    <div class="card">
        <h3>3. Componentes / Receta</h3>
        <div id="detalles-container"></div>
        <button type="button" class="btn-add" onclick="agregarDetalle()">&#43; Agregar componente</button>
    </div>

    <div class="card">
        <h3>4. Observaciones Adicionales</h3>
        <textarea name="observaciones" rows="3" class="field" style="width:100%; padding:.5rem; border:1.5px solid #e2e8f0; border-radius:7px;" placeholder="Notas técnicas, advertencias, contraindicaciones…">{{ old('observaciones') }}</textarea>
    </div>

    <div class="actions">
        <a href="{{ route('admin.formulas.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">&#10003; Guardar Fórmula</button>
    </div>
</form>

<script>
const medicamentos = @json($medicamentos->map(fn($m) => ['id'=>$m->id, 'nombre'=>$m->nombre]));
const presentaciones = @json($presentaciones->map(fn($p) => ['id'=>$p->id, 'nombre'=>$p->nombre]));
const unidades = @json($unidades->map(fn($u) => ['id'=>$u->id, 'nombre'=>$u->nombre]));

let idx = 0;
function agregarDetalle() {
    const i = idx++;
    const optMed = medicamentos.map(m => `<option value="${m.id}">${m.nombre}</option>`).join('');
    const optPres = '<option value="">—</option>' + presentaciones.map(p => `<option value="${p.id}">${p.nombre}</option>`).join('');
    const optUni = '<option value="">—</option>' + unidades.map(u => `<option value="${u.id}">${u.nombre}</option>`).join('');
    const html = `
    <div class="row-detalle" id="row-${i}">
        <div class="field"><label>Medicamento *</label><select name="detalles[${i}][medicamento_id]" required>${optMed}</select></div>
        <div class="field"><label>Presentación</label><select name="detalles[${i}][presentacion_id]">${optPres}</select></div>
        <div class="field"><label>Dosis *</label><input type="number" step="0.0001" name="detalles[${i}][dosis]" required></div>
        <div class="field"><label>Unidad</label><select name="detalles[${i}][unidad_medida_id]">${optUni}</select></div>
        <div class="field"><label>Observación</label><input type="text" name="detalles[${i}][observaciones]"></div>
        <button type="button" class="btn-del" onclick="document.getElementById('row-${i}').remove()">&#10005;</button>
    </div>`;
    document.getElementById('detalles-container').insertAdjacentHTML('beforeend', html);
}
agregarDetalle();
</script>
</x-app-layout>
