<x-app-layout>
@include('admin.reportes.trazabilidad._styles')

@php
    $fmt = fn($v, $d=0) => number_format((float)$v, $d, ',', '.');
    $palette = ['#7c3aed','#06b6d4','#f59e0b','#ec4899','#10b981','#6366f1','#f97316','#14b8a6'];

    // Donut: distribución por área
    $totalDist = max(1, array_sum($distribucion));
    $segments = [];
    $offset = 0;
    foreach($distribucion as $k => $v){
        $pct = ($v/$totalDist)*100;
        $segments[] = ['k'=>$k, 'v'=>$v, 'pct'=>$pct, 'offset'=>$offset, 'color'=>$palette[count($segments)]];
        $offset += $pct;
    }
@endphp

<div class="tz-page">
    {{-- Header --}}
    <div class="tz-hdr">
        <div>
            <h1>Centro de Trazabilidad 360°</h1>
            <p>Reconstruya en segundos la historia completa de cualquier lote, paciente, mezcla o incidente</p>
        </div>
        <div class="tz-hdr-actions">
            <a href="{{ route('admin.reportes.trazabilidad.recall') }}" class="tz-btn tz-btn-recall">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                Recall INVIMA
            </a>
            <a href="{{ route('admin.calidad.incidentes.index') }}" class="tz-btn tz-btn-ghost">Incidentes</a>
            <a href="{{ route('admin.reportes.insumos') }}" class="tz-btn tz-btn-ghost">Consumos</a>
        </div>
    </div>

    @if(session('warning'))
        <div class="tz-alert warn">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
            {{ session('warning') }}
        </div>
    @endif

    {{-- Buscador universal --}}
    <div class="tz-search">
        <div class="tz-search-title">
            <span class="ico">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
            </span>
            Buscador Universal
        </div>
        <form action="{{ route('admin.reportes.trazabilidad.buscar') }}" method="GET">
            <input type="text" name="q" placeholder="Lote, documento del paciente, código de preparación, mezcla, reempaque, incidente o equipo de cadena de frío..." autofocus>
            <button type="submit">Buscar</button>
        </form>
        <div class="hint">El sistema detecta automáticamente el tipo de objeto y muestra su trazabilidad completa.</div>
        <div class="chips">
            <span class="chip">Lote: VCX-2026-001</span>
            <span class="chip">Paciente: 12345678</span>
            <span class="chip">Preparación: PREP-2026-001</span>
            <span class="chip">Mezcla: MZ-2026-001</span>
            <span class="chip">Incidente: INC-2026-005</span>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="tz-kpis">
        <div class="tz-kpi ok">
            <span class="ic">📦</span>
            <div class="lbl">Lotes activos</div>
            <div class="val">{{ $fmt($kpis['lotes_activos']) }}</div>
            <div class="sub">de {{ $fmt($kpis['lotes_trazables']) }} totales</div>
        </div>
        <div class="tz-kpi pink">
            <span class="ic">👤</span>
            <div class="lbl">Pacientes impactados</div>
            <div class="val">{{ $fmt($kpis['pacientes_impactados']) }}</div>
            <div class="sub">con dispensación</div>
        </div>
        <div class="tz-kpi indigo">
            <span class="ic">🧪</span>
            <div class="lbl">Mezclas</div>
            <div class="val">{{ $fmt($kpis['mezclas']) }}</div>
        </div>
        <div class="tz-kpi cyan">
            <span class="ic">💉</span>
            <div class="lbl">Preparaciones</div>
            <div class="val">{{ $fmt($kpis['preparaciones']) }}</div>
        </div>
        <div class="tz-kpi amber">
            <span class="ic">📋</span>
            <div class="lbl">Reempaques</div>
            <div class="val">{{ $fmt($kpis['reempaques']) }}</div>
        </div>
        <div class="tz-kpi @if($kpis['incidentes_abiertos']>0) crit @endif">
            <span class="ic">⚠</span>
            <div class="lbl">Incidentes abiertos</div>
            <div class="val">{{ $fmt($kpis['incidentes_abiertos']) }}</div>
            <div class="sub">de {{ $fmt($kpis['incidentes']) }} registrados</div>
        </div>
        <div class="tz-kpi @if($kpis['alertas_cf']>0) warn @endif">
            <span class="ic">❄</span>
            <div class="lbl">Alertas cadena frío</div>
            <div class="val">{{ $fmt($kpis['alertas_cf']) }}</div>
            <div class="sub">activas</div>
        </div>
        <div class="tz-kpi @if($kpis['lotes_bloqueados']>0) crit @endif">
            <span class="ic">🔒</span>
            <div class="lbl">Lotes bloqueados</div>
            <div class="val">{{ $fmt($kpis['lotes_bloqueados']) }}</div>
        </div>
    </div>

    {{-- Flow Sankey --}}
    <div class="tz-card">
        <h3><span class="icon">↗</span> Flujo del medicamento <span class="pill">Visión global</span></h3>
        <div class="tz-flow">
            <div class="tz-flow-step">
                <div class="ico">📦</div>
                <div class="lbl">Inventario</div>
                <div class="val">{{ $fmt($distribucion['inventario']) }}</div>
                <div class="sub">lotes activos</div>
            </div>
            <div class="tz-flow-step">
                <div class="ico" style="background:#06b6d4;">🧪</div>
                <div class="lbl">Producción</div>
                <div class="val" style="color:#0e7490;">{{ $fmt($distribucion['produccion']) }}</div>
                <div class="sub">mezclas + prep + reemp</div>
            </div>
            <div class="tz-flow-step">
                <div class="ico" style="background:#f59e0b;">📤</div>
                <div class="lbl">Dispensación</div>
                <div class="val" style="color:#b45309;">{{ $fmt($distribucion['dispensacion']) }}</div>
                <div class="sub">entregas</div>
            </div>
            <div class="tz-flow-step">
                <div class="ico" style="background:#ec4899;">👤</div>
                <div class="lbl">Pacientes</div>
                <div class="val" style="color:#9d174d;">{{ $fmt($distribucion['pacientes']) }}</div>
                <div class="sub">impactados</div>
            </div>
        </div>
    </div>

    {{-- Row 1: Top medicamentos + Sparkline 30 días --}}
    <div class="tz-row2">
        <div class="tz-card">
            <h3><span class="icon">⭐</span> Top 10 medicamentos más trazados <span class="pill">por pacientes</span></h3>
            @if($topMedicamentos->isEmpty())
                <div class="tz-empty">Sin datos disponibles</div>
            @else
                @php $maxMed = $topMedicamentos->max('pacientes') ?: 1; @endphp
                <div class="tz-bars">
                    @foreach($topMedicamentos as $m)
                    <div class="tz-bar">
                        <div class="nm" title="{{ $m->medicamento }}">{{ \Illuminate\Support\Str::limit($m->medicamento, 35) }}</div>
                        <div class="bg"><span class="fg" style="width:{{ ($m->pacientes/$maxMed)*100 }}%"></span></div>
                        <div class="vl">{{ $fmt($m->pacientes) }}</div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="tz-card">
            <h3><span class="icon">📈</span> Actividad <span class="pill">30 días</span></h3>
            @if($movimientosDia->isEmpty())
                <div class="tz-empty">Sin movimientos recientes</div>
            @else
                @php $maxMov = $movimientosDia->max('total') ?: 1; @endphp
                <div class="tz-spark">
                    @foreach($movimientosDia as $d)
                    <div class="b" style="height:{{ max(4,($d->total/$maxMov)*100) }}%;">
                        <span class="t">{{ $d->dia }}<br>{{ $d->total }} mov.</span>
                    </div>
                    @endforeach
                </div>
                <div class="tz-spark-foot">
                    <span>{{ $movimientosDia->first()->dia ?? '' }}</span>
                    <strong>{{ $fmt($movimientosDia->sum('total')) }} movimientos</strong>
                    <span>{{ $movimientosDia->last()->dia ?? '' }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Row 2: Donut + Heatmap servicios --}}
    <div class="tz-row22">
        <div class="tz-card">
            <h3><span class="icon">🎯</span> Distribución del flujo</h3>
            <div class="tz-donut">
                <svg viewBox="0 0 42 42">
                    <circle cx="21" cy="21" r="15.91549430918954" fill="#fff" stroke="#f3f4f6" stroke-width="3"></circle>
                    @foreach($segments as $s)
                    <circle cx="21" cy="21" r="15.91549430918954" fill="transparent"
                        stroke="{{ $s['color'] }}" stroke-width="3"
                        stroke-dasharray="{{ $s['pct'] }} {{ 100 - $s['pct'] }}"
                        stroke-dashoffset="{{ -$s['offset'] + 25 }}"></circle>
                    @endforeach
                    <text x="21" y="20" text-anchor="middle" font-size="4" font-weight="700" fill="#1f2937">{{ $fmt($totalDist) }}</text>
                    <text x="21" y="25" text-anchor="middle" font-size="2.4" fill="#6b7280">objetos</text>
                </svg>
                <div class="lg">
                    @foreach($segments as $s)
                    <div>
                        <span class="sw" style="background:{{ $s['color'] }};"></span>
                        <span style="text-transform:capitalize;">{{ $s['k'] }}</span>
                        <b>{{ $fmt($s['v']) }}</b>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="tz-card">
            <h3><span class="icon">🏥</span> Distribución por servicio <span class="pill">{{ $heatmap->count() }} servicios</span></h3>
            @if($heatmap->isEmpty())
                <div class="tz-empty">Sin datos</div>
            @else
                <div class="tz-heat">
                    @php $maxH = $heatmap->max('lotes') ?: 1; @endphp
                    @foreach($heatmap as $h)
                        @php
                            $intensity = ($h->lotes/$maxH);
                            $bg = sprintf('rgba(124,58,237,%.2f)', 0.12 + $intensity*0.78);
                            $color = $intensity > 0.5 ? '#fff' : '#1f2937';
                            $sub = $intensity > 0.5 ? 'rgba(255,255,255,.8)' : '#6b7280';
                        @endphp
                        <div class="tz-heat-cell" style="background:{{ $bg }};color:{{ $color }};">
                            <div class="nm">{{ $h->servicio }}</div>
                            <div class="v">{{ $h->lotes }}</div>
                            <div class="sub" style="color:{{ $sub }};">{{ $h->pacientes }} pacientes</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Top lotes con más impacto --}}
    <div class="tz-card">
        <h3><span class="icon">🔗</span> Top lotes con mayor trazabilidad <span class="pill">por impacto</span></h3>
        @if($topLotes->isEmpty())
            <div class="tz-empty">Sin lotes registrados</div>
        @else
            <table class="tz-t">
                <thead>
                    <tr>
                        <th>Lote</th>
                        <th>Medicamento</th>
                        <th>Estado</th>
                        <th class="num">Stock</th>
                        <th class="num">Mezclas</th>
                        <th class="num">Preparaciones</th>
                        <th class="num">Reempaques</th>
                        <th class="num">Pacientes</th>
                        <th class="num">Incidentes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topLotes as $l)
                    <tr>
                        <td><a href="{{ route('admin.reportes.trazabilidad.lote', $l->lote_id) }}" style="font-family:'Consolas',monospace;">{{ $l->lote }}</a></td>
                        <td title="{{ $l->medicamento }}">{{ \Illuminate\Support\Str::limit($l->medicamento, 35) }}</td>
                        <td>
                            @php
                                $ec = $l->estado_calidad ?: $l->estado;
                                $cls = $ec==='APROBADO'||$ec==='ACTIVO'?'ok':($ec==='BLOQUEADO'?'crit':'warn');
                            @endphp
                            <span class="tz-pill {{ $cls }}">{{ $ec ?: '—' }}</span>
                            @if($l->bloqueado_incidente)<span class="tz-pill crit">🔒</span>@endif
                        </td>
                        <td class="num">{{ $fmt($l->cantidad_actual) }}</td>
                        <td class="num">{{ $l->total_mezclas ?? 0 }}</td>
                        <td class="num">{{ $l->total_preparaciones ?? 0 }}</td>
                        <td class="num">{{ $l->total_reempaques ?? 0 }}</td>
                        <td class="num"><strong style="color:var(--tz-d);">{{ $l->total_pacientes ?? 0 }}</strong></td>
                        <td class="num">@if(($l->total_incidentes??0)>0)<span class="tz-pill crit">{{ $l->total_incidentes }}</span>@else <span style="color:#cbd5e1;">0</span> @endif</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Quick links --}}
    <div class="tz-card">
        <h3><span class="icon">⚡</span> Accesos directos</h3>
        <div class="tz-quick">
            <a href="{{ route('admin.reportes.trazabilidad.recall') }}">
                <span class="ic">🚨</span>
                <div>Recall INVIMA <span class="meta">Búsqueda urgente por lote</span></div>
            </a>
            <a href="{{ route('admin.calidad.incidentes.index') }}">
                <span class="ic">⚠</span>
                <div>Incidentes <span class="meta">Gestión de calidad</span></div>
            </a>
            <a href="{{ route('admin.reportes.insumos') }}">
                <span class="ic">📊</span>
                <div>Reporte de Consumos <span class="meta">Data Mart de inventario</span></div>
            </a>
            <a href="{{ route('admin.dispensacion.entregas.index') }}">
                <span class="ic">📤</span>
                <div>Entregas <span class="meta">Dispensaciones registradas</span></div>
            </a>
        </div>
    </div>
</div>
</x-app-layout>
