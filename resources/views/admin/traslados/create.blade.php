@extends('layouts.app')

@section('title', 'Nuevo Traslado')

@section('content')
<style>
    :root { --inst:#2e3a75; --inst-dark:#1f2a5c; }
    .page-header { background:linear-gradient(135deg, var(--inst), #1a6ba3); color:#fff; padding:1.2rem 1.6rem; border-radius:12px; margin-bottom:1.4rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.8rem; }
    .page-header h1 { font-size:1.3rem; font-weight:700; margin:0; }
    .page-header p  { font-size:.82rem; opacity:.85; margin:0; }

    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.2rem; }
    @media(max-width:768px){ .form-grid { grid-template-columns:1fr; } }

    .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); padding:1.4rem; margin-bottom:1.2rem; }
    .card h3 { font-size:.95rem; font-weight:700; color:var(--inst); margin:0 0 1rem; padding-bottom:.6rem; border-bottom:2px solid #e2e8f0; }

    .form-group { display:flex; flex-direction:column; gap:.35rem; }
    .form-group label { font-size:.8rem; font-weight:600; color:#374151; text-transform:uppercase; letter-spacing:.04em; }
    .form-group input, .form-group select, .form-group textarea { border:1.5px solid #e2e8f0; border-radius:8px; padding:.5rem .8rem; font-size:.88rem; transition:border .2s; }
    .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline:none; border-color:var(--inst); }
    .form-group .error-msg { font-size:.75rem; color:#ef4444; }

    .btn-primary { background:var(--inst); color:#fff; border:none; border-radius:8px; padding:.55rem 1.2rem; font-size:.9rem; cursor:pointer; transition:background .2s; text-decoration:none; }
    .btn-primary:hover { background:var(--inst-dark); }
    .btn-secondary { background:#f1f5f9; color:#374151; border:1.5px solid #e2e8f0; border-radius:8px; padding:.5rem 1rem; font-size:.88rem; cursor:pointer; text-decoration:none; }
    .btn-danger { background:#ef4444; color:#fff; border:none; border-radius:6px; padding:.3rem .65rem; font-size:.78rem; cursor:pointer; }

    /* Tabla de detalles */
    .detalle-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:.8rem; }
    .detalle-table-wrap { overflow-x:auto; }
    table.detalle-tbl { width:100%; border-collapse:collapse; font-size:.83rem; }
    table.detalle-tbl thead th { background:var(--inst); color:#fff; padding:.55rem .7rem; text-align:left; white-space:nowrap; }
    table.detalle-tbl tbody tr:hover { background:#f8fafc; }
    table.detalle-tbl tbody td { padding:.45rem .6rem; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
    table.detalle-tbl input, table.detalle-tbl select { width:100%; border:1.5px solid #e2e8f0; border-radius:6px; padding:.3rem .5rem; font-size:.82rem; }
    table.detalle-tbl input:focus, table.detalle-tbl select:focus { outline:none; border-color:var(--inst); }

    .totals-bar { background:#f8fafc; border-radius:8px; padding:.8rem 1rem; display:flex; gap:2rem; flex-wrap:wrap; margin-top:.8rem; font-size:.85rem; }
    .totals-bar .tot-item { display:flex; flex-direction:column; }
    .totals-bar .tot-item span { font-weight:700; color:var(--inst); font-size:1rem; }

    .alert { padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.88rem; }
    .alert-error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
</style>

<div class="page-header">
    <div>
        <h1>&#43; Nuevo Traslado</h1>
        <p>Registre el movimiento logístico entre bodegas</p>
    </div>
    <a href="{{ route('admin.traslados.index') }}" class="btn-secondary">&#8592; Volver</a>
</div>

@if($errors->any())
<div class="alert alert-error">
    <strong>&#9888; Errores de validación:</strong>
    <ul style="margin:.4rem 0 0 1rem;padding:0">
        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
</div>
@endif
@if(session('error'))
<div class="alert alert-error">&#9888; {{ session('error') }}</div>
@endif

<form method="POST" action="{{ route('admin.traslados.store') }}" id="form-traslado">
@csrf

<div class="card">
    <h3>&#9881; Datos del Traslado</h3>
    <div class="form-grid">
        <div class="form-group">
            <label>Bodega Origen *</label>
            <select name="bodega_origen_id" required>
                <option value="">Seleccione bodega origen…</option>
                @foreach($bodegas as $b)
                <option value="{{ $b->id }}" @selected(old('bodega_origen_id') == $b->id)>
                    {{ $b->nombre }} — {{ $b->tipo }}
                </option>
                @endforeach
            </select>
            @error('bodega_origen_id')<span class="error-msg">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label>Bodega Destino *</label>
            <select name="bodega_destino_id" required>
                <option value="">Seleccione bodega destino…</option>
                @foreach($bodegas as $b)
                <option value="{{ $b->id }}" @selected(old('bodega_destino_id') == $b->id)>
                    {{ $b->nombre }} — {{ $b->tipo }}
                </option>
                @endforeach
            </select>
            @error('bodega_destino_id')<span class="error-msg">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
            <label>Tipo de Traslado *</label>
            <select name="tipo_traslado" required>
                @foreach(\App\Models\Traslado::TIPOS as $k => $v)
                <option value="{{ $k }}" @selected(old('tipo_traslado', 'INTERNO') === $k)>{{ $v }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Fecha de Solicitud *</label>
            <input type="datetime-local" name="fecha_solicitud" value="{{ old('fecha_solicitud', now()->format('Y-m-d\TH:i')) }}" required>
        </div>
        <div class="form-group" style="grid-column:1/-1">
            <label>Observaciones</label>
            <textarea name="observaciones" rows="2" placeholder="Motivo, instrucciones especiales…">{{ old('observaciones') }}</textarea>
        </div>
    </div>
</div>

<div class="card">
    <div class="detalle-header">
        <h3 style="margin:0;border:none;padding:0">&#128230; Medicamentos a Trasladar</h3>
        <button type="button" class="btn-primary" id="btn-add-row" style="font-size:.82rem;padding:.35rem .9rem">&#43; Agregar ítem</button>
    </div>
    <div class="detalle-table-wrap">
        <table class="detalle-tbl" id="tbl-detalles">
            <thead>
                <tr>
                    <th style="width:35%">Lote de Inventario</th>
                    <th style="width:10%">Stock Disp.</th>
                    <th style="width:10%">Cantidad *</th>
                    <th style="width:10%">Costo Unit.</th>
                    <th style="width:10%">Subtotal</th>
                    <th style="width:20%">Observación</th>
                    <th style="width:5%"></th>
                </tr>
            </thead>
            <tbody id="det-body">
                {{-- filas se agregan por JS --}}
            </tbody>
        </table>
    </div>
    <div class="totals-bar">
        <div class="tot-item">Ítems<span id="tot-items">0</span></div>
        <div class="tot-item">Unidades totales<span id="tot-uds">0</span></div>
        <div class="tot-item">Valor estimado<span id="tot-val">$ 0</span></div>
    </div>
</div>

<div style="display:flex;gap:.8rem;justify-content:flex-end">
    <a href="{{ route('admin.traslados.index') }}" class="btn-secondary">Cancelar</a>
    <button type="submit" class="btn-primary">Guardar Traslado</button>
</div>

</form>

@php
$lotesArr = $lotes->map(function($l) {
    return [
        'id'    => $l->id,
        'label' => ($l->medicamento->nombre ?? 'Sin nombre') . ' — Lote: ' . $l->lote . ' | Stock: ' . number_format($l->cantidad_actual, 2),
        'stock' => (float) $l->cantidad_actual,
        'costo' => (float) ($l->costo_unitario ?? 0),
    ];
})->values();
@endphp

<script>
const LOTES = {!! $lotesArr->toJson() !!};
let rowIdx = 0;

function buildSelect(name, selected) {
    let s = `<select name="${name}" class="lote-sel" required>`;
    s += '<option value="">Seleccione lote…</option>';
    LOTES.forEach(l => {
        s += `<option value="${l.id}" data-stock="${l.stock}" data-costo="${l.costo}" ${l.id==selected?'selected':''}>${l.label}</option>`;
    });
    s += '</select>';
    return s;
}

function addRow(data) {
    const i = rowIdx++;
    const tr = document.createElement('tr');
    tr.dataset.idx = i;
    tr.innerHTML = `
        <td>${buildSelect('detalles['+i+'][inventario_lote_id]', data?.lote_id||'')}</td>
        <td class="stock-cell" style="text-align:center;font-weight:600;color:#22c55e">—</td>
        <td><input type="number" name="detalles[${i}][cantidad]" min="0.01" step="0.01" class="cant-inp" value="${data?.cant||''}" required></td>
        <td class="costo-cell" style="text-align:right;color:#64748b">—</td>
        <td class="sub-cell" style="text-align:right;font-weight:600;color:#2e3a75">—</td>
        <td><input type="text" name="detalles[${i}][observacion]" placeholder="Opcional"></td>
        <td style="text-align:center"><button type="button" class="btn-danger del-row">&#215;</button></td>
    `;
    const sel = tr.querySelector('.lote-sel');
    const cantInp = tr.querySelector('.cant-inp');
    const stockCell = tr.querySelector('.stock-cell');
    const costoCell = tr.querySelector('.costo-cell');
    const subCell   = tr.querySelector('.sub-cell');

    function updateRow() {
        const opt = sel.options[sel.selectedIndex];
        const stock = opt ? parseFloat(opt.dataset.stock||0) : 0;
        const costo = opt ? parseFloat(opt.dataset.costo||0) : 0;
        stockCell.textContent = stock > 0 ? stock.toFixed(2) : '—';
        costoCell.textContent = costo > 0 ? '$ ' + costo.toLocaleString('es-CO') : '—';
        const cant = parseFloat(cantInp.value||0);
        subCell.textContent = cant > 0 && costo > 0 ? '$ ' + (cant*costo).toLocaleString('es-CO', {maximumFractionDigits:0}) : '—';
        if (cant > stock && stock > 0) {
            cantInp.style.borderColor = '#ef4444';
        } else {
            cantInp.style.borderColor = '';
        }
        recalcTotals();
    }
    sel.addEventListener('change', updateRow);
    cantInp.addEventListener('input', updateRow);
    tr.querySelector('.del-row').addEventListener('click', () => { tr.remove(); recalcTotals(); });
    document.getElementById('det-body').appendChild(tr);
    updateRow();
}

function recalcTotals() {
    let items = 0, uds = 0, val = 0;
    document.querySelectorAll('#det-body tr').forEach(tr => {
        const cant = parseFloat(tr.querySelector('.cant-inp')?.value||0);
        const opt  = tr.querySelector('.lote-sel')?.selectedOptions[0];
        const costo = opt ? parseFloat(opt.dataset.costo||0) : 0;
        if (cant > 0) { items++; uds += cant; val += cant * costo; }
    });
    document.getElementById('tot-items').textContent = items;
    document.getElementById('tot-uds').textContent   = uds.toFixed(2);
    document.getElementById('tot-val').textContent   = '$ ' + Math.round(val).toLocaleString('es-CO');
}

document.getElementById('btn-add-row').addEventListener('click', () => addRow());

// Restaurar old() detalles
@php $oldDets = old('detalles', []); @endphp
@foreach($oldDets as $i => $det)
addRow({ lote_id: {{ $det['inventario_lote_id'] ?? 0 }}, cant: {{ $det['cantidad'] ?? 0 }} });
@endforeach

// Formulario: al menos 1 detalle
document.getElementById('form-traslado').addEventListener('submit', function(e) {
    if (document.querySelectorAll('#det-body tr').length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un medicamento al traslado.');
    }
});
</script>
@endsection
