<x-app-layout>
@include('admin.reportes.trazabilidad._styles')

@php $fmt = fn($v, $d=0) => number_format((float)$v, $d, ',', '.'); @endphp

<div class="tz-page">
    <div class="tz-crumbs">
        <a href="{{ route('admin.reportes.trazabilidad') }}">← Trazabilidad</a> / Lote {{ $lote->lote }}
    </div>

    <div class="tz-hdr">
        <div>
            <h1>Trazabilidad 360° — Lote <span style="font-family:'Consolas',monospace;">{{ $lote->lote }}</span></h1>
            <p>{{ $lote->medicamento }}</p>
        </div>
        <div class="tz-hdr-actions">
            @if($lote->bloqueado_incidente)
                <span class="tz-btn tz-btn-recall" style="cursor:default;">🔒 BLOQUEADO POR INCIDENTE</span>
            @endif
            <a href="{{ route('admin.reportes.trazabilidad') }}" class="tz-btn tz-btn-ghost">← Volver</a>
        </div>
    </div>

    {{-- Ficha del lote --}}
    <div class="tz-card">
        <h3><span class="icon">📦</span> Ficha del lote</h3>
        <div class="tz-fields">
            <div class="tz-field"><div class="lbl">Medicamento</div><div class="val">{{ $lote->medicamento }}<small>{{ $lote->medicamento_codigo }}</small></div></div>
            <div class="tz-field"><div class="lbl">Lote</div><div class="val mono">{{ $lote->lote }}</div></div>
            <div class="tz-field"><div class="lbl">Proveedor</div><div class="val">{{ $lote->proveedor ?? $lote->proveedor_razon ?? '—' }}</div></div>
            <div class="tz-field"><div class="lbl">Fecha ingreso</div><div class="val">{{ $lote->fecha_ingreso }}</div></div>
            <div class="tz-field"><div class="lbl">Vencimiento</div><div class="val">{{ $lote->fecha_vencimiento }}</div></div>
            <div class="tz-field"><div class="lbl">Stock</div><div class="val"><strong>{{ $fmt($lote->cantidad_actual) }}</strong> / {{ $fmt($lote->cantidad_inicial) }}</div></div>
            <div class="tz-field"><div class="lbl">Ubicación</div><div class="val">{{ $lote->ubicacion ?: '—' }}</div></div>
            <div class="tz-field"><div class="lbl">Estado calidad</div><div class="val">
                @php $ec=$lote->estado_calidad; $cls=$ec==='APROBADO'?'ok':($ec==='BLOQUEADO'?'crit':'warn'); @endphp
                <span class="tz-pill {{ $cls }}">{{ $ec ?: 'PENDIENTE' }}</span>
            </div></div>
            @if($lote->equipo_codigo)
            <div class="tz-field"><div class="lbl">Cadena de frío</div><div class="val">
                <a href="{{ route('admin.reportes.trazabilidad.equipo', $lote->equipo_cadena_frio_id) }}" style="color:var(--tz);">❄ {{ $lote->equipo_codigo }} — {{ $lote->equipo_nombre }}</a>
            </div></div>
            @endif
        </div>
    </div>

    {{-- KPIs del lote --}}
    <div class="tz-kpis">
        <div class="tz-kpi"><span class="ic">↔</span><div class="lbl">Movimientos</div><div class="val">{{ $kpis['movimientos'] }}</div></div>
        <div class="tz-kpi indigo"><span class="ic">🧪</span><div class="lbl">Mezclas</div><div class="val">{{ $kpis['mezclas'] }}</div></div>
        <div class="tz-kpi cyan"><span class="ic">💉</span><div class="lbl">Preparaciones</div><div class="val">{{ $kpis['preparaciones'] }}</div></div>
        <div class="tz-kpi amber"><span class="ic">📋</span><div class="lbl">Reempaques</div><div class="val">{{ $kpis['reempaques'] }}</div></div>
        <div class="tz-kpi pink"><span class="ic">📤</span><div class="lbl">Dispensaciones</div><div class="val">{{ $kpis['dispensaciones'] }}</div></div>
        <div class="tz-kpi ok"><span class="ic">👤</span><div class="lbl">Pacientes únicos</div><div class="val">{{ $kpis['pacientes'] }}</div></div>
        <div class="tz-kpi @if($kpis['incidentes']>0) crit @endif"><span class="ic">⚠</span><div class="lbl">Incidentes</div><div class="val">{{ $kpis['incidentes'] }}</div></div>
    </div>

    {{-- Árbol de relaciones --}}
    <div class="tz-card">
        <h3><span class="icon">🌳</span> Árbol de trazabilidad</h3>
        <div class="tz-tree">
