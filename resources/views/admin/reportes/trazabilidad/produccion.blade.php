<x-app-layout>
@include('admin.reportes.trazabilidad._styles')

@php
    $rutaTipo = strtolower($tipo);
    $iconos = ['mezcla'=>'🧪','preparacion'=>'💉','reempaque'=>'📋'];
    $colores = ['mezcla'=>['#6366f1','#4338ca'],'preparacion'=>['#06b6d4','#0e7490'],'reempaque'=>['#f59e0b','#b45309']];
    $ic = $iconos[$rutaTipo] ?? '📄';
    $col = $colores[$rutaTipo] ?? ['#7c3aed','#5b21b6'];
@endphp

<div class="tz-page">
    <div class="tz-crumbs">
        <a href="{{ route('admin.reportes.trazabilidad') }}">← Trazabilidad</a> / {{ ucfirst($rutaTipo) }} {{ $documento->codigo }}
    </div>

    <div class="tz-hdr" style="background:linear-gradient(135deg,{{ $col[0] }} 0%, {{ $col[1] }} 100%); box-shadow:0 8px 25px {{ $col[0] }}40;">
        <div>
            <h1>{{ $ic }} {{ ucfirst($rutaTipo) }} {{ $documento->codigo }}</h1>
            <p>{{ $documento->fecha_programada }} · {{ $documento->estado }}</p>
        </div>
        <a href="{{ route('admin.reportes.trazabilidad') }}" class="tz-btn tz-btn-ghost">← Volver</a>
    </div>

    <div class="tz-card">
        <h3><span class="icon">📋</span> Información</h3>
        <div class="tz-fields">
            <div class="tz-field"><div class="lbl">Tipo</div><div class="val"><span class="tz-pill purple">{{ $tipo }}</span></div></div>
            <div class="tz-field"><div class="lbl">Código</div><div class="val mono">{{ $documento->codigo }}</div></div>
            <div class="tz-field"><div class="lbl">Fecha programada</div><div class="val">{{ $documento->fecha_programada }}</div></div>
            <div class="tz-field"><div class="lbl">Estado</div><div class="val"><span class="tz-pill purple">{{ $documento->estado }}</span></div></div>
            @if($tipo==='PREPARACION' && !empty($documento->paciente_id))
                <div class="tz-field"><div class="lbl">Paciente</div><div class="val"><a href="{{ route('admin.reportes.trazabilidad.paciente', $documento->paciente_id) }}" style="color:var(--tz);">{{ $documento->paciente }}</a><small>{{ $documento->documento }}</small></div></div>
                <div class="tz-field"><div class="lbl">Servicio</div><div class="val">{{ $documento->servicio ?? '—' }}</div></div>
            @endif
            <div class="tz-field"><div class="lbl">Costo total</div><div class="val">${{ number_format($documento->costo_total ?? 0, 2) }}</div></div>
        </div>
    </div>

    <div class="tz-card">
        <h3><span class="icon">📦</span> Lotes consumidos <span class="pill">{{ $consumos->count() }}</span></h3>
        @if($consumos->isEmpty())<div class="tz-empty">Sin consumos registrados</div>@else
        <table class="tz-t">
            <thead><tr><th>Lote</th><th>Medicamento</th><th>Vencimiento</th><th class="num">Cantidad</th><th class="num">Costo unit.</th><th class="num">Costo total</th></tr></thead>
            <tbody>
            @foreach($consumos as $c)
            <tr>
                <td>@if($c->inventario_lote_id)<a href="{{ route('admin.reportes.trazabilidad.lote', $c->inventario_lote_id) }}" style="font-family:'Consolas',monospace;">{{ $c->lote_codigo ?? $c->lote ?? '—' }}</a>@else <span style="font-family:'Consolas',monospace;">{{ $c->lote ?? '—' }}</span>@endif</td>
                <td>{{ $c->medicamento ?? '—' }}</td>
                <td>{{ $c->fecha_vencimiento ?? '—' }}</td>
                <td class="num">{{ $c->cantidad_consumida }}</td>
                <td class="num">${{ number_format($c->costo_unitario ?? 0, 2) }}</td>
                <td class="num">${{ number_format($c->costo_total ?? 0, 2) }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
</x-app-layout>
