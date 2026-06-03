<x-app-layout>
@include('admin.calidad.cadena-frio._styles')

<div class="page-header">
    <div>
        <h1>Lotes en Equipos Refrigerados</h1>
        <p>Trazabilidad de almacenamiento por equipo</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('mLote').style.display='flex'">+ Asignar lote</button>
</div>

@include('admin.calidad.cadena-frio._tabs')

@if(session('success'))<div class="alert-ok">&#10003; {{ session('success') }}</div>@endif

<form class="filters" method="GET">
    <div><label>Equipo</label>
        <select name="equipo_id">
            <option value="">— Todos —</option>
            @foreach($equipos as $eq)<option value="{{ $eq->id }}" {{ request('equipo_id')==$eq->id?'selected':'' }}>{{ $eq->nombre }}</option>@endforeach
        </select>
    </div>
    <div><label><input type="checkbox" name="activos" value="1" {{ request('activos')?'checked':'' }}> Solo activos</label></div>
    <button class="btn btn-primary">Filtrar</button>
    <a href="{{ route('admin.calidad.cadena-frio.lotes.index') }}" class="btn btn-secondary">Limpiar</a>
</form>

<div class="tabla">
    <table>
        <thead><tr><th>Lote</th><th>Medicamento</th><th>Equipo</th><th>Ingreso</th><th>Salida</th><th>Estado calidad</th><th></th></tr></thead>
        <tbody>
        @forelse($lotes as $l)
            <tr>
                <td><strong>{{ optional($l->inventarioLote)->lote }}</strong></td>
                <td>{{ optional($l->inventarioLote->medicamento ?? null)->nombre ?? '—' }}</td>
                <td>{{ $l->equipo->nombre }}</td>
                <td>{{ $l->fecha_ingreso->format('d/m/Y H:i') }}</td>
                <td>{{ $l->fecha_salida ? $l->fecha_salida->format('d/m/Y H:i') : '—' }}</td>
                <td><span class="badge b-{{ optional($l->inventarioLote)->estado_calidad }}">{{ optional($l->inventarioLote)->estado_calidad }}</span></td>
                <td>
                    @if(!$l->fecha_salida)
                    <form method="POST" action="{{ route('admin.calidad.cadena-frio.lotes.salida', $l) }}" onsubmit="return confirm('¿Registrar salida?')">
                        @csrf
                        <button class="btn-mini">Registrar salida</button>
                    </form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:2rem">Sin registros</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:.8rem">{{ $lotes->links() }}</div>

<div id="mLote" class="modal-bg" style="display:none">
    <div class="modal" style="max-width:560px">
        <div class="modal-h"><h3>Asignar Lote a Equipo</h3><button class="x" onclick="document.getElementById('mLote').style.display='none'">&times;</button></div>
        <form method="POST" action="{{ route('admin.calidad.cadena-frio.lotes.store') }}">
            @csrf
            <div class="grid-2c">
                <div style="grid-column:1/-1"><label>Lote *</label>
                    <select name="inventario_lote_id" required>
                        <option value="">— Seleccionar —</option>
                        @foreach($inventarioLotes as $il)
                            <option value="{{ $il->id }}">{{ $il->lote }} · {{ optional($il->medicamento)->nombre }} ({{ $il->estado_calidad }})</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Equipo *</label>
                    <select name="equipo_id" required>
                        <option value="">— Seleccionar —</option>
                        @foreach($equipos as $eq)<option value="{{ $eq->id }}">{{ $eq->nombre }}</option>@endforeach
                    </select>
                </div>
                <div><label>Fecha/Hora ingreso *</label><input type="datetime-local" name="fecha_ingreso" value="{{ now()->format('Y-m-d\TH:i') }}" required></div>
                <div style="grid-column:1/-1"><label>Observaciones</label><textarea name="observaciones" rows="2"></textarea></div>
            </div>
            <div style="margin-top:1rem;display:flex;justify-content:flex-end;gap:.5rem">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('mLote').style.display='none'">Cancelar</button>
                <button class="btn btn-primary">Asignar</button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
