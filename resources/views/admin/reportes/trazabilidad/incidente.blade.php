<x-app-layout>
@include('admin.reportes.trazabilidad._styles')

<div class="tz-page">
    <div class="tz-crumbs">
        <a href="{{ route('admin.reportes.trazabilidad') }}">← Trazabilidad</a> / Incidente {{ $incidente->codigo }}
    </div>

    <div class="tz-hdr" style="background:linear-gradient(135deg,#dc2626 0%,#991b1b 100%); box-shadow:0 8px 25px rgba(220,38,38,.28);">
        <div>
            <h1>⚠ Mapa de impacto — Incidente {{ $incidente->codigo }}</h1>
            <p>{{ $incidente->tipo_incidente }} · Severidad {{ $incidente->severidad }} · {{ $incidente->fecha_incidente }}</p>
        </div>
        <div class="tz-hdr-actions">
            <a href="{{ route('admin.calidad.incidentes.show', $incidente->id) }}" class="tz-btn tz-btn-primary">Ver incidente completo →</a>
            <a href="{{ route('admin.reportes.trazabilidad') }}" class="tz-btn tz-btn-ghost">← Volver</a>
        </div>
    </div>

    <div class="tz-card">
        <h3><span class="icon">📋</span> Información del incidente</h3>
        <div class="tz-fields">
            <div class="tz-field"><div class="lbl">Código</div><div class="val mono">{{ $incidente->codigo }}</div></div>
            <div class="tz-field"><div class="lbl">Fecha</div><div class="val">{{ $incidente->fecha_incidente }}</div></div>
            <div class="tz-field"><div class="lbl">Tipo</div><div class="val">{{ $incidente->tipo_incidente }}</div></div>
            <div class="tz-field"><div class="lbl">Severidad</div><div class="val"><span class="tz-pill {{ $incidente->severidad==='CRITICA'?'crit':($incidente->severidad==='ALTA'?'warn':'info') }}">{{ $incidente->severidad }}</span></div></div>
            <div class="tz-field"><div class="lbl">Estado</div><div class="val"><span class="tz-pill purple">{{ $incidente->estado }}</span></div></div>
        </div>
        @if($incidente->descripcion)
        <div style="margin-top:1rem; padding:1rem; background:#fef2f2; border-left:3px solid #dc2626; border-radius:6px; font-size:.88rem; color:#7f1d1d;">{{ $incidente->descripcion }}</div>
        @endif
    </div>

    <div class="tz-kpis">
        <div class="tz-kpi crit"><span class="ic">📦</span><div class="lbl">Lotes afectados</div><div class="val">{{ $kpis['lotes_afectados'] }}</div></div>
        <div class="tz-kpi indigo"><span class="ic">🧪</span><div class="lbl">Mezclas</div><div class="val">{{ $kpis['mezclas'] }}</div></div>
        <div class="tz-kpi cyan"><span class="ic">💉</span><div class="lbl">Preparaciones</div><div class="val">{{ $kpis['preparaciones'] }}</div></div>
        <div class="tz-kpi amber"><span class="ic">📋</span><div class="lbl">Reempaques</div><div class="val">{{ $kpis['reempaques'] }}</div></div>
        <div class="tz-kpi pink"><span class="ic">📤</span><div class="lbl">Entregas</div><div class="val">{{ $kpis['entregas'] }}</div></div>
        <div class="tz-kpi crit"><span class="ic">👤</span><div class="lbl">Pacientes directos</div><div class="val">{{ $kpis['pacientes_directos'] }}</div></div>
        <div class="tz-kpi warn"><span class="ic">⚠</span><div class="lbl">Pacientes indirectos</div><div class="val">{{ $kpis['pacientes_indirectos'] }}</div></div>
    </div>

    <div class="tz-card">
        <h3><span class="icon">🎯</span> Afectaciones registradas <span class="pill">{{ $afectaciones->count() }}</span></h3>
        @if($afectaciones->isEmpty())<div class="tz-empty">Sin afectaciones registradas</div>@else
        <table class="tz-t">
            <thead><tr><th>Lote</th><th>Medicamento</th><th>Mezcla</th><th>Preparación</th><th>Reempaque</th><th>Entrega</th><th>Paciente</th><th>Equipo CF</th></tr></thead>
            <tbody>
            @foreach($afectaciones as $a)
            <tr>
                <td>@if($a->inventario_lote_id)<a href="{{ route('admin.reportes.trazabilidad.lote', $a->inventario_lote_id) }}" style="font-family:'Consolas',monospace;">{{ $a->lote }}</a>@else <span style="color:#cbd5e1;">—</span> @endif</td>
                <td>{{ $a->medicamento ?? '—' }}</td>
                <td>{{ $a->mezcla_codigo ?? '—' }}</td>
                <td>{{ $a->preparacion_codigo ?? '—' }}</td>
                <td>{{ $a->reempaque_codigo ?? '—' }}</td>
                <td>{{ $a->entrega_codigo ?? '—' }}</td>
                <td>@if($a->paciente_id)<a href="{{ route('admin.reportes.trazabilidad.paciente', $a->paciente_id) }}">{{ $a->paciente }}</a>@else <span style="color:#cbd5e1;">—</span> @endif</td>
                <td>@if($a->equipo_cadena_frio_id)<a href="{{ route('admin.reportes.trazabilidad.equipo', $a->equipo_cadena_frio_id) }}">❄ {{ $a->equipo_codigo }}</a>@else <span style="color:#cbd5e1;">—</span> @endif</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>

    <div class="tz-card">
        <h3><span class="icon">👤</span> Pacientes potencialmente impactados <span class="pill">{{ $pacientesImpactados->count() }} entregas</span></h3>
        @if($pacientesImpactados->isEmpty())<div class="tz-empty">Ningún lote afectado tiene dispensaciones a pacientes</div>@else
        <table class="tz-t">
            <thead><tr><th>Fecha entrega</th><th>Paciente</th><th>Documento</th><th>Lote</th><th>Medicamento</th><th>Servicio</th><th class="num">Cantidad</th></tr></thead>
            <tbody>
            @foreach($pacientesImpactados as $p)
            <tr>
                <td>{{ $p->fecha_entrega }}</td>
                <td><a href="{{ route('admin.reportes.trazabilidad.paciente', $p->paciente_id) }}">{{ $p->paciente }}</a></td>
                <td>{{ $p->documento }}</td>
                <td style="font-family:'Consolas',monospace;">{{ $p->lote }}</td>
                <td>{{ $p->medicamento }}</td>
                <td>{{ $p->servicio ?? '—' }}</td>
                <td class="num">{{ $p->cantidad_entregada }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
</x-app-layout>
