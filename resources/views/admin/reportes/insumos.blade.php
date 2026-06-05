<x-app-layout>
<style>
:root{--ins:#0ea5e9;--ins-d:#0369a1;--bg:#f8fafc;}
.page-header{display:flex;justify-content:space-between;align-items:center;background:linear-gradient(135deg,var(--ins) 0%,var(--ins-d) 100%);color:#fff;padding:1.5rem 2rem;border-radius:14px;margin-bottom:1.25rem;box-shadow:0 8px 25px rgba(14,165,233,.25);}
.page-header h1{margin:0;font-size:1.55rem;font-weight:800;}
.page-header p{margin:.25rem 0 0;opacity:.92;font-size:.9rem;}
.btn{padding:.55rem 1rem;border-radius:8px;font-weight:600;border:none;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:.4rem;font-size:.875rem;}
.btn-primary{background:var(--ins);color:#fff;}
.btn-secondary{background:#e2e8f0;color:#0f172a;}
.btn-export{background:#16a34a;color:#fff;}
.kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.85rem;margin-bottom:1rem;}
.kpi{background:#fff;border-radius:12px;padding:1rem;box-shadow:0 2px 6px rgba(0,0,0,.05);border-left:4px solid var(--ins);}
.kpi .lbl{font-size:.72rem;color:#64748b;text-transform:uppercase;letter-spacing:.05em;font-weight:600;}
.kpi .val{font-size:1.55rem;font-weight:800;color:#0f172a;line-height:1.1;margin-top:.25rem;}
.kpi.k-cost{border-left-color:#16a34a;}
.kpi.k-pac{border-left-color:#a855f7;}
.kpi.k-prep{border-left-color:#f59e0b;}
.kpi.k-lot{border-left-color:#ef4444;}
.card{background:#fff;border-radius:12px;padding:1.25rem;box-shadow:0 2px 6px rgba(0,0,0,.05);margin-bottom:1rem;}
.card h3{margin:0 0 .85rem;font-size:1rem;font-weight:700;color:#0f172a;display:flex;align-items:center;gap:.5rem;}
.card h3 .pill{font-size:.65rem;background:#e0f2fe;color:#0369a1;padding:.15rem .5rem;border-radius:999px;font-weight:700;}
.t{width:100%;border-collapse:collapse;font-size:.82rem;}
.t th,.t td{padding:.55rem .6rem;text-align:left;border-bottom:1px solid #e2e8f0;vertical-align:middle;}
.t th{background:#f8fafc;font-weight:700;color:#475569;font-size:.72rem;text-transform:uppercase;letter-spacing:.05em;}
.t tbody tr:hover{background:#fafbfc;}
.t .num{text-align:right;font-variant-numeric:tabular-nums;}

/* Filtros */
.filters{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:.75rem;}
.filters label{display:block;font-weight:600;font-size:.74rem;color:#334155;margin-bottom:.25rem;}
.filters input,.filters select{width:100%;padding:.45rem .6rem;border:1px solid #cbd5e1;border-radius:7px;font-size:.82rem;background:#fff;}

/* Grids visualizaciones */
.row2{display:grid;grid-template-columns:2fr 1fr;gap:1rem;}
.row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:1rem;}
@media(max-width:900px){.row2,.row3{grid-template-columns:1fr;}}

/* Barras horizontales */
.bar-list{display:flex;flex-direction:column;gap:.4rem;}
.bar-row{display:grid;grid-template-columns:170px 1fr 100px;align-items:center;gap:.6rem;font-size:.78rem;}
.bar-row .nm{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#0f172a;font-weight:600;}
.bar-row .bar{height:14px;background:#e0f2fe;border-radius:7px;overflow:hidden;position:relative;}
.bar-row .bar > span{display:block;height:100%;background:linear-gradient(90deg,#0ea5e9,#0369a1);border-radius:7px;}
.bar-row .vl{text-align:right;font-variant-numeric:tabular-nums;color:#0f172a;font-weight:700;}

/* Dona/Treemap CSS */
.dona{display:flex;flex-direction:column;gap:.4rem;}
.dona .leg{display:flex;align-items:center;gap:.4rem;font-size:.78rem;}
.dona .sw{width:12px;height:12px;border-radius:3px;}
.donut-svg{display:block;margin:0 auto;}

.treemap{display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:.35rem;}
.tm-cell{padding:.55rem;border-radius:6px;color:#fff;font-size:.75rem;font-weight:700;display:flex;flex-direction:column;justify-content:space-between;min-height:70px;}
.tm-cell .pc{font-size:.7rem;opacity:.85;font-weight:500;}

/* Línea (sparkline / barras) */
.line-chart{display:flex;align-items:flex-end;gap:.4rem;height:140px;padding:.5rem 0;}
.line-chart .bar{flex:1;background:linear-gradient(180deg,#38bdf8,#0369a1);border-radius:4px 4px 0 0;position:relative;min-width:18px;transition:opacity .2s;}
.line-chart .bar:hover{opacity:.85;}
.line-chart .bar .lbl{position:absolute;bottom:-18px;left:0;right:0;text-align:center;font-size:.65rem;color:#64748b;}
.line-chart .bar .vv{position:absolute;top:-18px;left:0;right:0;text-align:center;font-size:.7rem;font-weight:700;color:#0f172a;}
.line-wrap{padding:1rem 0 1.6rem;}

/* Heatmap */
.heat{width:100%;border-collapse:separate;border-spacing:3px;font-size:.74rem;}
.heat th,.heat td{padding:.45rem;text-align:center;border-radius:5px;color:#0f172a;}
.heat thead th{background:#f1f5f9;font-weight:700;}
.heat tbody th{background:#fff;text-align:left;font-weight:700;}

/* Predictivo */
.pred-row{display:grid;grid-template-columns:1fr 90px 90px 90px;gap:.5rem;padding:.45rem .6rem;border-bottom:1px solid #e2e8f0;font-size:.8rem;align-items:center;}
.pred-row.head{background:#f8fafc;font-weight:700;color:#475569;font-size:.7rem;text-transform:uppercase;letter-spacing:.05em;}
.pred-row .badge{display:inline-block;padding:.15rem .5rem;border-radius:999px;font-weight:700;font-size:.7rem;}
.pred-row .b-crit{background:#fee2e2;color:#991b1b;}
.pred-row .b-warn{background:#fef3c7;color:#92400e;}
.pred-row .b-ok{background:#dcfce7;color:#166534;}

.tag{display:inline-block;padding:.18rem .5rem;border-radius:999px;font-size:.68rem;font-weight:700;background:#e0f2fe;color:#0369a1;}
</style>

@php
    $fmt = fn($v, $d=0) => number_format((float)$v, $d, ',', '.');
    $col = function($i){
        $cs = ['#0ea5e9','#22c55e','#f59e0b','#ef4444','#a855f7','#14b8a6','#f97316','#3b82f6','#84cc16','#ec4899','#6366f1','#06b6d4','#eab308','#dc2626','#10b981'];
        return $cs[$i % count($cs)];
    };
@endphp

<div style="padding:1.5rem;">
    <div class="page-header">
        <div>
            <h1>Reportes · Insumos / Consumos</h1>
            <p>Data Mart de consulta · Trazabilidad 360° del consumo · Inteligencia de negocio</p>
        </div>
        <div style="display:flex;gap:.5rem;">
            @puede('Consumos generales','Exportar')<a href="{{ route('admin.reportes.insumos.exportar', request()->query()) }}" class="btn btn-export">⬇ Exportar CSV</a>@endpuede
        </div>
    </div>

    {{-- Filtros --}}
    <div class="card">
        <h3>Filtros avanzados <span class="pill">Rango {{ $desde->format('d/m/Y') }} → {{ $hasta->format('d/m/Y') }}</span></h3>
        <form method="GET" class="filters">
            <div><label>Desde</label><input type="date" name="desde" value="{{ $desde->format('Y-m-d') }}"></div>
            <div><label>Hasta</label><input type="date" name="hasta" value="{{ $hasta->format('Y-m-d') }}"></div>
            <div>
                <label>Medicamento</label>
                <select name="medicamento_id">
                    <option value="">— Todos —</option>
                    @foreach($medicamentos as $m)
                        <option value="{{ $m->id }}" @selected($medicamentoId==$m->id)>{{ $m->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Laboratorio</label>
                <select name="laboratorio_id">
                    <option value="">— Todos —</option>
                    @foreach($laboratorios as $l)
                        <option value="{{ $l->id }}" @selected($laboratorioId==$l->id)>{{ $l->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Proveedor</label>
                <select name="proveedor_id">
                    <option value="">— Todos —</option>
                    @foreach($proveedores as $p)
                        <option value="{{ $p->id }}" @selected($proveedorId==$p->id)>{{ $p->nombre_comercial ?: $p->razon_social }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Servicio</label>
                <select name="servicio_id">
                    <option value="">— Todos —</option>
                    @foreach($servicios as $s)
                        <option value="{{ $s->id }}" @selected($servicioId==$s->id)>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Lote</label>
                <select name="lote_id">
                    <option value="">— Todos —</option>
                    @foreach($lotes as $l)
                        <option value="{{ $l->id }}" @selected($loteId==$l->id)>{{ $l->lote }} · {{ optional($l->medicamento)->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Tipo movimiento</label>
                <select name="tipo_mov">
                    <option value="">— Todos consumo —</option>
                    @foreach(\App\Models\MovimientoInventario::TIPOS as $k=>$v)
                        <option value="{{ $k }}" @selected($tipoMov==$k)>{{ $v['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;gap:.4rem;align-items:flex-end;">
                <button class="btn btn-primary" type="submit">Aplicar</button>
                <a href="{{ route('admin.reportes.insumos') }}" class="btn btn-secondary">Limpiar</a>
            </div>
        </form>
    </div>

    {{-- KPIs ejecutivos --}}
    <div class="kpi-grid">
        <div class="kpi k-cost"><div class="lbl">Consumo total ($)</div><div class="val">${{ $fmt($kpis['consumo_total'], 0) }}</div></div>
        <div class="kpi"><div class="lbl">Cantidad consumida</div><div class="val">{{ $fmt($kpis['cantidad_total'], 0) }}</div></div>
        <div class="kpi"><div class="lbl">Medicamentos</div><div class="val">{{ $fmt($kpis['medicamentos']) }}</div></div>
        <div class="kpi k-pac"><div class="lbl">Pacientes atendidos</div><div class="val">{{ $fmt($kpis['pacientes']) }}</div></div>
        <div class="kpi k-prep"><div class="lbl">Preparaciones</div><div class="val">{{ $fmt($kpis['preparaciones']) }}</div></div>
        <div class="kpi k-lot"><div class="lbl">Lotes consumidos</div><div class="val">{{ $fmt($kpis['lotes']) }}</div></div>
        <div class="kpi"><div class="lbl">Movimientos</div><div class="val">{{ $fmt($kpis['movimientos']) }}</div></div>
        <div class="kpi k-cost"><div class="lbl">Costo prom. paciente</div><div class="val">${{ $fmt($kpis['costo_promedio_paciente'], 0) }}</div></div>
    </div>

    {{-- Serie mensual --}}
    <div class="card">
        <h3>Tendencia · Consumo mensual (últimos 12 meses) <span class="pill">Costo</span></h3>
        @php $maxSerie = max($serie->pluck('costo')->map(fn($v)=>(float)$v)->all() ?: [1]); @endphp
        <div class="line-wrap">
            <div class="line-chart">
                @foreach($serie as $s)
                    @php $h = $maxSerie>0 ? max(2, ($s->costo/$maxSerie)*100) : 0; @endphp
                    <div class="bar" style="height:{{ $h }}%;" title="{{ $s->ym }}: ${{ $fmt($s->costo,0) }}">
                        <div class="vv">${{ $fmt($s->costo/1000,0) }}k</div>
                        <div class="lbl">{{ \Carbon\Carbon::parse($s->ym.'-01')->format('M/y') }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Top medicamentos + Por servicio (dona) --}}
    <div class="row2">
        <div class="card">
            <h3>Top 20 medicamentos consumidos <span class="pill">Cantidad</span></h3>
            @php $maxMed = max($topMedicamentos->pluck('cantidad')->map(fn($v)=>(float)$v)->all() ?: [1]); @endphp
            <div class="bar-list">
                @forelse($topMedicamentos as $m)
                    <div class="bar-row">
                        <div class="nm" title="{{ $m->nombre }}">{{ $m->nombre }}</div>
                        <div class="bar"><span style="width:{{ $maxMed>0 ? ($m->cantidad/$maxMed)*100 : 0 }}%;"></span></div>
                        <div class="vl">{{ $fmt($m->cantidad,0) }}</div>
                    </div>
                @empty
                    <div style="color:#94a3b8;font-size:.85rem;">Sin datos en el rango.</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <h3>Consumo por servicio <span class="pill">Dona</span></h3>
            @php
                $totalSrv = $porServicio->sum('cantidad') ?: 1;
                $acum = 0;
                $segs = [];
                foreach ($porServicio as $i=>$ps) {
                    $pct = ((float)$ps->cantidad / $totalSrv) * 100;
                    $segs[] = ['from'=>$acum,'to'=>$acum+$pct,'color'=>$col($i),'label'=>$ps->servicio,'pct'=>$pct,'val'=>$ps->cantidad];
                    $acum += $pct;
                }
            @endphp
            @if($porServicio->isEmpty())
                <div style="color:#94a3b8;font-size:.85rem;">Sin datos en el rango.</div>
            @else
                <svg class="donut-svg" viewBox="0 0 42 42" width="180" height="180">
                    <circle cx="21" cy="21" r="15.915" fill="#fff" stroke="#f1f5f9" stroke-width="6"/>
                    @php $offset = 25; @endphp
                    @foreach($segs as $s)
                        <circle cx="21" cy="21" r="15.915" fill="transparent"
                                stroke="{{ $s['color'] }}" stroke-width="6"
                                stroke-dasharray="{{ number_format($s['pct'],3,'.','') }} {{ number_format(100-$s['pct'],3,'.','') }}"
                                stroke-dashoffset="{{ number_format($offset,3,'.','') }}"/>
                        @php $offset = $offset - $s['pct']; @endphp
                    @endforeach
                    <text x="21" y="20" text-anchor="middle" font-size="4" font-weight="bold" fill="#0f172a">{{ $fmt($totalSrv,0) }}</text>
                    <text x="21" y="25" text-anchor="middle" font-size="2.5" fill="#64748b">unidades</text>
                </svg>
                <div class="dona" style="margin-top:.6rem;">
                    @foreach($segs as $s)
                        <div class="leg">
                            <span class="sw" style="background:{{ $s['color'] }}"></span>
                            <span style="flex:1;">{{ $s['label'] }}</span>
                            <strong>{{ number_format($s['pct'],1,',','.') }}%</strong>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Treemap laboratorio + Tabla proveedor --}}
    <div class="row2">
        <div class="card">
            <h3>Consumo por laboratorio <span class="pill">Treemap</span></h3>
            @php $totalLab = max($porLaboratorio->sum('costo'),1); @endphp
            @if($porLaboratorio->isEmpty())
                <div style="color:#94a3b8;font-size:.85rem;">Sin datos en el rango.</div>
            @else
                <div class="treemap">
                    @foreach($porLaboratorio as $i=>$pl)
                        @php $pct = ((float)$pl->costo/$totalLab)*100; $size = max(70, $pct*4); @endphp
                        <div class="tm-cell" style="background:{{ $col($i) }};min-height:{{ $size }}px;" title="{{ $pl->laboratorio }}: ${{ $fmt($pl->costo,0) }}">
                            <div>{{ $pl->laboratorio }}</div>
                            <div class="pc">${{ $fmt($pl->costo,0) }} · {{ number_format($pct,1,',','.') }}%</div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="card">
            <h3>Consumo por proveedor <span class="pill">Top 15</span></h3>
            <table class="t">
                <thead><tr><th>Proveedor</th><th class="num">Cantidad</th><th class="num">Costo</th></tr></thead>
                <tbody>
                @forelse($porProveedor as $pp)
                    <tr>
                        <td>{{ $pp->proveedor }}</td>
                        <td class="num">{{ $fmt($pp->cantidad,0) }}</td>
                        <td class="num">${{ $fmt($pp->costo,0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="3" style="text-align:center;color:#94a3b8;padding:1rem;">Sin datos.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Mapa de calor --}}
    <div class="card">
        <h3>Mapa de calor · Medicamento × Servicio <span class="pill">Top 15 × 8</span></h3>
        @if(empty($heatMeds) || empty($heatSrvs))
            <div style="color:#94a3b8;font-size:.85rem;">Sin dispensaciones en el rango para construir el mapa.</div>
        @else
            <div style="overflow-x:auto;">
                <table class="heat">
                    <thead>
                        <tr>
                            <th>Medicamento \ Servicio</th>
                            @foreach($heatSrvs as $srv)<th>{{ \Illuminate\Support\Str::limit($srv,16) }}</th>@endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($heatMeds as $med)
                            <tr>
                                <th>{{ \Illuminate\Support\Str::limit($med,28) }}</th>
                                @foreach($heatSrvs as $srv)
                                    @php
                                        $v = $heatMap[$med][$srv] ?? 0;
                                        $intensity = $heatMax>0 ? ($v/$heatMax) : 0;
                                        $bg = $intensity > 0
                                            ? 'rgba('.(int)(255-(255*0.7*$intensity)).','.(int)(180-(180*$intensity)).','.(int)(180-(180*$intensity)).',1)'
                                            : '#f8fafc';
                                        $color = $intensity > 0.5 ? '#fff' : '#0f172a';
                                    @endphp
                                    <td style="background:{{ $bg }};color:{{ $color }};font-weight:{{ $intensity>0?'700':'400' }};">
                                        {{ $v>0 ? number_format($v,0,',','.') : '·' }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Top pacientes + FEFO por lote --}}
    <div class="row2">
        <div class="card">
            <h3>Top pacientes (consumo dispensado) <span class="pill">Top 15</span></h3>
            <table class="t">
                <thead><tr><th>Paciente</th><th>Doc</th><th class="num">Cantidad</th><th class="num">Costo</th></tr></thead>
                <tbody>
                @forelse($topPacientes as $p)
                    <tr>
                        <td>{{ trim($p->paciente) ?: '—' }}</td>
                        <td>{{ $p->documento }}</td>
                        <td class="num">{{ $fmt($p->cantidad,0) }}</td>
                        <td class="num">${{ $fmt($p->costo,0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:1rem;">Sin datos.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3>Reporte FEFO · Consumo por lote <span class="pill">Vence pronto</span></h3>
            <table class="t">
                <thead><tr><th>Lote</th><th>Medicamento</th><th>Vence</th><th class="num">Cons.</th><th class="num">Resta</th></tr></thead>
                <tbody>
                @forelse($porLote as $l)
                    @php
                        $ini = max(1, (float)($l->cantidad_inicial ?: 1));
                        $cons = (float)$l->consumido;
                        $rest = (float)$l->cantidad_actual;
                        $pctC = min(100, round($cons/$ini*100));
                    @endphp
                    <tr>
                        <td><strong>{{ $l->lote }}</strong></td>
                        <td>{{ \Illuminate\Support\Str::limit($l->medicamento,30) }}</td>
                        <td>{{ $l->fecha_vencimiento }}</td>
                        <td class="num">{{ $fmt($cons,0) }} <span style="color:#64748b;">({{ $pctC }}%)</span></td>
                        <td class="num">{{ $fmt($rest,0) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:1rem;">Sin consumos por lote.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Producción --}}
    <div class="card">
        <h3>Consumos de producción <span class="pill">Mezclas · Preparaciones · Reempaques</span></h3>
        <div class="row3">
            @foreach(['mezclas'=>'Mezclas','preparaciones'=>'Preparaciones','reempaques'=>'Reempaques'] as $k=>$lbl)
                @php $p = $produccion[$k]; @endphp
                <div style="background:#f8fafc;border-radius:10px;padding:1rem;">
                    <div style="font-size:.78rem;color:#64748b;text-transform:uppercase;font-weight:700;letter-spacing:.05em;">{{ $lbl }}</div>
                    <div style="font-size:1.4rem;font-weight:800;color:#0f172a;margin-top:.25rem;">${{ $fmt(optional($p)->costo ?? 0, 0) }}</div>
                    <div style="font-size:.8rem;color:#475569;">{{ $fmt(optional($p)->cant ?? 0, 0) }} unidades · {{ $fmt(optional($p)->procesos ?? 0) }} procesos</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Predictivo --}}
    <div class="card">
        <h3>Vista predictiva · Días de inventario restantes <span class="pill">Stock ÷ consumo diario</span></h3>
        <div class="pred-row head">
            <div>Medicamento</div>
            <div style="text-align:right;">Stock</div>
            <div style="text-align:right;">Cons./día</div>
            <div style="text-align:right;">Días</div>
        </div>
        @forelse($predictivo as $pr)
            @php
                $cls = $pr->dias < 7 ? 'b-crit' : ($pr->dias < 21 ? 'b-warn' : 'b-ok');
            @endphp
            <div class="pred-row">
                <div>{{ $pr->nombre }}</div>
                <div style="text-align:right;">{{ $fmt($pr->stock,0) }}</div>
                <div style="text-align:right;">{{ $fmt($pr->promedio_diario,1) }}</div>
                <div style="text-align:right;"><span class="badge {{ $cls }}">{{ $fmt($pr->dias,0) }} d</span></div>
            </div>
        @empty
            <div style="color:#94a3b8;font-size:.85rem;padding:.85rem;">Sin datos suficientes para proyección.</div>
        @endforelse
    </div>

    {{-- Drill-down resumen final --}}
    <div class="card">
        <h3>Resumen drill-down · Consumo total → Servicio → Medicamento</h3>
        <table class="t">
            <thead>
                <tr><th>Servicio</th><th class="num">Cantidad</th><th class="num">Costo</th><th class="num">% Total</th></tr>
            </thead>
            <tbody>
                @php $totSrv = $porServicio->sum('costo') ?: 1; @endphp
                @forelse($porServicio as $ps)
                    <tr>
                        <td>{{ $ps->servicio }}</td>
                        <td class="num">{{ $fmt($ps->cantidad,0) }}</td>
                        <td class="num">${{ $fmt($ps->costo,0) }}</td>
                        <td class="num">{{ number_format(((float)$ps->costo/$totSrv)*100, 1, ',', '.') }}%</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:1rem;">Sin datos.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
