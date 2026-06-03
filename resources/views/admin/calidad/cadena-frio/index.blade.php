<x-app-layout>
<style>
    :root { --inst:#0c4a6e; }
    .page-header { background: linear-gradient(135deg, #0ea5e9 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }

    .nav-tabs { display:flex; gap:.4rem; margin-bottom:1.2rem; flex-wrap:wrap; }
    .nav-tabs a { padding:.55rem 1rem; border-radius:8px; text-decoration:none; font-size:.82rem; font-weight:600; color:#475569; background:#fff; border:1px solid #e2e8f0; }
    .nav-tabs a.active { background:#0ea5e9; color:#fff; border-color:#0ea5e9; }

    .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:1rem; margin-bottom:1.2rem; }
    .kpi { background:#fff; border-radius:12px; padding:1rem 1.1rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid #0ea5e9; }
    .kpi.k-temp { border-left-color:#06b6d4; }
    .kpi.k-alert { border-left-color:#f59e0b; }
    .kpi.k-q { border-left-color:#a855f7; }
    .kpi.k-blk { border-left-color:#ef4444; }
    .kpi .lbl { font-size:.7rem; color:#94a3b8; text-transform:uppercase; font-weight:600; }
    .kpi .val { font-size:1.7rem; font-weight:700; color:#1e293b; margin-top:.2rem; }

    .grid-2 { display:grid; grid-template-columns: 2fr 1fr; gap:1.2rem; }
    @media(max-width:1100px){ .grid-2 { grid-template-columns:1fr; } }

    .card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1.2rem; }
    .card h3 { margin:0 0 .8rem; font-size:.95rem; color:#0f172a; }

    .equipos-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); gap:.9rem; }
    .equipo-card { padding:.9rem; border-radius:10px; border:2px solid #e2e8f0; background:#fff; transition:all .2s; cursor:pointer; }
    .equipo-card:hover { transform:translateY(-2px); box-shadow:0 6px 14px rgba(0,0,0,.1); }
    .equipo-card.s-VERDE { border-color:#22c55e; }
    .equipo-card.s-AMARILLO { border-color:#facc15; }
    .equipo-card.s-ROJO { border-color:#ef4444; background:#fef2f2; }
    .equipo-card.s-GRIS { border-color:#cbd5e1; opacity:.75; }
    .equipo-card .nom { font-weight:700; color:#0f172a; font-size:.95rem; }
    .equipo-card .meta { font-size:.72rem; color:#64748b; margin-top:.15rem; }
    .equipo-card .temp { font-size:1.8rem; font-weight:700; margin:.5rem 0; }
    .equipo-card .rango { font-size:.7rem; color:#64748b; }
    .dot { width:12px; height:12px; border-radius:50%; display:inline-block; margin-right:.3rem; vertical-align:middle; }
    .d-VERDE { background:#22c55e; }
    .d-AMARILLO { background:#facc15; }
    .d-ROJO { background:#ef4444; animation:pulse 1.5s infinite; }
    .d-GRIS { background:#94a3b8; }
    @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

    .lst { list-style:none; padding:0; margin:0; }
    .lst li { padding:.55rem .2rem; border-bottom:1px solid #f1f5f9; font-size:.82rem; display:flex; justify-content:space-between; align-items:center; gap:.5rem; }
    .lst li:last-child { border-bottom:none; }
    .badge { display:inline-block; padding:.15rem .55rem; border-radius:8px; font-size:.66rem; font-weight:700; }
    .b-CRITICA { background:#fee2e2; color:#991b1b; }
    .b-ALTA { background:#ffedd5; color:#9a3412; }
    .b-MEDIA { background:#fef3c7; color:#92400e; }
    .b-BAJA { background:#dbeafe; color:#1e40af; }
    .b-ABIERTA { background:#fee2e2; color:#991b1b; }
    .b-INVESTIGACION { background:#fef3c7; color:#92400e; }
    .b-CERRADA { background:#dcfce7; color:#166534; }

    .tabla { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.07); }
    .tabla table { width:100%; border-collapse:collapse; font-size:.8rem; }
    .tabla th { background:#f1f5f9; padding:.6rem .8rem; text-align:left; font-size:.7rem; color:#475569; text-transform:uppercase; }
    .tabla td { padding:.55rem .8rem; border-top:1px solid #f1f5f9; }
</style>

<div class="page-header">
    <div>
        <h1>Calidad · Cadena de Frío</h1>
        <p>Monitoreo, alertas y trazabilidad de lotes en equipos refrigerados</p>
    </div>
</div>

@include('admin.calidad.cadena-frio._tabs')

@if(session('success'))<div style="background:#dcfce7;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#10003; {{ session('success') }}</div>@endif

<div class="kpi-grid">
    <div class="kpi"><div class="lbl">Equipos Activos</div><div class="val">{{ $stats['equipos_activos'] }}</div></div>
    <div class="kpi k-temp"><div class="lbl">Temp. Promedio</div><div class="val">{{ $stats['temp_promedio'] ?: '—' }}°C</div></div>
    <div class="kpi k-alert"><div class="lbl">Alertas Hoy</div><div class="val">{{ $stats['alertas_hoy'] }}</div></div>
    <div class="kpi k-alert"><div class="lbl">Alertas Abiertas</div><div class="val">{{ $stats['alertas_abiertas'] }}</div></div>
    <div class="kpi k-q"><div class="lbl">Lotes Cuarentena</div><div class="val">{{ $stats['lotes_cuarentena'] }}</div></div>
    <div class="kpi k-blk"><div class="lbl">Lotes Bloqueados</div><div class="val">{{ $stats['lotes_bloqueados'] }}</div></div>
</div>

<div class="card">
    <h3>Mapa de Equipos · Semáforo</h3>
    @if($equipos->isEmpty())
        <div style="color:#64748b;font-size:.85rem">No hay equipos registrados. <a href="{{ route('admin.calidad.cadena-frio.equipos.index') }}">Registrar equipo</a></div>
    @else
    <div class="equipos-grid">
        @foreach($equipos as $eq)
            @php $sem = $eq->semaforo; $u = $eq->ultimoMonitoreo; @endphp
            <a href="{{ route('admin.calidad.cadena-frio.equipos.show', $eq) }}" class="equipo-card s-{{ $sem }}" style="text-decoration:none;color:inherit;display:block">
                <div class="nom"><span class="dot d-{{ $sem }}"></span>{{ $eq->nombre }}</div>
                <div class="meta">{{ $eq->codigo }} · {{ $eq->tipo }}</div>
                <div class="temp">{{ $u ? number_format($u->temperatura,1).'°C' : '—' }}</div>
                <div class="rango">Rango: {{ $eq->temperatura_min }}°C – {{ $eq->temperatura_max }}°C</div>
                @if($u)<div class="rango">Últ: {{ $u->fecha_hora->diffForHumans() }}</div>@endif
            </a>
        @endforeach
    </div>
    @endif
</div>

<div class="grid-2">
    <div class="card">
        <h3>Monitoreo de hoy</h3>
        <div class="tabla">
            <table>
                <thead><tr><th>Hora</th><th>Equipo</th><th>Temp</th><th>Origen</th></tr></thead>
                <tbody>
                @forelse($monitoreoHoy as $m)
                    <tr style="{{ $m->fuera_rango ? 'background:#fef2f2' : '' }}">
                        <td>{{ $m->fecha_hora->format('H:i') }}</td>
                        <td>{{ $m->equipo->nombre }}</td>
                        <td><strong>{{ number_format($m->temperatura,1) }}°C</strong> @if($m->fuera_rango)<span class="badge b-ALTA">FUERA</span>@endif</td>
                        <td style="font-size:.7rem;color:#64748b">{{ $m->origen }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:1.5rem">Sin registros hoy</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h3>Alertas Recientes</h3>
        <ul class="lst">
            @forelse($alertasRecientes as $a)
                <li>
                    <div>
                        <a href="{{ route('admin.calidad.cadena-frio.alertas.show', $a) }}" style="color:#0f172a;font-weight:600;text-decoration:none">{{ $a->equipo->nombre }}</a>
                        <div style="font-size:.7rem;color:#64748b">{{ $a->fecha_inicio->format('d/m H:i') }} · {{ number_format($a->temperatura_registrada,1) }}°C</div>
                    </div>
                    <div style="display:flex;gap:.3rem;flex-direction:column;align-items:flex-end">
                        <span class="badge b-{{ $a->severidad }}">{{ $a->severidad }}</span>
                        <span class="badge b-{{ $a->estado }}">{{ $a->estado }}</span>
                    </div>
                </li>
            @empty
                <li style="color:#94a3b8;justify-content:center">Sin alertas</li>
            @endforelse
        </ul>
    </div>
</div>
</x-app-layout>
