<x-app-layout>
@include('admin.calidad.controles._styles')

<div style="padding:1.5rem;">
    <div class="page-header">
        <div><h1>Catálogo de Parámetros</h1><p>Define los parámetros que se evaluarán por tipo de control</p></div>
        <a href="{{ route('admin.calidad.controles.index') }}" class="btn btn-secondary">← Volver</a>
    </div>

    @include('admin.calidad.controles._tabs')

    @if(session('success'))<div class="card" style="background:#dcfce7;color:#166534;">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="card" style="background:#fee2e2;color:#991b1b;">
            <ul style="margin:0;padding-left:1rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <div class="card">
        <h3>Nuevo parámetro</h3>
        <form method="POST" action="{{ route('admin.calidad.controles.parametros.store') }}">
            @csrf
            <div class="form-row">
                <div class="form-group"><label>Nombre</label><input type="text" name="nombre" required placeholder="Ej. pH, Temperatura, Aspecto..."></div>
                <div class="form-group"><label>Tipo de control</label>
                    <select name="tipo_control" required>
                        @foreach(\App\Models\ControlCalidad::TIPOS as $k => $v)
                            <option value="{{ $k }}">{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label>Unidad</label><input type="text" name="unidad_medida" placeholder="°C, mL, %"></div>
                <div class="form-group"><label>Mínimo</label><input type="number" step="0.0001" name="valor_minimo"></div>
                <div class="form-group"><label>Máximo</label><input type="number" step="0.0001" name="valor_maximo"></div>
                <div class="form-group"><label>Obligatorio</label>
                    <select name="obligatorio"><option value="1">Sí</option><option value="0">No</option></select>
                </div>
                <div class="form-group"><label>Activo</label>
                    <select name="estado"><option value="1">Sí</option><option value="0">No</option></select>
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;"><button class="btn btn-primary">+ Crear</button></div>
            </div>
        </form>
    </div>

    <div class="card">
        <h3>Parámetros configurados ({{ $parametros->total() }})</h3>

        @foreach($parametros as $p)
            <form id="upd-{{ $p->id }}" method="POST" action="{{ route('admin.calidad.controles.parametros.update', $p) }}">@csrf @method('PUT')</form>
            <form id="del-{{ $p->id }}" method="POST" action="{{ route('admin.calidad.controles.parametros.destroy', $p) }}" onsubmit="return confirm('¿Eliminar este parámetro?')">@csrf @method('DELETE')</form>
        @endforeach

        <table class="t">
            <thead><tr><th>Nombre</th><th>Tipo</th><th>Unidad</th><th>Rango</th><th>Obligatorio</th><th>Estado</th><th colspan="2"></th></tr></thead>
            <tbody>
            @forelse($parametros as $p)
                <tr>
                    <td><input type="text" form="upd-{{ $p->id }}" name="nombre" value="{{ $p->nombre }}" required></td>
                    <td>
                        <select form="upd-{{ $p->id }}" name="tipo_control">
                            @foreach(\App\Models\ControlCalidad::TIPOS as $k => $v)
                                <option value="{{ $k }}" @selected($p->tipo_control===$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td><input type="text" form="upd-{{ $p->id }}" name="unidad_medida" value="{{ $p->unidad_medida }}" style="width:80px;"></td>
                    <td>
                        <input type="number" step="0.0001" form="upd-{{ $p->id }}" name="valor_minimo" value="{{ $p->valor_minimo }}" style="width:80px;">
                        <input type="number" step="0.0001" form="upd-{{ $p->id }}" name="valor_maximo" value="{{ $p->valor_maximo }}" style="width:80px;">
                    </td>
                    <td>
                        <select form="upd-{{ $p->id }}" name="obligatorio">
                            <option value="1" @selected($p->obligatorio)>Sí</option>
                            <option value="0" @selected(!$p->obligatorio)>No</option>
                        </select>
                    </td>
                    <td>
                        <select form="upd-{{ $p->id }}" name="estado">
                            <option value="1" @selected($p->estado)>Activo</option>
                            <option value="0" @selected(!$p->estado)>Inactivo</option>
                        </select>
                    </td>
                    <td><button form="upd-{{ $p->id }}" class="btn btn-mini btn-primary">Guardar</button></td>
                    <td><button form="del-{{ $p->id }}" class="btn btn-mini btn-danger">×</button></td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:1.5rem;">No hay parámetros configurados todavía.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:.8rem;">{{ $parametros->links() }}</div>
    </div>
</div>
</x-app-layout>
