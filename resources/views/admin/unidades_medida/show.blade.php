<x-app-layout>
    <x-slot name="header">
        <h2>Unidad de Medida · {{ $unidad->nombre }}</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }
        .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:9px 16px; border-radius:8px; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }

        .hero { background:#fff; border-radius:16px; padding:24px; box-shadow:0 6px 16px rgba(0,0,0,.05); display:flex; gap:22px; align-items:center; margin-bottom:22px; position:relative; overflow:hidden; }
        .hero::before { content:''; position:absolute; left:0; top:0; bottom:0; width:8px; background:{{ $unidad->color_tipo }}; }
        .hero-icon { width:110px; height:110px; border-radius:24px; background:{{ $unidad->color_tipo }}1f; color:{{ $unidad->color_tipo }}; display:flex; align-items:center; justify-content:center; font-size:3.5rem; flex-shrink:0; }
        .hero h1 { margin:0; font-size:1.7rem; color:var(--inst); font-weight:800; }
        .hero .meta { color:#6b7280; margin-top:6px; font-size:.95rem; }
        .hero-badges { display:flex; gap:6px; flex-wrap:wrap; margin-top:12px; }

        .badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:20px; font-size:.75rem; font-weight:700; border:1px solid transparent; }
        .badge-tipo     { background:{{ $unidad->color_tipo }}1f; color:{{ $unidad->color_tipo }}; border-color:{{ $unidad->color_tipo }}55; }
        .badge-active   { background:#d1fae5; color:#065f46; border-color:#a7f3d0; }
        .badge-inactive { background:#fee2e2; color:#991b1b; border-color:#fecaca; }
        .badge-base     { background:#eef0f7; color:var(--inst); border-color:#dde0ee; }

        .grid-2 { display:grid; grid-template-columns:2fr 1fr; gap:22px; align-items:start; }
        @media (max-width:1000px){ .grid-2 { grid-template-columns:1fr; } }

        .panel { background:#fff; border-radius:14px; padding:22px; box-shadow:0 6px 14px rgba(0,0,0,.04); margin-bottom:20px; }
        .panel h3 { margin:0 0 16px 0; font-size:1rem; color:var(--inst); border-bottom:2px solid #f3f4f6; padding-bottom:8px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }

        .kv { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:12px 22px; }
        .kv .item .l { color:#6b7280; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; font-weight:600; }
        .kv .item .v { color:#1f2937; font-size:.95rem; font-weight:700; margin-top:2px; }

        table { width:100%; border-collapse:collapse; font-size:.9rem; }
        table th { text-align:left; padding:10px 12px; background:#f9fafb; color:#374151; font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; }
        table td { padding:10px 12px; border-top:1px solid #f3f4f6; color:#4b5563; }
        table tr.self td { background:#eef0f7; font-weight:700; color:var(--inst); }

        .calc-mini { background:linear-gradient(135deg, #2e3a75, #4c5fb8); color:#fff; border-radius:14px; padding:18px; margin-top:14px; }
        .calc-mini input, .calc-mini select { padding:8px 10px; border-radius:8px; border:none; font-size:.9rem; color:#1f2937; }
        .calc-mini .out { background:rgba(255,255,255,.18); border-radius:10px; padding:10px 14px; font-weight:700; margin-top:10px; }
    </style>

    <div class="top-bar">
        <a href="{{ route('admin.unidades_medida.index') }}" class="btn-outline">← Volver al catálogo</a>
    </div>

    <div class="hero">
        <div class="hero-icon">{{ $unidad->icono_final }}</div>
        <div style="flex:1;">
            <h1>{{ $unidad->nombre }} <small style="font-size:1rem; color:#6b7280;">({{ $unidad->abreviatura }})</small></h1>
            <div class="meta"><strong>{{ $unidad->codigo }}</strong> · {{ $unidad->tipo_label }} @if($unidad->simbolo) · símbolo <strong>{{ $unidad->simbolo }}</strong> @endif</div>
            <div class="hero-badges">
                <span class="badge badge-tipo">{{ $unidad->tipo_label }}</span>
                @if(! $unidad->unidad_base_id) <span class="badge badge-base">● Unidad base</span> @endif
                @if($unidad->activa_calculos) <span class="badge badge-active">● Cálculos clínicos</span> @endif
                @if($unidad->estado) <span class="badge badge-active">● Activa</span> @else <span class="badge badge-inactive">● Inactiva</span> @endif
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div>
            <div class="panel">
                <h3>Información técnica</h3>
                <div class="kv">
                    <div class="item"><div class="l">Nombre</div><div class="v">{{ $unidad->nombre }}</div></div>
                    <div class="item"><div class="l">Código</div><div class="v">{{ $unidad->codigo }}</div></div>
                    <div class="item"><div class="l">Abreviatura</div><div class="v">{{ $unidad->abreviatura }}</div></div>
                    <div class="item"><div class="l">Símbolo</div><div class="v">{{ $unidad->simbolo ?: '—' }}</div></div>
                    <div class="item"><div class="l">Tipo</div><div class="v">{{ $unidad->tipo_label }}</div></div>
                    <div class="item"><div class="l">Precisión</div><div class="v">{{ $unidad->precision_decimal }} decimales</div></div>
                    <div class="item"><div class="l">Permite fracciones</div><div class="v">{{ $unidad->permite_fracciones ? 'Sí' : 'No' }}</div></div>
                    <div class="item"><div class="l">Unidad base</div><div class="v">{{ $unidad->unidadBase->nombre ?? '— (es base)' }}</div></div>
                    <div class="item"><div class="l">Factor</div><div class="v">{{ rtrim(rtrim(number_format((float)$unidad->factor_conversion, 8, '.', ''), '0'), '.') }}</div></div>
                </div>
                @if($unidad->observaciones)
                    <p style="margin-top:14px; color:#4b5563;">{{ $unidad->observaciones }}</p>
                @endif
            </div>

            <div class="panel">
                <h3>Conversión automática · {{ $unidad->tipo_label }}</h3>
                @if($hermanas->isEmpty())
                    <p style="color:#6b7280; margin:0;">No hay otras unidades del mismo tipo para comparar.</p>
                @else
                    @php
                        $valorRef = 1;
                        $factorRef = (float) $unidad->factor_conversion;
                    @endphp
                    <table>
                        <thead>
                            <tr>
                                <th>Equivalencia</th>
                                <th>Unidad</th>
                                <th>Valor</th>
                                <th>Factor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="self">
                                <td>1 {{ $unidad->abreviatura }}</td>
                                <td>{{ $unidad->nombre }}</td>
                                <td>{{ number_format(1, $unidad->precision_decimal) }} {{ $unidad->abreviatura }}</td>
                                <td>{{ rtrim(rtrim(number_format($factorRef, 8, '.', ''), '0'), '.') }}</td>
                            </tr>
                            @foreach($hermanas as $h)
                                @php
                                    $factorH = (float) $h->factor_conversion;
                                    $eq = $factorH > 0 ? ($valorRef * $factorRef) / $factorH : null;
                                @endphp
                                <tr>
                                    <td>1 {{ $unidad->abreviatura }} =</td>
                                    <td>{{ $h->nombre }}</td>
                                    <td>{{ $eq !== null ? rtrim(rtrim(number_format($eq, max($h->precision_decimal, 4), '.', ''), '0'), '.') : '—' }} {{ $h->abreviatura }}</td>
                                    <td>{{ rtrim(rtrim(number_format($factorH, 8, '.', ''), '0'), '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="calc-mini">
                        <strong>🧮 Calculadora rápida</strong>
                        <div style="display:flex; gap:10px; margin-top:10px; flex-wrap:wrap; align-items:center;">
                            <input type="number" id="qcalc-val" value="{{ $valorRef }}" step="any" style="flex:0 0 110px;">
                            <span>{{ $unidad->abreviatura }} →</span>
                            <select id="qcalc-to" style="flex:1; min-width:160px;">
                                @foreach($hermanas as $h)
                                    <option value="{{ $h->id }}" data-factor="{{ $h->factor_conversion }}" data-precision="{{ $h->precision_decimal }}" data-abrev="{{ $h->abreviatura }}">{{ $h->nombre }} ({{ $h->abreviatura }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="out" id="qcalc-out">—</div>
                    </div>
                    <script>
                        (function(){
                            const f0 = {{ $factorRef }};
                            const valIn = document.getElementById('qcalc-val');
                            const toSel = document.getElementById('qcalc-to');
                            const out   = document.getElementById('qcalc-out');
                            function go(){
                                const opt = toSel.selectedOptions[0];
                                const v = parseFloat(valIn.value);
                                if (isNaN(v) || !opt) { out.textContent = '—'; return; }
                                const fh = parseFloat(opt.dataset.factor);
                                const p  = parseInt(opt.dataset.precision)||2;
                                const r  = (v * f0) / fh;
                                out.innerHTML = `${v} {{ $unidad->abreviatura }} = <strong>${r.toFixed(p)} ${opt.dataset.abrev}</strong>`;
                            }
                            [valIn,toSel].forEach(e=>e.addEventListener('input',go));
                            go();
                        })();
                    </script>
                @endif
            </div>

            <div class="panel">
                <h3>Validaciones clínicas</h3>
                @php
                    $reglas = collect();
                    if (! $unidad->activa_calculos)   $reglas->push(['⛔', 'Esta unidad NO participa en cálculos farmacéuticos automáticos.']);
                    if (! $unidad->permite_fracciones) $reglas->push(['🔢', 'Solo admite valores enteros (no fracciones).']);
                    if ($unidad->tipo === 'CONCENTRACION') $reglas->push(['🧬', 'Sistema impide mezclar unidades de concentración con peso o volumen puro.']);
                    if ($unidad->tipo === 'PESO')      $reglas->push(['⚖️', 'Compatible solo con otras unidades de peso (mcg, mg, g, kg).']);
                    if ($unidad->tipo === 'VOLUMEN')   $reglas->push(['🧪', 'Compatible solo con otras unidades de volumen (mL, L, gtt).']);
                    if ($unidad->precision_decimal >= 3) $reglas->push(['🎯', 'Alta precisión: ideal para dosificación pediátrica o crítica.']);
                @endphp
                @forelse($reglas as $r)
                    <div style="display:flex; gap:10px; padding:10px 12px; border-radius:10px; background:#f9fafb; border:1px solid #f1f1f4; margin-bottom:8px; font-size:.88rem;">
                        <span>{{ $r[0] }}</span><span>{{ $r[1] }}</span>
                    </div>
                @empty
                    <p style="color:#6b7280; margin:0;">Sin reglas especiales aplicadas.</p>
                @endforelse
            </div>

            <div class="panel">
                <h3>Medicamentos / Presentaciones que la usan</h3>
                @if($unidad->presentaciones->isEmpty())
                    <p style="color:#6b7280; margin:0;">Aún no hay presentaciones que utilicen esta unidad.</p>
                @else
                    <table>
                        <thead><tr><th>Medicamento</th><th>Presentación</th><th>Concentración</th></tr></thead>
                        <tbody>
                            @foreach($unidad->presentaciones as $p)
                                <tr>
                                    <td>{{ $p->medicamento->nombre ?? '—' }}</td>
                                    <td>{{ $p->nombre }}</td>
                                    <td>{{ $p->concentracion }} {{ $unidad->abreviatura }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        <div>
            <div class="panel">
                <h3>Uso clínico</h3>
                <p style="color:#4b5563; font-size:.9rem; margin:0;">
                    @switch($unidad->tipo)
                        @case('PESO')          Dosificación másica (paracetamol, antibióticos). Crítica en pediatría. @break
                        @case('VOLUMEN')       Preparación de soluciones, sueros y reconstituciones. @break
                        @case('CONCENTRACION') Validación de mezclas, compatibilidad y diluciones. @break
                        @case('TIEMPO')        Programación de infusiones, ciclos y frecuencias de dosis. @break
                        @case('VELOCIDAD')     Bombas de infusión, microgoteros y drogas vasoactivas. @break
                        @case('TEMPERATURA')   Cadena de frío, refrigeración y estabilidad. @break
                        @case('CANTIDAD')      Dispensación discreta (tabletas, ampollas, viales). @break
                        @case('SUPERFICIE')    Cálculo de dosis por superficie corporal (oncología). @break
                        @default Uso clínico general dentro del módulo de Central de Mezclas.
                    @endswitch
                </p>
            </div>

            <div class="panel">
                <h3>Resumen de uso</h3>
                <div class="kv">
                    <div class="item"><div class="l">Presentaciones</div><div class="v">{{ $unidad->presentaciones->count() }}</div></div>
                    <div class="item"><div class="l">Unidades derivadas</div><div class="v">{{ $unidad->derivadas->count() }}</div></div>
                </div>
            </div>

            <div class="panel">
                <h3>Recomendación clínica</h3>
                <p style="margin:0; color:#6b7280; font-size:.86rem;">
                    Las unidades <strong>nunca</strong> deben escribirse a mano. Deben elegirse desde este catálogo para garantizar
                    <strong>conversiones automáticas</strong>, <strong>validaciones cruzadas</strong> y
                    <strong>auditabilidad</strong> en cada cálculo farmacéutico.
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
