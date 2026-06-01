@extends('layouts.app')

@section('title', 'Traslado ' . $traslado->codigo)

@section('content')
<style>
    :root { --inst:#2e3a75; --inst-dark:#1f2a5c; }

    .page-header { background:linear-gradient(135deg, var(--inst), #1a6ba3); color:#fff; padding:1.2rem 1.6rem; border-radius:12px; margin-bottom:1.4rem; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.8rem; }
    .page-header h1 { font-size:1.3rem; font-weight:700; margin:0; }
    .page-header .meta { font-size:.82rem; opacity:.85; }

    .layout-two { display:grid; grid-template-columns:1fr 340px; gap:1.2rem; }
    @media(max-width:1024px){ .layout-two { grid-template-columns:1fr; } }

    .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); padding:1.3rem; margin-bottom:1.2rem; }
    .card h3 { font-size:.92rem; font-weight:700; color:var(--inst); margin:0 0 .9rem; padding-bottom:.5rem; border-bottom:2px solid #e2e8f0; }

    /* Status badges */
    .badge { display:inline-block; padding:.25rem .75rem; border-radius:20px; font-size:.78rem; font-weight:700; }
    .badge-BORRADOR   { background:#e0f2fe; color:#0284c7; }
    .badge-PENDIENTE  { background:#fef9c3; color:#b45309; }
    .badge-EN_TRANSITO { background:#fce7f3; color:#9d174d; }
    .badge-RECIBIDO   { background:#dcfce7; color:#166534; }
    .badge-RECHAZADO  { background:#fee2e2; color:#991b1b; }
    .badge-ANULADO    { background:#f1f5f9; color:#64748b; }

    /* Info grid */
    .info-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(160px,1fr)); gap:.9rem; }
    .info-item label { font-size:.72rem; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em; display:block; margin-bottom:.15rem; }
    .info-item .val  { font-size:.9rem; font-weight:600; color:#1e293b; }

    /* Timeline */
    .timeline { position:relative; padding-left:1.8rem; }
    .timeline::before { content:''; position:absolute; left:.65rem; top:.4rem; bottom:.4rem; width:2px; background:#e2e8f0; }
    .tl-step { position:relative; margin-bottom:1.2rem; }
    .tl-step:last-child { margin-bottom:0; }
    .tl-dot { position:absolute; left:-1.15rem; top:.15rem; width:1rem; height:1rem; border-radius:50%; background:#e2e8f0; border:2px solid #fff; box-shadow:0 0 0 2px #e2e8f0; transition:all .3s; }
    .tl-step.done .tl-dot  { background:#22c55e; box-shadow:0 0 0 2px #bbf7d0; }
    .tl-step.active .tl-dot { background:#f59e0b; box-shadow:0 0 0 2px #fde68a; animation:pulse 1.5s infinite; }
    .tl-step.fail .tl-dot  { background:#ef4444; box-shadow:0 0 0 2px #fecaca; }
    @keyframes pulse { 0%,100%{transform:scale(1)} 50%{transform:scale(1.25)} }
    .tl-title { font-size:.85rem; font-weight:700; color:#1e293b; }
    .tl-step.done  .tl-title { color:#166534; }
    .tl-step.active .tl-title { color:#b45309; }
    .tl-step.fail  .tl-title { color:#991b1b; }
    .tl-meta { font-size:.75rem; color:#64748b; margin-top:.1rem; }

    /* Actions */
    .actions-card .btn { display:block; width:100%; text-align:center; padding:.6rem; border-radius:8px; font-size:.88rem; font-weight:600; cursor:pointer; border:none; margin-bottom:.55rem; text-decoration:none; transition:opacity .2s; }
    .actions-card .btn:hover { opacity:.85; }
    .btn-green  { background:#22c55e; color:#fff; }
    .btn-blue   { background:#3b82f6; color:#fff; }
    .btn-amber  { background:#f59e0b; color:#fff; }
    .btn-red    { background:#ef4444; color:#fff; }
    .btn-gray   { background:#e2e8f0; color:#374151; }
    .btn-inst   { background:var(--inst); color:#fff; }

    /* Tabla medicamentos */
    .tbl-med { width:100%; border-collapse:collapse; font-size:.82rem; }
    .tbl-med thead th { background:var(--inst); color:#fff; padding:.55rem .7rem; text-align:left; }
    .tbl-med tbody tr:hover { background:#f8fafc; }
    .tbl-med tbody td { padding:.5rem .65rem; border-bottom:1px solid #f1f5f9; }

    /* Totals */
    .total-row { display:flex; gap:1.5rem; flex-wrap:wrap; background:#f8fafc; border-radius:8px; padding:.7rem 1rem; margin-top:.7rem; font-size:.85rem; }
    .total-row .ti { font-weight:700; color:var(--inst); }

    .alert { padding:.75rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.88rem; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .alert-error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

    /* Modal recepción */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:9000; align-items:center; justify-content:center; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:14px; padding:1.6rem; max-width:480px; width:92%; box-shadow:0 20px 60px rgba(0,0,0,.25); }
    .modal-box h2 { font-size:1.05rem; font-weight:700; color:var(--inst); margin:0 0 1rem; }
    .modal-form .fg { margin-bottom:.9rem; }
    .modal-form label { font-size:.78rem; font-weight:600; color:#374151; text-transform:uppercase; letter-spacing:.04em; display:block; margin-bottom:.3rem; }
    .modal-form textarea, .modal-form select { width:100%; border:1.5px solid #e2e8f0; border-radius:8px; padding:.5rem .8rem; font-size:.88rem; }
    .modal-form textarea:focus, .modal-form select:focus { outline:none; border-color:var(--inst); }
    .modal-actions { display:flex; gap:.8rem; justify-content:flex-end; margin-top:1rem; }
</style>

<div class="page-header">
    <div>
        <h1>Traslado: {{ $traslado->codigo }}</h1>
        <div class="meta">
            <span class="badge badge-{{ $traslado->estado }}">{{ \App\Models\Traslado::ESTADOS[$traslado->estado] ?? $traslado->estado }}</span>
            &nbsp;&bull;&nbsp; {{ \App\Models\Traslado::TIPOS[$traslado->tipo_traslado] ?? $traslado->tipo_traslado }}
            &nbsp;&bull;&nbsp; Solicitado {{ \Carbon\Carbon::parse($traslado->fecha_solicitud)->format('d/m/Y H:i') }}
        </div>
    </div>
    <a href="{{ route('admin.traslados.index') }}" style="background:rgba(255,255,255,.2);color:#fff;border:1.5px solid rgba(255,255,255,.4);border-radius:8px;padding:.45rem 1rem;text-decoration:none;font-size:.88rem;">&#8592; Volver</a>
</div>

@if(session('success'))
<div class="alert alert-success">&#10003; {{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert alert-error">&#9888; {{ session('error') }}</div>
@endif

<div class="layout-two">

    {{-- Columna izquierda --}}
    <div>
        {{-- Info general --}}
        <div class="card">
            <h3>&#128204; Información General</h3>
            <div class="info-grid">
                <div class="info-item">
                    <label>Bodega Origen</label>
                    <div class="val">{{ $traslado->bodegaOrigen->nombre ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <label>Bodega Destino</label>
                    <div class="val">{{ $traslado->bodegaDestino->nombre ?? '—' }}</div>
                </div>
                <div class="info-item">
                    <label>Tipo</label>
                    <div class="val">{{ \App\Models\Traslado::TIPOS[$traslado->tipo_traslado] ?? $traslado->tipo_traslado }}</div>
                </div>
                <div class="info-item">
                    <label>Valor Total</label>
                    <div class="val" style="color:var(--inst)">$ {{ number_format($traslado->valor_total, 0, ',', '.') }}</div>
                </div>
                <div class="info-item">
                    <label>Solicitado por</label>
                    <div class="val">{{ $traslado->solicitante->name ?? '—' }}</div>
                </div>
                @if($traslado->enviador)
                <div class="info-item">
                    <label>Despachado por</label>
                    <div class="val">{{ $traslado->enviador->name }}</div>
                </div>
                @endif
                @if($traslado->receptor)
                <div class="info-item">
                    <label>Recibido por</label>
                    <div class="val">{{ $traslado->receptor->name }}</div>
                </div>
                @endif
                @if($traslado->observaciones)
                <div class="info-item" style="grid-column:1/-1">
                    <label>Observaciones</label>
                    <div class="val" style="font-weight:400">{{ $traslado->observaciones }}</div>
                </div>
                @endif
            </div>
        </div>

        {{-- Medicamentos --}}
        <div class="card">
            <h3>&#128230; Medicamentos ({{ $traslado->detalles->count() }} ítems)</h3>
            <div style="overflow-x:auto">
                <table class="tbl-med">
                    <thead>
                        <tr>
                            <th>Medicamento</th>
                            <th>Lote</th>
                            <th>Vence</th>
                            <th>Cantidad</th>
                            <th>Costo Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($traslado->detalles as $d)
                        <tr>
                            <td>{{ $d->medicamento->nombre ?? '—' }}</td>
                            <td><code style="font-size:.78rem">{{ $d->lote ?? '—' }}</code></td>
                            <td>{{ $d->fecha_vencimiento ? \Carbon\Carbon::parse($d->fecha_vencimiento)->format('d/m/Y') : '—' }}</td>
                            <td style="text-align:center;font-weight:600">{{ number_format($d->cantidad, 2) }}</td>
                            <td style="text-align:right">$ {{ number_format($d->costo_unitario, 2, ',', '.') }}</td>
                            <td style="text-align:right;font-weight:700;color:var(--inst)">$ {{ number_format($d->cantidad * $d->costo_unitario, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="total-row">
                <div>Total ítems: <span class="ti">{{ $traslado->detalles->count() }}</span></div>
                <div>Total unidades: <span class="ti">{{ number_format($traslado->detalles->sum('cantidad'), 2) }}</span></div>
                <div>Valor total: <span class="ti">$ {{ number_format($traslado->valor_total, 0, ',', '.') }}</span></div>
            </div>
        </div>
    </div>

    {{-- Columna derecha --}}
    <div>

        {{-- Timeline --}}
        <div class="card">
            <h3>&#9719; Línea de Tiempo</h3>
            @php
                $est = $traslado->estado;
                function tlClass($step, $est) {
                    $flow = ['BORRADOR' => 1, 'PENDIENTE' => 2, 'EN_TRANSITO' => 3, 'RECIBIDO' => 4];
                    $stepVal = $flow[$step] ?? 99;
                    $estVal  = $flow[$est]  ?? 0;
                    if ($est === 'RECHAZADO' && $step === 'PENDIENTE') return 'fail';
                    if ($est === 'ANULADO'   && in_array($step, ['PENDIENTE','EN_TRANSITO'])) return 'fail';
                    if ($stepVal < $estVal) return 'done';
                    if ($stepVal === $estVal) return 'active';
                    return '';
                }
            @endphp
            <div class="timeline">
                <div class="tl-step {{ tlClass('BORRADOR', $est) }}">
                    <div class="tl-dot"></div>
                    <div class="tl-title">&#9998; Solicitud</div>
                    <div class="tl-meta">{{ \Carbon\Carbon::parse($traslado->fecha_solicitud)->format('d/m/Y H:i') }}<br>{{ $traslado->solicitante->name ?? '—' }}</div>
                </div>
                <div class="tl-step {{ tlClass('PENDIENTE', $est) }}">
                    <div class="tl-dot"></div>
                    <div class="tl-title">&#9989; Aprobación</div>
                    <div class="tl-meta">
                        @if(in_array($est, ['PENDIENTE','EN_TRANSITO','RECIBIDO'])) Aprobado @elseif($est==='RECHAZADO') Rechazado @else Pendiente @endif
                    </div>
                </div>
                <div class="tl-step {{ tlClass('EN_TRANSITO', $est) }}">
                    <div class="tl-dot"></div>
                    <div class="tl-title">&#128666; Despacho</div>
                    <div class="tl-meta">
                        @if($traslado->fecha_envio) {{ \Carbon\Carbon::parse($traslado->fecha_envio)->format('d/m/Y H:i') }}<br>{{ $traslado->enviador->name ?? '—' }} @else Pendiente @endif
                    </div>
                </div>
                <div class="tl-step {{ tlClass('RECIBIDO', $est) }}">
                    <div class="tl-dot"></div>
                    <div class="tl-title">&#9745; Recepción</div>
                    <div class="tl-meta">
                        @if($traslado->fecha_recepcion) {{ \Carbon\Carbon::parse($traslado->fecha_recepcion)->format('d/m/Y H:i') }}<br>{{ $traslado->receptor->name ?? '—' }} @else Pendiente @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="card actions-card">
            <h3>&#9889; Acciones</h3>

            @if($traslado->estado === 'BORRADOR')
            <form method="POST" action="{{ route('admin.traslados.aprobar', $traslado) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-green">&#9989; Aprobar Traslado</button>
            </form>
            @endif

            @if(in_array($traslado->estado, ['BORRADOR', 'PENDIENTE']))
            <form method="POST" action="{{ route('admin.traslados.despachar', $traslado) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-blue">&#128666; Despachar (En Tránsito)</button>
            </form>
            @endif

            @if($traslado->estado === 'EN_TRANSITO')
            <button type="button" class="btn btn-inst" onclick="document.getElementById('modal-recibir').classList.add('open')">
                &#9745; Registrar Recepción
            </button>
            @endif

            @if(!in_array($traslado->estado, ['RECIBIDO', 'RECHAZADO', 'ANULADO']))
            <form method="POST" action="{{ route('admin.traslados.rechazar', $traslado) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn-amber" onclick="return confirm('¿Rechazar este traslado?')">&#10006; Rechazar</button>
            </form>
            @endif

            @if(!in_array($traslado->estado, ['RECIBIDO', 'ANULADO']))
            <form method="POST" action="{{ route('admin.traslados.anular', $traslado) }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-red" onclick="return confirm('¿Anular este traslado? Si estaba EN TRÁNSITO se revertirá el stock.')">&#9888; Anular</button>
            </form>
            @endif

            @if($traslado->estado === 'BORRADOR')
            <form method="POST" action="{{ route('admin.traslados.destroy', $traslado) }}">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-gray" onclick="return confirm('¿Eliminar permanentemente?')">&#128465; Eliminar</button>
            </form>
            @endif
        </div>

    </div>
</div>

{{-- Modal Recepción --}}
<div class="modal-overlay" id="modal-recibir">
    <div class="modal-box">
        <h2>&#9745; Registrar Recepción</h2>
        <form method="POST" action="{{ route('admin.traslados.recibir', $traslado) }}" class="modal-form">
            @csrf @method('PATCH')
            <div class="fg">
                <label>¿Recepción completa?</label>
                <select name="recibido_completo" required>
                    <option value="1">Sí — Todo recibido correctamente</option>
                    <option value="0">No — Recepción parcial o con diferencias</option>
                </select>
            </div>
            <div class="fg">
                <label>Observaciones</label>
                <textarea name="observaciones" rows="3" placeholder="Novedades, diferencias, temperatura, etc."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-gray" style="border:1.5px solid #e2e8f0;border-radius:8px;padding:.5rem 1rem;cursor:pointer;background:#f1f5f9" onclick="document.getElementById('modal-recibir').classList.remove('open')">Cancelar</button>
                <button type="submit" class="btn btn-inst" style="border:none;padding:.55rem 1.2rem;border-radius:8px;cursor:pointer;font-size:.9rem">Confirmar Recepción</button>
            </div>
        </form>
    </div>
</div>

@endsection
