<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #0891b2 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }

    .panel { background:#fff; border-radius:12px; padding:1.4rem; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1.2rem; }
    .panel h3 { margin:0 0 1rem 0; font-size:1rem; color:var(--inst); border-left:4px solid #0891b2; padding-left:.6rem; }

    .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:.9rem; }
    .field label { font-size:.72rem; color:#475569; font-weight:600; display:block; margin-bottom:.25rem; }
    .field input, .field select, .field textarea { width:100%; border:1.5px solid #e2e8f0; border-radius:7px; padding:.5rem .75rem; font-size:.88rem; }

    .conv-box { background:#ecfeff; border:2px dashed #0891b2; border-radius:10px; padding:1rem; text-align:center; font-size:.85rem; color:#155e75; margin:.8rem 0; }
    .conv-box strong { font-size:1.1rem; }

    table { width:100%; border-collapse:collapse; font-size:.88rem; }
    th { background:var(--inst); color:#fff; padding:.55rem .85rem; text-align:left; }
    td { padding:.5rem .85rem; border-bottom:1px solid #f1f5f9; }
    .num { text-align:right; font-family:monospace; }

    .btn { padding:.55rem 1.1rem; border-radius:8px; border:none; cursor:pointer; font-weight:600; font-size:.88rem; }
    .btn-primary { background:#0891b2; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; text-decoration:none; }
    .btn-icon { background:#fee2e2; color:#991b1b; padding:.3rem .55rem; border-radius:6px; border:none; cursor:pointer; }
    .alert-error { background:#fee2e2; color:#991b1b; padding:.7rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.88rem; }
</style>

<div class="page-header">
    <h1>Nuevo Reempaque</h1>
    <p>Programa la conversión de un medicamento desde una presentación origen a una destino</p>
</div>

@if($errors->any())
    <div class="alert-error">
        @foreach($errors->all() as $e)<div>&#9888; {{ $e }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('admin.reempaques.store') }}" id="formRep">
    @csrf

    <div class="panel">
        <h3>Datos generales</h3>
        <div class="grid">
            <div class="field">
                <label>Medicamento Origen *</label>
                <select name="medicamento_origen_id" required>
                    <option value="">— Seleccionar —</option>
                    @foreach($medicamentos as $m)
                        <option value="{{ $m->id }}" {{ old('medicamento_origen_id')==$m->id?'selected':'' }}>{{ $m->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Presentación Origen</label>
                <select name="presentacion_origen_id">
                    <option value="">— Seleccionar —</option>
                    @foreach($presentaciones as $p)
                        <option value="{{ $p->id }}" {{ old('presentacion_origen_id')==$p->id?'selected':'' }}>{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Presentación Destino</label>
                <select name="presentacion_destino_id">
                    <option value="">— Seleccionar —</option>
                    @foreach($presentaciones as $p)
                        <option value="{{ $p->id }}" {{ old('presentacion_destino_id')==$p->id?'selected':'' }}>{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Unidad de Medida Destino</label>
                <select name="unidad_medida_destino_id">
                    <option value="">— Seleccionar —</option>
                    @foreach($unidades as $u)
                        <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label>Factor de Conversión *</label>
                <input type="number" step="0.0001" name="factor_conversion" value="{{ old('factor_conversion', 100) }}" id="fact" required>
            </div>
            <div class="field">
                <label>Cantidad esperada a generar</label>
                <input type="number" step="0.01" name="cantidad_esperada" value="{{ old('cantidad_esperada') }}">
            </div>
            <div class="field">
                <label>Fecha programada *</label>
                <input type="datetime-local" name="fecha_programada" value="{{ old('fecha_programada', now()->format('Y-m-d\TH:i')) }}" required>
            </div>
        </div>
        <div class="conv-box">
            <strong>1 unidad consumida</strong> &nbsp;&rarr;&nbsp; <strong id="fact-display">100</strong> unidades generadas
        </div>
        <div class="field">
            <label>Observaciones</label>
            <textarea name="observaciones" rows="2">{{ old('observaciones') }}</textarea>
        </div>
    </div>

    <div class="panel">
        <h3>Insumos requeridos (opcional)</h3>
        <p style="font-size:.82rem; color:#64748b; margin:0 0 .6rem 0">Bolsas, etiquetas, frascos, jeringas, sellos, etc.</p>
        <table id="insumosTable">
            <thead>
                <tr><th>Insumo</th><th class="num">Cantidad</th><th>Unidad</th><th class="num">Costo Unit.</th><th></th></tr>
            </thead>
            <tbody id="insumosBody"></tbody>
        </table>
        <button type="button" class="btn btn-secondary" onclick="agregarInsumo()" style="margin-top:.7rem">+ Agregar insumo</button>
    </div>

    <div style="display:flex; gap:.6rem; justify-content:flex-end">
        <a href="{{ route('admin.reempaques.index') }}" class="btn btn-secondary" style="padding:.55rem 1.1rem">Cancelar</a>
        <button type="submit" class="btn btn-primary">Programar Reempaque</button>
    </div>
</form>

<script>
    const unidades = @json($unidades);
    let idx = 0;

    document.getElementById('fact').addEventListener('input', e => {
        document.getElementById('fact-display').textContent = e.target.value || 0;
    });

    function agregarInsumo() {
        const i = idx++;
        const opts = unidades.map(u => `<option value="${u.id}">${u.nombre}</option>`).join('');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type="text" name="detalles[${i}][insumo]" required style="width:100%; border:1px solid #e2e8f0; padding:.35rem .55rem; border-radius:6px"></td>
            <td><input type="number" step="0.01" name="detalles[${i}][cantidad]" value="1" required style="width:90px; border:1px solid #e2e8f0; padding:.35rem .55rem; border-radius:6px; text-align:right"></td>
            <td><select name="detalles[${i}][unidad_medida_id]" style="border:1px solid #e2e8f0; padding:.35rem .55rem; border-radius:6px"><option value="">—</option>${opts}</select></td>
            <td><input type="number" step="0.01" name="detalles[${i}][costo_unitario]" value="0" style="width:110px; border:1px solid #e2e8f0; padding:.35rem .55rem; border-radius:6px; text-align:right"></td>
            <td><button type="button" class="btn-icon" onclick="this.closest('tr').remove()">&#128465;</button></td>
        `;
        document.getElementById('insumosBody').appendChild(row);
    }
</script>
</x-app-layout>