<span class="node">📦 Lote {{ $lote->lote }}</span> <span class="meta">— {{ $lote->medicamento }}</span>
        │
        ├── 🧪 Mezclas              <span class="num">{{ $kpis['mezclas'] }}</span>
        ├── 💉 Preparaciones        <span class="num">{{ $kpis['preparaciones'] }}</span>
        ├── 📋 Reempaques           <span class="num">{{ $kpis['reempaques'] }}</span>
        ├── 📤 Dispensaciones       <span class="num">{{ $kpis['dispensaciones'] }}</span>
        ├── <span class="leaf">👤 Pacientes únicos</span>     <span class="num">{{ $kpis['pacientes'] }}</span>
        └── ⚠  Incidentes asociados  <span class="num">{{ $kpis['incidentes'] }}</span>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tz-card">
        <div class="tz-tabs">
            <button class="active" data-tab="movs">↔ Movimientos <span class="cnt">{{ $movimientos->count() }}</span></button>
            <button data-tab="mezclas">🧪 Mezclas <span class="cnt">{{ $mezclas->count() }}</span></button>
            <button data-tab="preps">💉 Preparaciones <span class="cnt">{{ $preparaciones->count() }}</span></button>
            <button data-tab="reemps">📋 Reempaques <span class="cnt">{{ $reempaques->count() }}</span></button>
            <button data-tab="pacs">👤 Pacientes <span class="cnt">{{ $pacientes->count() }}</span></button>
            <button data-tab="incs">⚠ Incidentes <span class="cnt">{{ $incidentes->count() }}</span></button>
            @if($controles->isNotEmpty())<button data-tab="ccs">✓ Calidad <span class="cnt">{{ $controles->count() }}</span></button>@endif
        </div>

        <div id="tab-movs" class="tz-tab-pane active">
            @if($movimientos->isEmpty())<div class="tz-empty">Sin movimientos</div>@else
            <table class="tz-t">
                <thead><tr><th>Fecha</th><th>Tipo</th><th class="num">Cantidad</th><th class="num">Stock anterior</th><th class="num">Stock nuevo</th><th>Observación</th></tr></thead>
                <tbody>
                @foreach($movimientos as $m)
                <tr>
                    <td>{{ $m->fecha_movimiento }}</td>
                    <td><span class="tz-pill {{ str_contains($m->tipo_movimiento,'SALIDA')||$m->tipo_movimiento==='DISPENSACION'?'warn':'info' }}">{{ $m->tipo_movimiento }}</span></td>
                    <td class="num">{{ $m->cantidad }}</td>
                    <td class="num">{{ $m->stock_anterior }}</td>
                    <td class="num">{{ $m->stock_nuevo }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($m->observacion, 60) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div id="tab-mezclas" class="tz-tab-pane">
            @if($mezclas->isEmpty())<div class="tz-empty">Este lote no se ha usado en mezclas</div>@else
            <table class="tz-t">
                <thead><tr><th>Código</th><th>Fecha</th><th>Estado</th><th class="num">Cantidad</th><th class="num">Costo</th></tr></thead>
                <tbody>
                @foreach($mezclas as $m)
                <tr>
                    <td><a href="{{ route('admin.reportes.trazabilidad.mezcla', $m->id) }}">{{ $m->codigo }}</a></td>
                    <td>{{ $m->fecha_programada }}</td>
                    <td><span class="tz-pill purple">{{ $m->estado }}</span></td>
                    <td class="num">{{ $m->cantidad_consumida }}</td>
                    <td class="num">${{ number_format($m->costo_total ?? 0, 2) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div id="tab-preps" class="tz-tab-pane">
            @if($preparaciones->isEmpty())<div class="tz-empty">Este lote no se ha usado en preparaciones</div>@else
            <table class="tz-t">
                <thead><tr><th>Código</th><th>Fecha</th><th>Estado</th><th>Paciente</th><th class="num">Cantidad</th><th class="num">Costo</th></tr></thead>
                <tbody>
                @foreach($preparaciones as $p)
                <tr>
                    <td><a href="{{ route('admin.reportes.trazabilidad.preparacion', $p->id) }}">{{ $p->codigo }}</a></td>
                    <td>{{ $p->fecha_programada }}</td>
                    <td><span class="tz-pill purple">{{ $p->estado }}</span></td>
                    <td>@if($p->paciente_id)<a href="{{ route('admin.reportes.trazabilidad.paciente', $p->paciente_id) }}">{{ $p->paciente }}</a><br><small style="color:#94a3b8;">{{ $p->documento }}</small>@else <span style="color:#9ca3af;">—</span> @endif</td>
                    <td class="num">{{ $p->cantidad_consumida }}</td>
                    <td class="num">${{ number_format($p->costo_total ?? 0, 2) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div id="tab-reemps" class="tz-tab-pane">
            @if($reempaques->isEmpty())<div class="tz-empty">Este lote no se ha usado en reempaques</div>@else
            <table class="tz-t">
                <thead><tr><th>Código</th><th>Fecha</th><th>Estado</th><th class="num">Cantidad</th><th class="num">Costo</th></tr></thead>
                <tbody>
                @foreach($reempaques as $r)
                <tr>
                    <td><a href="{{ route('admin.reportes.trazabilidad.reempaque', $r->id) }}">{{ $r->codigo }}</a></td>
                    <td>{{ $r->fecha_programada }}</td>
                    <td><span class="tz-pill purple">{{ $r->estado }}</span></td>
                    <td class="num">{{ $r->cantidad_consumida }}</td>
                    <td class="num">${{ number_format($r->costo_total ?? 0, 2) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div id="tab-pacs" class="tz-tab-pane">
            @if($pacientes->isEmpty())<div class="tz-empty">Este lote aún no se ha dispensado</div>@else
            <table class="tz-t">
                <thead><tr><th>Fecha</th><th>Entrega</th><th>Paciente</th><th>Documento</th><th>Servicio</th><th class="num">Cantidad</th><th class="num">Costo unit.</th></tr></thead>
                <tbody>
                @foreach($pacientes as $p)
                <tr>
                    <td>{{ $p->fecha_entrega }}</td>
                    <td>{{ $p->entrega_codigo }}</td>
                    <td>@if($p->paciente_id)<a href="{{ route('admin.reportes.trazabilidad.paciente', $p->paciente_id) }}">{{ $p->paciente }}</a>@else <span style="color:#9ca3af;">— ({{ $p->tipo_entrega }})</span> @endif</td>
                    <td>{{ $p->documento ?? '—' }}</td>
                    <td>{{ $p->servicio ?? '—' }}</td>
                    <td class="num">{{ $p->cantidad_entregada }}</td>
                    <td class="num">${{ number_format($p->costo_unitario ?? 0, 2) }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div id="tab-incs" class="tz-tab-pane">
            @if($incidentes->isEmpty())<div class="tz-empty">Sin incidentes asociados</div>@else
            <table class="tz-t">
                <thead><tr><th>Código</th><th>Fecha</th><th>Tipo</th><th>Severidad</th><th>Estado</th></tr></thead>
                <tbody>
                @foreach($incidentes as $i)
                <tr>
                    <td><a href="{{ route('admin.reportes.trazabilidad.incidente', $i->id) }}">{{ $i->codigo }}</a></td>
                    <td>{{ $i->fecha_incidente }}</td>
                    <td>{{ $i->tipo_incidente }}</td>
                    <td><span class="tz-pill {{ $i->severidad==='CRITICA'?'crit':($i->severidad==='ALTA'?'warn':'info') }}">{{ $i->severidad }}</span></td>
                    <td><span class="tz-pill purple">{{ $i->estado }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        @if($controles->isNotEmpty())
        <div id="tab-ccs" class="tz-tab-pane">
            <table class="tz-t">
                <thead><tr><th>Código</th><th>Fecha</th><th>Tipo</th><th>Resultado</th></tr></thead>
                <tbody>
                @foreach($controles as $c)
                <tr>
                    <td>{{ $c->codigo ?? '—' }}</td>
                    <td>{{ $c->fecha_control ?? '—' }}</td>
                    <td>{{ $c->tipo_control ?? '—' }}</td>
                    <td><span class="tz-pill {{ ($c->resultado??'')==='APROBADO'?'ok':'warn' }}">{{ $c->resultado ?? '—' }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.querySelectorAll('.tz-tabs button').forEach(b=>{
    b.addEventListener('click', ()=>{
        document.querySelectorAll('.tz-tabs button').forEach(x=>x.classList.remove('active'));
        document.querySelectorAll('.tz-tab-pane').forEach(x=>x.classList.remove('active'));
        b.classList.add('active');
        document.getElementById('tab-'+b.dataset.tab).classList.add('active');
    });
});
</script>
@endpush
</x-app-layout>
