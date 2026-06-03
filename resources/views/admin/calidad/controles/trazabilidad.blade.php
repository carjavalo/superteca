<x-app-layout>
@include('admin.calidad.controles._styles')

<div style="padding:1.5rem;">
    <div class="page-header">
        <div><h1>Trazabilidad de calidad</h1><p>Línea de tiempo del lote en el sistema</p></div>
        <a href="{{ route('admin.calidad.controles.index') }}" class="btn btn-secondary">← Volver</a>
    </div>

    @include('admin.calidad.controles._tabs')

    <div class="card">
        <h3>Lote</h3>
        <table class="t">
            <tr><th style="width:25%;">Lote</th><td><strong>{{ $lote->lote }}</strong></td></tr>
            <tr><th>Producto</th><td>{{ optional($lote->medicamento)->nombre }}</td></tr>
            <tr><th>Vencimiento</th><td>{{ optional($lote->fecha_vencimiento)->format('Y-m-d') }}</td></tr>
            <tr><th>Cantidad actual</th><td>{{ $lote->cantidad_actual }}</td></tr>
            <tr><th>Estado calidad</th><td><span class="badge b-{{ $lote->estado_calidad }}">{{ $lote->estado_calidad ?? 'N/D' }}</span></td></tr>
        </table>
    </div>

    <div class="card">
        <h3>Línea de tiempo</h3>
        @if($controles->isEmpty())
            <div style="color:#94a3b8;padding:1rem;text-align:center;">Sin controles de calidad registrados para este lote.</div>
        @else
            <div class="timeline">
                @foreach($controles as $c)
                    <div class="timeline-item r-{{ $c->resultado }}">
                        <div style="display:flex;justify-content:space-between;align-items:center;gap:.5rem;flex-wrap:wrap;">
                            <strong>{{ $c->codigo }}</strong>
                            <span class="badge b-{{ $c->resultado }}">{{ $c->resultado }}</span>
                        </div>
                        <div style="font-size:.78rem;color:#64748b;">
                            {{ \App\Models\ControlCalidad::TIPOS[$c->tipo_control] ?? $c->tipo_control }}
                            · {{ $c->fecha_control->format('Y-m-d H:i') }}
                            · {{ optional($c->usuario)->name }}
                        </div>
                        @if($c->observaciones)
                            <div style="font-size:.8rem;color:#475569;margin-top:.3rem;">{{ $c->observaciones }}</div>
                        @endif
                        @if($c->resultados->count())
                            <ul style="list-style:none;padding-left:0;margin:.4rem 0 0;font-size:.78rem;">
                            @foreach($c->resultados as $r)
                                <li>· {{ optional($r->parametro)->nombre }}: <strong>{{ $r->valor_obtenido ?? '—' }}</strong> {{ optional($r->parametro)->unidad_medida }}
                                    @if(!$r->cumple)<span class="badge b-RECHAZADO">No cumple</span>@endif
                                </li>
                            @endforeach
                            </ul>
                        @endif
                        <a href="{{ route('admin.calidad.controles.show', $c) }}" class="btn btn-mini btn-link">Ver detalle →</a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
</x-app-layout>
