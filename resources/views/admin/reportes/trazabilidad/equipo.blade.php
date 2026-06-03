<x-app-layout>
@include('admin.reportes.trazabilidad._styles')

<div class="tz-page">
    <div class="tz-crumbs">
        <a href="{{ route('admin.reportes.trazabilidad') }}">← Trazabilidad</a> / Equipo {{ $equipo->codigo }}
    </div>

    <div class="tz-hdr" style="background:linear-gradient(135deg,#06b6d4 0%, #0e7490 100%); box-shadow:0 8px 25px rgba(6,182,212,.28);">
        <div>
            <h1>❄ Cadena de frío — {{ $equipo->nombre }}</h1>
            <p>Código: {{ $equipo->codigo }} · {{ $equipo->ubicacion ?? 'Sin ubicación' }} · Rango {{ $equipo->temperatura_min }}°C – {{ $equipo->temperatura_max }}°C</p>
        </div>
        <a href="{{ route('admin.reportes.trazabilidad') }}" class="tz-btn tz-btn-ghost">← Volver</a>
    </div>

    <div class="tz-card">
        <h3><span class="icon">❄</span> Información del equipo</h3>
        <div class="tz-fields">
            <div class="tz-field"><div class="lbl">Código</div><div class="val mono">{{ $equipo->codigo }}</div></div>
            <div class="tz-field"><div class="lbl">Nombre</div><div class="val">{{ $equipo->nombre }}</div></div>
            <div class="tz-field"><div class="lbl">Tipo</div><div class="val">{{ $equipo->tipo }}</div></div>
            <div class="tz-field"><div class="lbl">Ubicación</div><div class="val">{{ $equipo->ubicacion }}</div></div>
            <div class="tz-field"><div class="lbl">Rango Temperatura</div><div class="val">{{ $equipo->temperatura_min }}°C – {{ $equipo->temperatura_max }}°C</div></div>
            <div class="tz-field"><div class="lbl">Estado</div><div class="val"><span class="tz-pill {{ $equipo->estado==='ACTIVO'?'ok':'warn' }}">{{ $equipo->estado }}</span></div></div>
        </div>
    </div>

    <div class="tz-kpis">
        <div class="tz-kpi"><span class="ic">📦</span><div class="lbl">Lotes en equipo</div><div class="val">{{ $kpis['lotes'] }}</div></div>
        <div class="tz-kpi @if($kpis['alertas']>0) warn @endif"><span class="ic">⚠</span><div class="lbl">Alertas</div><div class="val">{{ $kpis['alertas'] }}</div></div>
        <div class="tz-kpi cyan"><span class="ic">🌡</span><div class="lbl">Lecturas</div><div class="val">{{ number_format($kpis['lecturas']) }}</div></div>
        <div class="tz-kpi pink"><span class="ic">👤</span><div class="lbl">Pacientes potenciales</div><div class="val">{{ $kpis['pacientes'] }}</div></div>
    </div>

    <div class="tz-row22">
        <div class="tz-card">
            <h3><span class="icon">📦</span> Lotes almacenados <span class="pill">{{ $lotes->count() }}</span></h3>
            @if($lotes->isEmpty())<div class="tz-empty">Sin lotes</div>@else
            <table class="tz-t">
                <thead><tr><th>Lote</th><th>Medicamento</th><th class="num">Stock</th><th>Vencimiento</th></tr></thead>
                <tbody>
                @foreach($lotes as $l)
                <tr>
                    <td><a href="{{ route('admin.reportes.trazabilidad.lote', $l->id) }}" style="font-family:'Consolas',monospace;">{{ $l->lote }}</a></td>
                    <td>{{ \Illuminate\Support\Str::limit($l->medicamento, 30) }}</td>
                    <td class="num">{{ $l->cantidad_actual }}</td>
                    <td>{{ $l->fecha_vencimiento }}</td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>

        <div class="tz-card">
            <h3><span class="icon">⚠</span> Alertas registradas <span class="pill">{{ $alertas->count() }}</span></h3>
            @if($alertas->isEmpty())<div class="tz-empty">Sin alertas</div>@else
            <table class="tz-t">
                <thead><tr><th>Inicio</th><th>Severidad</th><th>Temp.</th><th>Estado</th></tr></thead>
                <tbody>
                @foreach($alertas as $a)
                <tr>
                    <td>{{ $a->fecha_inicio }}</td>
                    <td><span class="tz-pill {{ $a->severidad==='CRITICA'?'crit':($a->severidad==='ALTA'?'warn':'info') }}">{{ $a->severidad }}</span></td>
                    <td>{{ $a->temperatura_registrada }}°C</td>
                    <td><span class="tz-pill {{ $a->estado==='ACTIVA'?'crit':'gray' }}">{{ $a->estado }}</span></td>
                </tr>
                @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>

    @if($pacientesAfectados->isNotEmpty())
    <div class="tz-card">
        <h3><span class="icon">👤</span> Pacientes potencialmente afectados <span class="pill">{{ $pacientesAfectados->pluck('paciente_id')->unique()->count() }} únicos</span></h3>
        <table class="tz-t">
            <thead><tr><th>Fecha</th><th>Paciente</th><th>Documento</th><th>Lote</th><th>Medicamento</th></tr></thead>
            <tbody>
            @foreach($pacientesAfectados as $p)
            <tr>
                <td>{{ $p->fecha_entrega }}</td>
                <td><a href="{{ route('admin.reportes.trazabilidad.paciente', $p->paciente_id) }}">{{ $p->paciente }}</a></td>
                <td>{{ $p->documento }}</td>
                <td style="font-family:'Consolas',monospace;">{{ $p->lote }}</td>
                <td>{{ $p->medicamento }}</td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
</x-app-layout>
