<x-app-layout>
@include('admin.calidad.incidentes._styles')

<div style="padding:1.5rem;">
    <div class="page-header">
        <div>
            <h1>Bandeja de Incidentes</h1>
            <p>Listado completo con filtros</p>
        </div>
        <div style="display:flex;gap:.5rem;">
            <a href="{{ route('admin.calidad.incidentes.index') }}" class="btn" style="background:#fff;color:var(--inc);">← Volver</a>
            <a href="{{ route('admin.calidad.incidentes.create') }}" class="btn" style="background:#fff;color:var(--inc);">+ Nuevo</a>
        </div>
    </div>

    @include('admin.calidad.incidentes._tabs')

    @if(session('success'))<div class="card" style="background:#dcfce7;color:#166534;">{{ session('success') }}</div>@endif

    <div class="card">
        <form method="GET" class="form-grid">
            <div>
                <label>Buscar</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Código, descripción">
            </div>
            <div>
                <label>Tipo</label>
                <select name="tipo">
                    <option value="">— Todos —</option>
                    @foreach(\App\Models\Incidente::TIPOS as $k=>$v)
                        <option value="{{ $k }}" @selected(request('tipo')===$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Estado</label>
                <select name="estado">
                    <option value="">— Todos —</option>
                    @foreach(\App\Models\Incidente::ESTADOS as $k=>$v)
                        <option value="{{ $k }}" @selected(request('estado')===$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Severidad</label>
                <select name="severidad">
                    <option value="">— Todas —</option>
                    @foreach(\App\Models\Incidente::SEVERIDADES as $k=>$v)
                        <option value="{{ $k }}" @selected(request('severidad')===$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Clasificación</label>
                <select name="clasificacion">
                    <option value="">— Todas —</option>
                    @foreach(\App\Models\Incidente::CLASIFICACIONES as $k=>$v)
                        <option value="{{ $k }}" @selected(request('clasificacion')===$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div><label>Desde</label><input type="date" name="desde" value="{{ request('desde') }}"></div>
            <div><label>Hasta</label><input type="date" name="hasta" value="{{ request('hasta') }}"></div>
            <div style="display:flex;align-items:flex-end;gap:.4rem;">
                <button class="btn btn-primary" type="submit">Filtrar</button>
                <a href="{{ route('admin.calidad.incidentes.bandeja') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    <div class="card">
        <table class="t">
            <thead>
                <tr>
                    <th>Código</th><th>Tipo</th><th>Clasificación</th><th>Severidad</th>
                    <th>Estado</th><th>Fecha</th><th>Reporta</th><th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($incidentes as $i)
                <tr>
                    <td><strong>{{ $i->codigo }}</strong></td>
                    <td>{{ \App\Models\Incidente::TIPOS[$i->tipo_incidente] ?? $i->tipo_incidente }}</td>
                    <td>{{ \App\Models\Incidente::CLASIFICACIONES[$i->clasificacion] ?? $i->clasificacion }}</td>
                    <td><span class="badge b-{{ $i->severidad }}">{{ $i->severidad }}</span></td>
                    <td><span class="badge b-{{ $i->estado }}">{{ $i->estado }}</span></td>
                    <td>{{ $i->fecha_incidente->format('Y-m-d H:i') }}</td>
                    <td>{{ optional($i->usuarioReporta)->name ?? '—' }}</td>
                    <td><a href="{{ route('admin.calidad.incidentes.show', $i) }}" class="btn btn-mini btn-secondary">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:1.5rem;">No se encontraron incidentes con los filtros aplicados.</td></tr>
            @endforelse
            </tbody>
        </table>
        <div style="margin-top:.85rem;">{{ $incidentes->links() }}</div>
    </div>
</div>
</x-app-layout>
