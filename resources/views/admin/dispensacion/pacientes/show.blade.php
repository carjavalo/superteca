<x-app-layout>
@php
    $bclass = ['ACTIVO'=>'b-act','EGRESADO'=>'b-egr','FALLECIDO'=>'b-fal'][$paciente->estado_clinico ?? 'ACTIVO'] ?? 'b-act';
@endphp
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #14b8a6 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }
    .badge { display:inline-block; padding:.25rem .8rem; border-radius:14px; font-size:.78rem; font-weight:700; }
    .b-act { background:#dcfce7; color:#166534; }
    .b-egr { background:#e0e7ff; color:#3730a3; }
    .b-fal { background:#fee2e2; color:#991b1b; }

    .alert-box { background:#fee2e2; color:#991b1b; padding:.7rem 1rem; border-left:4px solid #dc2626; border-radius:8px; margin-bottom:1rem; font-weight:600; }

    .tabs { background:#fff; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); overflow:hidden; margin-bottom:1rem; }
    .tabs-nav { display:flex; border-bottom:2px solid #f1f5f9; overflow-x:auto; }
    .tabs-nav a { padding:.8rem 1.2rem; text-decoration:none; color:#64748b; font-weight:600; font-size:.85rem; border-bottom:3px solid transparent; white-space:nowrap; }
    .tabs-nav a.active { color:#14b8a6; border-color:#14b8a6; }
    .tabs-nav a:hover { background:#f8fafc; }
    .tab-content { padding:1.4rem; }

    .info-grid { display:grid; gap:1rem; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); margin-bottom:1rem; }
    .info { background:#f8fafc; padding:.8rem 1rem; border-radius:8px; }
    .info .lbl { font-size:.7rem; color:#94a3b8; text-transform:uppercase; font-weight:600; }
    .info .val { font-size:.92rem; font-weight:600; color:#1e293b; margin-top:.2rem; }

    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:.6rem .8rem; font-size:.7rem; text-align:left; color:#475569; text-transform:uppercase; }
    td { padding:.55rem .8rem; font-size:.83rem; border-top:1px solid #f1f5f9; vertical-align:top; }

    .btn { padding:.5rem 1rem; border-radius:8px; text-decoration:none; font-size:.82rem; font-weight:600; border:none; cursor:pointer; display:inline-block; }
    .btn-primary { background:#14b8a6; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-warn { background:#f59e0b; color:#fff; }
    .btn-sm { padding:.3rem .6rem; font-size:.75rem; }
    label { font-size:.7rem; color:#475569; font-weight:600; display:block; margin-bottom:.2rem; }
    input,select,textarea { width:100%; padding:.5rem .65rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.83rem; box-sizing:border-box; }

    .timeline { display:flex; gap:.8rem; margin:1rem 0; flex-wrap:wrap; }
    .tline-item { flex:1; min-width:160px; background:#f8fafc; padding:.7rem .9rem; border-radius:8px; border-left:4px solid #14b8a6; }
    .tline-item .h { font-size:.7rem; color:#64748b; text-transform:uppercase; font-weight:600; }
    .tline-item .v { font-size:.95rem; font-weight:600; color:#1e293b; margin-top:.2rem; }

    .lote-tag { background:#e0f2fe; color:#075985; padding:.15rem .55rem; border-radius:8px; font-family:monospace; font-size:.72rem; }
    .sev-leve { background:#fef3c7; color:#92400e; padding:.15rem .55rem; border-radius:10px; font-size:.7rem; font-weight:600; }
    .sev-mod  { background:#fed7aa; color:#9a3412; padding:.15rem .55rem; border-radius:10px; font-size:.7rem; font-weight:600; }
    .sev-sev  { background:#fee2e2; color:#991b1b; padding:.15rem .55rem; border-radius:10px; font-size:.7rem; font-weight:600; }
</style>

<div class="page-header">
    <div>
        <h1>{{ trim($paciente->apellidos.' '.$paciente->nombres) }}</h1>
        <p>{{ $paciente->tipo_documento }} {{ $paciente->documento }} · {{ $paciente->edad ?? '—' }} años · {{ $paciente->sexo ?? '—' }} · Cama {{ $paciente->cama ?? '—' }} · {{ $paciente->servicio->nombre ?? 'Sin servicio' }}</p>
    </div>
    <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
        <span class="badge {{ $bclass }}">{{ \App\Models\Paciente::ESTADOS_CLINICOS[$paciente->estado_clinico ?? 'ACTIVO'] ?? 'Activo' }}</span>
        <a href="{{ route('admin.dispensacion.pacientes.index') }}" class="btn btn-secondary">← Volver</a>
    </div>
</div>

@if(session('success'))<div style="background:#dcfce7;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#10003; {{ session('success') }}</div>@endif
@if(session('error'))<div style="background:#fee2e2;color:#991b1b;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#9888; {{ session('error') }}</div>@endif

@foreach($alertas as $a)
    <div class="alert-box">⚠ {{ $a }}</div>
@endforeach

<div class="timeline">
    <div class="tline-item"><div class="h">Prescripciones</div><div class="v">{{ $paciente->prescripciones->count() }}</div></div>
    <div class="tline-item"><div class="h">Preparaciones</div><div class="v">{{ $totalPrep }}</div></div>
    <div class="tline-item"><div class="h">Entregas</div><div class="v">{{ $totalEntregas }}</div></div>
    <div class="tline-item"><div class="h">Lotes recibidos</div><div class="v">{{ $paciente->dispensaciones->pluck('lote')->unique()->count() }}</div></div>
    <div class="tline-item"><div class="h">Costo dispensado</div><div class="v">${{ number_format($totalDispensado,0,',','.') }}</div></div>
</div>

<div class="tabs">
    <div class="tabs-nav">
        @php $tabs = ['general'=>'General','alergias'=>'Alergias ('.$paciente->alergias->count().')','diagnosticos'=>'Diagnósticos ('.$paciente->diagnosticos->count().')','prescripciones'=>'Prescripciones ('.$paciente->prescripciones->count().')','tratamientos'=>'Tratamientos','dispensaciones'=>'Dispensaciones ('.$paciente->dispensaciones->count().')','timeline'=>'Timeline','trazabilidad'=>'Trazabilidad lotes']; @endphp
        @foreach($tabs as $k=>$v)
            <a href="?tab={{ $k }}" class="{{ $tab===$k?'active':'' }}">{{ $v }}</a>
        @endforeach
    </div>

    <div class="tab-content">
    @if($tab==='general')
        <form method="POST" action="{{ route('admin.dispensacion.pacientes.update', $paciente) }}">
            @csrf @method('PUT')
            <div class="info-grid">
                <div><label>Tipo doc.</label>
                    <select name="tipo_documento" required>
                        @foreach(['CC'=>'CC','TI'=>'TI','RC'=>'RC','CE'=>'CE','PA'=>'PA'] as $k=>$v)
                            <option value="{{ $k }}" {{ $paciente->tipo_documento==$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Documento</label><input type="text" name="documento" required value="{{ $paciente->documento }}"></div>
                <div><label>Nombres</label><input type="text" name="nombres" required value="{{ $paciente->nombres }}"></div>
                <div><label>Apellidos</label><input type="text" name="apellidos" required value="{{ $paciente->apellidos }}"></div>
                <div><label>F. nacimiento</label><input type="date" name="fecha_nacimiento" value="{{ $paciente->fecha_nacimiento?->format('Y-m-d') }}"></div>
                <div><label>Sexo</label><select name="sexo"><option value="">—</option>@foreach(['M','F','O'] as $s)<option value="{{ $s }}" {{ $paciente->sexo==$s?'selected':'' }}>{{ $s }}</option>@endforeach</select></div>
                <div><label>Peso</label><input type="number" step="0.01" name="peso" value="{{ $paciente->peso }}"></div>
                <div><label>Talla</label><input type="number" step="0.01" name="talla" value="{{ $paciente->talla }}"></div>
                <div><label>EPS</label><input type="text" name="eps" value="{{ $paciente->eps }}"></div>
                <div><label>Servicio</label>
                    <select name="servicio_id"><option value="">—</option>
                    @foreach(\App\Models\ServicioHospitalario::where('estado',1)->orderBy('nombre')->get() as $s)
                        <option value="{{ $s->id }}" {{ $paciente->servicio_id==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
                    @endforeach
                    </select>
                </div>
                <div><label>Cama</label><input type="text" name="cama" value="{{ $paciente->cama }}"></div>
                <div><label>F. ingreso</label><input type="datetime-local" name="fecha_ingreso" value="{{ $paciente->fecha_ingreso?->format('Y-m-d\TH:i') }}"></div>
                <div><label>F. egreso</label><input type="datetime-local" name="fecha_egreso" value="{{ $paciente->fecha_egreso?->format('Y-m-d\TH:i') }}"></div>
                <div><label>Estado clínico</label>
                    <select name="estado_clinico" required>
                        @foreach(\App\Models\Paciente::ESTADOS_CLINICOS as $k=>$v)
                            <option value="{{ $k }}" {{ ($paciente->estado_clinico??'ACTIVO')==$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label>Teléfono</label><input type="text" name="telefono" value="{{ $paciente->telefono }}"></div>
                <div><label>Correo</label><input type="email" name="correo" value="{{ $paciente->correo }}"></div>
                <div style="grid-column:1/-1"><label>Dirección</label><input type="text" name="direccion" value="{{ $paciente->direccion }}"></div>
                <div style="grid-column:1/-1"><label>Observaciones</label><textarea name="observaciones" rows="2">{{ $paciente->observaciones }}</textarea></div>
            </div>
            <div style="text-align:right"><button class="btn btn-primary">Guardar Cambios</button></div>
        </form>
    @endif

    @if($tab==='alergias')
        <form method="POST" action="{{ route('admin.dispensacion.pacientes.alergias.store', $paciente) }}" style="display:grid;grid-template-columns:1fr 1fr 150px 120px;gap:.5rem;align-items:end;margin-bottom:1rem">
            @csrf
            <div><label>Descripción *</label><input type="text" name="descripcion" required></div>
            <div><label>Medicamento</label>
                <select name="medicamento_id"><option value="">—</option>
                @foreach($medicamentos as $m)<option value="{{ $m->id }}">{{ $m->nombre }}</option>@endforeach
                </select>
            </div>
            <div><label>Severidad *</label>
                <select name="severidad" required>@foreach(\App\Models\PacienteAlergia::SEVERIDADES as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
            </div>
            <button class="btn btn-primary">+ Agregar</button>
        </form>
        <table>
            <thead><tr><th>Descripción</th><th>Medicamento</th><th>Severidad</th><th>Observaciones</th><th></th></tr></thead>
            <tbody>
                @forelse($paciente->alergias as $a)
                    <tr>
                        <td><strong>{{ $a->descripcion }}</strong></td>
                        <td>{{ $a->medicamento->nombre ?? '—' }}</td>
                        <td><span class="sev-{{ ['LEVE'=>'leve','MODERADA'=>'mod','SEVERA'=>'sev'][$a->severidad] }}">{{ $a->severidad }}</span></td>
                        <td>{{ $a->observaciones }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.dispensacion.pacientes.alergias.destroy', [$paciente, $a]) }}" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')<button class="btn btn-danger btn-sm">×</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:1.5rem">Sin alergias registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if($tab==='diagnosticos')
        <form method="POST" action="{{ route('admin.dispensacion.pacientes.diagnosticos.store', $paciente) }}" style="display:grid;grid-template-columns:130px 1fr 100px 130px 120px;gap:.5rem;align-items:end;margin-bottom:1rem">
            @csrf
            <div><label>CIE-10</label><input type="text" name="codigo_cie10" placeholder="J45"></div>
            <div><label>Descripción *</label><input type="text" name="descripcion" required></div>
            <div><label>Principal</label><select name="principal"><option value="0">No</option><option value="1">Sí</option></select></div>
            <div><label>Fecha</label><input type="date" name="fecha_diagnostico" value="{{ now()->format('Y-m-d') }}"></div>
            <button class="btn btn-primary">+ Agregar</button>
        </form>
        <table>
            <thead><tr><th>CIE-10</th><th>Descripción</th><th>Principal</th><th>Fecha</th><th></th></tr></thead>
            <tbody>
                @forelse($paciente->diagnosticos as $d)
                    <tr>
                        <td style="font-family:monospace">{{ $d->codigo_cie10 ?? '—' }}</td>
                        <td>{{ $d->descripcion }}</td>
                        <td>{{ $d->principal ? 'Sí' : 'No' }}</td>
                        <td>{{ $d->fecha_diagnostico?->format('d/m/Y') ?? '—' }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.dispensacion.pacientes.diagnosticos.destroy', [$paciente, $d]) }}" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')<button class="btn btn-danger btn-sm">×</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:1.5rem">Sin diagnósticos.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if($tab==='prescripciones')
        <details style="margin-bottom:1.2rem;background:#f8fafc;padding:1rem;border-radius:8px">
            <summary style="cursor:pointer;font-weight:600;color:#14b8a6">+ Nueva Prescripción</summary>
            <form method="POST" action="{{ route('admin.dispensacion.pacientes.prescripciones.store', $paciente) }}" style="margin-top:1rem">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.5rem">
                    <div><label>Médico</label>
                        <select name="medico_id"><option value="">—</option>
                        @foreach($medicos as $m)<option value="{{ $m->id }}">{{ $m->name }}</option>@endforeach
                        </select>
                    </div>
                    <div><label>Fecha *</label><input type="datetime-local" name="fecha_prescripcion" required value="{{ now()->format('Y-m-d\TH:i') }}"></div>
                </div>
                <div style="margin-top:.6rem"><label>Observaciones</label><textarea name="observaciones" rows="2"></textarea></div>
                <h4 style="margin:1rem 0 .5rem 0">Detalles</h4>
                <table id="rxDet">
                    <thead><tr><th>Medicamento *</th><th>Dosis *</th><th>Unidad</th><th>Frecuencia</th><th>Días</th><th>Vía</th><th></th></tr></thead>
                    <tbody></tbody>
                </table>
                <button type="button" class="btn btn-secondary btn-sm" onclick="addRx()" style="margin-top:.5rem">+ Línea</button>
                <div style="text-align:right;margin-top:.7rem"><button class="btn btn-primary">Crear Prescripción</button></div>
            </form>
        </details>

        @forelse($paciente->prescripciones->sortByDesc('fecha_prescripcion') as $rx)
            <div style="border:1px solid #e2e8f0;border-radius:10px;margin-bottom:.7rem">
                <div style="padding:.7rem 1rem;background:#f8fafc;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #e2e8f0">
                    <div>
                        <strong style="font-family:monospace;color:#14b8a6">{{ $rx->codigo }}</strong>
                        · {{ $rx->fecha_prescripcion->format('d/m/Y H:i') }}
                        · {{ $rx->medico->name ?? '—' }}
                        @php $bk=['ACTIVA'=>'b-act','SUSPENDIDA'=>'b-egr','FINALIZADA'=>'b-fal'][$rx->estado] ?? 'b-act'; @endphp
                        <span class="badge {{ $bk }}">{{ \App\Models\PacientePrescripcion::ESTADOS[$rx->estado] }}</span>
                    </div>
                    <div style="display:flex;gap:.3rem">
                        <form method="POST" action="{{ route('admin.dispensacion.pacientes.prescripciones.estado', [$paciente, $rx]) }}" style="display:inline">
                            @csrf @method('PATCH')
                            <select name="estado" onchange="this.form.submit()" style="padding:.3rem">
                                @foreach(\App\Models\PacientePrescripcion::ESTADOS as $k=>$v)
                                    <option value="{{ $k }}" {{ $rx->estado==$k?'selected':'' }}>{{ $v }}</option>
                                @endforeach
                            </select>
                        </form>
                        <form method="POST" action="{{ route('admin.dispensacion.pacientes.prescripciones.destroy', [$paciente, $rx]) }}" onsubmit="return confirm('¿Eliminar prescripción?')">
                            @csrf @method('DELETE')<button class="btn btn-danger btn-sm">×</button>
                        </form>
                    </div>
                </div>
                <table>
                    <thead><tr><th>Medicamento</th><th>Dosis</th><th>Frec.</th><th>Días</th><th>Vía</th><th>Obs.</th></tr></thead>
                    <tbody>
                        @foreach($rx->detalles as $d)
                            <tr>
                                <td><strong>{{ $d->medicamento->nombre ?? '—' }}</strong></td>
                                <td>{{ rtrim(rtrim(number_format($d->dosis,4,'.',''),'0'),'.') }} {{ $d->unidadMedida->nombre ?? '' }}</td>
                                <td>{{ $d->frecuencia ?? '—' }}</td>
                                <td>{{ $d->duracion_dias ?? '—' }}</td>
                                <td>{{ $d->viaAdministracion->nombre ?? '—' }}</td>
                                <td>{{ $d->observaciones }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($rx->observaciones)<div style="padding:.5rem 1rem;font-size:.8rem;color:#475569;background:#fafafa">{{ $rx->observaciones }}</div>@endif
            </div>
        @empty
            <div style="text-align:center;color:#94a3b8;padding:2rem">Sin prescripciones registradas.</div>
        @endforelse

        <script>
            const meds = @json($medicamentos->map(fn($m)=>['id'=>$m->id,'nombre'=>$m->nombre]));
            const uns  = @json($unidades->map(fn($u)=>['id'=>$u->id,'nombre'=>$u->nombre]));
            const vias = @json($vias->map(fn($v)=>['id'=>$v->id,'nombre'=>$v->nombre]));
            let rxC = 0;
            function addRx() {
                const i = rxC++;
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><select name="detalles[${i}][medicamento_id]" required>${meds.map(m=>`<option value="${m.id}">${m.nombre}</option>`).join('')}</select></td>
                    <td><input type="number" step="0.01" name="detalles[${i}][dosis]" required></td>
                    <td><select name="detalles[${i}][unidad_medida_id]"><option value="">—</option>${uns.map(u=>`<option value="${u.id}">${u.nombre}</option>`).join('')}</select></td>
                    <td><input type="text" name="detalles[${i}][frecuencia]" placeholder="cada 8h"></td>
                    <td><input type="number" name="detalles[${i}][duracion_dias]"></td>
                    <td><select name="detalles[${i}][via_administracion_id]"><option value="">—</option>${vias.map(v=>`<option value="${v.id}">${v.nombre}</option>`).join('')}</select></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">×</button></td>
                `;
                document.querySelector('#rxDet tbody').appendChild(tr);
            }
            document.addEventListener('DOMContentLoaded', () => { if (document.querySelector('#rxDet')) addRx(); });
        </script>
    @endif

    @if($tab==='tratamientos')
        <table>
            <thead><tr><th>Inicio</th><th>Fin</th><th>Preparación</th><th>Mezcla</th><th>Estado</th><th>Observaciones</th></tr></thead>
            <tbody>
                @forelse($paciente->tratamientos as $t)
                    <tr>
                        <td>{{ $t->fecha_inicio?->format('d/m/Y H:i') }}</td>
                        <td>{{ $t->fecha_fin?->format('d/m/Y H:i') ?? '—' }}</td>
                        <td>{{ $t->preparacion->codigo ?? '—' }}</td>
                        <td>{{ $t->mezcla->codigo ?? '—' }}</td>
                        <td>{{ \App\Models\PacienteTratamiento::ESTADOS[$t->estado] ?? $t->estado }}</td>
                        <td>{{ $t->observaciones }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:1.5rem">Sin tratamientos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if($tab==='dispensaciones')
        <p style="color:#475569;font-size:.85rem;margin-bottom:.7rem">Lotes de medicamentos efectivamente entregados a este paciente (registro automático al confirmar entregas).</p>
        <table>
            <thead><tr><th>Fecha</th><th>Entrega</th><th>Medicamento</th><th>Lote</th><th>Vence</th><th>Cantidad</th><th>Costo</th></tr></thead>
            <tbody>
                @forelse($paciente->dispensaciones->sortByDesc('fecha_entrega') as $d)
                    <tr>
                        <td>{{ $d->fecha_entrega?->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($d->entrega)
                                <a href="{{ route('admin.dispensacion.entregas.show', $d->entrega_id) }}" style="color:#14b8a6;font-family:monospace;text-decoration:none">{{ $d->entrega->codigo }}</a>
                            @else — @endif
                        </td>
                        <td>{{ $d->medicamento->nombre ?? '—' }}</td>
                        <td><span class="lote-tag">{{ $d->lote }}</span></td>
                        <td>{{ $d->fecha_vencimiento?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ rtrim(rtrim(number_format($d->cantidad,2,'.',''),'0'),'.') }}</td>
                        <td>${{ number_format($d->costo_total,0,',','.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:1.5rem">Sin dispensaciones registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif

    @if($tab==='timeline')
        <p style="color:#475569;font-size:.85rem;margin-bottom:1rem">Línea de tiempo farmacoterapéutico del paciente.</p>
        @php
            $eventos = collect();
            if ($paciente->fecha_ingreso) $eventos->push(['t'=>$paciente->fecha_ingreso,'tipo'=>'Ingreso','color'=>'#0ea5e9','desc'=>'Ingreso hospitalario']);
            foreach ($paciente->prescripciones as $r) $eventos->push(['t'=>$r->fecha_prescripcion,'tipo'=>'Prescripción','color'=>'#a855f7','desc'=>"$r->codigo · ".$r->detalles->count().' ítem(s)']);
            foreach ($paciente->preparaciones as $p) $eventos->push(['t'=>$p->fecha_programada,'tipo'=>'Preparación','color'=>'#f59e0b','desc'=>"$p->codigo · ".($p->tipo->nombre ?? '')]);
            foreach ($paciente->entregas as $e) $eventos->push(['t'=>$e->fecha_entrega,'tipo'=>'Entrega','color'=>'#14b8a6','desc'=>"$e->codigo · ".$e->detalles->count().' ítems · '.\App\Models\DispensacionEntrega::ESTADOS[$e->estado]]);
            foreach ($paciente->dispensaciones as $d) $eventos->push(['t'=>$d->fecha_entrega,'tipo'=>'Lote entregado','color'=>'#22c55e','desc'=>($d->medicamento->nombre ?? '')." · Lote $d->lote"]);
            if ($paciente->fecha_egreso) $eventos->push(['t'=>$paciente->fecha_egreso,'tipo'=>'Egreso','color'=>'#64748b','desc'=>'Egreso hospitalario']);
            $eventos = $eventos->filter(fn($e)=>$e['t'])->sortByDesc('t');
        @endphp
        <div style="border-left:3px solid #e2e8f0;padding-left:1.2rem">
            @forelse($eventos as $e)
                <div style="position:relative;margin-bottom:1rem">
                    <span style="position:absolute;left:-1.55rem;top:.2rem;width:14px;height:14px;border-radius:50%;background:{{ $e['color'] }};border:3px solid #fff;box-shadow:0 0 0 2px {{ $e['color'] }}"></span>
                    <div style="font-size:.7rem;color:#94a3b8">{{ \Carbon\Carbon::parse($e['t'])->format('d/m/Y H:i') }}</div>
                    <div style="font-weight:700;color:{{ $e['color'] }}">{{ $e['tipo'] }}</div>
                    <div style="font-size:.85rem;color:#1e293b">{{ $e['desc'] }}</div>
                </div>
            @empty
                <div style="color:#94a3b8;text-align:center;padding:1rem">Sin eventos.</div>
            @endforelse
        </div>
    @endif

    @if($tab==='trazabilidad')
        <p style="color:#475569;font-size:.85rem;margin-bottom:1rem">Trazabilidad de lotes recibidos por el paciente.</p>
        @php $lotes = $paciente->dispensaciones->groupBy(fn($d)=>($d->medicamento->nombre ?? 'N/D').'|'.$d->lote); @endphp
        @forelse($lotes as $key=>$grupo)
            @php [$med,$lote] = explode('|',$key); $first=$grupo->first(); @endphp
            <div style="background:#f8fafc;padding:.9rem 1rem;border-radius:8px;margin-bottom:.6rem;border-left:4px solid #22c55e">
                <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:.5rem">
                    <div>
                        <strong>{{ $med }}</strong> ·
                        <span class="lote-tag">{{ $lote }}</span>
                        @if($first->fecha_vencimiento)<span style="color:#64748b;font-size:.78rem">vence {{ $first->fecha_vencimiento->format('d/m/Y') }}</span>@endif
                    </div>
                    <div style="font-size:.8rem;color:#475569">
                        Total: <strong>{{ rtrim(rtrim(number_format($grupo->sum('cantidad'),2,'.',''),'0'),'.') }}</strong>
                        · Costo: <strong>${{ number_format($grupo->sum('costo_total'),0,',','.') }}</strong>
                        · {{ $grupo->count() }} entrega(s)
                    </div>
                </div>
            </div>
        @empty
            <div style="color:#94a3b8;text-align:center;padding:1rem">Sin lotes dispensados.</div>
        @endforelse
    @endif
    </div>
</div>
</x-app-layout>
