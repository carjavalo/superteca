<x-app-layout>
@include('admin.calidad.controles._styles')

<div style="padding:1.5rem;">
    <div class="page-header">
        <div>
            <h1>Centro de Calidad 360°</h1>
            <p>Control transversal: recepción · cadena de frío · producción · dispensación</p>
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('admin.calidad.controles.create') }}" class="btn" style="background:#fff;color:var(--calq);">+ Nuevo Control</a>
        </div>
    </div>

    @include('admin.calidad.controles._tabs')

    @if(session('success'))<div class="card" style="background:#dcfce7;color:#166534;">{{ session('success') }}</div>@endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi"><div class="lbl">Controles hoy</div><div class="val">{{ $stats['controles_hoy'] }}</div></div>
        <div class="kpi k-ok"><div class="lbl">Aprobados</div><div class="val">{{ $stats['aprobados'] }}</div></div>
        <div class="kpi k-cond"><div class="lbl">Condicionales</div><div class="val">{{ $stats['condicionales'] }}</div></div>
        <div class="kpi k-rech"><div class="lbl">Rechazados</div><div class="val">{{ $stats['rechazados'] }}</div></div>
        <div class="kpi k-pend"><div class="lbl">Pendientes</div><div class="val">{{ $stats['pendientes'] }}</div></div>
        <div class="kpi k-q"><div class="lbl">Lotes en cuarentena</div><div class="val">{{ $stats['cuarentena'] }}</div></div>
        <div class="kpi k-blk"><div class="lbl">Lotes bloqueados</div><div class="val">{{ $stats['bloqueados'] }}</div></div>
        <div class="kpi k-rech"><div class="lbl">Acciones abiertas</div><div class="val">{{ $stats['acciones_abiertas'] }}</div></div>
    </div>

    {{-- Semáforo por tipo (Centro 360°) --}}
    <div class="card">
        <h3>Semáforo por proceso</h3>
        <div class="sem-grid">
            @foreach($semaforo as $key => $info)
                <div class="sem-card s-{{ $info['estado'] }}">
                    <div class="nom"><span class="dot d-{{ $info['estado'] }}"></span>{{ $info['label'] }}</div>
                    <div class="meta">
                        @if($info['estado']==='VERDE') Conforme
                        @elseif($info['estado']==='AMARILLO') En revisión
                        @elseif($info['estado']==='ROJO') No conforme
                        @else Sin actividad
                        @endif
                    </div>
                    <div class="nums">
                        <span class="ok">✔ {{ $info['aprobados'] }}</span>
                        <span class="co">⚠ {{ $info['condicionales'] }}</span>
                        <span class="re">✖ {{ $info['rechazados'] }}</span>
                        <span class="pe">⏳ {{ $info['pendientes'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Recientes --}}
    <div class="card">
        <h3>Controles recientes</h3>
        <table class="t">
            <thead>
                <tr><th>Código</th><th>Tipo</th><th>Fecha</th><th>Lote</th><th>Resultado</th><th>Responsable</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($recientes as $c)
                <tr>
                    <td><strong>{{ $c->codigo }}</strong></td>
                    <td>{{ \App\Models\ControlCalidad::TIPOS[$c->tipo_control] ?? $c->tipo_control }}</td>
                    <td>{{ $c->fecha_control->format('Y-m-d H:i') }}</td>
                    <td>
                        @php $det = $c->detalle->first(); @endphp
                        @if($det && $det->inventarioLote)
                            {{ $det->inventarioLote->lote }} · {{ optional($det->inventarioLote->medicamento)->nombre }}
                        @else
                            <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td><span class="badge b-{{ $c->resultado }}">{{ $c->resultado }}</span></td>
                    <td>{{ optional($c->usuario)->name ?? '—' }}</td>
                    <td><a href="{{ route('admin.calidad.controles.show', $c) }}" class="btn btn-mini btn-secondary">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:1.5rem;">Aún no hay controles registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
