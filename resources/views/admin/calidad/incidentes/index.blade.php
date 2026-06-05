<x-app-layout>
@include('admin.calidad.incidentes._styles')

<div style="padding:1.5rem;">
    <div class="page-header">
        <div>
            <h1>Centro de Gestión de Incidentes</h1>
            <p>Sistema CAPA · Trazabilidad 360° · Bloqueo automático de inventario</p>
        </div>
        <div style="display:flex;gap:.5rem;">
            @puede('Incidentes','Crear')<a href="{{ route('admin.calidad.incidentes.create') }}" class="btn" style="background:#fff;color:var(--inc);">+ Nuevo Incidente</a>@endpuede
        </div>
    </div>

    @include('admin.calidad.incidentes._tabs')

    @if(session('success'))<div class="card" style="background:#dcfce7;color:#166534;">{{ session('success') }}</div>@endif

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi k-open"><div class="lbl">Abiertos</div><div class="val">{{ $stats['abiertos'] }}</div></div>
        <div class="kpi k-crit"><div class="lbl">Críticos</div><div class="val">{{ $stats['criticos'] }}</div></div>
        <div class="kpi k-inv"><div class="lbl">En investigación</div><div class="val">{{ $stats['investigacion'] }}</div></div>
        <div class="kpi k-acc"><div class="lbl">Acción correctiva</div><div class="val">{{ $stats['accion'] }}</div></div>
        <div class="kpi k-cls"><div class="lbl">Cerrados (mes)</div><div class="val">{{ $stats['cerrados_mes'] }}</div></div>
        <div class="kpi"><div class="lbl">Reportados hoy</div><div class="val">{{ $stats['hoy'] }}</div></div>
        <div class="kpi k-blk"><div class="lbl">Lotes bloqueados</div><div class="val">{{ $stats['lotes_bloqueados'] }}</div></div>
        <div class="kpi k-acc"><div class="lbl">Acciones pendientes</div><div class="val">{{ $stats['acciones_pend'] }}</div></div>
    </div>

    {{-- Kanban --}}
    <div class="card">
        <h3>Flujo Kanban de Incidentes</h3>
        <div class="kanban">
            @foreach(['ABIERTO'=>'Abierto','INVESTIGACION'=>'Investigación','ACCION_CORRECTIVA'=>'Acción correctiva','CERRADO'=>'Cerrado'] as $est => $lbl)
                <div class="kb-col">
                    <h4>{{ $lbl }} <span>{{ $kanban[$est]->count() }}</span></h4>
                    @forelse($kanban[$est] as $i)
                        <div class="kb-card sev-{{ $i->severidad }}">
                            <a href="{{ route('admin.calidad.incidentes.show', $i) }}">
                                <div class="codigo">{{ $i->codigo }}</div>
                                <div class="desc">{{ \Illuminate\Support\Str::limit($i->descripcion, 80) }}</div>
                                <div class="meta">
                                    <span class="badge b-{{ $i->severidad }}">{{ $i->severidad }}</span>
                                    <span>{{ $i->fecha_incidente->format('d/m H:i') }}</span>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div style="text-align:center;color:#94a3b8;font-size:.78rem;padding:1rem 0;">Sin registros</div>
                    @endforelse
                </div>
            @endforeach
        </div>
    </div>

    {{-- Semáforo por proceso --}}
    <div class="card">
        <h3>Semáforo por proceso</h3>
        <div class="sem-grid">
            @foreach($semaforo as $key => $info)
                <div class="sem-card s-{{ $info['estado'] }}">
                    <div class="nom"><span class="dot d-{{ $info['estado'] }}"></span>{{ $info['label'] }}</div>
                    <div class="meta">
                        @if($info['estado']==='VERDE') Sin incidentes activos
                        @elseif($info['estado']==='AMARILLO') Incidentes abiertos
                        @elseif($info['estado']==='ROJO') Incidentes críticos
                        @else Sin actividad
                        @endif
                    </div>
                    <div class="nums">
                        <span class="ok">✔ {{ $info['cerrados'] }}</span>
                        <span class="op">⚠ {{ $info['abiertos'] }}</span>
                        <span class="cr">✖ {{ $info['criticos'] }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Matriz de riesgo --}}
    <div class="card">
        <h3>Matriz de Riesgo (Severidad × Clasificación)</h3>
        <table class="matrix">
            <thead>
                <tr>
                    <th>Severidad \ Clasif.</th>
                    @foreach(\App\Models\Incidente::CLASIFICACIONES as $c => $cl)
                        <th>{{ $cl }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\Incidente::SEVERIDADES as $sev => $sl)
                    @php
                        $lvl = match($sev){'BAJA'=>1,'MEDIA'=>2,'ALTA'=>3,'CRITICA'=>4,default=>1};
                    @endphp
                    <tr>
                        <th style="background:#fff;">{{ $sl }}</th>
                        @foreach(\App\Models\Incidente::CLASIFICACIONES as $c => $cl)
                            @php $val = $matriz[$sev][$c] ?? 0; @endphp
                            <td class="{{ $val>0 ? 'lvl-'.$lvl : '' }}">{{ $val }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Recientes --}}
    <div class="card">
        <h3>Incidentes recientes</h3>
        <table class="t">
            <thead>
                <tr>
                    <th>Código</th><th>Tipo</th><th>Clasificación</th><th>Severidad</th>
                    <th>Estado</th><th>Fecha</th><th>Reporta</th><th></th>
                </tr>
            </thead>
            <tbody>
            @forelse($recientes as $i)
                <tr>
                    <td><strong>{{ $i->codigo }}</strong></td>
                    <td>{{ \App\Models\Incidente::TIPOS[$i->tipo_incidente] ?? $i->tipo_incidente }}</td>
                    <td>{{ \App\Models\Incidente::CLASIFICACIONES[$i->clasificacion] ?? $i->clasificacion }}</td>
                    <td><span class="badge b-{{ $i->severidad }}">{{ $i->severidad }}</span></td>
                    <td><span class="badge b-{{ $i->estado }}">{{ $i->estado }}</span></td>
                    <td>{{ $i->fecha_incidente->format('Y-m-d H:i') }}</td>
                    <td>{{ optional($i->usuarioReporta)->name ?? '—' }}</td>
                    <td><a href="{{ route('admin.calidad.incidentes.show', $i) }}" class="btn btn-mini btn-secondary">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;color:#94a3b8;padding:1.5rem;">Sin incidentes registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
