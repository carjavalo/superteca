@php $r = request()->route()->getName(); @endphp
<div class="nav-tabs">
    <a href="{{ route('admin.calidad.cadena-frio.index') }}" class="{{ $r==='admin.calidad.cadena-frio.index' ? 'active' : '' }}">Dashboard</a>
    <a href="{{ route('admin.calidad.cadena-frio.equipos.index') }}" class="{{ str_starts_with($r,'admin.calidad.cadena-frio.equipos') ? 'active' : '' }}">Equipos</a>
    <a href="{{ route('admin.calidad.cadena-frio.monitoreo.index') }}" class="{{ str_starts_with($r,'admin.calidad.cadena-frio.monitoreo') ? 'active' : '' }}">Monitoreo</a>
    <a href="{{ route('admin.calidad.cadena-frio.alertas.index') }}" class="{{ str_starts_with($r,'admin.calidad.cadena-frio.alertas') ? 'active' : '' }}">Alertas</a>
    <a href="{{ route('admin.calidad.cadena-frio.lotes.index') }}" class="{{ str_starts_with($r,'admin.calidad.cadena-frio.lotes') ? 'active' : '' }}">Lotes en equipos</a>
</div>
