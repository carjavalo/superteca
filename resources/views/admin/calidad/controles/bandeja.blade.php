<x-app-layout>
@include('admin.calidad.controles._styles')

<div style="padding:1.5rem;">
    <div class="page-header">
        <div><h1>Bandeja de Controles</h1><p>Histórico completo y filtros</p></div>
        <a href="{{ route('admin.calidad.controles.create') }}" class="btn" style="background:#fff;color:var(--calq);">+ Nuevo Control</a>
    </div>

    @include('admin.calidad.controles._tabs')

    @if(session('success'))<div class="card" style="background:#dcfce7;color:#166534;">{{ session('success') }}</div>@endif

    <div class="card">
        <form method="GET" class="form-row" style="margin-bottom:0;">
            <div class="form-group">
                <label>Tipo</label>
                <select name="tipo">
                    <option value="">— Todos —</option>
                    @foreach(\App\Models\ControlCalidad::TIPOS as $k => $v)
                        <option value="{{ $k }}" @selected(request('tipo')===$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Resultado</label>
                <select name="resultado">
                    <option value="">— Todos —</option>
                    @foreach(\App\Models\ControlCalidad::RESULTADOS as $k => $v)
                        <option value="{{ $k }}" @selected(request('resultado')===$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group"><label>Desde</label><input type="date" name="desde" value="{{ request('desde') }}"></div>
            <div class="form-group"><label>Hasta</label><input type="date" name="hasta" value="{{ request('hasta') }}"></div>
            <div class="form-group"><label>Código</label><input type="text" name="q" value="{{ request('q') }}" placeholder="CC-2026-..."></div>
            <div class="form-group" style="display:flex;align-items:flex-end;gap:.5rem;">
                <button class="btn btn-primary">Filtrar</button>
                <a href="{{ route('admin.calidad.controles.bandeja') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card">
        <table class="t">
            <thead>
                <tr><th>Código</th><th>Tipo</th><th>Fecha</th><th>Lote/Entidad</th><th>Resultado</th><th>Resp.</th><th></th></tr>
            </thead>
            <tbody>
            @forelse($controles as $c)
                <tr>
                    <td><strong>{{ $c->codigo }}</strong></td>
                    <td>{{ \App\Models\ControlCalidad::TIPOS[$c->tipo_control] ?? $c->tipo_control }}</td>
                    <td>{{ $c->fecha_control->format('Y-m-d H:i') }}</td>
                    <td>
                        @php $det = $c->detalle->first(); @endphp
                        @if($det && $det->inventario_lote_id)
                            Lote #{{ $det->inventario_lote_id }}
                        @elseif($det && $det->mezcla_id) Mezcla #{{ $det->mezcla_id }}
                        @elseif($det && $det->preparacion_id) Preparación #{{ $det->preparacion_id }}
                        @elseif($det && $det->reempaque_id) Reempaque #{{ $det->reempaque_id }}
                        @elseif($det && $det->entrega_id) Entrega #{{ $det->entrega_id }}
                        @else <span style="color:#94a3b8;">—</span>
                        @endif
                    </td>
                    <td><span class="badge b-{{ $c->resultado }}">{{ $c->resultado }}</span></td>
                    <td>{{ optional($c->usuario)->name ?? '—' }}</td>
                    <td><a href="{{ route('admin.calidad.controles.show', $c) }}" class="btn btn-mini btn-secondary">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:1.5rem;">No hay registros con esos filtros.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:.8rem;">{{ $controles->links() }}</div>
    </div>
</div>
</x-app-layout>
