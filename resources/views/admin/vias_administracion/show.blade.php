<x-app-layout>
    <x-slot name="header">
        <h2>Vía de Administración · {{ $via->nombre }}</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }
        .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:9px 16px; border-radius:8px; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }

        .hero { background:#fff; border-radius:16px; padding:24px; box-shadow:0 6px 16px rgba(0,0,0,.05); display:flex; gap:22px; align-items:center; margin-bottom:22px; position:relative; overflow:hidden; }
        .hero::before { content:''; position:absolute; left:0; top:0; bottom:0; width:8px; background:{{ $via->color_identificacion ?: '#94a3b8' }}; }
        .hero-icon { width:110px; height:110px; border-radius:24px; background:{{ ($via->color_identificacion ?: '#94a3b8').'1f' }}; display:flex; align-items:center; justify-content:center; font-size:3.5rem; flex-shrink:0; }
        .hero h1 { margin:0; font-size:1.7rem; color:var(--inst); font-weight:800; }
        .hero .meta { color:#6b7280; margin-top:6px; font-size:.95rem; }
        .hero-badges { display:flex; gap:6px; flex-wrap:wrap; margin-top:12px; }

        .badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:20px; font-size:.75rem; font-weight:700; border:1px solid transparent; }
        .badge-tipo     { background:#f3f4f6; color:#374151; border-color:#e5e7eb; }
        .badge-active   { background:#d1fae5; color:#065f46; border-color:#a7f3d0; }
        .badge-inactive { background:#fee2e2; color:#991b1b; border-color:#fecaca; }

        .risk { display:inline-flex; align-items:center; gap:5px; font-size:.75rem; font-weight:700; padding:4px 10px; border-radius:20px; }
        .risk-BAJO    { background:#dcfce7; color:#065f46; border:1px solid #86efac; }
        .risk-MEDIO   { background:#fef9c3; color:#854d0e; border:1px solid #fde68a; }
        .risk-ALTO    { background:#ffedd5; color:#9a3412; border:1px solid #fdba74; }
        .risk-CRITICO { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }

        .grid-2 { display:grid; grid-template-columns:2fr 1fr; gap:22px; align-items:start; }
        @media (max-width:1000px) { .grid-2 { grid-template-columns:1fr; } }

        .panel { background:#fff; border-radius:14px; padding:22px; box-shadow:0 6px 14px rgba(0,0,0,.04); margin-bottom:20px; }
        .panel h3 { margin:0 0 16px 0; font-size:1rem; color:var(--inst); border-bottom:2px solid #f3f4f6; padding-bottom:8px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }

        .kv { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:12px 22px; }
        .kv .item .l { color:#6b7280; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; font-weight:600; }
        .kv .item .v { color:#1f2937; font-size:.95rem; font-weight:600; margin-top:2px; word-break:break-word; }

        .attrs { display:grid; grid-template-columns:repeat(auto-fit, minmax(170px,1fr)); gap:10px; }
        .attr { background:#f9fafb; border:1px solid #f1f1f4; border-radius:10px; padding:10px 12px; display:flex; align-items:center; justify-content:space-between; }
        .attr .l { font-size:.82rem; color:#4b5563; }
        .attr .v { font-weight:700; font-size:.85rem; }
        .v-yes { color:#059669; } .v-no  { color:#9ca3af; }

        table { width:100%; border-collapse:collapse; font-size:.9rem; }
        table th { text-align:left; padding:10px 12px; background:#f9fafb; color:#374151; font-weight:700; font-size:.8rem; text-transform:uppercase; letter-spacing:.04em; }
        table td { padding:10px 12px; border-top:1px solid #f3f4f6; color:#4b5563; }

        .rule { display:flex; gap:10px; align-items:flex-start; padding:10px 12px; border-radius:10px; border:1px solid #f1f1f4; background:#f9fafb; margin-bottom:8px; font-size:.88rem; color:#374151; }
        .rule .ico { font-size:1.1rem; }
    </style>

    <div class="top-bar">
        <a href="{{ route('admin.vias_administracion.index') }}" class="btn-outline">← Volver al catálogo</a>
    </div>

    <div class="hero">
        <div class="hero-icon">{{ $via->icono ?: '💉' }}</div>
        <div style="flex:1;">
            <h1>{{ $via->nombre }} @if($via->nombre_corto) <small style="font-size:1rem; color:#6b7280;">({{ $via->nombre_corto }})</small> @endif</h1>
            <div class="meta">
                @if($via->codigo) <strong>{{ $via->codigo }}</strong> · @endif
                {{ $via->tipo_label }}
            </div>
            <div class="hero-badges">
                @if($via->tipo) <span class="badge badge-tipo">{{ $via->tipo_label }}</span> @endif
                @if($via->riesgo_clinico)
                    <span class="risk risk-{{ $via->riesgo_clinico }}">● Riesgo {{ $via->riesgo_label }}</span>
                @endif
                @if($via->estado)
                    <span class="badge badge-active">● Activa</span>
                @else
                    <span class="badge badge-inactive">● Inactiva</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div>
            {{-- Información clínica --}}
            <div class="panel">
                <h3>Información clínica</h3>
                @if($via->descripcion)
                    <p style="color:#4b5563; margin-top:0;">{{ $via->descripcion }}</p>
                @endif
                <div class="attrs">
                    <div class="attr"><span class="l">🧪 Estéril requerido</span> <span class="v {{ $via->esteril_requerido ? 'v-yes' : 'v-no' }}">{{ $via->esteril_requerido ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">⏵ Bomba de infusión</span> <span class="v {{ $via->requiere_bomba_infusion ? 'v-yes' : 'v-no' }}">{{ $via->requiere_bomba_infusion ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">🧷 Filtro</span> <span class="v {{ $via->requiere_filtro ? 'v-yes' : 'v-no' }}">{{ $via->requiere_filtro ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">💧 Permite bolo</span> <span class="v {{ $via->permite_bolo ? 'v-yes' : 'v-no' }}">{{ $via->permite_bolo ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">📈 Infusión continua</span> <span class="v {{ $via->permite_infusion_continua ? 'v-yes' : 'v-no' }}">{{ $via->permite_infusion_continua ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">🌤 Fotosensible</span> <span class="v {{ $via->fotosensible ? 'v-yes' : 'v-no' }}">{{ $via->fotosensible ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">👁 Monitorización</span> <span class="v {{ $via->requiere_monitorizacion ? 'v-yes' : 'v-no' }}">{{ $via->requiere_monitorizacion ? 'Sí' : 'No' }}</span></div>
                </div>
            </div>

            {{-- Límites farmacológicos --}}
            <div class="panel">
                <h3>Límites farmacológicos</h3>
                <div class="kv">
                    <div class="item"><div class="l">Velocidad mínima</div><div class="v">{{ !is_null($via->velocidad_min_ml_h) ? $via->velocidad_min_ml_h.' ml/h' : '—' }}</div></div>
                    <div class="item"><div class="l">Velocidad máxima</div><div class="v">{{ !is_null($via->velocidad_max_ml_h) ? $via->velocidad_max_ml_h.' ml/h' : '—' }}</div></div>
                    <div class="item"><div class="l">Osmolaridad máxima</div><div class="v">{{ !is_null($via->osmolaridad_max) ? $via->osmolaridad_max.' mOsm/L' : '—' }}</div></div>
                    <div class="item"><div class="l">Color identificación</div><div class="v">
                        @if($via->color_identificacion)
                            <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:{{ $via->color_identificacion }};vertical-align:middle;margin-right:6px;"></span>
                            {{ $via->color_identificacion }}
                        @else — @endif
                    </div></div>
                </div>
            </div>

            {{-- Reglas automáticas --}}
            <div class="panel">
                <h3>Reglas automáticas del sistema</h3>
                @php
                    $reglas = collect();
                    if ($via->esteril_requerido) $reglas->push(['🧪', 'Exige esterilidad obligatoria en preparación.']);
                    if ($via->requiere_bomba_infusion) $reglas->push(['⏵', 'Obliga al uso de bomba de infusión.']);
                    if ($via->requiere_filtro) $reglas->push(['🧷', 'Requiere filtro en línea para administración.']);
                    if ($via->fotosensible) $reglas->push(['🌤', 'Protección de la luz durante preparación y administración.']);
                    if ($via->requiere_monitorizacion) $reglas->push(['👁', 'Monitorización clínica continua del paciente.']);
                    if (in_array($via->riesgo_clinico, ['ALTO','CRITICO'])) $reglas->push(['⚠', 'Activa doble validación clínica y revisión farmacéutica.']);
                    if ($via->codigo === 'IT') $reglas->push(['🚫', 'Restringe medicamentos no compatibles por vía intratecal.']);
                    if (!$via->permite_bolo && $via->permite_infusion_continua) $reglas->push(['📈', 'No permite administración en bolo: solo infusión continua.']);
                @endphp
                @forelse($reglas as $r)
                    <div class="rule">
                        <span class="ico">{{ $r[0] }}</span>
                        <span>{{ $r[1] }}</span>
                    </div>
                @empty
                    <p style="color:#6b7280; margin:0;">Esta vía no aplica reglas automáticas adicionales.</p>
                @endforelse
            </div>

            {{-- Medicamentos compatibles --}}
            <div class="panel">
                <h3>Medicamentos / Presentaciones compatibles</h3>
                @if($via->presentaciones->isEmpty())
                    <p style="color:#6b7280; margin:0;">Aún no hay presentaciones vinculadas a esta vía de administración.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Medicamento</th>
                                <th>Presentación</th>
                                <th>Concentración</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($via->presentaciones as $p)
                                <tr>
                                    <td>{{ $p->medicamento->nombre ?? '—' }}</td>
                                    <td>{{ $p->nombre }}</td>
                                    <td>{{ $p->concentracion }} {{ $p->unidad_concentracion }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if($via->observaciones)
                <div class="panel">
                    <h3>Observaciones</h3>
                    <p style="margin:0; color:#4b5563;">{{ $via->observaciones }}</p>
                </div>
            @endif
        </div>

        <div>
            <div class="panel">
                <h3>Semáforo de seguridad</h3>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div style="display:flex; gap:8px; align-items:center;"><span style="width:14px;height:14px;border-radius:50%;background:#10b981;"></span> <strong>Bajo</strong> — sin alertas</div>
                    <div style="display:flex; gap:8px; align-items:center;"><span style="width:14px;height:14px;border-radius:50%;background:#f59e0b;"></span> <strong>Medio</strong> — observación</div>
                    <div style="display:flex; gap:8px; align-items:center;"><span style="width:14px;height:14px;border-radius:50%;background:#fb923c;"></span> <strong>Alto</strong> — validación clínica</div>
                    <div style="display:flex; gap:8px; align-items:center;"><span style="width:14px;height:14px;border-radius:50%;background:#ef4444;"></span> <strong>Crítico</strong> — doble validación obligatoria</div>
                </div>
            </div>

            <div class="panel">
                <h3>Resumen de uso</h3>
                <div class="kv">
                    <div class="item"><div class="l">Presentaciones</div><div class="v">{{ $via->presentaciones->count() }}</div></div>
                    <div class="item"><div class="l">Medicamentos únicos</div><div class="v">{{ $via->presentaciones->pluck('medicamento_id')->unique()->count() }}</div></div>
                </div>
            </div>

            <div class="panel">
                <h3>Impacto en Central de Mezclas</h3>
                <p style="color:#6b7280; font-size:.86rem; margin:0;">
                    La vía controla automáticamente
                    <strong>validaciones clínicas</strong>, <strong>preparación</strong>, <strong>compatibilidad</strong>,
                    <strong>límites farmacológicos</strong> y <strong>alertas de riesgo</strong> al dispensar.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
