<x-app-layout>
<style>
    :root { --inst:#2e3a75; --teal:#14b8a6; }

    .split-layout { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:1.2rem; align-items:start; }
    @media (max-width:1150px) { .split-layout { grid-template-columns:1fr; } }

    .col-header { color:#fff; padding:1rem 1.4rem; border-radius:12px; margin-bottom:1rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .col-header h1 { margin:0; font-size:1.15rem; }
    .col-header .sub { font-size:.78rem; opacity:.9; }
    .col-header.form { background:linear-gradient(135deg, var(--teal) 0%, var(--inst) 100%); }
    .col-header.list { background:linear-gradient(135deg, var(--inst) 0%, #3b4a96 100%); }
    .col-header .count { background:rgba(255,255,255,.2); padding:.25rem .7rem; border-radius:20px; font-size:.8rem; font-weight:700; }

    .card-box { background:#fff; padding:1.1rem 1.2rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .card-box h2 { margin:0 0 .9rem 0; font-size:.92rem; color:var(--inst); border-bottom:2px solid #f1f5f9; padding-bottom:.5rem; }
    .grid { display:grid; gap:.7rem; }
    .grid-2 { grid-template-columns:repeat(2,1fr); }
    @media (max-width:480px) { .grid-2 { grid-template-columns:1fr; } }
    label { font-size:.72rem; color:#475569; font-weight:600; display:block; margin-bottom:.25rem; }
    input,select,textarea { width:100%; padding:.55rem .7rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; box-sizing:border-box; outline:none; font-family:inherit; }
    input:focus,select:focus,textarea:focus { border-color:var(--teal); box-shadow:0 0 0 3px rgba(20,184,166,.12); }

    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:var(--teal); color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }

    /* ---- Datatable lateral ---- */
    .list-tools { display:flex; gap:.5rem; margin-bottom:.8rem; flex-wrap:wrap; }
    .list-tools input, .list-tools select { padding:.5rem .7rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.82rem; }
    .list-tools .search { flex:1 1 180px; }
    .tabla-wrap { max-height:68vh; overflow-y:auto; border:1px solid #f1f5f9; border-radius:10px; }
    table.dt { width:100%; border-collapse:collapse; }
    table.dt th { position:sticky; top:0; background:#f1f5f9; padding:.6rem .7rem; text-align:left; font-size:.68rem; color:#475569; text-transform:uppercase; z-index:1; }
    table.dt td { padding:.55rem .7rem; font-size:.82rem; border-top:1px solid #f1f5f9; vertical-align:middle; }
    table.dt tr:hover td { background:#f8fafc; cursor:pointer; }
    table.dt td small { color:#94a3b8; font-size:.72rem; }

    .badge { display:inline-block; padding:.16rem .55rem; border-radius:10px; font-size:.68rem; font-weight:600; white-space:nowrap; }
    .b-act { background:#dcfce7; color:#166534; }
    .b-egr { background:#e0e7ff; color:#3730a3; }
    .b-fal { background:#fee2e2; color:#991b1b; }
    .cama-tag { background:#f1f5f9; color:#475569; padding:.1rem .45rem; border-radius:6px; font-family:monospace; font-size:.72rem; }
    .empty-row td { text-align:center; padding:2rem; color:#94a3b8; }
</style>

@if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">
        <ul style="margin:0 0 0 1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<div class="split-layout">

    {{-- ===================== IZQUIERDA · FORMULARIO ===================== --}}
    <form method="POST" action="{{ route('admin.dispensacion.pacientes.store') }}">
        @csrf
        <div class="col-header form">
            <h1>Registrar Paciente</h1>
            <span class="sub">Formulario de ingreso</span>
        </div>

        <div class="card-box">
            <h2>Identificación</h2>
            <div class="grid grid-2">
                <div>
                    <label>Tipo doc. *</label>
                    <select name="tipo_documento" required>
                        @foreach(['CC'=>'Cédula','TI'=>'T. Identidad','RC'=>'R. Civil','CE'=>'Extranjería','PA'=>'Pasaporte'] as $k=>$v)
                            <option value="{{ $k }}" {{ old('tipo_documento','CC')==$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Documento *</label><input type="text" name="documento" required value="{{ old('documento') }}"></div>
                <div><label>Nombres *</label><input type="text" name="nombres" required value="{{ old('nombres') }}"></div>
                <div><label>Apellidos *</label><input type="text" name="apellidos" required value="{{ old('apellidos') }}"></div>
            </div>
        </div>

        <div class="card-box">
            <h2>Datos clínicos</h2>
            <div class="grid grid-2">
                <div><label>Fecha nacimiento</label><input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"></div>
                <div><label>Sexo</label>
                    <select name="sexo">
                        <option value="">—</option>
                        <option value="M" {{ old('sexo')=='M'?'selected':'' }}>Masculino</option>
                        <option value="F" {{ old('sexo')=='F'?'selected':'' }}>Femenino</option>
                        <option value="O" {{ old('sexo')=='O'?'selected':'' }}>Otro</option>
                    </select>
                </div>
                <div><label>Peso (kg)</label><input type="number" step="0.01" name="peso" value="{{ old('peso') }}"></div>
                <div><label>Talla (cm)</label><input type="number" step="0.01" name="talla" value="{{ old('talla') }}"></div>
                <div><label>EPS</label>
                    <select name="eps">
                        <option value="">—</option>
                        @foreach($eps as $e)
                            <option value="{{ $e->Detalle }}" {{ old('eps')==$e->Detalle?'selected':'' }}>{{ $e->Detalle }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Servicio</label>
                    <select name="servicio_id">
                        <option value="">—</option>
                        @foreach($servicios as $s)
                            <option value="{{ $s->id }}" {{ old('servicio_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Cama</label><input type="text" name="cama" value="{{ old('cama') }}"></div>
                <div><label>Estado clínico *</label>
                    <select name="estado_clinico" required>
                        @foreach(\App\Models\Paciente::ESTADOS_CLINICOS as $k=>$v)
                            <option value="{{ $k }}" {{ old('estado_clinico','ACTIVO')==$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Fecha ingreso</label><input type="datetime-local" name="fecha_ingreso" value="{{ old('fecha_ingreso', now()->format('Y-m-d\TH:i')) }}"></div>
            </div>
        </div>

        <div class="card-box">
            <h2>Contacto</h2>
            <div class="grid grid-2">
                <div><label>Teléfono</label><input type="text" name="telefono" value="{{ old('telefono') }}"></div>
                <div><label>Correo</label><input type="email" name="correo" value="{{ old('correo') }}"></div>
            </div>
            <div style="margin-top:.7rem"><label>Dirección</label><input type="text" name="direccion" value="{{ old('direccion') }}"></div>
            <div style="margin-top:.7rem">
                <label>Observaciones</label>
                <textarea name="observaciones" rows="2">{{ old('observaciones') }}</textarea>
            </div>
        </div>

        <div style="display:flex;gap:.7rem;justify-content:flex-end">
            <a href="{{ route('admin.dispensacion.pacientes.index') }}" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-primary">Registrar</button>
        </div>
    </form>

    {{-- ===================== DERECHA · GRILLA DE CONSULTA ===================== --}}
    <div>
        <div class="col-header list">
            <h1>Pacientes registrados</h1>
            <span class="count"><span id="dt-count">{{ $pacientes->count() }}</span> de {{ $pacientes->count() }}</span>
        </div>

        <div class="card-box">
            <div class="list-tools">
                <input type="text" id="dt-search" class="search" placeholder="🔍 Buscar documento, nombre o cama...">
                <select id="dt-estado">
                    <option value="">Todos los estados</option>
                    @foreach(\App\Models\Paciente::ESTADOS_CLINICOS as $k=>$v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
                <select id="dt-servicio">
                    <option value="">Todos los servicios</option>
                    @foreach($servicios as $s)
                        <option value="{{ $s->id }}">{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="tabla-wrap">
                <table class="dt">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Paciente</th>
                            <th>Servicio</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody id="dt-body">
                        @forelse($pacientes as $p)
                            @php
                                $bclass = ['ACTIVO'=>'b-act','EGRESADO'=>'b-egr','FALLECIDO'=>'b-fal'][$p->estado_clinico ?? 'ACTIVO'] ?? 'b-act';
                                $estLabel = \App\Models\Paciente::ESTADOS_CLINICOS[$p->estado_clinico ?? 'ACTIVO'] ?? 'Activo';
                            @endphp
                            <tr class="dt-row"
                                data-search="{{ \Illuminate\Support\Str::lower($p->documento.' '.$p->apellidos.' '.$p->nombres.' '.$p->cama) }}"
                                data-estado="{{ $p->estado_clinico ?? 'ACTIVO' }}"
                                data-servicio="{{ $p->servicio_id ?? '' }}"
                                onclick="window.location='{{ route('admin.dispensacion.pacientes.show', $p) }}'">
                                <td style="font-family:monospace">{{ $p->documento }}</td>
                                <td>
                                    <strong>{{ trim($p->apellidos.' '.$p->nombres) }}</strong>
                                    @if($p->cama)<br><small>Cama <span class="cama-tag">{{ $p->cama }}</span></small>@endif
                                </td>
                                <td>{{ $p->servicio->nombre ?? '—' }}</td>
                                <td><span class="badge {{ $bclass }}">{{ $estLabel }}</span></td>
                            </tr>
                        @empty
                            <tr class="empty-row"><td colspan="4">Aún no hay pacientes registrados.</td></tr>
                        @endforelse
                        <tr id="dt-noresults" style="display:none"><td colspan="4" style="text-align:center;padding:2rem;color:#94a3b8">Sin coincidencias con la búsqueda.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const search   = document.getElementById('dt-search');
        const fEstado  = document.getElementById('dt-estado');
        const fServ    = document.getElementById('dt-servicio');
        const rows     = Array.from(document.querySelectorAll('.dt-row'));
        const noRes    = document.getElementById('dt-noresults');
        const countEl  = document.getElementById('dt-count');

        function applyFilters() {
            const q   = search.value.trim().toLowerCase();
            const est = fEstado.value;
            const srv = fServ.value;
            let visible = 0;

            rows.forEach(tr => {
                const okQ   = !q   || (tr.dataset.search || '').includes(q);
                const okEst = !est || tr.dataset.estado === est;
                const okSrv = !srv || tr.dataset.servicio === srv;
                const show  = okQ && okEst && okSrv;
                tr.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            if (countEl) countEl.textContent = visible;
            if (noRes) noRes.style.display = (visible === 0 && rows.length > 0) ? '' : 'none';
        }

        [search, fEstado, fServ].forEach(el => {
            if (!el) return;
            el.addEventListener('input', applyFilters);
            el.addEventListener('change', applyFilters);
        });
    })();
</script>
</x-app-layout>
