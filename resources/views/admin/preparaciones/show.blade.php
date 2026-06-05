<x-app-layout>
<style>
    :root { --inst:#2e3a75; --inst-dark:#1f2a5c; }
    .page-header { background:linear-gradient(135deg, var(--inst) 0%, #7c3aed 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.5rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.3rem; }
    .page-header a { color:#fff; opacity:.85; text-decoration:none; font-size:.85rem; }

    .pipeline { display:grid; grid-template-columns:repeat(6,1fr); gap:.4rem; background:#fff; padding:1rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1.4rem; }
    @media (max-width:900px) { .pipeline { grid-template-columns:repeat(3,1fr); } }
    .step { text-align:center; padding:.7rem .3rem; border-radius:8px; font-size:.74rem; font-weight:600; color:#94a3b8; background:#f8fafc; border:2px solid transparent; }
    .step.done   { background:#dcfce7; color:#166534; border-color:#22c55e; }
    .step.active { background:#fef9c3; color:#a16207; border-color:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,.2); }
    .step .num { display:block; font-size:1.1rem; font-weight:800; }

    .grid-main { display:grid; grid-template-columns:1.4fr 1fr; gap:1.2rem; }
    @media (max-width:1100px) { .grid-main { grid-template-columns:1fr; } }
    .card { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); padding:1.3rem; margin-bottom:1.2rem; }
    .card h3 { color:var(--inst); margin:0 0 .9rem; font-size:.98rem; font-weight:700; border-bottom:2px solid #f1f5f9; padding-bottom:.45rem; display:flex; justify-content:space-between; align-items:center; }

    .info-row { display:grid; grid-template-columns:140px 1fr; padding:.4rem 0; border-bottom:1px dashed #f1f5f9; font-size:.85rem; }
    .info-row .lbl { color:#64748b; font-weight:600; }
    .info-row .val { color:#1e293b; }

    .badge { display:inline-block; padding:.18rem .6rem; border-radius:18px; font-size:.7rem; font-weight:700; }
    .badge-PROGRAMADA      { background:#dbeafe; color:#1d4ed8; }
    .badge-EN_PROCESO      { background:#fef9c3; color:#a16207; }
    .badge-CONTROL_CALIDAD { background:#fce7f3; color:#9d174d; }
    .badge-LIBERADA        { background:#dcfce7; color:#166534; }
    .badge-ENTREGADA       { background:#e0e7ff; color:#3730a3; }
    .badge-ANULADA         { background:#fee2e2; color:#991b1b; }

    table.tbl { width:100%; border-collapse:collapse; font-size:.83rem; }
    table.tbl th { background:#f1f5f9; padding:.5rem .6rem; text-align:left; font-size:.74rem; }
    table.tbl td { padding:.5rem .6rem; border-bottom:1px solid #f1f5f9; }

    .btn { background:var(--inst); color:#fff; border:none; padding:.5rem 1rem; border-radius:7px; font-size:.83rem; cursor:pointer; text-decoration:none; display:inline-block; margin:.15rem; }
    .btn:hover { background:var(--inst-dark); }
    .btn-warn { background:#f59e0b; }
    .btn-pink { background:#ec4899; }
    .btn-green { background:#22c55e; }
    .btn-purple { background:#8b5cf6; }
    .btn-danger { background:#ef4444; }
    .btn-sm { padding:.3rem .65rem; font-size:.75rem; }

    input, select, textarea { width:100%; padding:.45rem .7rem; border:1.5px solid #e2e8f0; border-radius:7px; font-size:.83rem; }
    label { font-size:.74rem; font-weight:600; color:#64748b; display:block; margin-bottom:.25rem; }

    .alert { padding:.7rem 1rem; border-radius:8px; margin-bottom:1rem; font-size:.88rem; }
    .alert-success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .alert-error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
</style>

@php
    $orden = ['PROGRAMADA'=>1,'EN_PROCESO'=>2,'CONTROL_CALIDAD'=>3,'LIBERADA'=>4,'ENTREGADA'=>5,'ANULADA'=>6];
    $cur = $orden[$preparacion->estado] ?? 0;
@endphp

<div class="page-header">
    <div>
        <h1>{{ $preparacion->codigo }} <span class="badge badge-{{ $preparacion->estado }}" style="margin-left:.5rem">{{ \App\Models\Preparacion::ESTADOS[$preparacion->estado] }}</span></h1>
        <p style="margin:.2rem 0 0;font-size:.82rem;opacity:.85">{{ $preparacion->tipo?->nombre }} · {{ \Carbon\Carbon::parse($preparacion->fecha_programada)->format('d/m/Y H:i') }}</p>
    </div>
    <a href="{{ route('admin.preparaciones.index') }}">&#8592; Volver al Kanban</a>
</div>

@if(session('success'))<div class="alert alert-success">&#10003; {{ session('success') }}</div>@endif
@if(session('error'))  <div class="alert alert-error">&#9888; {{ session('error') }}</div>@endif

{{-- LÍNEA DE PRODUCCIÓN --}}
<div class="pipeline">
    @foreach(['Prescripción'=>1,'Preparación'=>2,'Control Calidad'=>3,'Liberación'=>4,'Entrega'=>5,'Cierre'=>5] as $lbl=>$min)
        @php $cls = $cur > $min ? 'done' : ($cur == $min ? 'active' : ''); @endphp
        <div class="step {{ $cls }}">
            <span class="num">{{ $loop->iteration }}</span>
            {{ $lbl }}
        </div>
    @endforeach
</div>

<div class="grid-main">
    <div>
        {{-- Datos --}}
        <div class="card">
            <h3>Información</h3>
            <div class="info-row"><span class="lbl">Tipo</span><span class="val">{{ $preparacion->tipo?->nombre ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">Paciente</span><span class="val">{{ $preparacion->paciente?->nombre_completo ?? 'Sin paciente asignado' }} @if($preparacion->paciente) · Doc. {{ $preparacion->paciente->documento }} @endif</span></div>
            <div class="info-row"><span class="lbl">Servicio</span><span class="val">{{ $preparacion->servicio?->nombre ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">Programada</span><span class="val">{{ \Carbon\Carbon::parse($preparacion->fecha_programada)->format('d/m/Y H:i') }}</span></div>
            <div class="info-row"><span class="lbl">Inicio</span><span class="val">{{ $preparacion->fecha_inicio ? \Carbon\Carbon::parse($preparacion->fecha_inicio)->format('d/m/Y H:i') : '—' }}</span></div>
            <div class="info-row"><span class="lbl">Fin</span><span class="val">{{ $preparacion->fecha_fin ? \Carbon\Carbon::parse($preparacion->fecha_fin)->format('d/m/Y H:i') : '—' }}</span></div>
            <div class="info-row"><span class="lbl">Volumen final</span><span class="val">{{ $preparacion->volumen_final ?? '—' }} {{ $preparacion->unidadVolumen?->nombre }}</span></div>
            <div class="info-row"><span class="lbl">Costo total</span><span class="val">$ {{ number_format($preparacion->costo_total, 2, ',', '.') }}</span></div>
            <div class="info-row"><span class="lbl">Preparador</span><span class="val">{{ $preparacion->preparador?->name ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">Validador</span><span class="val">{{ $preparacion->validador?->name ?? '—' }}</span></div>
            @if($preparacion->observaciones)
            <div class="info-row"><span class="lbl">Observaciones</span><span class="val">{{ $preparacion->observaciones }}</span></div>
            @endif
        </div>

        {{-- Fórmula --}}
        <div class="card">
            <h3>Fórmula · Componentes ({{ $preparacion->detalles->count() }})</h3>
            <table class="tbl">
                <thead><tr><th>Medicamento</th><th>Presentación</th><th>Dosis</th><th>Unidad</th><th>Concentración</th><th>Obs.</th></tr></thead>
                <tbody>
                @forelse($preparacion->detalles as $d)
                    <tr>
                        <td>{{ $d->medicamento?->nombre ?? '—' }}</td>
                        <td>{{ $d->presentacion?->nombre ?? '—' }}</td>
                        <td>{{ $d->dosis }}</td>
                        <td>{{ $d->unidadMedida?->nombre ?? '—' }}</td>
                        <td>{{ $d->concentracion ?? '—' }}</td>
                        <td>{{ $d->observaciones ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#94a3b8">Sin componentes</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Consumo por lotes --}}
        <div class="card">
            <h3>
                Consumo real por lote ({{ $preparacion->consumos->count() }})
                <span style="font-size:.75rem;color:#64748b;font-weight:500">Trazabilidad por paciente</span>
            </h3>

            @if(in_array($preparacion->estado, ['PROGRAMADA','EN_PROCESO']))
            <form method="POST" action="{{ route('admin.preparaciones.consumo', $preparacion) }}" style="display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:.5rem;align-items:end;background:#f8fafc;padding:.7rem;border-radius:8px;margin-bottom:.8rem">
                @csrf
                <div>
                    <label>Lote</label>
                    <select name="inventario_lote_id" required>
                        <option value="">— Seleccione lote —</option>
                        @foreach($lotes as $l)
                            <option value="{{ $l->id }}">{{ $l->medicamento?->nombre }} · Lote {{ $l->lote }} · stock {{ $l->cantidad_actual }} · vto {{ $l->fecha_vencimiento ? \Carbon\Carbon::parse($l->fecha_vencimiento)->format('d/m/Y') : '—' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Cantidad consumida</label>
                    <input type="number" step="0.0001" min="0.0001" name="cantidad_consumida" required>
                </div>
                <div></div>
                <button type="submit" class="btn btn-sm btn-green">&#43; Registrar</button>
            </form>
            @endif

            <table class="tbl">
                <thead><tr><th>Medicamento</th><th>Lote</th><th>Vto.</th><th>Cantidad</th><th>Costo unit.</th><th>Costo total</th><th></th></tr></thead>
                <tbody>
                @forelse($preparacion->consumos as $c)
                    <tr>
                        <td>{{ $c->medicamento?->nombre ?? '—' }}</td>
                        <td><code>{{ $c->lote }}</code></td>
                        <td>{{ $c->fecha_vencimiento ? \Carbon\Carbon::parse($c->fecha_vencimiento)->format('d/m/Y') : '—' }}</td>
                        <td>{{ $c->cantidad_consumida }}</td>
                        <td>$ {{ number_format($c->costo_unitario, 2, ',', '.') }}</td>
                        <td>$ {{ number_format($c->costo_total, 2, ',', '.') }}</td>
                        <td>
                            @if(in_array($preparacion->estado, ['PROGRAMADA','EN_PROCESO','CONTROL_CALIDAD']))
                            <form method="POST" action="{{ route('admin.preparaciones.consumo.eliminar', [$preparacion, $c]) }}" onsubmit="return confirm('¿Eliminar consumo?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">&times;</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#94a3b8">Sin consumos registrados</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Controles de calidad --}}
        @if($preparacion->controles->count())
        <div class="card">
            <h3>Controles de calidad ({{ $preparacion->controles->count() }})</h3>
            <table class="tbl">
                <thead><tr><th>Fecha</th><th>Aspecto</th><th>Volumen</th><th>pH</th><th>Osmolaridad</th><th>Cumple</th><th>Usuario</th></tr></thead>
                <tbody>
                @foreach($preparacion->controles as $cc)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($cc->fecha_control)->format('d/m/Y H:i') }}</td>
                        <td>{{ $cc->aspecto_visual ?? '—' }}</td>
                        <td>{{ $cc->volumen_verificado ?? '—' }}</td>
                        <td>{{ $cc->ph ?? '—' }}</td>
                        <td>{{ $cc->osmolaridad ?? '—' }}</td>
                        <td><span class="badge {{ $cc->cumple ? 'badge-LIBERADA' : 'badge-ANULADA' }}">{{ $cc->cumple ? 'Sí' : 'No' }}</span></td>
                        <td>{{ $cc->usuario?->name ?? '—' }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- Entrega --}}
        @if($preparacion->entrega)
        <div class="card">
            <h3>Entrega</h3>
            <div class="info-row"><span class="lbl">Fecha</span><span class="val">{{ \Carbon\Carbon::parse($preparacion->entrega->fecha_entrega)->format('d/m/Y H:i') }}</span></div>
            <div class="info-row"><span class="lbl">Recibido por</span><span class="val">{{ $preparacion->entrega->recibido_por ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">Servicio destino</span><span class="val">{{ $preparacion->entrega->servicio?->nombre ?? '—' }}</span></div>
            <div class="info-row"><span class="lbl">Entrega por</span><span class="val">{{ $preparacion->entrega->usuario?->name ?? '—' }}</span></div>
            @if($preparacion->entrega->observaciones)
            <div class="info-row"><span class="lbl">Observaciones</span><span class="val">{{ $preparacion->entrega->observaciones }}</span></div>
            @endif
        </div>
        @endif
    </div>

    {{-- COLUMNA ACCIONES --}}
    <div>
        <div class="card">
            <h3>Acciones</h3>

            @if($preparacion->estado === 'PROGRAMADA')
            <form method="POST" action="{{ route('admin.preparaciones.iniciar', $preparacion) }}">
                @csrf @method('PATCH')
                <button class="btn btn-warn" style="width:100%">&#9654; Iniciar preparación</button>
            </form>
            @endif

            @if($preparacion->estado === 'EN_PROCESO')
            <form method="POST" action="{{ route('admin.preparaciones.enviarControl', $preparacion) }}">
                @csrf @method('PATCH')
                <button class="btn btn-pink" style="width:100%">&#10145; Enviar a Control de Calidad</button>
            </form>
            @endif

            @if($preparacion->estado === 'CONTROL_CALIDAD')
            <form method="POST" action="{{ route('admin.preparaciones.control', $preparacion) }}" style="margin-bottom:.8rem">
                @csrf
                <h4 style="font-size:.9rem;color:var(--inst);margin:0 0 .5rem">Registrar control</h4>
                <label>Aspecto visual</label>
                <input type="text" name="aspecto_visual" placeholder="Claro, sin precipitados...">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.4rem;margin-top:.4rem">
                    <div><label>Volumen verificado</label><input type="number" step="0.01" name="volumen_verificado"></div>
                    <div><label>pH</label><input type="number" step="0.01" name="ph"></div>
                </div>
                <label style="margin-top:.4rem">Osmolaridad</label>
                <input type="number" step="0.01" name="osmolaridad">
                <label style="margin-top:.4rem">¿Cumple? *</label>
                <select name="cumple" required>
                    <option value="1">Sí, cumple</option>
                    <option value="0">No cumple</option>
                </select>
                <label style="margin-top:.4rem">Observaciones</label>
                <textarea name="observaciones" rows="2"></textarea>
                <button class="btn btn-pink" style="width:100%;margin-top:.5rem">Guardar control</button>
            </form>

            @php $ult = $preparacion->controles->sortByDesc('fecha_control')->first(); @endphp
            @if($ult && $ult->cumple)
            @puede('Preparaciones','Aprobar')
            <form method="POST" action="{{ route('admin.preparaciones.liberar', $preparacion) }}">
                @csrf @method('PATCH')
                <button class="btn btn-green" style="width:100%">&#10003; Liberar (descuenta inventario)</button>
            </form>
            @endpuede
            @endif
            @endif

            @if($preparacion->estado === 'LIBERADA')
            <form method="POST" action="{{ route('admin.preparaciones.entregar', $preparacion) }}">
                @csrf
                <h4 style="font-size:.9rem;color:var(--inst);margin:0 0 .5rem">Entregar al servicio</h4>
                <label>Recibido por</label>
                <input type="text" name="recibido_por" placeholder="Nombre del responsable">
                <label style="margin-top:.4rem">Observaciones</label>
                <textarea name="observaciones" rows="2"></textarea>
                <button class="btn btn-purple" style="width:100%;margin-top:.5rem">&#128230; Registrar entrega</button>
            </form>
            @endif

            @if(!in_array($preparacion->estado, ['ENTREGADA','ANULADA']))
            @puede('Preparaciones','Anular')
            <form method="POST" action="{{ route('admin.preparaciones.anular', $preparacion) }}" onsubmit="return confirm('¿Anular esta preparación?')" style="margin-top:.7rem">
                @csrf @method('PATCH')
                <button class="btn btn-danger" style="width:100%">&#9888; Anular preparación</button>
            </form>
            @endpuede
            @endif

            @if($preparacion->estado === 'PROGRAMADA')
            <form method="POST" action="{{ route('admin.preparaciones.destroy', $preparacion) }}" onsubmit="return confirm('¿Eliminar definitivamente?')" style="margin-top:.5rem">
                @csrf @method('DELETE')
                <button class="btn btn-danger" style="width:100%;background:#7f1d1d">&times; Eliminar</button>
            </form>
            @endif
        </div>

        <div class="card">
            <h3>Trazabilidad</h3>
            <p style="font-size:.8rem;color:#64748b;line-height:1.5">
                Esta preparación tiene <strong>{{ $preparacion->consumos->count() }}</strong> lote(s) consumido(s).
                Al liberar, se generan movimientos tipo <code>PRODUCCION</code> en el Kardex con referencia a esta preparación.
            </p>
            @if($preparacion->estado === 'LIBERADA' || $preparacion->estado === 'ENTREGADA')
                <a href="{{ route('admin.kardex.index', ['referencia' => 'Preparacion', 'id' => $preparacion->id]) }}" class="btn btn-sm" style="margin-top:.5rem">Ver en Kardex &#8594;</a>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
