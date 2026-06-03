<x-app-layout>
@include('admin.reportes.trazabilidad._styles')

@php $fmt = fn($v, $d=0) => number_format((float)$v, $d, ',', '.'); @endphp

<div class="tz-page">
    <div class="tz-crumbs">
        <a href="{{ route('admin.reportes.trazabilidad') }}">← Trazabilidad</a> / Recall INVIMA
    </div>

    <div class="tz-hdr" style="background:linear-gradient(135deg,#dc2626 0%,#991b1b 100%); box-shadow:0 8px 25px rgba(220,38,38,.28);">
        <div>
            <h1>🚨 Recall INVIMA</h1>
            <p>Búsqueda urgente — Identifica en segundos pacientes, preparaciones, mezclas y existencias de un lote</p>
        </div>
        <a href="{{ route('admin.reportes.trazabilidad') }}" class="tz-btn tz-btn-ghost">← Volver</a>
    </div>

    <div class="tz-search" style="border-color:#fca5a5; background:linear-gradient(135deg,#fff 0%, #fef2f2 100%);">
        <div class="tz-search-title" style="color:#991b1b;">
            <span class="ico" style="background:#dc2626;">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
            </span>
            Búsqueda por código de lote
        </div>
        <form action="{{ route('admin.reportes.trazabilidad.recall') }}" method="GET">
            <input type="text" name="lote" value="{{ $loteCodigo }}" placeholder="Ej: VCX-2026-001" autofocus
                style="border-color:#fca5a5; background:#fff url(&quot;data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='18' height='18' fill='none' stroke='%23dc2626' stroke-width='2' viewBox='0 0 24 24'><circle cx='11' cy='11' r='7'/><path d='M21 21l-4.3-4.3'/></svg>&quot;) no-repeat .85rem center;">
            <button type="submit" style="background:#dc2626;">Generar Recall</button>
        </form>
        <div class="hint">Ingrese el código del lote ordenado a retiro y obtenga toda la información de impacto.</div>
    </div>

    @if($loteCodigo && !$lote)
        <div class="tz-alert crit">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
            No se encontró el lote: <strong>{{ $loteCodigo }}</strong>
        </div>
    @elseif($lote)
        <div class="tz-card" style="border-left:4px solid #dc2626;">
            <h3 style="color:#991b1b;"><span class="icon" style="background:#fee2e2; color:#991b1b;">📦</span> Lote en evaluación</h3>
            <div class="tz-fields">
                <div class="tz-field"><div class="lbl">Lote</div><div class="val mono">{{ $lote->lote }}</div></div>
                <div class="tz-field"><div class="lbl">Medicamento</div><div class="val">{{ $lote->medicamento }}</div></div>
                <div class="tz-field"><div class="lbl">Proveedor</div><div class="val">{{ $lote->proveedor ?? '—' }}</div></div>
                <div class="tz-field"><div class="lbl">Vencimiento</div><div class="val">{{ $lote->fecha_vencimiento }}</div></div>
                <div class="tz-field"><div class="lbl">Estado</div><div class="val">{{ $stock['estado'] }} @if($stock['bloqueado'])<span class="tz-pill crit">🔒 BLOQUEADO</span>@endif</div></div>
            </div>
            <div style="margin-top:1rem;">
                <a href="{{ route('admin.reportes.trazabilidad.lote', $lote->id) }}" class="tz-btn tz-btn-out">Ver trazabilidad 360° →</a>
            </div>
        </div>

        <div class="tz-kpis">
            <div class="tz-kpi crit"><span class="ic">👤</span><div class="lbl">Pacientes a notificar</div><div class="val">{{ $pacientes->pluck('paciente_id')->unique()->count() }}</div></div>
            <div class="tz-kpi cyan"><span class="ic">💉</span><div class="lbl">Preparaciones</div><div class="val">{{ $preparaciones->count() }}</div></div>
            <div class="tz-kpi indigo"><span class="ic">🧪</span><div class="lbl">Mezclas</div><div class="val">{{ $mezclas->count() }}</div></div>
            <div class="tz-kpi amber"><span class="ic">📋</span><div class="lbl">Reempaques</div><div class="val">{{ $reempaques->count() }}</div></div>
            <div class="tz-kpi ok"><span class="ic">📦</span><div class="lbl">Stock restante</div><div class="val">{{ $fmt($stock['cantidad_actual']) }}</div></div>
            <div class="tz-kpi"><span class="ic">↘</span><div class="lbl">Consumido</div><div class="val">{{ $fmt($stock['consumido']) }}</div></div>
        </div>

        <div class="tz-card">
            <h3><span class="icon">📦</span> Existencias actuales</h3>
            <div class="tz-fields">
                <div class="tz-field"><div class="lbl">Cantidad inicial</div><div class="val">{{ $fmt($stock['cantidad_inicial']) }}</div></div>
                <div class="tz-field"><div class="lbl">Cantidad actual</div><div class="val" style="color:#dc2626;">{{ $fmt($stock['cantidad_actual']) }}</div></div>
                <div class="tz-field"><div class="lbl">Consumido</div><div class="val">{{ $fmt($stock['consumido']) }}</div></div>
                <div class="tz-field"><div class="lbl">Ubicación</div><div class="val">{{ $stock['ubicacion'] ?: '—' }}</div></div>
                <div class="tz-field"><div class="lbl">Estado calidad</div><div class="val">{{ $stock['estado_calidad'] ?? '—' }}</div></div>
            </div>
        </div>

        <div class="tz-card">
            <h3><span class="icon">👤</span> Pacientes que recibieron este lote <span class="pill">{{ $pacientes->count() }} entregas</span></h3>
            @if($pacientes->isEmpty())<div class="tz-empty">Aún no se ha dispensado a pacientes</div>@else
            <table class="tz-t">
                <thead><tr><th>Fecha</th><th>Paciente</th><th>Documento</th><th>Servicio</th><th>Entrega</th><th class="num">Cantidad</th></tr></thead>
                <tbody>
                @foreach($pacientes as $p)
                <tr>
                    <td>{{ $p->fecha_entrega }}</td>
                    <td>@if($p->paciente_id)<a href="{{ route('admin.reportes.trazabilidad.paciente', $p->paciente_id) }}">{{ $p->paciente }}</a>@else — @endif</td>
                    <td>{{ $p->documento ?? '—' }}</td>
                    <td>{{ $p->servicio ?? '—' }}</td>
                    <td>{{ $p->entrega_codigo }}</td>
                    <td class="num">{{ $p->cantidad_entregada }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="tz-row22">
            <div class="tz-card">
                <h3><span class="icon">💉</span> Preparaciones realizadas</h3>
                @if($preparaciones->isEmpty())<div class="tz-empty">Sin preparaciones</div>@else
                <table class="tz-t">
                    <thead><tr><th>Código</th><th>Fecha</th><th>Estado</th><th class="num">Cant.</th></tr></thead>
                    <tbody>
                    @foreach($preparaciones as $p)
                    <tr><td><a href="{{ route('admin.reportes.trazabilidad.preparacion', $p->id) }}">{{ $p->codigo }}</a></td><td>{{ $p->fecha_programada }}</td><td><span class="tz-pill purple">{{ $p->estado }}</span></td><td class="num">{{ $p->cantidad_consumida }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            </div>

            <div class="tz-card">
                <h3><span class="icon">🧪</span> Mezclas / Reempaques</h3>
                @if($mezclas->isEmpty() && $reempaques->isEmpty())<div class="tz-empty">Sin producción</div>@else
                <table class="tz-t">
                    <thead><tr><th>Tipo</th><th>Código</th><th>Fecha</th><th>Estado</th><th class="num">Cant.</th></tr></thead>
                    <tbody>
                    @foreach($mezclas as $m)
                    <tr><td><span class="tz-pill info">MEZCLA</span></td><td><a href="{{ route('admin.reportes.trazabilidad.mezcla', $m->id) }}">{{ $m->codigo }}</a></td><td>{{ $m->fecha_programada }}</td><td><span class="tz-pill purple">{{ $m->estado }}</span></td><td class="num">{{ $m->cantidad_consumida }}</td></tr>
                    @endforeach
                    @foreach($reempaques as $r)
                    <tr><td><span class="tz-pill info">REEMPAQUE</span></td><td><a href="{{ route('admin.reportes.trazabilidad.reempaque', $r->id) }}">{{ $r->codigo }}</a></td><td>{{ $r->fecha_programada }}</td><td><span class="tz-pill purple">{{ $r->estado }}</span></td><td class="num">{{ $r->cantidad_consumida }}</td></tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    @endif
</div>
</x-app-layout>
