<x-app-layout>
@php
    $val = $validacion;
    $criticas = $val->alertas->where('severidad','CRITICA')->where('resuelta',false)->count();
    $altas    = $val->alertas->where('severidad','ALTA')->where('resuelta',false)->count();
    $medias   = $val->alertas->where('severidad','MEDIA')->where('resuelta',false)->count();
    if ($criticas) { $semColor='#ef4444'; $semTxt='Riesgo crítico'; $semIcon='🔴'; }
    elseif ($altas) { $semColor='#fb923c'; $semTxt='Riesgo moderado'; $semIcon='🟠'; }
    elseif ($medias) { $semColor='#facc15'; $semTxt='Requiere revisión'; $semIcon='🟡'; }
    else { $semColor='#22c55e'; $semTxt='Sin observaciones'; $semIcon='🟢'; }

    $bMap=['PENDIENTE'=>['#fef3c7','#92400e'],'APROBADA'=>['#dcfce7','#166534'],'OBSERVADA'=>['#ede9fe','#6b21a8'],'RECHAZADA'=>['#fee2e2','#991b1b']];
    [$bbg,$btx] = $bMap[$val->resultado];
@endphp

<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #6366f1 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }

    .layout { display:grid; grid-template-columns:300px 1fr 320px; gap:1rem; }
    @media (max-width:1200px) { .layout { grid-template-columns:1fr; } }
    .card { background:#fff; border-radius:12px; padding:1rem 1.2rem; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .card h3 { margin:0 0 .8rem; font-size:.92rem; color:#1e293b; border-bottom:2px solid #f1f5f9; padding-bottom:.4rem; }

    .badge { display:inline-block; padding:.22rem .7rem; border-radius:12px; font-size:.74rem; font-weight:700; }
    .info-row { display:flex; justify-content:space-between; padding:.35rem 0; border-bottom:1px dashed #f1f5f9; font-size:.83rem; }
    .info-row:last-child { border-bottom:none; }
    .info-row .lbl { color:#64748b; font-weight:600; }
    .info-row .val { color:#1e293b; font-weight:600; }

    .semaforo-box { background:#fff; border-radius:12px; padding:1.4rem; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; text-align:center; border-top:4px solid {{ $semColor }}; }
    .semaforo-box .icon { font-size:2.6rem; line-height:1; }
    .semaforo-box .txt { font-size:1.05rem; font-weight:700; color:{{ $semColor }}; margin-top:.4rem; }

    .alerta-item { padding:.7rem .9rem; border-radius:8px; margin-bottom:.5rem; border-left:4px solid #facc15; background:#fffbeb; display:flex; justify-content:space-between; gap:.5rem; }
    .alerta-item.sev-CRITICA { border-color:#ef4444; background:#fef2f2; }
    .alerta-item.sev-ALTA { border-color:#fb923c; background:#fff7ed; }
    .alerta-item.sev-MEDIA { border-color:#facc15; background:#fefce8; }
    .alerta-item.sev-BAJA { border-color:#0ea5e9; background:#f0f9ff; }
    .alerta-item.resuelta { opacity:.5; text-decoration:line-through; }
    .alerta-item .tipo { font-size:.68rem; text-transform:uppercase; font-weight:700; color:#475569; letter-spacing:.5px; }
    .alerta-item .desc { font-size:.85rem; color:#1e293b; margin-top:.15rem; }

    .alergia-tag { background:#fee2e2; color:#991b1b; padding:.18rem .55rem; border-radius:8px; font-size:.72rem; font-weight:600; margin:.15rem; display:inline-block; }
    .diag-tag { background:#e0f2fe; color:#075985; padding:.18rem .55rem; border-radius:8px; font-size:.72rem; font-weight:600; margin:.15rem; display:inline-block; }

    table { width:100%; border-collapse:collapse; }
    th { background:#f1f5f9; padding:.55rem .7rem; font-size:.7rem; text-align:left; color:#475569; text-transform:uppercase; }
    td { padding:.5rem .7rem; font-size:.83rem; border-top:1px solid #f1f5f9; }

    .stock-ok { color:#16a34a; font-weight:700; }
    .stock-bad { color:#dc2626; font-weight:700; }

    .timeline-step { display:flex; gap:.8rem; padding:.4rem 0; align-items:center; }
    .step-circle { width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.8rem; flex-shrink:0; font-weight:700; }
    .step-done { background:#22c55e; color:#fff; }
    .step-current { background:#6366f1; color:#fff; box-shadow:0 0 0 4px #6366f133; }
    .step-pending { background:#e2e8f0; color:#94a3b8; }
    .step-line { width:2px; height:18px; background:#e2e8f0; margin-left:12px; }
    .step-line.done { background:#22c55e; }

    .btn { padding:.5rem 1rem; border-radius:8px; font-size:.83rem; font-weight:600; border:none; cursor:pointer; text-decoration:none; display:inline-block; }
    .btn-primary { background:#6366f1; color:#fff; }
    .btn-success { background:#22c55e; color:#fff; }
    .btn-warn { background:#f59e0b; color:#fff; }
    .btn-danger { background:#ef4444; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }
    .btn-sm { padding:.25rem .55rem; font-size:.72rem; }
</style>

<div class="page-header">
    <div>
        <h1>{{ $val->codigo }} <span style="opacity:.7;font-size:.8rem">· {{ \App\Models\Validacion::TIPOS[$val->tipo_validacion] }}</span></h1>
        <p>{{ $val->fecha_validacion->format('d/m/Y H:i') }} · Farmacéutico: {{ $val->farmaceutico->name ?? '—' }}</p>
    </div>
    <div style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap">
        <span class="badge" style="background:{{ $bbg }};color:{{ $btx }}">{{ \App\Models\Validacion::RESULTADOS[$val->resultado] }}</span>
        <a href="{{ route('admin.dispensacion.validaciones.index') }}" class="btn btn-secondary">← Volver</a>
    </div>
</div>

@if(session('success'))<div style="background:#dcfce7;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#10003; {{ session('success') }}</div>@endif

<div class="semaforo-box">
    <div class="icon">{{ $semIcon }}</div>
    <div class="txt">{{ $semTxt }}</div>
    <div style="font-size:.78rem;color:#64748b;margin-top:.3rem">
        {{ $val->alertas->where('resuelta',false)->count() }} alerta(s) activa(s)
        @if($criticas) · <strong style="color:#ef4444">{{ $criticas }} crítica(s)</strong>@endif
        @if($altas) · <strong style="color:#fb923c">{{ $altas }} alta(s)</strong>@endif
    </div>
</div>

<div class="layout">
    {{-- COLUMNA IZQUIERDA: PANEL CLÍNICO DEL PACIENTE --}}
    <div>
        <div class="card">
            <h3>Paciente</h3>
            @if($val->paciente)
                <div style="font-weight:700;color:#1e293b;font-size:1rem">{{ trim($val->paciente->apellidos.' '.$val->paciente->nombres) }}</div>
                <div style="font-size:.8rem;color:#64748b;margin-bottom:.6rem">{{ $val->paciente->tipo_documento }} {{ $val->paciente->documento }}</div>
                <div class="info-row"><span class="lbl">Edad</span><span class="val">{{ $val->paciente->edad ?? '—' }} años</span></div>
                <div class="info-row"><span class="lbl">Sexo</span><span class="val">{{ $val->paciente->sexo ?? '—' }}</span></div>
                <div class="info-row"><span class="lbl">Peso</span><span class="val">{{ $val->paciente->peso ? rtrim(rtrim(number_format($val->paciente->peso,2,'.',''),'0'),'.').' kg' : '—' }}</span></div>
                <div class="info-row"><span class="lbl">Talla</span><span class="val">{{ $val->paciente->talla ? rtrim(rtrim(number_format($val->paciente->talla,2,'.',''),'0'),'.').' cm' : '—' }}</span></div>
                <div class="info-row"><span class="lbl">Servicio</span><span class="val">{{ $val->paciente->servicio->nombre ?? '—' }}</span></div>
                <div class="info-row"><span class="lbl">Cama</span><span class="val">{{ $val->paciente->cama ?? '—' }}</span></div>
                <a href="{{ route('admin.dispensacion.pacientes.show', $val->paciente) }}" class="btn btn-secondary btn-sm" style="margin-top:.7rem;width:100%;text-align:center">Ver ficha clínica</a>
            @else
                <div style="color:#94a3b8;text-align:center">Sin paciente asociado</div>
            @endif
        </div>

        <div class="card">
            <h3>Alergias</h3>
            @forelse($val->paciente?->alergias ?? [] as $al)
                <div class="alergia-tag" title="{{ $al->severidad }}">⚠ {{ $al->medicamento->nombre ?? $al->descripcion }} ({{ $al->severidad }})</div>
            @empty
                <div style="color:#94a3b8;font-size:.82rem">Sin alergias registradas.</div>
            @endforelse
        </div>

        <div class="card">
            <h3>Diagnósticos</h3>
            @forelse($val->paciente?->diagnosticos ?? [] as $d)
                <div class="diag-tag" title="{{ $d->codigo_cie10 }}">{{ $d->codigo_cie10 ? $d->codigo_cie10.' · ' : '' }}{{ $d->descripcion }}</div>
            @empty
                <div style="color:#94a3b8;font-size:.82rem">Sin diagnósticos.</div>
            @endforelse
        </div>
    </div>

    {{-- COLUMNA CENTRAL: MEDICAMENTOS + IMPACTO INVENTARIO + APROBACIÓN --}}
    <div>
        <div class="card">
            <h3>Medicamentos prescritos</h3>
            <table>
                <thead><tr><th>Medicamento</th><th>Dosis prescrita</th><th>Recomendada</th><th>Unidad</th><th>Vía</th><th>Estado</th></tr></thead>
                <tbody>
                    @forelse($val->detalles as $d)
                        @php
                            $tieneAlergia = $val->paciente?->alergias->where('medicamento_id', $d->medicamento_id)->count() > 0;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $d->medicamento->nombre ?? '—' }}</strong>
                                @if($tieneAlergia)<span style="color:#ef4444;font-size:.7rem;margin-left:.3rem">⚠ ALERGIA</span>@endif
                            </td>
                            <td>{{ rtrim(rtrim(number_format($d->dosis_prescrita ?? 0,4,'.',''),'0'),'.') ?: '—' }}</td>
                            <td>{{ $d->dosis_recomendada ? rtrim(rtrim(number_format($d->dosis_recomendada,4,'.',''),'0'),'.') : '—' }}</td>
                            <td>{{ $d->unidadMedida->nombre ?? '—' }}</td>
                            <td>{{ $d->viaAdministracion->nombre ?? '—' }}</td>
                            <td>{{ \App\Models\ValidacionDetalle::ESTADOS[$d->estado] ?? $d->estado }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:1rem">Sin detalles.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>Impacto en inventario</h3>
            <table>
                <thead><tr><th>Medicamento</th><th>Stock disponible</th><th>Requerido</th><th>Lotes</th><th>Próx. vencimiento</th><th>Estado</th></tr></thead>
                <tbody>
                    @foreach($val->detalles as $d)
                        @php
                            $lotes = $stockInfo[$d->medicamento_id] ?? collect();
                            $stock = $lotes->sum('cantidad_actual');
                            $req = (float) ($d->dosis_prescrita ?? 0);
                            $ok = $stock > 0 && ($req == 0 || $stock >= $req);
                            $proxVence = $lotes->sortBy('fecha_vencimiento')->first()?->fecha_vencimiento;
                        @endphp
                        <tr>
                            <td>{{ $d->medicamento->nombre ?? '—' }}</td>
                            <td class="{{ $stock>0?'stock-ok':'stock-bad' }}">{{ rtrim(rtrim(number_format($stock,2,'.',''),'0'),'.') }}</td>
                            <td>{{ $req ? rtrim(rtrim(number_format($req,4,'.',''),'0'),'.') : '—' }}</td>
                            <td>{{ $lotes->count() }}</td>
                            <td>{{ $proxVence?->format('d/m/Y') ?? '—' }}</td>
                            <td>{!! $ok ? '<span class="stock-ok">✔</span>' : '<span class="stock-bad">✖</span>' !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($val->resultado === 'PENDIENTE' || $val->resultado === 'OBSERVADA')
            <div class="card">
                <h3>Decisión farmacéutica</h3>
                <form method="POST" action="{{ route('admin.dispensacion.validaciones.aprobar', $val) }}">
                    @csrf
                    <textarea name="observaciones" rows="2" placeholder="Observaciones de la decisión..." style="width:100%;padding:.55rem;border:1.5px solid #e2e8f0;border-radius:8px;margin-bottom:.7rem"></textarea>
                    <div style="display:flex;gap:.4rem;flex-wrap:wrap">
                        <button name="accion" value="APROBADA" class="btn btn-success">✓ Aprobar</button>
                        <button name="accion" value="OBSERVADA" class="btn btn-warn">⚠ Marcar como observada</button>
                        <button name="accion" value="DEVUELTA" class="btn btn-secondary">↩ Devolver</button>
                        <button name="accion" value="RECHAZADA" class="btn btn-danger" onclick="return confirm('¿Rechazar la validación?')">✗ Rechazar</button>
                    </div>
                </form>
            </div>
        @endif

        @if($val->aprobaciones->count())
            <div class="card">
                <h3>Historial de decisiones</h3>
                <table>
                    <thead><tr><th>Fecha</th><th>Usuario</th><th>Acción</th><th>Observaciones</th></tr></thead>
                    <tbody>
                        @foreach($val->aprobaciones->sortByDesc('fecha_aprobacion') as $a)
                            <tr>
                                <td>{{ $a->fecha_aprobacion->format('d/m/Y H:i') }}</td>
                                <td>{{ $a->usuario->name ?? '—' }}</td>
                                <td><strong>{{ \App\Models\ValidacionAprobacion::ACCIONES[$a->accion] }}</strong></td>
                                <td>{{ $a->observaciones }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- COLUMNA DERECHA: ALERTAS + TIMELINE --}}
    <div>
        <div class="card">
            <h3>Alertas farmacéuticas ({{ $val->alertas->count() }})</h3>
            @forelse($val->alertas->sortByDesc(fn($a)=>['BAJA'=>1,'MEDIA'=>2,'ALTA'=>3,'CRITICA'=>4][$a->severidad] ?? 0) as $a)
                <div class="alerta-item sev-{{ $a->severidad }} {{ $a->resuelta?'resuelta':'' }}">
                    <div style="flex:1">
                        <div class="tipo">{{ $a->tipo_alerta }} · {{ $a->severidad }}</div>
                        <div class="desc">{{ $a->descripcion }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.dispensacion.validaciones.alertas.toggle', [$val, $a]) }}">
                        @csrf @method('PATCH')
                        <button class="btn btn-sm btn-secondary" title="{{ $a->resuelta?'Reactivar':'Marcar como resuelta' }}">{{ $a->resuelta?'↻':'✓' }}</button>
                    </form>
                </div>
            @empty
                <div style="color:#94a3b8;text-align:center;padding:.8rem;font-size:.82rem">Sin alertas detectadas.</div>
            @endforelse
        </div>

        <div class="card">
            <h3>Timeline de validación</h3>
            @php
                $aprobada = $val->resultado === 'APROBADA';
                $rechazada = $val->resultado === 'RECHAZADA';
            @endphp
            <div class="timeline-step">
                <div class="step-circle step-done">✓</div>
                <div><strong style="font-size:.85rem">Prescripción</strong><div style="font-size:.72rem;color:#64748b">{{ $val->prescripcion?->fecha_prescripcion?->format('d/m/Y H:i') ?? 'Origen registrado' }}</div></div>
            </div>
            <div class="step-line done"></div>
            <div class="timeline-step">
                <div class="step-circle step-done">✓</div>
                <div><strong style="font-size:.85rem">Análisis automático</strong><div style="font-size:.72rem;color:#64748b">{{ $val->alertas->count() }} alertas</div></div>
            </div>
            <div class="step-line {{ $aprobada || $rechazada ? 'done' : '' }}"></div>
            <div class="timeline-step">
                <div class="step-circle {{ $aprobada ? 'step-done' : ($rechazada ? 'step-pending' : 'step-current') }}">{{ $aprobada?'✓':($rechazada?'✗':'…') }}</div>
                <div><strong style="font-size:.85rem">Validación farmacéutica</strong><div style="font-size:.72rem;color:#64748b">{{ \App\Models\Validacion::RESULTADOS[$val->resultado] }}</div></div>
            </div>
            <div class="step-line {{ $aprobada?'done':'' }}"></div>
            <div class="timeline-step">
                <div class="step-circle {{ $aprobada?'step-current':'step-pending' }}">{{ $aprobada?'…':'·' }}</div>
                <div><strong style="font-size:.85rem">Producción</strong><div style="font-size:.72rem;color:#64748b">{{ $aprobada?'Habilitada':'Pendiente' }}</div></div>
            </div>
            <div class="step-line"></div>
            <div class="timeline-step">
                <div class="step-circle step-pending">·</div>
                <div><strong style="font-size:.85rem">Dispensación</strong><div style="font-size:.72rem;color:#64748b">Pendiente</div></div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
