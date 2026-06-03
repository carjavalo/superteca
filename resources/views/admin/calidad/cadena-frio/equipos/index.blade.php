<x-app-layout>
@include('admin.calidad.cadena-frio._styles')

<div class="page-header">
    <div>
        <h1>Equipos de Cadena de Frío</h1>
        <p>Neveras, congeladores, cuartos fríos y transporte refrigerado</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('mEquipo')">+ Nuevo Equipo</button>
</div>

@include('admin.calidad.cadena-frio._tabs')

@if(session('success'))<div class="alert-ok">&#10003; {{ session('success') }}</div>@endif
@if($errors->any())<div class="alert-err">{!! implode('<br>', $errors->all()) !!}</div>@endif

<div class="tabla">
    <table>
        <thead>
            <tr>
                <th>Código</th><th>Nombre</th><th>Tipo</th><th>Ubicación</th>
                <th>Rango</th><th>Última Temp.</th><th>Calibración</th><th>Estado</th><th></th>
            </tr>
        </thead>
        <tbody>
        @forelse($equipos as $eq)
            @php $sem = $eq->semaforo; $u = $eq->ultimoMonitoreo; @endphp
            <tr>
                <td><strong>{{ $eq->codigo }}</strong></td>
                <td><a href="{{ route('admin.calidad.cadena-frio.equipos.show', $eq) }}" style="color:#0ea5e9;text-decoration:none;font-weight:600">{{ $eq->nombre }}</a></td>
                <td><span class="badge-tipo">{{ $eq->tipo }}</span></td>
                <td>{{ $eq->ubicacion ?: '—' }}</td>
                <td>{{ $eq->temperatura_min }}°C / {{ $eq->temperatura_max }}°C</td>
                <td><span class="dot d-{{ $sem }}"></span>{{ $u ? number_format($u->temperatura,1).'°C' : '—' }}</td>
                <td style="font-size:.78rem">
                    @if($eq->proxima_calibracion)
                        {{ \Carbon\Carbon::parse($eq->proxima_calibracion)->format('d/m/Y') }}
                        @if(\Carbon\Carbon::parse($eq->proxima_calibracion)->isPast())<span class="badge b-CRITICA">VENCIDA</span>@endif
                    @else — @endif
                </td>
                <td>{!! $eq->estado ? '<span class="badge b-CERRADA">Activo</span>' : '<span class="badge b-CRITICA">Inactivo</span>' !!}</td>
                <td>
                    <button class="btn-mini" onclick='editarEquipo(@json($eq))'>Editar</button>
                    <form method="POST" action="{{ route('admin.calidad.cadena-frio.equipos.destroy', $eq) }}" style="display:inline" onsubmit="return confirm('¿Eliminar equipo?')">
                        @csrf @method('DELETE')
                        <button class="btn-mini btn-del">Eliminar</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="9" style="text-align:center;color:#94a3b8;padding:2rem">Sin equipos registrados</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
<div style="margin-top:.8rem">{{ $equipos->links() }}</div>

{{-- MODAL --}}
<div id="mEquipo" class="modal-bg" style="display:none">
    <div class="modal">
        <div class="modal-h"><h3 id="mTitle">Nuevo Equipo</h3><button onclick="closeModal('mEquipo')" class="x">&times;</button></div>
        <form id="fEquipo" method="POST" action="{{ route('admin.calidad.cadena-frio.equipos.store') }}">
            @csrf
            <input type="hidden" name="_method" id="fMethod" value="POST">
            <div class="grid-2c">
                <div><label>Código *</label><input name="codigo" required></div>
                <div><label>Nombre *</label><input name="nombre" required></div>
                <div><label>Tipo *</label>
                    <select name="tipo" required>
                        <option value="NEVERA">Nevera</option>
                        <option value="CONGELADOR">Congelador</option>
                        <option value="CUARTO_FRIO">Cuarto Frío</option>
                        <option value="TRANSPORTE">Transporte</option>
                    </select>
                </div>
                <div><label>Ubicación</label><input name="ubicacion"></div>
                <div><label>Temp. mín (°C) *</label><input type="number" step="0.1" name="temperatura_min" required></div>
                <div><label>Temp. máx (°C) *</label><input type="number" step="0.1" name="temperatura_max" required></div>
                <div><label>Humedad mín (%)</label><input type="number" step="0.1" name="humedad_min"></div>
                <div><label>Humedad máx (%)</label><input type="number" step="0.1" name="humedad_max"></div>
                <div><label>Fabricante</label><input name="fabricante"></div>
                <div><label>Modelo</label><input name="modelo"></div>
                <div><label>Serial</label><input name="serial"></div>
                <div><label>Estado</label>
                    <select name="estado"><option value="1">Activo</option><option value="0">Inactivo</option></select>
                </div>
                <div><label>Última calibración</label><input type="date" name="fecha_calibracion"></div>
                <div><label>Próxima calibración</label><input type="date" name="proxima_calibracion"></div>
            </div>
            <div style="margin-top:1rem;display:flex;justify-content:flex-end;gap:.5rem">
                <button type="button" class="btn btn-secondary" onclick="closeModal('mEquipo')">Cancelar</button>
                <button class="btn btn-primary">Guardar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }
function editarEquipo(eq) {
    const f = document.getElementById('fEquipo');
    f.action = '{{ url("admin/calidad/cadena-frio/equipos") }}/' + eq.id;
    document.getElementById('fMethod').value = 'PUT';
    document.getElementById('mTitle').innerText = 'Editar Equipo';
    ['codigo','nombre','tipo','ubicacion','temperatura_min','temperatura_max','humedad_min','humedad_max','fabricante','modelo','serial','fecha_calibracion','proxima_calibracion'].forEach(k => {
        if (f.elements[k]) f.elements[k].value = eq[k] ?? '';
    });
    f.elements['estado'].value = eq.estado ? '1' : '0';
    openModal('mEquipo');
}
</script>
</x-app-layout>
