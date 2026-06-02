<x-app-layout>
<style>
    :root { --inst:#2e3a75; --inst-dark:#1f2a5c; }
    .page-header { background: linear-gradient(135deg, var(--inst) 0%, #7c3aed 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.5rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap; }
    .page-header h1 { font-size:1.3rem; font-weight:700; margin:0; }
    .page-header a { color:#fff; text-decoration:none; font-size:.85rem; opacity:.85; }
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); padding:1.4rem; margin-bottom:1.4rem; }
    .card h3 { color:var(--inst); margin:0 0 1rem; font-size:1rem; font-weight:700; border-bottom:2px solid #f1f5f9; padding-bottom:.5rem; }
    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
    @media (max-width:768px) { .grid-2 { grid-template-columns:1fr; } }
    label { font-size:.78rem; font-weight:600; color:#64748b; display:block; margin-bottom:.3rem; }
    input, select, textarea { width:100%; padding:.55rem .8rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.88rem; background:#fff; }
    input:focus, select:focus, textarea:focus { outline:none; border-color:var(--inst); }
    .btn-primary { background:var(--inst); color:#fff; border:none; border-radius:8px; padding:.6rem 1.3rem; font-size:.9rem; cursor:pointer; font-weight:600; }
    .btn-primary:hover { background:var(--inst-dark); }
    .btn-secondary { background:#f1f5f9; color:#1e293b; border:1.5px solid #e2e8f0; border-radius:8px; padding:.6rem 1.3rem; font-size:.9rem; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-danger { background:#ef4444; color:#fff; border:none; border-radius:6px; padding:.3rem .6rem; font-size:.78rem; cursor:pointer; }
    .alert-error { background:#fee2e2; color:#991b1b; padding:.7rem 1rem; border-radius:8px; margin-bottom:1rem; }
    table.detalles { width:100%; border-collapse:collapse; margin-top:.5rem; }
    table.detalles th { background:#f1f5f9; padding:.5rem; font-size:.75rem; text-align:left; }
    table.detalles td { padding:.4rem; border-bottom:1px solid #f1f5f9; }
    table.detalles input, table.detalles select { padding:.35rem .5rem; font-size:.82rem; }
    .add-row { background:#22c55e; color:#fff; border:none; border-radius:6px; padding:.4rem .8rem; font-size:.8rem; cursor:pointer; margin-top:.5rem; }
</style>

<div class="page-header">
    <div>
        <h1>&#43; Nueva Preparación</h1>
        <p style="margin:0;font-size:.82rem;opacity:.85">Define la fórmula. El consumo real de lotes se registra al iniciar la preparación.</p>
    </div>
    <a href="{{ route('admin.preparaciones.index') }}">&#8592; Volver al Kanban</a>
</div>

@if($errors->any())
    <div class="alert-error">
        @foreach($errors->all() as $e)<div>&#9888; {{ $e }}</div>@endforeach
    </div>
@endif
@if(session('error'))<div class="alert-error">&#9888; {{ session('error') }}</div>@endif

<form method="POST" action="{{ route('admin.preparaciones.store') }}">
    @csrf

    <div class="card">
        <h3>Datos generales</h3>
        <div class="grid-2">
            <div>
                <label>Tipo de preparación *</label>
                <select name="tipo_preparacion_id" required>
                    <option value="">— Seleccione —</option>
                    @foreach($tipos as $t)
                        <option value="{{ $t->id }}" @selected(old('tipo_preparacion_id')==$t->id)>{{ $t->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Fecha programada *</label>
                <input type="datetime-local" name="fecha_programada" value="{{ old('fecha_programada', now()->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div>
                <label>Paciente</label>
                <select name="paciente_id">
                    <option value="">— Sin paciente —</option>
                    @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" @selected(old('paciente_id')==$p->id)>{{ $p->documento }} · {{ $p->nombre_completo }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Servicio</label>
                <select name="servicio_id">
                    <option value="">—</option>
                    @foreach($servicios as $s)
                        <option value="{{ $s->id }}" @selected(old('servicio_id')==$s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Volumen final</label>
                <input type="number" step="0.01" min="0" name="volumen_final" value="{{ old('volumen_final') }}">
            </div>
            <div>
                <label>Unidad de volumen</label>
                <select name="unidad_volumen_id">
                    <option value="">—</option>
                    @foreach($unidades as $u)
                        <option value="{{ $u->id }}" @selected(old('unidad_volumen_id')==$u->id)>{{ $u->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div style="margin-top:1rem">
            <label>Observaciones</label>
            <textarea name="observaciones" rows="2">{{ old('observaciones') }}</textarea>
        </div>
    </div>

    <div class="card">
        <h3>Fórmula · Componentes</h3>
        <table class="detalles" id="tbl-detalles">
            <thead>
                <tr>
                    <th>Medicamento *</th>
                    <th>Presentación</th>
                    <th>Dosis *</th>
                    <th>Unidad</th>
                    <th>Concentración</th>
                    <th>Observaciones</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="detalles[0][medicamento_id]" required>
                            <option value="">—</option>
                            @foreach($medicamentos as $m)
                                <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" name="detalles[0][presentacion_id]" placeholder="ID opc."></td>
                    <td><input type="number" step="0.0001" name="detalles[0][dosis]" required></td>
                    <td>
                        <select name="detalles[0][unidad_medida_id]">
                            <option value="">—</option>
                            @foreach($unidades as $u)
                                <option value="{{ $u->id }}">{{ $u->nombre }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="number" step="0.0001" name="detalles[0][concentracion]"></td>
                    <td><input type="text" name="detalles[0][observaciones]"></td>
                    <td><button type="button" class="btn-danger" onclick="this.closest('tr').remove()">&times;</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="add-row" onclick="addRow()">&#43; Agregar componente</button>
    </div>

    <div style="display:flex;gap:.7rem;justify-content:flex-end">
        <a href="{{ route('admin.preparaciones.index') }}" class="btn-secondary">Cancelar</a>
        <button type="submit" class="btn-primary">Guardar Preparación</button>
    </div>
</form>

<script>
let idx = 1;
function addRow() {
    const tbody = document.querySelector('#tbl-detalles tbody');
    const tr = tbody.rows[0].cloneNode(true);
    tr.querySelectorAll('input, select').forEach(el => {
        el.name = el.name.replace(/\[0\]/, '['+idx+']');
        el.value = '';
    });
    tbody.appendChild(tr);
    idx++;
}
</script>
</x-app-layout>
