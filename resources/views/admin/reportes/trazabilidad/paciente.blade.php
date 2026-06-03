<x-app-layout>
@include('admin.reportes.trazabilidad._styles')

@php $fmt = fn($v, $d=0) => number_format((float)$v, $d, ',', '.'); @endphp

<div class="tz-page">
    <div class="tz-crumbs">
        <a href="{{ route('admin.reportes.trazabilidad') }}">← Trazabilidad</a> / Paciente
    </div>

    <div class="tz-hdr">
        <div>
            <h1>👤 {{ $paciente->nombres }} {{ $paciente->apellidos }}</h1>
            <p>{{ $paciente->tipo_documento }} {{ $paciente->documento }} · {{ $paciente->servicio ?? 'Sin servicio' }}</p>
        </div>
        <div class="tz-hdr-actions">
            <a href="{{ route('admin.reportes.trazabilidad') }}" class="tz-btn tz-btn-ghost">← Volver</a>
        </div>
    </div>

    <div class="tz-card">
        <h3><span class="icon">🪪</span> Información del paciente</h3>
        <div class="tz-fields">
            <div class="tz-field"><div class="lbl">Documento</div><div class="val mono">{{ $paciente->tipo_documento }} {{ $paciente->documento }}</div></div>
            <div class="tz-field"><div class="lbl">Nombre completo</div><div class="val">{{ $paciente->nombres }} {{ $paciente->apellidos }}</div></div>
            <div class="tz-field"><div class="lbl">Sexo</div><div class="val">{{ $paciente->sexo ?? '—' }}</div></div>
            <div class="tz-field"><div class="lbl">Servicio</div><div class="val">{{ $paciente->servicio ?? '—' }}</div></div>
            <div class="tz-field"><div class="lbl">Cama</div><div class="val">{{ $paciente->cama ?? '—' }}</div></div>
            <div class="tz-field"><div class="lbl">EPS</div><div class="val">{{ $paciente->eps ?? '—' }}</div></div>
        </div>
    </div>

    <div class="tz-kpis">
        <div class="tz-kpi"><span class="ic">📦</span><div class="lbl">Lotes recibidos</div><div class="val">{{ $kpis['lotes'] }}</div></div>
        <div class="tz-kpi indigo"><span class="ic">💊</span><div class="lbl">Medicamentos</div><div class="val">{{ $kpis['medicamentos'] }}</div></div>
        <div class="tz-kpi cyan"><span class="ic">💉</span><div class="lbl">Preparaciones</div><div class="val">{{ $kpis['preparaciones'] }}</div></div>
        <div class="tz-kpi pink"><span class="ic">📤</span><div class="lbl">Entregas</div><div class="val">{{ $kpis['entregas'] }}</div></div>
    </div>

    <div class="tz-card">
        <h3><span class="icon">📦</span> Lotes recibidos <span class="pill">{{ $lotes->count() }}</span></h3>
        @if($lotes->isEmpty())<div class="tz-empty">Este paciente no tiene lotes asociados</div>@else
        <table class="tz-t">
            <thead><tr><th>Fecha</th><th>Lote</th><th>Medicamento</th><th>Servicio</th><th class="num">Cantidad</th><th>Entrega</th></tr></thead>
            <tbody>
            @foreach($lotes as $l)
            <tr>
                <td>{{ $l->fecha_entrega }}</td>
                <td><a href="{{ route('admin.reportes.trazabilidad.lote', $l->lote_id) }}" style="font-family:'Consolas',monospace;">{{ $l->lote }}</a></td>
                <td>{{ $l->medicamento }}</td>
                <td>{{ $l->servicio ?? '—' }}</td>
                <td class="num">{{ $l->cantidad_entregada }}</td>
                <td>{{ $l->entrega_codigo }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="tz-row22">
        <div class="tz-card">
            <h3><span class="icon">💉</span> Preparaciones del paciente</h3>
            @if($preparaciones->isEmpty())<div class="tz-empty">Sin preparaciones</div>@else
            <table class="tz-t">
                <thead><tr><th>Código</th><th>Fecha</th><th>Estado</th></tr></thead>
                <tbody>
                @foreach($preparaciones as $p)
                <tr>
                    <td><a href="{{ route('admin.reportes.trazabilidad.preparacion', $p->id) }}">{{ $p->codigo }}</a></td>
                    <td>{{ $p->fecha_programada }}</td>
                    <td><span class="tz-pill purple">{{ $p->estado }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="tz-card">
            <h3><span class="icon">📤</span> Entregas registradas</h3>
            @if($entregas->isEmpty())<div class="tz-empty">Sin entregas</div>@else
            <table class="tz-t">
                <thead><tr><th>Código</th><th>Fecha</th><th>Tipo</th><th>Servicio</th></tr></thead>
                <tbody>
                @foreach($entregas as $e)
                <tr>
                    <td>{{ $e->codigo }}</td>
                    <td>{{ $e->fecha_entrega }}</td>
                    <td>{{ $e->tipo_entrega }}</td>
                    <td>{{ $e->servicio ?? '—' }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
