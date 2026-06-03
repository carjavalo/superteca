<x-app-layout>
@include('admin.calidad.cadena-frio._styles')

<div class="page-header">
    <div>
        <h1>Monitoreo de Temperatura</h1>
        <p>Registro manual y automático de lecturas</p>
    </div>
    <button class="btn btn-primary" onclick="document.getElementById('mReg').style.display='flex'">+ Registrar lectura</button>
</div>

@include('admin.calidad.cadena-frio._tabs')

@if(session('success'))<div class="alert-ok">&#10003; {{ session('success') }}</div>@endif
@if($errors->any())<div class="alert-err">{!! implode('<br>', $errors->all()) !!}</div>@endif

<form class="filters" method="GET">
    <div><label>Equipo</label>
        <select name="equipo_id">
            <option value="">— Todos —</option>
            @foreach($equipos as $eq)<option value="{{ $eq->id }}" {{ request('equipo_id')==$eq->id?'selected':'' }}>{{ $eq->nombre }}</option>@endforeach
        </select>
    </div>
    <div><label><input type="checkbox" name="fuera_rango" value="1" {{ request('fuera_rango')?'checked':'' }}> Solo fuera de rango</label></div>
    <button class="btn btn-primary">Filtrar</button>
    <a href="{{ route('admin.calidad.cadena-frio.monitoreo.index') }}" class="btn btn-secondary">Limpiar</a>
</form>

<div class="tabla">
    <table>
        <thead><tr><th>Fecha/Hora</th><th>Equipo</th><th>Temp.</th><th>Humedad</th><th>Origen</th><th>Usuario</th><th>Estado</th></tr></thead>
        <tbody>
        @forelse($registros as $m)
            <tr style="{{ $m->fuera_rango ? 'background:#fef2f2' : '' }}">
                <td>{{ $m->fecha_hora->format('d/m/Y H:i') }}</td>
                <td>{{ $m->equipo->nombre }}</td>
                <td><strong>{{ number_format($m->temperatura,1) }}°C</strong></td>
                <td>{{ $m->humedad ? number_format($m->humedad,1).'%' : '—' }}</td>
                <td style="font-size:.72rem">{{ $m->origen }}</td>
                <td style="font-size:.72rem">{{ optional($m->usuario)->name ?? '—' }}</td>
                <td>{!! $m->fuera_rango ? '<span class="badge b-ALTA">FUERA</span>' : '<span class="badge b-CERRADA">OK</span>' !!}</td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:2rem">Sin registros</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:.8rem">{{ $registros->links() }}</div>

<div id="mReg" class="modal-bg" style="display:none">
    <div class="modal" style="max-width:500px">
        <div class="modal-h"><h3>Registrar Lectura</h3><button class="x" onclick="document.getElementById('mReg').style.display='none'">&times;</button></div>
        <form method="POST" action="{{ route('admin.calidad.cadena-frio.monitoreo.store') }}">
            @csrf
            <div class="grid-2c">
                <div style="grid-column:1/-1"><label>Equipo *</label>
                    <select name="equipo_id" required>
                        <option value="">— Seleccionar —</option>
                        @foreach($equipos as $eq)<option value="{{ $eq->id }}">{{ $eq->nombre }} ({{ $eq->temperatura_min }}–{{ $eq->temperatura_max }}°C)</option>@endforeach
                    </select>
                </div>
                <div><label>Fecha/Hora *</label><input type="datetime-local" name="fecha_hora" value="{{ now()->format('Y-m-d\TH:i') }}" required></div>
                <div><label>Origen</label><select name="origen"><option value="MANUAL">Manual</option><option value="AUTOMATICO">Automático</option></select></div>
                <div><label>Temperatura (°C) *</label><input type="number" step="0.1" name="temperatura" required></div>
                <div><label>Humedad (%)</label><input type="number" step="0.1" name="humedad"></div>
                <div style="grid-column:1/-1"><label>Observaciones</label><textarea name="observaciones" rows="2"></textarea></div>
            </div>
            <div style="margin-top:1rem;display:flex;justify-content:flex-end;gap:.5rem">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('mReg').style.display='none'">Cancelar</button>
                <button class="btn btn-primary">Registrar</button>
            </div>
        </form>
    </div>
</div>
</x-app-layout>
