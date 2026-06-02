<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, var(--inst) 0%, #1a6ba3 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; font-size:.82rem; opacity:.85; }
    .badge-tipo { display:inline-block; padding:.25rem .7rem; border-radius:14px; font-size:.72rem; font-weight:700; background:rgba(255,255,255,.2); }

    .grid-info { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.5rem; }
    .info-card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .info-card .lbl { font-size:.7rem; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; }
    .info-card .val { font-size:1.1rem; font-weight:700; color:#1e293b; margin-top:.15rem; }

    .panel { background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1.2rem; }
    .panel h3 { margin:0 0 1rem 0; font-size:1rem; color:var(--inst); border-left:4px solid var(--inst); padding-left:.6rem; }

    /* Timeline */
    .timeline { display:flex; align-items:center; justify-content:space-between; gap:.5rem; padding:1rem 0; }
    .timeline .step { flex:1; text-align:center; padding:.7rem .5rem; background:#f1f5f9; border-radius:10px; font-size:.8rem; color:#475569; font-weight:600; position:relative; }
    .timeline .arrow { color:#cbd5e1; font-size:1.2rem; }
    .timeline .step.active { background:var(--inst); color:#fff; }

    /* Componentes */
    table { width:100%; border-collapse:collapse; font-size:.9rem; }
    table th { background:var(--inst); color:#fff; padding:.7rem .9rem; text-align:left; }
    table td { padding:.6rem .9rem; border-bottom:1px solid #f1f5f9; }
    .num { text-align:right; font-family:monospace; }
    .alert { padding:.4rem .8rem; border-radius:14px; font-size:.72rem; font-weight:600; display:inline-block; }
    .alert-ok { background:#dcfce7; color:#166534; }
    .alert-warn { background:#fee2e2; color:#991b1b; }

    .actions { display:flex; gap:.5rem; }
    .btn { padding:.5rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; }
    .btn-primary { background:var(--inst); color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }
    .btn-info { background:#06b6d4; color:#fff; }
    .btn-danger { background:#ef4444; color:#fff; border:none; cursor:pointer; }
</style>

<div class="page-header">
    <div>
        <h1>{{ $formula->nombre }}</h1>
        <p><code>{{ $formula->codigo }}</code> &middot; <span class="badge-tipo">{{ \App\Models\Formula::TIPOS[$formula->tipo_formula] }}</span></p>
    </div>
    <div class="actions">
        <a href="{{ route('admin.formulas.simulador', $formula) }}" class="btn btn-info">&#128202; Simulador</a>
        <a href="{{ route('admin.formulas.index') }}" class="btn btn-secondary">&#8592; Volver</a>
    </div>
</div>

<div class="grid-info">
    <div class="info-card"><div class="lbl">Componentes</div><div class="val">{{ $formula->detalles->count() }}</div></div>
    <div class="info-card"><div class="lbl">Volumen Final</div><div class="val">{{ $formula->volumen_final ? $formula->volumen_final.' mL' : '—' }}</div></div>
    <div class="info-card"><div class="lbl">Estabilidad</div><div class="val">{{ $formula->tiempo_estabilidad_horas ? $formula->tiempo_estabilidad_horas.' h' : '—' }}</div></div>
    <div class="info-card"><div class="lbl">Temperatura</div><div class="val">{{ $formula->temperatura_min ?? '—' }} a {{ $formula->temperatura_max ?? '—' }} °C</div></div>
    <div class="info-card"><div class="lbl">Refrigeración</div><div class="val">{{ $formula->requiere_refrigeracion ? '✓ Sí' : 'No' }}</div></div>
</div>

@if($formula->descripcion)
<div class="panel">
    <h3>Descripción Clínica</h3>
    <p style="margin:0; color:#475569; line-height:1.5;">{{ $formula->descripcion }}</p>
</div>
@endif

<div class="panel">
    <h3>Pasos de Preparación (Timeline)</h3>
    <div class="timeline">
        <div class="step active">1. Reconstitución</div>
        <span class="arrow">&#9654;</span>
        <div class="step active">2. Dilución</div>
        <span class="arrow">&#9654;</span>
        <div class="step active">3. Control de Calidad</div>
        <span class="arrow">&#9654;</span>
        <div class="step active">4. Liberación</div>
    </div>
</div>

<div class="panel">
    <h3>Componentes de la Fórmula</h3>
    <table>
        <thead>
            <tr><th>#</th><th>Medicamento</th><th>Presentación</th><th class="num">Dosis</th><th>Unidad</th><th>Observación</th></tr>
        </thead>
        <tbody>
            @forelse($formula->detalles as $d)
            <tr>
                <td>{{ $d->orden_preparacion }}</td>
                <td><strong>{{ $d->medicamento->nombre ?? '—' }}</strong></td>
                <td>{{ $d->presentacion->nombre ?? '—' }}</td>
                <td class="num">{{ rtrim(rtrim(number_format($d->dosis, 4, '.', ''), '0'), '.') }}</td>
                <td>{{ $d->unidadMedida->nombre ?? ($d->medicamento->unidad_medida ?? '—') }}</td>
                <td>{{ $d->observaciones ?? '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center; padding:1.5rem; color:#94a3b8">Sin componentes definidos</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="panel">
    <h3>Impacto Estimado en Inventario (1 preparación)</h3>
    <table>
        <thead>
            <tr><th>Medicamento</th><th class="num">Dosis</th><th>Unidad</th><th class="num">Stock Actual</th><th>Disponibilidad</th></tr>
        </thead>
        <tbody>
            @foreach($impacto as $i)
            <tr>
                <td>{{ $i['medicamento'] }}</td>
                <td class="num">{{ rtrim(rtrim(number_format($i['dosis'], 4, '.', ''), '0'), '.') }}</td>
                <td>{{ $i['unidad'] }}</td>
                <td class="num">{{ number_format($i['stock_actual'], 2) }}</td>
                <td>
                    @if($i['stock_actual'] >= $i['dosis'])
                        <span class="alert alert-ok">&#10003; Disponible</span>
                    @else
                        <span class="alert alert-warn">&#9888; Stock insuficiente</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($formula->observaciones)
<div class="panel">
    <h3>Observaciones</h3>
    <p style="margin:0; white-space:pre-line; color:#475569;">{{ $formula->observaciones }}</p>
</div>
@endif

<form method="POST" action="{{ route('admin.formulas.destroy', $formula) }}" onsubmit="return confirm('¿Eliminar esta fórmula? Esta acción no se puede deshacer.')" style="margin-top:1rem">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">&#128465; Eliminar Fórmula</button>
</form>
</x-app-layout>
