<x-app-layout>
@include('admin.calidad.cadena-frio._styles')

<div class="page-header">
    <div>
        <h1>Alerta · {{ $alerta->equipo->nombre }}</h1>
        <p>{{ $alerta->fecha_inicio->format('d/m/Y H:i') }} · Temp. registrada: <strong>{{ number_format($alerta->temperatura_registrada,1) }}°C</strong></p>
    </div>
    <a href="{{ route('admin.calidad.cadena-frio.alertas.index') }}" class="btn btn-secondary">← Volver</a>
</div>

@include('admin.calidad.cadena-frio._tabs')

@if(session('success'))<div class="alert-ok">&#10003; {{ session('success') }}</div>@endif

<div style="display:grid;grid-template-columns:1fr 350px;gap:1.2rem">
    <div>
        <div class="card">
            <h3>Lotes afectados</h3>
            @if($alerta->afectaciones->isEmpty())
                <div style="color:#64748b;font-size:.85rem">Sin lotes afectados</div>
            @else
            <div class="tabla">
                <table>
                    <thead><tr><th>Lote</th><th>Medicamento</th><th>Estado</th><th>Acción</th></tr></thead>
                    <tbody>
                    @foreach($alerta->afectaciones as $af)
                        <tr>
                            <td><strong>{{ optional($af->inventarioLote)->lote }}</strong></td>
                            <td>{{ optional($af->inventarioLote->medicamento ?? null)->nombre ?? '—' }}</td>
                            <td><span class="badge b-{{ $af->estado }}">{{ $af->estado }}</span></td>
                            <td>
                                <form method="POST" action="{{ route('admin.calidad.cadena-frio.afectaciones.update', $af) }}" style="display:flex;gap:.3rem;align-items:center">
                                    @csrf @method('PATCH')
                                    <select name="estado" style="padding:.3rem;border:1px solid #e2e8f0;border-radius:6px;font-size:.75rem">
                                        <option value="PENDIENTE_EVALUACION" {{ $af->estado=='PENDIENTE_EVALUACION'?'selected':'' }}>Pendiente</option>
                                        <option value="LIBERADO" {{ $af->estado=='LIBERADO'?'selected':'' }}>Liberar</option>
                                        <option value="BLOQUEADO" {{ $af->estado=='BLOQUEADO'?'selected':'' }}>Bloquear</option>
                                        <option value="DESECHADO" {{ $af->estado=='DESECHADO'?'selected':'' }}>Desechar</option>
                                    </select>
                                    <input name="observaciones" placeholder="Observación" style="padding:.3rem;border:1px solid #e2e8f0;border-radius:6px;font-size:.75rem;flex:1">
                                    <button class="btn-mini">Aplicar</button>
                                </form>
                            </td>
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
            <h3>Detalles</h3>
            <div style="font-size:.82rem;line-height:1.8">
                <div><strong>Severidad:</strong> <span class="badge b-{{ $alerta->severidad }}">{{ $alerta->severidad }}</span></div>
                <div><strong>Estado:</strong> <span class="badge b-{{ $alerta->estado }}">{{ $alerta->estado }}</span></div>
                <div><strong>Inicio:</strong> {{ $alerta->fecha_inicio->format('d/m/Y H:i') }}</div>
                <div><strong>Fin:</strong> {{ $alerta->fecha_fin ? $alerta->fecha_fin->format('d/m/Y H:i') : '—' }}</div>
                <div><strong>Temp. permitida:</strong> {{ $alerta->temperatura_permitida_min }}°C – {{ $alerta->temperatura_permitida_max }}°C</div>
                <div><strong>Temp. registrada:</strong> {{ number_format($alerta->temperatura_registrada,1) }}°C</div>
                <div><strong>Usuario:</strong> {{ optional($alerta->usuario)->name ?? '—' }}</div>
            </div>
            @if($alerta->observaciones)
                <div style="margin-top:.7rem;padding:.6rem;background:#f8fafc;border-radius:6px;font-size:.8rem">
                    <strong>Observaciones:</strong><br>{{ $alerta->observaciones }}
                </div>
            @endif
        </div>

        @if($alerta->estado !== 'CERRADA')
        <div class="card">
            <h3>Cerrar alerta</h3>
            <form method="POST" action="{{ route('admin.calidad.cadena-frio.alertas.cerrar', $alerta) }}">
                @csrf
                <textarea name="observaciones" rows="3" placeholder="Resumen de acciones tomadas" style="width:100%;padding:.5rem;border:1px solid #e2e8f0;border-radius:6px;font-size:.85rem"></textarea>
                <button class="btn btn-primary" style="margin-top:.5rem;width:100%">Cerrar alerta</button>
            </form>
        </div>
        @endif
    </div>
</div>
</x-app-layout>
