<x-app-layout>
@include('admin.calidad.cadena-frio._styles')

<div class="page-header">
    <div>
        <h1>Alertas de Cadena de Frío</h1>
        <p>Eventos de temperatura fuera de rango y lotes afectados</p>
    </div>
</div>

@include('admin.calidad.cadena-frio._tabs')

@if(session('success'))<div class="alert-ok">&#10003; {{ session('success') }}</div>@endif

<form class="filters" method="GET">
    <div><label>Estado</label>
        <select name="estado">
            <option value="">— Todos —</option>
            @foreach(['ABIERTA','INVESTIGACION','CERRADA'] as $e)<option value="{{ $e }}" {{ request('estado')==$e?'selected':'' }}>{{ $e }}</option>@endforeach
        </select>
    </div>
    <div><label>Severidad</label>
        <select name="severidad">
            <option value="">— Todas —</option>
            @foreach(['BAJA','MEDIA','ALTA','CRITICA'] as $s)<option value="{{ $s }}" {{ request('severidad')==$s?'selected':'' }}>{{ $s }}</option>@endforeach
        </select>
    </div>
    <button class="btn btn-primary">Filtrar</button>
    <a href="{{ route('admin.calidad.cadena-frio.alertas.index') }}" class="btn btn-secondary">Limpiar</a>
</form>

<div class="tabla">
    <table>
        <thead><tr><th>Inicio</th><th>Fin</th><th>Equipo</th><th>Temp.</th><th>Rango</th><th>Severidad</th><th>Estado</th><th>Lotes afectados</th><th></th></tr></thead>
        <tbody>
        @forelse($alertas as $a)
            <tr>
                <td>{{ $a->fecha_inicio->format('d/m/Y H:i') }}</td>
                <td>{{ $a->fecha_fin ? $a->fecha_fin->format('d/m/Y H:i') : '—' }}</td>
                <td>{{ $a->equipo->nombre }}</td>
                <td><strong>{{ number_format($a->temperatura_registrada,1) }}°C</strong></td>
                <td style="font-size:.78rem">{{ $a->temperatura_permitida_min }}–{{ $a->temperatura_permitida_max }}°C</td>
                <td><span class="badge b-{{ $a->severidad }}">{{ $a->severidad }}</span></td>
                <td><span class="badge b-{{ $a->estado }}">{{ $a->estado }}</span></td>
                <td>{{ $a->afectaciones->count() }}</td>
                <td><a href="{{ route('admin.calidad.cadena-frio.alertas.show', $a) }}" class="btn-mini">Ver</a></td>
            </tr>
        @empty
            <tr><td colspan="9" style="text-align:center;color:#94a3b8;padding:2rem">Sin alertas</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:.8rem">{{ $alertas->links() }}</div>
</x-app-layout>
