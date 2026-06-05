<x-app-layout>
    <x-slot name="header">
        <h2>Inventario · Entradas</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; }
        .st-alert { padding:14px 18px; border-radius:10px; margin-bottom:20px; font-size:.92rem; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:5px solid #10b981; }
        .st-alert.error   { background:#fee2e2; color:#991b1b; border-left:5px solid #ef4444; }

        .dash-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px,1fr)); gap:16px; margin-bottom:24px; }
        .dash-card { background:#fff; border-radius:14px; padding:18px 20px; box-shadow:0 4px 12px rgba(0,0,0,.05); border-left:5px solid var(--inst); display:flex; align-items:center; justify-content:space-between; gap:14px; }
        .dash-card .label { color:#6b7280; font-size:.78rem; text-transform:uppercase; letter-spacing:.05em; font-weight:600; }
        .dash-card .value { color:var(--inst); font-size:1.85rem; font-weight:800; line-height:1; margin-top:4px; }
        .dash-card .ic { font-size:1.6rem; }
        .dash-card.green { border-left-color:#10b981; }
        .dash-card.amber { border-left-color:#f59e0b; }
        .dash-card.red   { border-left-color:#ef4444; }
        .dash-card.blue  { border-left-color:#3b82f6; }

        .toolbar { background:#fff; border-radius:14px; padding:16px 20px; box-shadow:0 4px 12px rgba(0,0,0,.04); display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:18px; }
        .toolbar form { display:flex; gap:10px; flex-wrap:wrap; align-items:center; flex:1; }
        .toolbar input, .toolbar select { padding:8px 11px; border:1.5px solid #d1d5db; border-radius:8px; font-size:.88rem; outline:none; }
        .toolbar input:focus, .toolbar select:focus { border-color:var(--inst); }

        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:10px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; gap:7px; text-decoration:none; }
        .btn-primary:hover { background:#3b4a96; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:10px 16px; border-radius:8px; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; cursor:pointer; }
        .btn-icon { width:32px; height:32px; border-radius:8px; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; }
        .btn-view { background:#f5f3ff; color:#7c3aed; } .btn-view:hover { background:#7c3aed; color:#fff; }
        .btn-edit { background:#eff6ff; color:#2563eb; } .btn-edit:hover { background:#2563eb; color:#fff; }
        .btn-del  { background:#fee2e2; color:#dc2626; } .btn-del:hover  { background:#dc2626; color:#fff; }

        .table-wrap { background:#fff; border-radius:14px; box-shadow:0 6px 14px rgba(0,0,0,.04); overflow:hidden; }
        table { width:100%; border-collapse:collapse; font-size:.92rem; }
        th { text-align:left; padding:13px 14px; background:#f9fafb; color:#374151; font-weight:700; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid #e5e7eb; }
        td { padding:14px; border-top:1px solid #f3f4f6; color:#4b5563; }
        tr:hover td { background:#fafbff; }

        .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:.72rem; font-weight:700; border:1px solid transparent; }
        .b-COMPRA     { background:#eef0f7; color:var(--inst); border-color:#dde0ee; }
        .b-DONACION   { background:#dcfce7; color:#065f46; border-color:#a7f3d0; }
        .b-DEVOLUCION { background:#ffedd5; color:#9a3412; border-color:#fdba74; }
        .b-TRASLADO   { background:#e0e7ff; color:#3730a3; border-color:#c7d2fe; }
        .b-AJUSTE     { background:#fef9c3; color:#854d0e; border-color:#fde68a; }
        .b-PRODUCCION { background:#fce7f3; color:#9d174d; border-color:#fbcfe8; }

        .est { display:inline-flex; align-items:center; gap:5px; padding:4px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
        .est-BORRADOR   { background:#fef9c3; color:#854d0e; border:1px solid #fde68a; }
        .est-CONFIRMADA { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
        .est-ANULADA    { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }
    </style>

    @if(session('success')) <div class="st-alert success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="st-alert error">{{ session('error') }}</div> @endif

    <div class="dash-grid">
        <div class="dash-card blue"><div><div class="label">Entradas hoy</div><div class="value">{{ $stats['hoy'] }}</div></div><div class="ic">📥</div></div>
        <div class="dash-card green"><div><div class="label">Unidades recibidas (mes)</div><div class="value">{{ number_format($stats['recibidos'], 0) }}</div></div><div class="ic">📦</div></div>
        <div class="dash-card amber"><div><div class="label">Próximas a vencer (90d)</div><div class="value">{{ $stats['por_vencer'] }}</div></div><div class="ic">⏳</div></div>
        <div class="dash-card red"><div><div class="label">Pendientes de validar</div><div class="value">{{ $stats['pendientes'] }}</div></div><div class="ic">📝</div></div>
    </div>

    <div class="toolbar">
        <form method="GET" action="{{ route('admin.entradas.index') }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar código, factura, remisión...">
            <select name="tipo">
                <option value="">Todos los tipos</option>
                @foreach(\App\Models\Entrada::TIPOS as $k => $l)
                    <option value="{{ $k }}" {{ request('tipo')==$k?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <select name="estado">
                <option value="">Todos los estados</option>
                @foreach(\App\Models\Entrada::ESTADOS as $k => $l)
                    <option value="{{ $k }}" {{ request('estado')==$k?'selected':'' }}>{{ $l }}</option>
                @endforeach
            </select>
            <select name="proveedor">
                <option value="">Todos los proveedores</option>
                @foreach($proveedores as $p)
                    <option value="{{ $p->id }}" {{ request('proveedor')==$p->id?'selected':'' }}>{{ $p->razon_social }}</option>
                @endforeach
            </select>
            <input type="date" name="desde" value="{{ request('desde') }}">
            <input type="date" name="hasta" value="{{ request('hasta') }}">
            <button type="submit" class="btn-outline">Filtrar</button>
            <a href="{{ route('admin.entradas.index') }}" class="btn-outline">Limpiar</a>
        </form>
        @puede('Entradas','Crear')
        <a href="{{ route('admin.entradas.create') }}" class="btn-primary">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="18" height="18"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
            Nueva Entrada
        </a>
        @endpuede
    </div>

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Proveedor</th>
                    <th>Factura / Remisión</th>
                    <th>Fecha</th>
                    <th style="text-align:center;">Ítems</th>
                    <th style="text-align:right;">Total</th>
                    <th>Estado</th>
                    <th style="text-align:right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($entradas as $e)
                    <tr>
                        <td><strong style="color:var(--inst);">{{ $e->codigo }}</strong></td>
                        <td><span class="badge b-{{ $e->tipo_entrada }}">{{ $e->tipo_label }}</span></td>
                        <td>{{ $e->proveedor->razon_social ?? '—' }}</td>
                        <td>
                            @if($e->numero_factura) <div>📄 {{ $e->numero_factura }}</div> @endif
                            @if($e->numero_remision) <div style="color:#9ca3af; font-size:.82rem;">🚚 {{ $e->numero_remision }}</div> @endif
                            @if(! $e->numero_factura && ! $e->numero_remision) — @endif
                        </td>
                        <td>{{ $e->fecha_entrada->format('d/m/Y H:i') }}</td>
                        <td style="text-align:center;"><strong>{{ $e->detalles_count }}</strong></td>
                        <td style="text-align:right;"><strong>$ {{ number_format($e->total, 2) }}</strong></td>
                        <td><span class="est est-{{ $e->estado }}">● {{ $e->estado_label }}</span></td>
                        <td style="text-align:right;">
                            <div style="display:inline-flex; gap:6px;">
                                <a href="{{ route('admin.entradas.show', $e) }}" class="btn-icon btn-view" title="Ver">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                @if($e->estado === 'BORRADOR')
                                    @puede('Entradas','Editar')
                                    <a href="{{ route('admin.entradas.edit', $e) }}" class="btn-icon btn-edit" title="Editar">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                    @endpuede
                                    @puede('Entradas','Eliminar')
                                    <form action="{{ route('admin.entradas.destroy', $e) }}" method="POST" onsubmit="return confirm('¿Eliminar entrada en borrador?')" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-icon btn-del" title="Eliminar">
                                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                                        </button>
                                    </form>
                                    @endpuede
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" style="text-align:center; padding:3rem; color:#6b7280;">No hay entradas registradas con esos filtros.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:18px;">{{ $entradas->links() }}</div>
</x-app-layout>
