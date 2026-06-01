<x-app-layout>
    <x-slot name="header">
        <h2>Forma Farmacéutica · {{ $forma->nombre }}</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }
        .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:9px 16px; border-radius:8px; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }

        .hero { background:#fff; border-radius:16px; padding:24px; box-shadow:0 6px 16px rgba(0,0,0,.05); display:flex; gap:22px; align-items:center; margin-bottom:22px; position:relative; overflow:hidden; }
        .hero::before { content:''; position:absolute; left:0; top:0; bottom:0; width:8px; background:{{ $forma->color_identificacion ?: '#94a3b8' }}; }
        .hero-icon { width:110px; height:110px; border-radius:24px; background:{{ ($forma->color_identificacion ?: '#94a3b8').'1f' }}; display:flex; align-items:center; justify-content:center; font-size:3.5rem; flex-shrink:0; }
        .hero h1 { margin:0; font-size:1.7rem; color:var(--inst); font-weight:800; }
        .hero .meta { color:#6b7280; margin-top:6px; font-size:.95rem; }
        .hero-badges { display:flex; gap:6px; flex-wrap:wrap; margin-top:12px; }

        .badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:20px; font-size:.75rem; font-weight:700; border:1px solid transparent; }
        .badge-tipo     { background:#f3f4f6; color:#374151; border-color:#e5e7eb; }
        .badge-active   { background:#d1fae5; color:#065f46; border-color:#a7f3d0; }
        .badge-inactive { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
        .badge-frio     { background:#eff6ff; color:#1e40af; border-color:#bfdbfe; }
        .badge-esteril  { background:#ecfeff; color:#155e75; border-color:#a5f3fc; }

        .risk { display:inline-flex; align-items:center; gap:5px; font-size:.75rem; font-weight:700; padding:4px 10px; border-radius:20px; }
        .risk-BAJO  { background:#dcfce7; color:#065f46; border:1px solid #86efac; }
        .risk-MEDIO { background:#fef9c3; color:#854d0e; border:1px solid #fde68a; }
        .risk-ALTO  { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }

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

        .semaforo { display:flex; flex-direction:column; gap:8px; }
        .dot { width:14px; height:14px; border-radius:50%; display:inline-block; margin-right:6px; }

        .sem-row { display:flex; align-items:center; gap:8px; padding:8px 12px; background:#f9fafb; border-radius:10px; border:1px solid #f1f1f4; }
    </style>

    <div class="top-bar">
        <a href="{{ route('admin.formas_farmaceuticas.index') }}" class="btn-outline">← Volver al catálogo</a>
    </div>

    <div class="hero">
        <div class="hero-icon">{{ $forma->icono ?: '💊' }}</div>
        <div style="flex:1;">
            <h1>{{ $forma->nombre }}</h1>
            <div class="meta">
                @if($forma->codigo) <strong>{{ $forma->codigo }}</strong> · @endif
                {{ $forma->tipo_label }}
                @if($forma->nombre_corto) · ({{ $forma->nombre_corto }}) @endif
            </div>
            <div class="hero-badges">
                @if($forma->tipo)
                    <span class="badge badge-tipo">{{ $forma->tipo_label }}</span>
                @endif
                @if($forma->riesgo_contaminacion)
                    <span class="risk risk-{{ $forma->riesgo_contaminacion }}">● Riesgo {{ $forma->riesgo_label }}</span>
                @endif
                @if($forma->esteril) <span class="badge badge-esteril">🧪 Estéril</span> @endif
                @if($forma->requiere_cadena_frio) <span class="badge badge-frio">❄ Cadena de frío</span> @endif
                @if($forma->estado)
                    <span class="badge badge-active">● Activo</span>
                @else
                    <span class="badge badge-inactive">● Inactivo</span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div>
            {{-- Información técnica --}}
            <div class="panel">
                <h3>Información técnica</h3>
                @if($forma->descripcion)
                    <p style="color:#4b5563; margin-top:0;">{{ $forma->descripcion }}</p>
                @endif
                <div class="attrs">
                    <div class="attr"><span class="l">🧪 Estéril</span> <span class="v {{ $forma->esteril ? 'v-yes' : 'v-no' }}">{{ $forma->esteril ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">❄ Cadena de frío</span> <span class="v {{ $forma->requiere_cadena_frio ? 'v-yes' : 'v-no' }}">{{ $forma->requiere_cadena_frio ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">🔁 Reconstitución</span> <span class="v {{ $forma->requiere_reconstitucion ? 'v-yes' : 'v-no' }}">{{ $forma->requiere_reconstitucion ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">💧 Dilución</span> <span class="v {{ $forma->requiere_dilucion ? 'v-yes' : 'v-no' }}">{{ $forma->requiere_dilucion ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">📦 Multidosis</span> <span class="v {{ $forma->multidosis ? 'v-yes' : 'v-no' }}">{{ $forma->multidosis ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">♻ Reutilizable</span> <span class="v {{ $forma->reutilizable ? 'v-yes' : 'v-no' }}">{{ $forma->reutilizable ? 'Sí' : 'No' }}</span></div>
                    <div class="attr"><span class="l">✂ Fraccionable</span> <span class="v {{ $forma->permite_fraccionamiento ? 'v-yes' : 'v-no' }}">{{ $forma->permite_fraccionamiento ? 'Sí' : 'No' }}</span></div>
                </div>
            </div>

            {{-- Estabilidad / Almacenamiento --}}
            <div class="panel">
                <h3>Estabilidad y almacenamiento</h3>
                <div class="kv">
                    <div class="item"><div class="l">Temp. mínima</div><div class="v">{{ !is_null($forma->temperatura_min) ? $forma->temperatura_min.' °C' : '—' }}</div></div>
                    <div class="item"><div class="l">Temp. máxima</div><div class="v">{{ !is_null($forma->temperatura_max) ? $forma->temperatura_max.' °C' : '—' }}</div></div>
                    <div class="item"><div class="l">Tiempo de estabilidad</div><div class="v">{{ $forma->tiempo_estabilidad_horas ? $forma->tiempo_estabilidad_horas.' horas' : '—' }}</div></div>
                    <div class="item"><div class="l">Color identificación</div><div class="v">
                        @if($forma->color_identificacion)
                            <span style="display:inline-block;width:14px;height:14px;border-radius:50%;background:{{ $forma->color_identificacion }};vertical-align:middle;margin-right:6px;"></span>
                            {{ $forma->color_identificacion }}
                        @else — @endif
                    </div></div>
                </div>
            </div>

            {{-- Compatibilidades (placeholder técnico) --}}
            <div class="panel">
                <h3>Compatibilidades sugeridas</h3>
                <ul style="margin:0; padding-left:18px; color:#4b5563; font-size:.92rem;">
                    @if($forma->esteril)
                        <li>Manejo bajo cabina de flujo laminar.</li>
                    @endif
                    @if($forma->requiere_cadena_frio)
                        <li>Almacenar a {{ $forma->temperatura_min ?? 2 }}°C - {{ $forma->temperatura_max ?? 8 }}°C, no congelar.</li>
                    @endif
                    @if($forma->requiere_dilucion)
                        <li>Verificar compatibilidad de diluyente (SSN 0.9%, DAD 5%).</li>
                    @endif
                    @if($forma->permite_fraccionamiento)
                        <li>Permite fraccionamiento controlado en central de mezclas.</li>
                    @endif
                    @if($forma->multidosis)
                        <li>Validar tiempo de vida útil tras primera apertura.</li>
                    @endif
                    @if(!$forma->esteril && !$forma->requiere_cadena_frio && !$forma->requiere_dilucion && !$forma->permite_fraccionamiento && !$forma->multidosis)
                        <li>Producto estable a temperatura ambiente sin requisitos especiales.</li>
                    @endif
                </ul>
            </div>

            {{-- Medicamentos asociados --}}
            <div class="panel">
                <h3>Medicamentos / Presentaciones asociadas</h3>
                @if($forma->presentaciones->isEmpty())
                    <p style="color:#6b7280; margin:0;">Aún no hay presentaciones vinculadas a esta forma farmacéutica.</p>
                @else
                    <table>
                        <thead>
                            <tr>
                                <th>Medicamento</th>
                                <th>Presentación</th>
                                <th>Concentración</th>
                                <th>Vía</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($forma->presentaciones as $p)
                                <tr>
                                    <td>{{ $p->medicamento->nombre ?? '—' }}</td>
                                    <td>{{ $p->nombre }}</td>
                                    <td>{{ $p->concentracion }} {{ $p->unidad_concentracion }}</td>
                                    <td>{{ $p->via_administracion ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            @if($forma->observaciones)
                <div class="panel">
                    <h3>Observaciones</h3>
                    <p style="margin:0; color:#4b5563;">{{ $forma->observaciones }}</p>
                </div>
            @endif
        </div>

        <div>
            {{-- Semáforos --}}
            <div class="panel">
                <h3>Semáforos operativos</h3>
                <div class="semaforo">
                    <div class="sem-row">
                        <span>❄ Cadena de frío</span>
                        <span style="margin-left:auto;">
                            @if($forma->requiere_cadena_frio)
                                <span class="dot" style="background:#3b82f6;"></span> Requiere
                            @else
                                <span class="dot" style="background:#10b981;"></span> No requiere
                            @endif
                        </span>
                    </div>
                    <div class="sem-row">
                        <span>🧪 Esterilidad</span>
                        <span style="margin-left:auto;">
                            @if($forma->esteril)
                                <span class="dot" style="background:#06b6d4;"></span> Estéril
                            @else
                                <span class="dot" style="background:#9ca3af;"></span> No estéril
                            @endif
                        </span>
                    </div>
                    <div class="sem-row">
                        <span>📦 Multidosis</span>
                        <span style="margin-left:auto;">
                            @if($forma->multidosis)
                                <span class="dot" style="background:#f59e0b;"></span> Sí
                            @else
                                <span class="dot" style="background:#10b981;"></span> No
                            @endif
                        </span>
                    </div>
                    <div class="sem-row">
                        <span>⚠ Riesgo</span>
                        <span style="margin-left:auto;">
                            @if($forma->riesgo_contaminacion === 'BAJO')   <span class="dot" style="background:#10b981;"></span> Bajo
                            @elseif($forma->riesgo_contaminacion === 'MEDIO') <span class="dot" style="background:#f59e0b;"></span> Medio
                            @elseif($forma->riesgo_contaminacion === 'ALTO')  <span class="dot" style="background:#ef4444;"></span> Alto
                            @else <span class="dot" style="background:#9ca3af;"></span> Sin definir
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <div class="panel">
                <h3>Resumen de uso</h3>
                <div class="kv">
                    <div class="item"><div class="l">Presentaciones</div><div class="v">{{ $forma->presentaciones->count() }}</div></div>
                    <div class="item"><div class="l">Medicamentos únicos</div><div class="v">{{ $forma->presentaciones->pluck('medicamento_id')->unique()->count() }}</div></div>
                </div>
            </div>

            <div class="panel">
                <h3>Impacto en Central de Mezclas</h3>
                <p style="color:#6b7280; font-size:.86rem; margin:0;">
                    Esta forma farmacéutica define automáticamente reglas de
                    <strong>estabilidad</strong>, <strong>dilución</strong>, <strong>cadena de frío</strong>,
                    <strong>tiempo de uso</strong> y <strong>riesgo microbiológico</strong>
                    al preparar mezclas y dispensar.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
