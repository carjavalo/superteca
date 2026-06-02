<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #0ea5e9 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }

    .card-box { background:#fff; padding:1.2rem 1.4rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .card-box h2 { margin:0 0 1rem 0; font-size:1.05rem; color:#1e293b; }
    .grid { display:grid; gap:.7rem; }
    .grid-2 { grid-template-columns:repeat(2,1fr); }
    .grid-4 { grid-template-columns:repeat(4,1fr); }
    @media (max-width:800px) { .grid-2,.grid-4 { grid-template-columns:1fr; } }
    label { font-size:.72rem; color:#475569; font-weight:600; display:block; margin-bottom:.25rem; }
    input,select,textarea { width:100%; padding:.55rem .7rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; box-sizing:border-box; }
    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:#0ea5e9; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-add { background:#22c55e; color:#fff; padding:.4rem .9rem; font-size:.8rem; border-radius:6px; cursor:pointer; border:none; }

    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:.55rem; font-size:.7rem; text-align:left; color:#475569; text-transform:uppercase; }
    td { padding:.4rem; font-size:.82rem; border-top:1px solid #f1f5f9; vertical-align:top; }
    .det-row { background:#f8fafc; }
    .lote-row td { padding-left:1.5rem; font-size:.78rem; color:#475569; background:#fff; }
    .err { color:#dc2626; font-size:.75rem; margin-top:.2rem; }
    .pill { display:inline-block; background:#e0f2fe; color:#075985; padding:.15rem .55rem; border-radius:10px; font-size:.7rem; }
</style>

<div class="page-header">
    <h1>Nueva Entrega de Dispensación</h1>
    <p>Registra la entrega y los lotes específicos para garantizar trazabilidad.</p>
</div>

@if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">
        <strong>Errores:</strong>
        <ul style="margin:.3rem 0 0 1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.dispensacion.entregas.store') }}" id="formEntrega">
    @csrf

    <div class="card-box">
        <h2>Información General</h2>
        <div class="grid grid-4">
            <div>
                <label>Tipo de Entrega *</label>
                <select name="tipo_entrega" id="tipoEntrega" required>
                    @foreach(\App\Models\DispensacionEntrega::TIPOS as $k=>$v)
                        <option value="{{ $k }}" {{ old('tipo_entrega','PACIENTE')==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Fecha y Hora *</label>
                <input type="datetime-local" name="fecha_entrega" value="{{ old('fecha_entrega', now()->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div id="boxPaciente">
                <label>Paciente</label>
                <select name="paciente_id">
                    <option value="">— Seleccione —</option>
                    @foreach($pacientes as $p)
                        <option value="{{ $p->id }}" {{ old('paciente_id')==$p->id?'selected':'' }}>{{ trim($p->apellidos.' '.$p->nombres) }} ({{ $p->documento }})</option>
                    @endforeach
                </select>
            </div>
            <div id="boxServicio">
                <label>Servicio</label>
                <select name="servicio_id">
                    <option value="">— Seleccione —</option>
                    @foreach($servicios as $s)
                        <option value="{{ $s->id }}" {{ old('servicio_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="grid grid-2" style="margin-top:.7rem">
            <div>
                <label>Recibe (nombre)</label>
                <input type="text" name="recibe_nombre" value="{{ old('recibe_nombre') }}">
            </div>
            <div>
                <label>Recibe (documento)</label>
                <input type="text" name="recibe_documento" value="{{ old('recibe_documento') }}">
            </div>
        </div>
        <div style="margin-top:.7rem">
            <label>Observaciones</label>
            <textarea name="observaciones" rows="2">{{ old('observaciones') }}</textarea>
        </div>
    </div>

    <div class="card-box">
        <h2 style="display:flex;justify-content:space-between;align-items:center">
            Productos a Entregar
            <button type="button" class="btn-add" onclick="addItem()">+ Agregar Producto</button>
        </h2>
        <table>
            <thead>
                <tr>
                    <th style="width:25%">Medicamento</th>
                    <th style="width:13%">Cantidad</th>
                    <th style="width:15%">Unidad</th>
                    <th>Lotes (trazabilidad)</th>
                    <th style="width:50px"></th>
                </tr>
            </thead>
            <tbody id="items"></tbody>
        </table>
    </div>

    <div style="display:flex;gap:.7rem;justify-content:flex-end">
        <a href="{{ route('admin.dispensacion.entregas.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary">Crear Entrega (Pendiente)</button>
    </div>
</form>

<script>
    const lotes = @json($lotes->map(fn($l)=>[
        'id'=>$l->id,
        'medicamento_id'=>$l->medicamento_id,
        'medicamento'=>$l->medicamento?->nombre,
        'lote'=>$l->lote,
        'fv'=>$l->fecha_vencimiento?->format('Y-m-d'),
        'stock'=>(float)$l->cantidad_actual,
        'cu'=>(float)($l->costo_unitario ?? 0),
        'presentacion_id'=>$l->presentacion_id,
        'unidad_medida_id'=>$l->unidad_medida_id ?? null,
    ]));
    const medicamentos = @json($medicamentos->map(fn($m)=>['id'=>$m->id,'nombre'=>$m->nombre]));
    const unidades = @json($unidades->map(fn($u)=>['id'=>$u->id,'nombre'=>$u->nombre]));

    let counter = 0;

    function addItem() {
        const idx = counter++;
        const tbody = document.getElementById('items');
        const tr = document.createElement('tr');
        tr.className = 'det-row';
        tr.dataset.idx = idx;
        tr.innerHTML = `
            <td>
                <select name="detalles[${idx}][medicamento_id]" onchange="filtrarLotes(${idx})" required>
                    <option value="">— Seleccione —</option>
                    ${medicamentos.map(m=>`<option value="${m.id}">${m.nombre}</option>`).join('')}
                </select>
            </td>
            <td><input type="number" step="0.01" min="0.01" name="detalles[${idx}][cantidad]" required></td>
            <td>
                <select name="detalles[${idx}][unidad_medida_id]">
                    <option value="">—</option>
                    ${unidades.map(u=>`<option value="${u.id}">${u.nombre}</option>`).join('')}
                </select>
            </td>
            <td>
                <div id="lotes-${idx}" class="lote-box">
                    <em style="color:#94a3b8;font-size:.75rem">Seleccione medicamento</em>
                </div>
                <button type="button" class="btn-add" style="margin-top:.4rem" onclick="addLote(${idx})">+ Agregar lote</button>
            </td>
            <td><button type="button" class="btn-danger" style="padding:.3rem .55rem;font-size:.8rem;border-radius:6px" onclick="this.closest('tr').remove()">×</button></td>
        `;
        tbody.appendChild(tr);
    }

    function filtrarLotes(idx) {
        const tr = document.querySelector(`tr[data-idx="${idx}"]`);
        const mid = tr.querySelector(`select[name="detalles[${idx}][medicamento_id]"]`).value;
        const box = document.getElementById(`lotes-${idx}`);
        box.dataset.medicamento = mid;
        box.innerHTML = '';
        addLote(idx);
    }

    function addLote(idx) {
        const box = document.getElementById(`lotes-${idx}`);
        const mid = box.dataset.medicamento;
        if (!mid) { alert('Primero seleccione un medicamento'); return; }
        const filtro = lotes.filter(l=>l.medicamento_id == mid);
        if (filtro.length === 0) { box.innerHTML = '<em style="color:#dc2626;font-size:.75rem">Sin lotes con stock</em>'; return; }
        const lid = 'lote-'+idx+'-'+Math.random().toString(36).slice(2,7);
        const div = document.createElement('div');
        div.style = 'display:flex;gap:.4rem;margin-bottom:.3rem;align-items:center';
        div.innerHTML = `
            <select name="detalles[${idx}][lotes][${lid}][inventario_lote_id]" required style="flex:2">
                <option value="">— Lote —</option>
                ${filtro.map(l=>`<option value="${l.id}">${l.lote} · vence ${l.fv ?? '—'} · stock ${l.stock}</option>`).join('')}
            </select>
            <input type="number" step="0.01" min="0.01" placeholder="Cant." name="detalles[${idx}][lotes][${lid}][cantidad]" required style="flex:1">
            <button type="button" onclick="this.parentElement.remove()" style="background:#fee2e2;color:#991b1b;border:none;padding:.4rem .55rem;border-radius:6px;cursor:pointer">×</button>
        `;
        box.appendChild(div);
    }

    function syncTipo() {
        const t = document.getElementById('tipoEntrega').value;
        const isPaciente = t === 'PACIENTE';
        document.getElementById('boxPaciente').style.opacity = isPaciente ? 1 : .5;
        document.getElementById('boxServicio').style.opacity = isPaciente ? .5 : 1;
    }
    document.getElementById('tipoEntrega').addEventListener('change', syncTipo);
    syncTipo();

    addItem();
</script>
</x-app-layout>
