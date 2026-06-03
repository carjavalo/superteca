<x-app-layout>
@include('admin.calidad.cadena-frio._styles')

<div class="page-header">
    <div>
        <h1>{{ $equipo->nombre }}</h1>
        <p>{{ $equipo->codigo }} · {{ $equipo->tipo }} · {{ $equipo->ubicacion ?: 'Sin ubicación' }}</p>
    </div>
    <a href="{{ route('admin.calidad.cadena-frio.equipos.index') }}" class="btn btn-secondary">← Volver</a>
</div>

@include('admin.calidad.cadena-frio._tabs')

@if(session('success'))<div class="alert-ok">&#10003; {{ session('success') }}</div>@endif

<div style="display:grid;grid-template-columns:2fr 1fr;gap:1.2rem">
    <div>
        <div class="card">
            <h3>Últimos monitoreos</h3>
            <div class="tabla">
                <table>
                    <thead><tr><th>Fecha/Hora</th><th>Temp.</th><th>Humedad</th><th>Origen</th><th>Usuario</th></tr></thead>
                    <tbody>
                    @forelse($monitoreos as $m)
                        <tr style="{{ $m->fuera_rango ? 'background:#fef2f2' : '' }}">
                            <td>{{ $m->fecha_hora->format('d/m/Y H:i') }}</td>
                            <td><strong>{{ number_format($m->temperatura,1) }}°C</strong></td>
                            <td>{{ $m->humedad ? number_format($m->humedad,1).'%' : '—' }}</td>
                            <td style="font-size:.72rem">{{ $m->origen }}</td>
                            <td style="font-size:.72rem">{{ optional($m->usuario)->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:1.5rem">Sin monitoreos</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h3>Lotes actualmente en este equipo</h3>
            @if($lotes->isEmpty())
                <div style="color:#64748b;font-size:.85rem">Sin lotes asignados</div>
            @else
            <div class="tabla">
                <table>
                    <thead><tr><th>Lote</th><th>Medicamento</th><th>Ingreso</th><th>Estado calidad</th></tr></thead>
                    <tbody>
                    @foreach($lotes as $l)
                        <tr>
                            <td>{{ optional($l->inventarioLote)->lote }}</td>
                            <td>{{ optional($l->inventarioLote->medicamento ?? null)->nombre ?? '—' }}</td>
                            <td>{{ $l->fecha_ingreso->format('d/m/Y H:i') }}</td>
                            <td><span class="badge b-{{ optional($l->inventarioLote)->estado_calidad }}">{{ optional($l->inventarioLote)->estado_calidad }}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    <div>
        <div class="card">
            <h3>Información</h3>
            <div style="font-size:.82rem;line-height:1.8">
                <div><strong>Rango permitido:</strong> {{ $equipo->temperatura_min }}°C – {{ $equipo->temperatura_max }}°C</div>
                <div><strong>Fabricante:</strong> {{ $equipo->fabricante ?: '—' }}</div>
                <div><strong>Modelo:</strong> {{ $equipo->modelo ?: '—' }}</div>
                <div><strong>Serial:</strong> {{ $equipo->serial ?: '—' }}</div>
                <div><strong>Última calibración:</strong> {{ $equipo->fecha_calibracion ? $equipo->fecha_calibracion->format('d/m/Y') : '—' }}</div>
                <div><strong>Próxima calibración:</strong> {{ $equipo->proxima_calibracion ? $equipo->proxima_calibracion->format('d/m/Y') : '—' }}</div>
            </div>
        </div>

        <div class="card">
            <h3>Sensores</h3>
            <ul style="list-style:none;padding:0;margin:0">
                @forelse($equipo->sensores as $s)
                    <li style="padding:.5rem 0;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center">
                        <div>
                            <strong style="font-size:.85rem">{{ $s->codigo_sensor }}</strong>
                            <div style="font-size:.7rem;color:#64748b">{{ $s->marca }} {{ $s->modelo }}</div>
                        </div>
                        <form method="POST" action="{{ route('admin.calidad.cadena-frio.sensores.destroy', $s) }}" onsubmit="return confirm('¿Eliminar?')">
                            @csrf @method('DELETE')
                            <button class="btn-mini btn-del">×</button>
                        </form>
                    </li>
                @empty
                    <li style="color:#94a3b8;font-size:.82rem;padding:.5rem 0">Sin sensores</li>
                @endforelse
            </ul>
            <form method="POST" action="{{ route('admin.calidad.cadena-frio.sensores.store', $equipo) }}" style="margin-top:.7rem;display:grid;gap:.4rem">
                @csrf
                <input name="codigo_sensor" placeholder="Código sensor *" required style="padding:.45rem;border:1px solid #e2e8f0;border-radius:6px;font-size:.8rem">
                <input name="marca" placeholder="Marca" style="padding:.45rem;border:1px solid #e2e8f0;border-radius:6px;font-size:.8rem">
                <input name="modelo" placeholder="Modelo" style="padding:.45rem;border:1px solid #e2e8f0;border-radius:6px;font-size:.8rem">
                <button class="btn btn-primary" style="font-size:.78rem">+ Agregar sensor</button>
            </form>
        </div>

        <div class="card">
            <h3>Alertas recientes</h3>
            <ul style="list-style:none;padding:0;margin:0">
                @forelse($equipo->alertas as $a)
                    <li style="padding:.5rem 0;border-bottom:1px solid #f1f5f9">
                        <a href="{{ route('admin.calidad.cadena-frio.alertas.show', $a) }}" style="color:#0f172a;text-decoration:none">
                            <div style="display:flex;justify-content:space-between;gap:.3rem">
                                <strong style="font-size:.82rem">{{ number_format($a->temperatura_registrada,1) }}°C</strong>
                                <span class="badge b-{{ $a->severidad }}">{{ $a->severidad }}</span>
                            </div>
                            <div style="font-size:.7rem;color:#64748b">{{ $a->fecha_inicio->format('d/m H:i') }} · {{ $a->estado }}</div>
                        </a>
                    </li>
                @empty
                    <li style="color:#94a3b8;font-size:.82rem;padding:.5rem 0">Sin alertas</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
</x-app-layout>
