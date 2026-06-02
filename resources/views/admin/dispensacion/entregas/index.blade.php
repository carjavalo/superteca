<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #0ea5e9 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
    .page-header h1 { margin:0; font-size:1.4rem; }
    .page-header p { margin:0; opacity:.85; font-size:.85rem; }

    .kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1rem; margin-bottom:1.2rem; }
    .kpi { background:#fff; border-radius:12px; padding:1rem 1.1rem; box-shadow:0 2px 8px rgba(0,0,0,.07); border-left:4px solid #0ea5e9; }
    .kpi .lbl { font-size:.7rem; color:#94a3b8; text-transform:uppercase; font-weight:600; }
    .kpi .val { font-size:1.6rem; font-weight:700; color:#1e293b; margin-top:.2rem; }

    .filters { background:#fff; padding:1rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); display:flex; gap:.6rem; align-items:end; flex-wrap:wrap; margin-bottom:1.2rem; }
    .filters input, .filters select { padding:.5rem .8rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; }
    .filters label { font-size:.7rem; color:#475569; font-weight:600; display:block; margin-bottom:.2rem; }
    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:#0ea5e9; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }

    .kanban { display:grid; grid-template-columns:repeat(5,1fr); gap:.7rem; }
    @media (max-width:1100px) { .kanban { grid-template-columns:repeat(2,1fr); } }
    .col { background:#f8fafc; border-radius:12px; padding:.7rem; min-height:300px; }
    .col h3 { margin:0 0 .7rem 0; font-size:.85rem; padding:.4rem .6rem; border-radius:6px; color:#fff; display:flex; justify-content:space-between; align-items:center; }
    .c-pen h3 { background:#f59e0b; }
    .c-ent h3 { background:#22c55e; }
    .c-par h3 { background:#0ea5e9; }
    .c-dev h3 { background:#a855f7; }
    .c-anu h3 { background:#ef4444; }
    .col h3 .count { background:rgba(255,255,255,.25); padding:.1rem .55rem; border-radius:14px; font-size:.75rem; }
    .card { background:#fff; border-radius:8px; padding:.7rem .8rem; margin-bottom:.55rem; box-shadow:0 1px 3px rgba(0,0,0,.08); border-left:3px solid #0ea5e9; cursor:pointer; transition:transform .12s; }
    .card:hover { transform:translateX(2px); }
    .card .codigo { font-size:.78rem; font-family:monospace; color:#0ea5e9; font-weight:700; }
    .card .titulo { font-size:.88rem; font-weight:600; color:#1e293b; margin:.15rem 0; }
    .card .meta { font-size:.72rem; color:#64748b; display:flex; justify-content:space-between; }
    .empty { text-align:center; padding:1.5rem 1rem; color:#94a3b8; font-size:.78rem; }

    .tabla { background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-top:1.2rem; }
    .tabla table { width:100%; border-collapse:collapse; }
    .tabla th { background:#f1f5f9; padding:.7rem .9rem; text-align:left; font-size:.72rem; color:#475569; text-transform:uppercase; }
    .tabla td { padding:.7rem .9rem; font-size:.83rem; border-top:1px solid #f1f5f9; }
    .badge { display:inline-block; padding:.18rem .55rem; border-radius:10px; font-size:.7rem; font-weight:600; }
    .b-pen { background:#fef3c7; color:#92400e; }
    .b-ent { background:#dcfce7; color:#166534; }
    .b-par { background:#e0f2fe; color:#075985; }
    .b-dev { background:#f3e8ff; color:#6b21a8; }
    .b-anu { background:#fee2e2; color:#991b1b; }
</style>

<div class="page-header">
    <div>
        <h1>Dispensación · Entregas</h1>
        <p>Trazabilidad completa: Inventario → Producción → Dispensación → Paciente</p>
    </div>
    <a href="{{ route('admin.dispensacion.entregas.create') }}" class="btn btn-primary">+ Nueva Entrega</a>
</div>

@if(session('success'))<div style="background:#dcfce7;color:#166534;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#10003; {{ session('success') }}</div>@endif
@if(session('error'))<div style="background:#fee2e2;color:#991b1b;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">&#9888; {{ session('error') }}</div>@endif

<div class="kpi-grid">
    <div class="kpi"><div class="lbl">Entregas Hoy</div><div class="val">{{ $stats['hoy'] }}</div></div>
    <div class="kpi"><div class="lbl">Pacientes Atendidos</div><div class="val">{{ $stats['pacientes'] }}</div></div>
    <div class="kpi"><div class="lbl">Pendientes</div><div class="val">{{ $stats['pendientes'] }}</div></div>
    <div class="kpi"><div class="lbl">Entregadas Hoy</div><div class="val">{{ $stats['entregadas'] }}</div></div>
    <div class="kpi"><div class="lbl">Devoluciones Hoy</div><div class="val">{{ $stats['devoluciones'] }}</div></div>
</div>

<form class="filters" method="GET">
    <div><label>Buscar</label><input type="text" name="search" value="{{ request('search') }}" placeholder="código, paciente, servicio"></div>
    <div><label>Estado</label>
        <select name="estado">
            <option value="">— Todos —</option>
            @foreach(\App\Models\DispensacionEntrega::ESTADOS as $k=>$v)
                <option value="{{ $k }}" {{ request('estado')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div><label>Tipo</label>
        <select name="tipo">
            <option value="">— Todos —</option>
            @foreach(\App\Models\DispensacionEntrega::TIPOS as $k=>$v)
                <option value="{{ $k }}" {{ request('tipo')==$k?'selected':'' }}>{{ $v }}</option>
            @endforeach
        </select>
    </div>
    <div><label>Desde</label><input type="date" name="desde" value="{{ request('desde') }}"></div>
    <div><label>Hasta</label><input type="date" name="hasta" value="{{ request('hasta') }}"></div>
    <button class="btn btn-primary">Filtrar</button>
    <a href="{{ route('admin.dispensacion.entregas.index') }}" class="btn btn-secondary">Limpiar</a>
</form>

<div class="kanban">
    @php $cols = ['PENDIENTE'=>'c-pen','ENTREGADA'=>'c-ent','PARCIAL'=>'c-par','DEVUELTA'=>'c-dev','ANULADA'=>'c-anu']; @endphp
    @foreach($cols as $estado => $clase)
    <div class="col {{ $clase }}">
        <h3>{{ \App\Models\DispensacionEntrega::ESTADOS[$estado] }} <span class="count">{{ $kanban[$estado]->count() }}</span></h3>
        @forelse($kanban[$estado] as $e)
            <a href="{{ route('admin.dispensacion.entregas.show', $e) }}" style="text-decoration:none;color:inherit">
            <div class="card">
                <div class="codigo">{{ $e->codigo }}</div>
                <div class="titulo">{{ $e->destino }}</div>
                <div class="meta">
                    <span>{{ $e->fecha_entrega?->format('d/m H:i') }}</span>
                    <span>{{ $e->detalles_count }} ítems</span>
                </div>
            </div>
            </a>
        @empty
            <div class="empty">Sin registros</div>
        @endforelse
    </div>
    @endforeach
</div>

<div class="tabla">
    <table>
        <thead>
            <tr>
                <th>Código</th><th>Tipo</th><th>Destino</th><th>Fecha</th>
                <th>Dispensador</th><th>Ítems</th><th>Costo</th><th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($todos as $e)
                @php $bclass = ['PENDIENTE'=>'b-pen','ENTREGADA'=>'b-ent','PARCIAL'=>'b-par','DEVUELTA'=>'b-dev','ANULADA'=>'b-anu'][$e->estado] ?? 'b-pen'; @endphp
                <tr style="cursor:pointer" onclick="window.location='{{ route('admin.dispensacion.entregas.show', $e) }}'">
                    <td><strong style="color:#0ea5e9;font-family:monospace">{{ $e->codigo }}</strong></td>
                    <td>{{ \App\Models\DispensacionEntrega::TIPOS[$e->tipo_entrega] ?? $e->tipo_entrega }}</td>
                    <td>{{ $e->destino }}</td>
                    <td>{{ $e->fecha_entrega?->format('d/m/Y H:i') }}</td>
                    <td>{{ $e->dispensador->name ?? '—' }}</td>
                    <td>{{ $e->detalles_count }}</td>
                    <td>${{ number_format($e->costo_total, 0, ',', '.') }}</td>
                    <td><span class="badge {{ $bclass }}">{{ \App\Models\DispensacionEntrega::ESTADOS[$e->estado] }}</span></td>
                </tr>
            @empty
                <tr><td colspan="8" style="text-align:center;padding:2rem;color:#94a3b8">No hay entregas registradas.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
</x-app-layout>
