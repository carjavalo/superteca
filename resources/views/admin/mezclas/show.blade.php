<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #6d28d9 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
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
    .pipeline .step.current { background:#6d28d9; color:#fff; }
    .pipeline .arrow { color:#cbd5e1; }

    .panel { background:#fff; border-radius:12px; padding:1.4rem; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1.2rem; }
    .panel h3 { margin:0 0 1rem 0; font-size:1rem; color:var(--inst); border-left:4px solid #6d28d9; padding-left:.6rem; }

    table { width:100%; border-collapse:collapse; font-size:.88rem; }
    th { background:var(--inst); color:#fff; padding:.6rem .85rem; text-align:left; }
    td { padding:.55rem .85rem; border-bottom:1px solid #f1f5f9; }
    .num { text-align:right; font-family:monospace; }

    .btn { padding:.5rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:var(--inst); color:#fff; }
    .btn-success { background:#22c55e; color:#fff; }
    .btn-warn { background:#f59e0b; color:#fff; }
    .btn-violet { background:#6d28d9; color:#fff; }
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
</style>

@php
    $orden = ['PROGRAMADA'=>1,'EN_PROCESO'=>2,'CONTROL_CALIDAD'=>3,'LIBERADA'=>4];
    $actual = $orden[$mezcla->estado] ?? 0;
@endphp

<div class="page-header">
    <div>
        <h1>{{ $mezcla->codigo }}</h1>
        <p>
            {{ $mezcla->formula->nombre ?? 'Mezcla manual' }}
            <span style="opacity:.7"> · </span>
            {{ \App\Models\Mezcla::TIPOS[$mezcla->tipo_mezcla] }}
            <span class="badge-estado">{{ \App\Models\Mezcla::ESTADOS[$mezcla->estado] }}</span>
        </p>
    </div>
    <a href="{{ route('admin.mezclas.index') }}" class="btn btn-secondary">&#8592; Volver</a>
</div>

@if(session('success'))<div class="alert alert-success">&#10003; {{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error">&#9888; {{ session('error') }}</div>@endif

<div class="pipeline">
    <div class="step {{ $actual >= 1 ? ($actual == 1 ? 'current' : 'done') : '' }}">1. Programada</div>
    <span class="arrow">&#9654;</span>
    <div class="step {{ $actual >= 2 ? ($actual == 2 ? 'current' : 'done') : '' }}">2. En Proceso</div>
    <span class="arrow">&#9654;</span>
    <div class="step {{ $actual >= 3 ? ($actual == 3 ? 'current' : 'done') : '' }}">3. Control Calidad</div>
    <span class="arrow">&#9654;</span>
    <div class="step {{ $actual >= 4 ? ($actual == 4 ? 'current' : 'done') : '' }}">4. Liberada</div>
</div>

<div class="info-grid">
    <div class="info-card"><div class="lbl">Programada</div><div class="val">{{ $mezcla->fecha_programada->format('d/m/Y H:i') }}</div></div>
    <div class="info-card"><div class="lbl">Inicio</div><div class="val">{{ $mezcla->fecha_inicio?->format('d/m H:i') ?? '—' }}</div></div>
    <div class="info-card"><div class="lbl">Fin</div><div class="val">{{ $mezcla->fecha_fin?->format('d/m H:i') ?? '—' }}</div></div>
    <div class="info-card"><div class="lbl">Volumen</div><div class="val">{{ $mezcla->volumen_programado ? $mezcla->volumen_programado.' '.($mezcla->unidadVolumen->codigo ?? '') : '—' }}</div></div>
    <div class="info-card"><div class="lbl">Preparaciones</div><div class="val">{{ $mezcla->cantidad_preparaciones }}</div></div>
    <div class="info-card"><div class="lbl">Costo total</div><div class="val">$ {{ number_format($mezcla->costo_total, 0, ',', '.') }}</div></div>
    <div class="info-card"><div class="lbl">Preparador</div><div class="val">{{ $mezcla->preparador->name ?? '—' }}</div></div>
    <div class="info-card"><div class="lbl">Validador</div><div class="val">{{ $mezcla->validador->name ?? '—' }}</div></div>
</div>

<div class="panel">
    <h3>Acciones del flujo</h3>
    <div style="display:flex; gap:.5rem; flex-wrap:wrap">
        @if($mezcla->estado === 'PROGRAMADA')
            <form method="POST" action="{{ route('admin.mezclas.iniciar', $mezcla) }}">@csrf @method('PATCH')
                <button class="btn btn-warn">&#9881; Iniciar Mezcla</button>
            </form>
        @endif
        @if($mezcla->estado === 'EN_PROCESO')
            <form method="POST" action="{{ route('admin.mezclas.enviarControl', $mezcla) }}">@csrf @method('PATCH')
                <button class="btn btn-violet">&#9889; Enviar a Control de Calidad</button>
            </form>
        @endif
        @if($mezcla->estado === 'CONTROL_CALIDAD')
            @puede('Mezclas IV','Aprobar')
            <form method="POST" action="{{ route('admin.mezclas.liberar', $mezcla) }}">@csrf @method('PATCH')
                <button class="btn btn-success">&#10003; Liberar Mezcla (descuenta inventario)</button>
            </form>
            @endpuede
        @endif
        @if(!in_array($mezcla->estado, ['LIBERADA','CANCELADA']))
            @puede('Mezclas IV','Anular')
            <form method="POST" action="{{ route('admin.mezclas.cancelar', $mezcla) }}" onsubmit="return confirm('¿Cancelar esta mezcla?')">@csrf @method('PATCH')
                <button class="btn btn-danger">&#10005; Cancelar</button>
            </form>
            @endpuede
        @endif
    </div>
</div>

<div class="panel">
    <h3>Componentes Programados (Receta)</h3>
    <table>
        <thead><tr><th>#</th><th>Medicamento</th><th class="num">Dosis</th><th>Unidad</th><th>Observación</th></tr></thead>
        <tbody>
            @forelse($mezcla->detalles as $d)
            <tr>
                <td>{{ $d->orden_preparacion }}</td>
                <td><strong>{{ $d->medicamento->nombre ?? '—' }}</strong></td>
                <td class="num">{{ rtrim(rtrim(number_format($d->dosis_requerida,4,'.',''),'0'),'.') }}</td>
                <td>{{ $d->unidadMedida->nombre ?? '—' }}</td>
                <td>{{ $d->observaciones ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;color:#94a3b8">Sin componentes</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="panel">
    <h3>Trazabilidad de Lotes Consumidos</h3>
    @if($mezcla->estado === 'EN_PROCESO')
    <form method="POST" action="{{ route('admin.mezclas.consumo', $mezcla) }}" class="form-inline" style="margin-bottom:.8rem">
        @csrf
        <div>
            <label>Lote disponible</label>
            <select name="inventario_lote_id" required>
                <option value="">— Seleccionar lote —</option>
                @foreach($lotesDisponibles as $l)
                    <option value="{{ $l->id }}">{{ $l->medicamento->nombre ?? '' }} — Lote {{ $l->lote }} (Vence {{ \Carbon\Carbon::parse($l->fecha_vencimiento)->format('d/m/Y') }}) · Stock {{ $l->cantidad_actual }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label>Cantidad a consumir</label>
            <input type="number" step="0.0001" name="cantidad_consumida" required>
        </div>
        <button class="btn btn-primary">&#43; Registrar consumo</button>
    </form>
    @endif

    <table>
        <thead>
            <tr>
                <th>Medicamento</th><th>Lote</th><th>Vencimiento</th>
                <th class="num">Cantidad</th><th class="num">Costo Unit.</th><th class="num">Costo Total</th>
                @if($mezcla->estado === 'EN_PROCESO')<th></th>@endif
            </tr>
        </thead>
        <tbody>
            @forelse($mezcla->consumos as $c)
            <tr>
                <td><strong>{{ $c->medicamento->nombre ?? '—' }}</strong></td>
                <td><code>{{ $c->lote }}</code></td>
                <td>{{ $c->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                <td class="num">{{ rtrim(rtrim(number_format($c->cantidad_consumida,4,'.',''),'0'),'.') }}</td>
                <td class="num">$ {{ number_format($c->costo_unitario, 2) }}</td>
                <td class="num">$ {{ number_format($c->costo_total, 2) }}</td>
                @if($mezcla->estado === 'EN_PROCESO')
                <td>
                    <form method="POST" action="{{ route('admin.mezclas.consumo.eliminar', [$mezcla, $c]) }}" onsubmit="return confirm('¿Eliminar?')">
                        @csrf @method('DELETE')
                        <button class="btn-icon" type="submit">&#128465;</button>
                    </form>
                </td>
                @endif
            </tr>
            @empty
            <tr><td colspan="{{ $mezcla->estado === 'EN_PROCESO' ? 7 : 6 }}" style="text-align:center;color:#94a3b8">Sin consumos registrados</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="panel">
    <h3>Control de Calidad</h3>
    @if($mezcla->estado === 'CONTROL_CALIDAD')
    <form method="POST" action="{{ route('admin.mezclas.control', $mezcla) }}" style="margin-bottom:1rem">
        @csrf
        <div class="qa-grid">
            <div class="field"><label>Aspecto visual</label><input type="text" name="aspecto_visual" placeholder="Sin partículas, color esperado…"></div>
            <div class="field"><label>Volumen verificado</label><input type="number" step="0.01" name="volumen_verificado"></div>
            <div class="field"><label>pH</label><input type="number" step="0.01" name="ph"></div>
            <div class="field"><label>Osmolaridad</label><input type="number" step="0.01" name="osmolaridad"></div>
            <div class="field"><label>Temperatura (°C)</label><input type="number" step="0.1" name="temperatura"></div>
            <div class="field"><label>Cumple</label>
                <select name="cumple"><option value="1">✓ Sí, cumple</option><option value="0">✗ No cumple</option></select>
            </div>
        </div>
        <div class="field" style="margin-top:.6rem"><label>Observaciones</label><textarea name="observaciones" rows="2"></textarea></div>
        <button class="btn btn-violet" style="margin-top:.7rem">&#10003; Registrar control</button>
    </form>
    @endif

    <table>
        <thead><tr><th>Fecha</th><th>Aspecto</th><th class="num">Volumen</th><th class="num">pH</th><th class="num">Osmol.</th><th class="num">T°</th><th>Cumple</th><th>Responsable</th></tr></thead>
        <tbody>
            @forelse($mezcla->controles as $c)
            <tr>
                <td>{{ $c->fecha_control->format('d/m H:i') }}</td>
                <td>{{ $c->aspecto_visual ?? '—' }}</td>
                <td class="num">{{ $c->volumen_verificado ?? '—' }}</td>
                <td class="num">{{ $c->ph ?? '—' }}</td>
                <td class="num">{{ $c->osmolaridad ?? '—' }}</td>
                <td class="num">{{ $c->temperatura ?? '—' }}</td>
                <td>{!! $c->cumple ? '<span class="pill-ok">CUMPLE</span>' : '<span class="pill-bad">NO CUMPLE</span>' !!}</td>
                <td>{{ $c->usuario->name ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;color:#94a3b8">Sin controles registrados</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($mezcla->productosFinales->count())
<div class="panel">
    <h3>Producto Final</h3>
    <table>
        <thead><tr><th>Lote producción</th><th>F. Producción</th><th>F. Vencimiento</th><th class="num">Volumen</th><th class="num">Unidades</th></tr></thead>
        <tbody>
            @foreach($mezcla->productosFinales as $p)
            <tr>
                <td><code>{{ $p->lote_produccion }}</code></td>
                <td>{{ $p->fecha_produccion->format('d/m/Y H:i') }}</td>
                <td>{{ $p->fecha_vencimiento?->format('d/m/Y H:i') ?? '—' }}</td>
                <td class="num">{{ $p->volumen_final }}</td>
                <td class="num">{{ $p->cantidad_unidades }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

@if($mezcla->observaciones)
<div class="panel"><h3>Observaciones</h3><p style="margin:0; white-space:pre-line; color:#475569">{{ $mezcla->observaciones }}</p></div>
@endif
</x-app-layout>
