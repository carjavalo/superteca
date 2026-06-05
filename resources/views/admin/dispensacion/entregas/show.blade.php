<x-app-layout>
@php
    $bclass = ['PENDIENTE'=>'b-pen','ENTREGADA'=>'b-ent','PARCIAL'=>'b-par','DEVUELTA'=>'b-dev','ANULADA'=>'b-anu'][$entrega->estado] ?? 'b-pen';
@endphp
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #0ea5e9 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; font-family:monospace; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }
    .badge { display:inline-block; padding:.25rem .8rem; border-radius:14px; font-size:.78rem; font-weight:700; }
    .b-pen { background:#fef3c7; color:#92400e; }
    .b-ent { background:#dcfce7; color:#166534; }
    .b-par { background:#e0f2fe; color:#075985; }
    .b-dev { background:#f3e8ff; color:#6b21a8; }
    .b-anu { background:#fee2e2; color:#991b1b; }

    .grid { display:grid; gap:1rem; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); margin-bottom:1.2rem; }
    .info { background:#fff; padding:1rem 1.2rem; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,.06); }
    .info .lbl { font-size:.7rem; color:#94a3b8; text-transform:uppercase; font-weight:600; }
    .info .val { font-size:.95rem; font-weight:600; color:#1e293b; margin-top:.2rem; }

    .card-box { background:#fff; padding:1.2rem 1.4rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .card-box h2 { margin:0 0 1rem 0; font-size:1.05rem; color:#1e293b; display:flex; justify-content:space-between; align-items:center; }
    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:.6rem .8rem; font-size:.72rem; text-align:left; color:#475569; text-transform:uppercase; }
    td { padding:.55rem .8rem; font-size:.83rem; border-top:1px solid #f1f5f9; }
    .lote-tag { background:#e0f2fe; color:#075985; padding:.15rem .55rem; border-radius:8px; font-family:monospace; font-size:.72rem; margin-right:.3rem; display:inline-block; margin-top:.15rem; }

    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; display:inline-block; }
    .btn-primary { background:#0ea5e9; color:#fff; }
    .btn-success { background:#22c55e; color:#fff; }
    .btn-warn { background:#f59e0b; color:#fff; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }
    label { font-size:.72rem; color:#475569; font-weight:600; display:block; margin-bottom:.25rem; }
    input,textarea,select { width:100%; padding:.5rem .65rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.83rem; box-sizing:border-box; }

    .timeline { display:grid; grid-template-columns:repeat(5,1fr); gap:.4rem; margin:.5rem 0 1rem 0; }
    .step { background:#f1f5f9; padding:.5rem .6rem; border-radius:8px; text-align:center; font-size:.72rem; color:#64748b; position:relative; }
    .step.act { background:#0ea5e9; color:#fff; font-weight:600; }
    .step.done { background:#22c55e; color:#fff; }
</style>

<div class="page-header">
    <div>
        <h1>{{ $entrega->codigo }}</h1>
        <p>{{ \App\Models\DispensacionEntrega::TIPOS[$entrega->tipo_entrega] ?? '' }} · {{ $entrega->destino }}</p>
    </div>
    <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
        <span class="badge {{ $bclass }}">{{ \App\Models\DispensacionEntrega::ESTADOS[$entrega->estado] }}</span>
        <a href="{{ route('admin.dispensacion.entregas.index') }}" class="btn btn-secondary">← Volver</a>
    </div>
</div>

@if(session('success'))<div style="background:#dcfce7;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#10003; {{ session('success') }}</div>@endif
@if(session('error'))<div style="background:#fee2e2;color:#991b1b;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#9888; {{ session('error') }}</div>@endif

@php
    $stages = ['Inventario','Producción','Preparación','Dispensación','Paciente'];
    $current = match($entrega->estado) {
        'PENDIENTE' => 3,
        'ENTREGADA' => 4,
        'PARCIAL' => 4,
        'DEVUELTA' => 4,
        default => 3,
    };
@endphp
<div class="card-box">
    <h2>Línea de Trazabilidad</h2>
    <div class="timeline">
        @foreach($stages as $i => $s)
            <div class="step {{ $i < $current ? 'done' : ($i == $current ? 'act' : '') }}">{{ $s }}</div>
        @endforeach
    </div>
</div>

<div class="grid">
    <div class="info"><div class="lbl">Fecha entrega</div><div class="val">{{ $entrega->fecha_entrega?->format('d/m/Y H:i') }}</div></div>
    <div class="info"><div class="lbl">Dispensado por</div><div class="val">{{ $entrega->dispensador->name ?? '—' }}</div></div>
    <div class="info"><div class="lbl">Recibido por</div><div class="val">{{ $entrega->recibe_nombre ?? '—' }}</div></div>
    <div class="info"><div class="lbl">Costo total</div><div class="val">${{ number_format($entrega->costo_total, 0, ',', '.') }}</div></div>
</div>

<div class="card-box">
    <h2>Productos y Lotes Entregados</h2>
    <table>
        <thead>
            <tr>
                <th>#</th><th>Medicamento</th><th>Cantidad</th><th>Lotes</th><th>Costo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($entrega->detalles as $i => $d)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>
                        <strong>{{ $d->medicamento->nombre ?? '—' }}</strong>
                        @if($d->presentacion)<div style="color:#64748b;font-size:.75rem">{{ $d->presentacion->nombre }}</div>@endif
                        @if($d->observaciones)<div style="color:#64748b;font-size:.72rem;margin-top:.2rem">{{ $d->observaciones }}</div>@endif
                    </td>
                    <td>{{ rtrim(rtrim(number_format($d->cantidad,2,'.',''),'0'),'.') }} {{ $d->unidadMedida->nombre ?? '' }}</td>
                    <td>
                        @foreach($d->lotes as $l)
                            <span class="lote-tag" title="Vence {{ $l->fecha_vencimiento?->format('d/m/Y') }}">
                                {{ $l->lote }} · {{ rtrim(rtrim(number_format($l->cantidad_entregada,2,'.',''),'0'),'.') }}
                            </span>
                        @endforeach
                    </td>
                    <td>${{ number_format($d->costo_total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:1rem">Sin productos.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($entrega->estado === 'PENDIENTE')
    <div class="card-box">
        <h2>Acciones</h2>
        <div style="display:flex;gap:.7rem;flex-wrap:wrap">
            <form method="POST" action="{{ route('admin.dispensacion.entregas.entregar', $entrega) }}" onsubmit="return confirm('Confirmar la entrega? Se descontará el inventario y se registrará el movimiento DISPENSACION en el Kardex.')">
                @csrf @method('PATCH')
                <button class="btn btn-success">✓ Confirmar Entrega</button>
            </form>
            @puede('Entregas','Anular')
            <form method="POST" action="{{ route('admin.dispensacion.entregas.anular', $entrega) }}" onsubmit="return confirm('¿Anular esta entrega?')">
                @csrf @method('PATCH')
                <button class="btn btn-warn">Anular</button>
            </form>
            @endpuede
            @puede('Entregas','Eliminar')
            <form method="POST" action="{{ route('admin.dispensacion.entregas.destroy', $entrega) }}" onsubmit="return confirm('¿Eliminar definitivamente?')">
                @csrf @method('DELETE')
                <button class="btn btn-danger">Eliminar</button>
            </form>
            @endpuede
        </div>
    </div>
@endif

@if($entrega->estado === 'ENTREGADA')
    <div class="card-box">
        <h2>Confirmación de Recepción</h2>
        <form method="POST" action="{{ route('admin.dispensacion.entregas.recibir', $entrega) }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:.7rem">
                <div><label>Quien recibe (nombre) *</label><input type="text" name="recibe_nombre" required value="{{ $entrega->recibe_nombre }}"></div>
                <div><label>Documento</label><input type="text" name="recibe_documento" value="{{ $entrega->recibe_documento }}"></div>
            </div>
            <div style="margin-top:.7rem"><label>Observaciones</label><textarea name="observaciones" rows="2"></textarea></div>
            <div style="margin-top:.7rem;text-align:right"><button class="btn btn-primary">Registrar Recepción</button></div>
        </form>

        @if($entrega->recepciones->count())
            <div style="margin-top:1rem">
                <strong style="font-size:.8rem;color:#475569">Recepciones registradas:</strong>
                <ul style="margin:.4rem 0 0 1.2rem;font-size:.82rem;color:#475569">
                    @foreach($entrega->recepciones as $r)
                        <li>{{ $r->fecha_recepcion->format('d/m/Y H:i') }} · {{ $r->recibe_nombre }} ({{ $r->recibe_documento ?? 's/d' }}) — {{ $r->usuario->name ?? '' }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif

@if(in_array($entrega->estado, ['ENTREGADA','PARCIAL']))
    <div class="card-box">
        <h2>Devolución</h2>
        <form method="POST" action="{{ route('admin.dispensacion.entregas.devolver', $entrega) }}">
            @csrf
            <table>
                <thead>
                    <tr><th></th><th>Lote</th><th>Entregado</th><th>Devolver</th><th>Reingresar stock</th></tr>
                </thead>
                <tbody>
                    @foreach($entrega->detalles as $d)
                        @foreach($d->lotes as $l)
                            <tr>
                                <td><input type="checkbox" onchange="this.closest('tr').dataset.checked=this.checked"></td>
                                <td><strong>{{ $d->medicamento->nombre ?? '—' }}</strong> · <span style="font-family:monospace">{{ $l->lote }}</span></td>
                                <td>{{ rtrim(rtrim(number_format($l->cantidad_entregada,2,'.',''),'0'),'.') }}</td>
                                <td><input type="hidden" name="lineas[{{ $l->id }}][entrega_lote_id]" value="{{ $l->id }}"><input type="number" step="0.01" min="0" max="{{ $l->cantidad_entregada }}" name="lineas[{{ $l->id }}][cantidad]" value="0"></td>
                                <td><select name="lineas[{{ $l->id }}][reingresa_stock]"><option value="1">Sí</option><option value="0">No</option></select></td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
            <div style="margin-top:.7rem"><label>Motivo *</label><textarea name="motivo" rows="2" required></textarea></div>
            <div style="margin-top:.7rem;text-align:right"><button class="btn btn-warn" onclick="return confirm('¿Registrar devolución?')">Registrar Devolución</button></div>
        </form>

        @if($entrega->devoluciones->count())
            <div style="margin-top:1rem">
                <strong style="font-size:.8rem;color:#475569">Historial:</strong>
                <ul style="margin:.4rem 0 0 1.2rem;font-size:.82rem;color:#475569">
                    @foreach($entrega->devoluciones as $dv)
                        <li>{{ $dv->fecha_devolucion->format('d/m/Y H:i') }} — {{ $dv->codigo }} · {{ rtrim(rtrim(number_format($dv->cantidad_devuelta,2,'.',''),'0'),'.') }} unidades · ${{ number_format($dv->costo_total,0,',','.') }} <em>({{ $dv->motivo }})</em></li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif
</x-app-layout>
