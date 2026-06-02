<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #0891b2 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }
    .badge-estado { display:inline-block; padding:.3rem .8rem; border-radius:14px; font-size:.72rem; font-weight:700; background:rgba(255,255,255,.25); }

    .info-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:1rem; margin-bottom:1.2rem; }
    .info-card { background:#fff; border-radius:12px; padding:.9rem 1.1rem; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .info-card .lbl { font-size:.7rem; color:#94a3b8; text-transform:uppercase; }
    .info-card .val { font-size:1rem; font-weight:700; color:#1e293b; margin-top:.15rem; }

    .pipeline { display:flex; gap:.4rem; padding:1rem; background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1.2rem; align-items:center; flex-wrap:wrap; }
    .pipeline .step { flex:1; min-width:140px; text-align:center; padding:.7rem; background:#f1f5f9; border-radius:10px; font-size:.8rem; color:#64748b; font-weight:600; }
    .pipeline .step.done { background:#dcfce7; color:#166534; }
    .pipeline .step.current { background:#0891b2; color:#fff; }
    .pipeline .arrow { color:#cbd5e1; }

    .panel { background:#fff; border-radius:12px; padding:1.4rem; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1.2rem; }
    .panel h3 { margin:0 0 1rem 0; font-size:1rem; color:var(--inst); border-left:4px solid #0891b2; padding-left:.6rem; }

    table { width:100%; border-collapse:collapse; font-size:.88rem; }
    th { background:var(--inst); color:#fff; padding:.55rem .85rem; text-align:left; }
    td { padding:.5rem .85rem; border-bottom:1px solid #f1f5f9; }
    .num { text-align:right; font-family:monospace; }

    .btn { padding:.5rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:#0891b2; color:#fff; }
    .btn-success { background:#22c55e; color:#fff; }
    .btn-warn { background:#f59e0b; color:#fff; }
    .btn-cyan { background:#0e7490; color:#fff; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }
    .btn-icon { background:#fee2e2; color:#991b1b; padding:.3rem .55rem; border-radius:6px; border:none; cursor:pointer; font-size:.8rem; }

    .form-inline { display:grid; grid-template-columns:2fr 1fr auto; gap:.5rem; align-items:end; padding:.7rem; background:#f8fafc; border-radius:8px; }
    .form-inline label { font-size:.7rem; color:#475569; font-weight:600; display:block; margin-bottom:.2rem; }
    .form-inline input, .form-inline select { width:100%; border:1.5px solid #e2e8f0; border-radius:7px; padding:.45rem .7rem; font-size:.85rem; }

    .qa-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:.7rem; }
    @media (max-width:900px) { .qa-grid { grid-template-columns:1fr; } }
    .qa-grid .field label { font-size:.7rem; color:#475569; font-weight:600; }
    .qa-grid .field input, .qa-grid .field select, .qa-grid .field textarea { width:100%; border:1.5px solid #e2e8f0; border-radius:7px; padding:.45rem .7rem; font-size:.85rem; margin-top:.2rem; }

    .alert { padding:.7rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.88rem; }
    .alert-success { background:#dcfce7; color:#166534; }
    .alert-error { background:#fee2e2; color:#991b1b; }
    .pill-ok { background:#dcfce7; color:#166534; padding:.15rem .5rem; border-radius:14px; font-size:.7rem; font-weight:600; }
    .pill-bad { background:#fee2e2; color:#991b1b; padding:.15rem .5rem; border-radius:14px; font-size:.7rem; font-weight:600; }

    .traza { display:grid; grid-template-columns:1fr auto 1fr auto 1fr; gap:.6rem; align-items:center; background:linear-gradient(90deg,#ecfeff,#f0fdfa); padding:1.2rem; border-radius:12px; margin-bottom:1.2rem; }
    .traza .box { background:#fff; padding:.9rem; border-radius:10px; text-align:center; border:2px solid #0891b2; }
    .traza .box .lbl { font-size:.65rem; color:#0891b2; text-transform:uppercase; font-weight:700; }
    .traza .box .val { font-family:monospace; font-size:1rem; font-weight:700; color:#1e293b; margin-top:.25rem; }
    .traza .arrow { color:#0891b2; font-size:1.4rem; font-weight:700; }
    @media (max-width:900px) { .traza { grid-template-columns:1fr; } .traza .arrow { transform:rotate(90deg); } }
</style>

@php
    $orden = ['PROGRAMADO'=>1,'EN_PROCESO'=>2,'CONTROL_CALIDAD'=>3,'LIBERADO'=>4];
    $actual = $orden[$reempaque->estado] ?? 0;
    $loteOrigen = $reempaque->consumos->first()?->lote_origen;
    $totalGenerado = $reempaque->productosFinales->sum('cantidad_generada');
@endphp

<div class="page-header">
    <div>
        <h1>{{ $reempaque->codigo }}</h1>
        <p>
            {{ $reempaque->medicamentoOrigen->nombre ?? '—' }}
            <span style="opacity:.7"> · </span>
            Factor: 1 → {{ rtrim(rtrim(number_format($reempaque->factor_conversion,4,'.',''),'0'),'.') }}
            <span class="badge-estado">{{ \App\Models\Reempaque::ESTADOS[$reempaque->estado] }}</span>
        </p>
    </div>
    <a href="{{ route('admin.reempaques.index') }}" class="btn btn-secondary">&#8592; Volver</a>
</div>

@if(session('success'))<div class="alert alert-success">&#10003; {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">&#9888; {{ session('error') }}</div>@endif

<div class="pipeline">
    <div class="step {{ $actual >= 1 ? ($actual == 1 ? 'current' : 'done') : '' }}">1. Programado</div>
    <span class="arrow">&#9654;</span>
    <div class="step {{ $actual >= 2 ? ($actual == 2 ? 'current' : 'done') : '' }}">2. En Proceso</div>
    <span class="arrow">&#9654;</span>
    <div class="step {{ $actual >= 3 ? ($actual == 3 ? 'current' : 'done') : '' }}">3. Control Calidad</div>
    <span class="arrow">&#9654;</span>
    <div class="step {{ $actual >= 4 ? ($actual == 4 ? 'current' : 'done') : '' }}">4. Liberado</div>
</div>

@if($loteOrigen || $totalGenerado > 0)
<div class="traza">
    <div class="box">
        <div class="lbl">Lote Origen</div>
        <div class="val">{{ $loteOrigen ?? '—' }}</div>
        <div style="font-size:.7rem; color:#64748b; margin-top:.2rem">{{ $reempaque->presentacionOrigen->nombre ?? 'Original' }}</div>
    </div>
    <div class="arrow">&#10142;</div>
    <div class="box">
        <div class="lbl">Reempaque</div>
        <div class="val">{{ $reempaque->codigo }}</div>
        <div style="font-size:.7rem; color:#64748b; margin-top:.2rem">{{ \App\Models\Reempaque::ESTADOS[$reempaque->estado] }}</div>
    </div>
    <div class="arrow">&#10142;</div>
    <div class="box">
        <div class="lbl">Lote Reempaque</div>
        <div class="val">{{ $reempaque->codigo }}</div>
        <div style="font-size:.7rem; color:#64748b; margin-top:.2rem">{{ rtrim(rtrim(number_format($totalGenerado,2,'.',''),'0'),'.') }} unidades · {{ $reempaque->presentacionDestino->nombre ?? '—' }}</div>
    </div>
</div>
@endif

<div class="info-grid">
    <div class="info-card"><div class="lbl">Programado</div><div class="val">{{ $reempaque->fecha_programada->format('d/m/Y H:i') }}</div></div>
    <div class="info-card"><div class="lbl">Inicio</div><div class="val">{{ $reempaque->fecha_inicio?->format('d/m H:i') ?? '—' }}</div></div>
    <div class="info-card"><div class="lbl">Fin</div><div class="val">{{ $reempaque->fecha_fin?->format('d/m H:i') ?? '—' }}</div></div>
    <div class="info-card"><div class="lbl">Cantidad esperada</div><div class="val">{{ rtrim(rtrim(number_format($reempaque->cantidad_esperada,2,'.',''),'0'),'.') }}</div></div>
    <div class="info-card"><div class="lbl">Generadas</div><div class="val">{{ rtrim(rtrim(number_format($totalGenerado,2,'.',''),'0'),'.') }}</div></div>
    <div class="info-card"><div class="lbl">Costo total</div><div class="val">$ {{ number_format($reempaque->costo_total, 0, ',', '.') }}</div></div>
    <div class="info-card"><div class="lbl">Responsable</div><div class="val">{{ $reempaque->responsable->name ?? '—' }}</div></div>
    <div class="info-card"><div class="lbl">Aprobador</div><div class="val">{{ $reempaque->aprobador->name ?? '—' }}</div></div>
</div>

<div class="panel">
    <h3>Acciones del flujo</h3>
    <div style="display:flex; gap:.5rem; flex-wrap:wrap">
        @if($reempaque->estado === 'PROGRAMADO')
            <form method="POST" action="{{ route('admin.reempaques.iniciar', $reempaque) }}">@csrf @method('PATCH')
                <button class="btn btn-warn">&#9881; Iniciar Reempaque</button>
            </form>
        @endif
        @if($reempaque->estado === 'EN_PROCESO')
            <form method="POST" action="{{ route('admin.reempaques.enviarControl', $reempaque) }}">@csrf @method('PATCH')
                <button class="btn btn-cyan">&#9889; Enviar a Control de Calidad</button>
            </form>
        @endif
        @if($reempaque->estado === 'CONTROL_CALIDAD')
            <form method="POST" action="{{ route('admin.reempaques.liberar', $reempaque) }}" onsubmit="return confirm('¿Liberar reempaque? Se descontará inventario y se creará un nuevo lote.')">@csrf @method('PATCH')
                <button class="btn btn-success">&#10003; Liberar (genera nuevo lote)</button>
            </form>
        @endif
        @if(!in_array($reempaque->estado, ['LIBERADO','ANULADO']))
            <form method="POST" action="{{ route('admin.reempaques.anular', $reempaque) }}" onsubmit="return confirm('¿Anular este reempaque?')">@csrf @method('PATCH')
                <button class="btn btn-danger">&#10005; Anular</button>
            </form>
        @endif
    </div>
</div>

<div class="panel">
    <h3>Lotes Origen Consumidos</h3>
    @if($reempaque->estado === 'EN_PROCESO')
    <form method="POST" action="{{ route('admin.reempaques.consumo', $reempaque) }}" class="form-inline" style="margin-bottom:.8rem">
        @csrf
        <div>
            <label>Lote disponible (mismo medicamento)</label>
            <select name="inventario_lote_id" required>
                <option value="">— Seleccionar lote —</option>
                @foreach($lotesDisponibles as $l)
                    <option value="{{ $l->id }}">{{ $l->medicamento->nombre ?? '' }} — Lote {{ $l->lote }} (Vence {{ \Carbon\Carbon::parse($l->fecha_vencimiento)->format('d/m/Y') }}) · Stock {{ $l->cantidad_actual }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Cantidad a consumir</label>
            <input type="number" step="0.01" name="cantidad_consumida" required>
        </div>
        <button class="btn btn-primary">+ Registrar consumo</button>
    </form>
    @endif

    <table>
        <thead>
            <tr>
                <th>Medicamento</th><th>Lote Origen</th><th>Vencimiento</th>
                <th class="num">Consumido</th><th class="num">Costo Unit.</th><th class="num">Costo Total</th>
                @if($reempaque->estado === 'EN_PROCESO')<th></th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse($reempaque->consumos as $c)
            <tr>
                <td><strong>{{ $c->medicamento->nombre ?? '—' }}</strong></td>
                <td><code>{{ $c->lote_origen }}</code></td>
                <td>{{ $c->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                <td class="num">{{ rtrim(rtrim(number_format($c->cantidad_consumida,2,'.',''),'0'),'.') }}</td>
                <td class="num">$ {{ number_format($c->costo_unitario, 2) }}</td>
                <td class="num">$ {{ number_format($c->costo_total, 2) }}</td>
                @if($reempaque->estado === 'EN_PROCESO')
                <td>
                    <form method="POST" action="{{ route('admin.reempaques.consumo.eliminar', [$reempaque, $c]) }}" onsubmit="return confirm('¿Eliminar?')">
                        @csrf @method('DELETE')
                        <button class="btn-icon" type="submit">&#128465;</button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="{{ $reempaque->estado === 'EN_PROCESO' ? 7 : 6 }}" style="text-align:center;color:#94a3b8">Sin consumos registrados</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($reempaque->detalles->count())
<div class="panel">
    <h3>Insumos Utilizados</h3>
    <table>
        <thead><tr><th>Insumo</th><th class="num">Cantidad</th><th>Unidad</th><th class="num">Costo Unit.</th><th class="num">Costo Total</th></tr></thead>
        <tbody>
            @foreach($reempaque->detalles as $d)
            <tr>
                <td>{{ $d->insumo }}</td>
                <td class="num">{{ rtrim(rtrim(number_format($d->cantidad,2,'.',''),'0'),'.') }}</td>
                <td>{{ $d->unidadMedida->nombre ?? '—' }}</td>
                <td class="num">$ {{ number_format($d->costo_unitario, 2) }}</td>
                <td class="num">$ {{ number_format($d->costo_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<div class="panel">
    <h3>Control de Calidad</h3>
    @if($reempaque->estado === 'CONTROL_CALIDAD')
    <form method="POST" action="{{ route('admin.reempaques.control', $reempaque) }}" style="margin-bottom:1rem">
        @csrf
        <div class="qa-grid">
            <div class="field"><label>Cantidad verificada</label><input type="number" step="0.01" name="cantidad_verificada"></div>
            <div class="field"><label>Etiquetado correcto</label>
                <select name="etiquetado_correcto"><option value="1">✓ Sí</option><option value="0">✗ No</option></select>
            </div>
            <div class="field"><label>Lote visible</label>
                <select name="lote_visible"><option value="1">✓ Sí</option><option value="0">✗ No</option></select>
            </div>
            <div class="field"><label>Vencimiento visible</label>
                <select name="fecha_vencimiento_visible"><option value="1">✓ Sí</option><option value="0">✗ No</option></select>
            </div>
            <div class="field"><label>Cumple</label>
                <select name="cumple"><option value="1">✓ Sí, cumple</option><option value="0">✗ No cumple</option></select>
            </div>
        </div>
        <div class="field" style="margin-top:.6rem"><label>Observaciones</label><textarea name="observaciones" rows="2" style="width:100%; border:1.5px solid #e2e8f0; border-radius:7px; padding:.45rem .7rem"></textarea></div>
        <button class="btn btn-cyan" style="margin-top:.7rem">&#10003; Registrar control</button>
    </form>
    @endif

    <table>
        <thead><tr><th>Fecha</th><th class="num">Cant. Verif.</th><th>Etiqueta</th><th>Lote</th><th>Vencimiento</th><th>Cumple</th><th>Responsable</th></tr></thead>
        <tbody>
            @forelse($reempaque->controles as $c)
            <tr>
                <td>{{ $c->fecha_control->format('d/m H:i') }}</td>
                <td class="num">{{ $c->cantidad_verificada ?? '—' }}</td>
                <td>{!! $c->etiquetado_correcto ? '<span class="pill-ok">OK</span>' : '<span class="pill-bad">NO</span>' !!}</td>
                <td>{!! $c->lote_visible ? '<span class="pill-ok">OK</span>' : '<span class="pill-bad">NO</span>' !!}</td>
                <td>{!! $c->fecha_vencimiento_visible ? '<span class="pill-ok">OK</span>' : '<span class="pill-bad">NO</span>' !!}</td>
                <td>{!! $c->cumple ? '<span class="pill-ok">CUMPLE</span>' : '<span class="pill-bad">NO CUMPLE</span>' !!}</td>
                <td>{{ $c->usuario->name ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#94a3b8">Sin controles registrados</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($reempaque->productosFinales->count())
<div class="panel">
    <h3>Productos Reempacados Generados</h3>
    <table>
        <thead><tr><th>Lote Reempaque</th><th>Lote Origen</th><th>Medicamento</th><th>Presentación</th><th class="num">Cantidad</th><th>Vencimiento</th></tr></thead>
        <tbody>
            @foreach($reempaque->productosFinales as $p)
            <tr>
                <td><code style="background:#dcfce7;color:#166534;padding:.15rem .4rem;border-radius:4px">{{ $p->lote_reempaque }}</code></td>
                <td><code>{{ $p->lote_origen ?? '—' }}</code></td>
                <td>{{ $p->medicamento->nombre ?? '—' }}</td>
                <td>{{ $p->presentacion->nombre ?? '—' }}</td>
                <td class="num">{{ rtrim(rtrim(number_format($p->cantidad_generada,2,'.',''),'0'),'.') }} {{ $p->unidadMedida->codigo ?? '' }}</td>
                <td>{{ $p->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($reempaque->observaciones)
<div class="panel"><h3>Observaciones</h3><p style="margin:0; white-space:pre-line; color:#475569">{{ $reempaque->observaciones }}</p></div>
@endif
</x-app-layout>
