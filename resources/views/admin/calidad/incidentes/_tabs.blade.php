<div class="tabs">
    <a href="{{ route('admin.calidad.incidentes.index') }}"   class="{{ request()->routeIs('admin.calidad.incidentes.index') ? 'active' : '' }}">Dashboard 360°</a>
    <a href="{{ route('admin.calidad.incidentes.bandeja') }}" class="{{ request()->routeIs('admin.calidad.incidentes.bandeja') ? 'active' : '' }}">Bandeja</a>
    <a href="{{ route('admin.calidad.incidentes.create') }}"  class="{{ request()->routeIs('admin.calidad.incidentes.create') ? 'active' : '' }}">+ Nuevo Incidente</a>
</div>
