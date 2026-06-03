@php $r = request()->route()->getName(); @endphp
<div class="nav-tabs">
    <a href="{{ route('admin.calidad.controles.index') }}" class="{{ $r==='admin.calidad.controles.index' ? 'active' : '' }}">Dashboard 360°</a>
    <a href="{{ route('admin.calidad.controles.bandeja') }}" class="{{ str_starts_with($r,'admin.calidad.controles.bandeja') || $r==='admin.calidad.controles.show' ? 'active' : '' }}">Bandeja</a>
    <a href="{{ route('admin.calidad.controles.create') }}" class="{{ $r==='admin.calidad.controles.create' ? 'active' : '' }}">Nuevo Control</a>
    <a href="{{ route('admin.calidad.controles.parametros.index') }}" class="{{ str_starts_with($r,'admin.calidad.controles.parametros') ? 'active' : '' }}">Parámetros</a>
</div>
