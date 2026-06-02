<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #06b6d4 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.9; font-size:.85rem; }

    .panel { background:#fff; border-radius:12px; padding:1.5rem; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1.2rem; }
    .panel h3 { margin:0 0 1rem 0; font-size:1rem; color:var(--inst); border-left:4px solid #06b6d4; padding-left:.6rem; }

    .sim-form { display:flex; gap:1rem; align-items:end; }
    .sim-form .field { display:flex; flex-direction:column; gap:.3rem; flex:1; max-width:260px; }
    .sim-form label { font-size:.78rem; color:#475569; font-weight:600; }
    .sim-form input { border:1.5px solid #e2e8f0; border-radius:7px; padding:.6rem .9rem; font-size:1.2rem; font-weight:700; text-align:center; }
    .btn { padding:.7rem 1.5rem; border-radius:8px; border:none; font-size:.9rem; font-weight:600; cursor:pointer; text-decoration:none; }
    .btn-primary { background:#06b6d4; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }

    table { width:100%; border-collapse:collapse; font-size:.9rem; }
    table th { background:var(--inst); color:#fff; padding:.7rem .9rem; text-align:left; }
    table td { padding:.65rem .9rem; border-bottom:1px solid #f1f5f9; }
    .num { text-align:right; font-family:monospace; }
    .row-warn { background:#fef2f2; }

    .alert-pill { display:inline-block; padding:.25rem .7rem; border-radius:14px; font-size:.72rem; font-weight:600; }
    .alert-ok { background:#dcfce7; color:#166534; }
    .alert-warn { background:#fee2e2; color:#991b1b; }

    .summary { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; margin-bottom:1.2rem; }
    .summary .box { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid #06b6d4; }
    .summary .box .lbl { font-size:.7rem; color:#64748b; text-transform:uppercase; }
    .summary .box .val { font-size:1.6rem; font-weight:800; color:#0f172a; }
    .summary .box.warn { border-color:#ef4444; }
    .summary .box.warn .val { color:#991b1b; }
</style>

<div class="page-header">
    <div>
        <h1>&#128202; Simulador de Consumo</h1>
        <p>{{ $formula->nombre }} <span style="opacity:.7">·</span> <code>{{ $formula->codigo }}</code></p>
    </div>
    <div style="display:flex; gap:.5rem">
        <a href="{{ route('admin.formulas.show', $formula) }}" class="btn btn-secondary">&#8592; Ver Fórmula</a>
        <a href="{{ route('admin.formulas.index') }}" class="btn btn-secondary">Catálogo</a>
    </div>
</div>

<div class="panel">
    <h3>Parámetros de Simulación</h3>
    <form method="GET" action="{{ route('admin.formulas.simulador', $formula) }}" class="sim-form">
        <div class="field">
            <label>Cantidad de pacientes / preparaciones</label>
            <input type="number" name="pacientes" min="1" value="{{ $cantidad }}">
        </div>
        <button type="submit" class="btn btn-primary">&#9881; Simular</button>
    </form>
    <p style="margin-top:.7rem; color:#64748b; font-size:.82rem;">El simulador calcula cuánto inventario se consumiría si se ejecutaran <strong>{{ $cantidad }}</strong> preparaciones de esta fórmula sin afectar el stock real.</p>
</div>

@php
    $totalAlertas = collect($simulacion)->where('alerta', true)->count();
    $totalConsumo = collect($simulacion)->sum('consumo_total');
@endphp

<div class="summary">
    <div class="box"><div class="lbl">Pacientes simulados</div><div class="val">{{ $cantidad }}</div></div>
    <div class="box"><div class="lbl">Componentes evaluados</div><div class="val">{{ count($simulacion) }}</div></div>
    <div class="box"><div class="lbl">Unidades totales requeridas</div><div class="val">{{ number_format($totalConsumo, 2) }}</div></div>
    <div class="box {{ $totalAlertas > 0 ? 'warn' : '' }}"><div class="lbl">Alertas de stock</div><div class="val">{{ $totalAlertas }}</div></div>
</div>

<div class="panel">
    <h3>Impacto Proyectado en Inventario</h3>
    <table>
        <thead>
            <tr>
                <th>Medicamento</th>
                <th class="num">Dosis x preparación</th>
                <th>Unidad</th>
                <th class="num">Consumo total</th>
                <th class="num">Stock actual</th>
                <th class="num">Stock proyectado</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($simulacion as $s)
            <tr class="{{ $s['alerta'] ? 'row-warn' : '' }}">
                <td><strong>{{ $s['medicamento'] }}</strong></td>
                <td class="num">{{ rtrim(rtrim(number_format($s['dosis_unitaria'], 4, '.', ''), '0'), '.') }}</td>
                <td>{{ $s['unidad'] }}</td>
                <td class="num"><strong>{{ number_format($s['consumo_total'], 2) }}</strong></td>
                <td class="num">{{ number_format($s['stock_actual'], 2) }}</td>
                <td class="num"><strong style="color:{{ $s['alerta'] ? '#991b1b' : '#166534' }}">{{ number_format($s['stock_proyectado'], 2) }}</strong></td>
                <td>
                    @if($s['alerta'])
                        <span class="alert-pill alert-warn">&#9888; FALTANTE</span>
                    @else
                        <span class="alert-pill alert-ok">&#10003; SUFICIENTE</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($totalAlertas > 0)
<div class="panel" style="border-left:5px solid #ef4444;">
    <h3 style="border-color:#ef4444;">&#9888; Recomendación</h3>
    <p style="margin:0; color:#475569; line-height:1.5;">Hay <strong>{{ $totalAlertas }}</strong> componente(s) con stock insuficiente para esta simulación. Se recomienda planificar compras o reducir la cantidad de pacientes antes de iniciar las preparaciones reales.</p>
</div>
@endif
</x-app-layout>
