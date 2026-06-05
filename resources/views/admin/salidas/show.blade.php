<x-app-layout>
    <x-slot name="header">
        <h2>Salida · {{ $salida->codigo }}</h2>
    </x-slot>

    <style>
        :root { --inst: #2e3a75; --salida: #b91c1c; }
        .st-alert { padding:14px 18px; border-radius:10px; margin-bottom:20px; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:5px solid #10b981; }
        .st-alert.error   { background:#fee2e2; color:#991b1b; border-left:5px solid #ef4444; }

        .top-bar { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:10px; }
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:9px 16px; border-radius:8px; font-weight:600; font-size:.9rem; text-decoration:none; display:inline-flex; align-items:center; gap:6px; }
        .btn-success { background:#10b981; color:#fff; border:none; cursor:pointer; padding:10px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; gap:7px; }
        .btn-success:hover { background:#059669; }
        .btn-danger  { background:#ef4444; color:#fff; border:none; cursor:pointer; padding:10px 18px; border-radius:8px; font-weight:600; font-size:.92rem; display:inline-flex; align-items:center; gap:7px; }
        .btn-danger:hover { background:#dc2626; }
        .btn-primary { background:var(--inst); color:#fff; border:none; cursor:pointer; padding:10px 18px; border-radius:8px; font-weight:600; font-size:.92rem; text-decoration:none; display:inline-flex; align-items:center; gap:7px; }

        .hero { background:linear-gradient(135deg, #fff 0%, #fef2f2 100%); border-radius:16px; padding:24px; box-shadow:0 6px 16px rgba(0,0,0,.05); display:flex; gap:22px; align-items:center; margin-bottom:22px; position:relative; overflow:hidden; }
        .hero::before { content:''; position:absolute; left:0; top:0; bottom:0; width:8px; background:var(--salida); }
        .hero-icon { width:96px; height:96px; border-radius:22px; background:#fef2f2; display:flex; align-items:center; justify-content:center; font-size:3rem; flex-shrink:0; }
        .hero h1 { margin:0; font-size:1.6rem; color:var(--salida); font-weight:800; }
        .hero .meta { color:#6b7280; margin-top:6px; font-size:.95rem; }
        .hero-badges { display:flex; gap:6px; flex-wrap:wrap; margin-top:12px; }

        .badge { display:inline-flex; align-items:center; gap:4px; padding:4px 10px; border-radius:20px; font-size:.75rem; font-weight:700; border:1px solid transparent; }
        .b-PRODUCCION           { background:#fce7f3; color:#9d174d; border-color:#fbcfe8; }
        .b-DISPENSACION         { background:#dbeafe; color:#1e40af; border-color:#bfdbfe; }
        .b-VENCIMIENTO          { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }
        .b-DANO                 { background:#ffedd5; color:#9a3412; border-color:#fdba74; }
        .b-DEVOLUCION_PROVEEDOR { background:#e0e7ff; color:#3730a3; border-color:#c7d2fe; }
        .b-TRASLADO             { background:#f3e8ff; color:#6b21a8; border-color:#e9d5ff; }
        .b-CONSUMO_INTERNO      { background:#dcfce7; color:#065f46; border-color:#a7f3d0; }
        .b-AJUSTE_NEGATIVO      { background:#fef9c3; color:#854d0e; border-color:#fde68a; }
        .b-SALIDA   { background:#fee2e2; color:#991b1b; border-color:#fca5a5; }
        .b-AJUSTE   { background:#fef9c3; color:#854d0e; border-color:#fde68a; }
        .est-BORRADOR   { background:#fef9c3; color:#854d0e; border:1px solid #fde68a; }
        .est-CONFIRMADA { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; }
        .est-ANULADA    { background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; }

        .grid-2 { display:grid; grid-template-columns:2fr 1fr; gap:22px; align-items:start; }
        @media (max-width:1000px){ .grid-2 { grid-template-columns:1fr; } }

        .panel { background:#fff; border-radius:14px; padding:22px; box-shadow:0 6px 14px rgba(0,0,0,.04); margin-bottom:20px; }
        .panel h3 { margin:0 0 16px 0; font-size:1rem; color:var(--inst); border-bottom:2px solid #f3f4f6; padding-bottom:8px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }

        .kv { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px,1fr)); gap:12px 22px; }
        .kv .item .l { color:#6b7280; font-size:.78rem; text-transform:uppercase; letter-spacing:.04em; font-weight:600; }
        .kv .item .v { color:#1f2937; font-size:.95rem; font-weight:700; margin-top:2px; }

        table { width:100%; border-collapse:collapse; font-size:.9rem; }
        table th { text-align:left; padding:10px 12px; background:#f9fafb; color:#374151; font-weight:700; font-size:.76rem; text-transform:uppercase; letter-spacing:.04em; }
        table td { padding:10px 12px; border-top:1px solid #f3f4f6; color:#4b5563; vertical-align:middle; }
        .sem { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:5px; vertical-align:middle; }
        .sem-green { background:#10b981; }
        .sem-amber { background:#f59e0b; }
        .sem-red   { background:#ef4444; }
        .sem-gray  { background:#9ca3af; }

        .totals { background:#f9fafb; border-radius:12px; padding:18px; }
        .totals .row { display:flex; justify-content:space-between; padding:6px 0; font-size:.95rem; }
        .totals .row.big { font-size:1.3rem; font-weight:800; color:var(--salida); border-top:2px solid #e5e7eb; padding-top:10px; margin-top:6px; }

        .timeline { list-style:none; padding:0; margin:0; }
        .timeline li { padding:10px 12px; border-left:3px solid var(--salida); background:#fef2f2; border-radius:0 10px 10px 0; margin-bottom:8px; font-size:.86rem; }
        .timeline li b { color:var(--salida); }
    </style>

    @if(session('success')) <div class="st-alert success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="st-alert error">{{ session('error') }}</div> @endif

    <div class="top-bar">
        <a href="{{ route('admin.salidas.index') }}" class="btn-outline">← Volver al listado</a>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            @if($salida->estado === 'BORRADOR')
                @puede('Salidas','Editar')<a href="{{ route('admin.salidas.edit', $salida) }}" class="btn-primary">✏ Editar</a>@endpuede
                @puede('Salidas','Aprobar')
                <form action="{{ route('admin.salidas.confirm', $salida) }}" method="POST" onsubmit="return confirm('¿Confirmar salida y descontar stock?')">
                    @csrf
                    <button type="submit" class="btn-success">✅ Confirmar salida</button>
                </form>
                @endpuede
            @endif
            @if(in_array($salida->estado, ['BORRADOR','CONFIRMADA']))
                @puede('Salidas','Anular')
                <form action="{{ route('admin.salidas.annul', $salida) }}" method="POST" onsubmit="return confirm('¿Anular salida? Se reintegrará el stock si estaba confirmada.')">
                    @csrf
                    <button type="submit" class="btn-danger">⛔ Anular</button>
                </form>
                @endpuede
            @endif
        </div>
    </div>

    <div class="hero">
        <div class="hero-icon">📤</div>
        <div style="flex:1;">
            <h1>{{ $salida->codigo }}</h1>
            <div class="meta">
                {{ $salida->fecha_salida->format('d/m/Y H:i') }}
                @if($salida->numero_documento) · 📄 {{ $salida->numero_documento }} @endif
                @if($salida->paciente_id) · 👤 Paciente #{{ $salida->paciente_id }} @endif
            </div>
            <div class="hero-badges">
                <span class="badge b-{{ $salida->tipo_salida }}">{{ $salida->tipo_label }}</span>
                <span class="badge est-{{ $salida->estado }}">● {{ $salida->estado_label }}</span>
            </div>
        </div>
        <div style="text-align:right;">
            <div style="color:#6b7280; font-size:.8rem; text-transform:uppercase; font-weight:600;">Total</div>
            <div style="color:var(--salida); font-size:2rem; font-weight:800;">$ {{ number_format($salida->total, 2) }}</div>
        </div>
    </div>

    <div class="grid-2">
        <div>
            <div class="panel">
                <h3>💊 Productos egresados ({{ $salida->detalles->count() }})</h3>
                <div style="overflow-x:auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Medicamento</th>
                                <th>Presentación</th>
                                <th>Lote</th>
                                <th>Vence</th>
                                <th>Cant.</th>
                                <th>Unidad</th>
                                <th style="text-align:right;">Costo</th>
                                <th style="text-align:right;">Total</th>
                                <th>Motivo / Prep.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($salida->detalles as $d)
                                <tr>
                                    <td><strong>{{ $d->medicamento->nombre ?? '—' }}</strong></td>
                                    <td>{{ $d->presentacion->nombre ?? '—' }}</td>
                                    <td><code>{{ $d->lote }}</code></td>
                                    <td>
                                        @if($d->fecha_vencimiento)
                                            <span class="sem sem-{{ $d->semaforo_vencimiento }}"></span>
                                            {{ $d->fecha_vencimiento->format('d/m/Y') }}
                                        @else — @endif
                                    </td>
                                    <td><strong>{{ rtrim(rtrim(number_format($d->cantidad, 2), '0'), '.') }}</strong></td>
                                    <td>{{ $d->unidadMedida->abreviatura ?? '—' }}</td>
                                    <td style="text-align:right;">$ {{ number_format($d->costo_unitario, 2) }}</td>
                                    <td style="text-align:right;"><strong>$ {{ number_format($d->costo_total, 2) }}</strong></td>
                                    <td style="font-size:.82rem;">
                                        @if($d->motivo_salida) <div>{{ $d->motivo_salida }}</div> @endif
                                        @if($d->numero_preparacion) <div style="color:#9ca3af;">⚗️ {{ $d->numero_preparacion }}</div> @endif
                                        @if($d->paciente_id) <div style="color:#9ca3af;">👤 #{{ $d->paciente_id }}</div> @endif
                                        @if(!$d->motivo_salida && !$d->numero_preparacion && !$d->paciente_id) — @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" style="text-align:center; padding:2rem; color:#6b7280;">Sin productos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($movimientos->isNotEmpty())
                <div class="panel">
                    <h3>📊 Kardex generado · movimientos de inventario</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Lote</th>
                                <th style="text-align:right;">Anterior</th>
                                <th style="text-align:right;">Movimiento</th>
                                <th style="text-align:right;">Nuevo</th>
                                <th>Usuario</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientos as $m)
                                <tr>
                                    <td>{{ $m->fecha_movimiento->format('d/m/Y H:i') }}</td>
                                    <td><span class="badge b-{{ $m->tipo_movimiento }}">{{ $m->tipo_movimiento }}</span></td>
                                    <td><code>{{ $m->lote->lote ?? '—' }}</code></td>
                                    <td style="text-align:right;">{{ rtrim(rtrim(number_format($m->stock_anterior, 2), '0'), '.') }}</td>
                                    <td style="text-align:right; color:{{ $m->cantidad >= 0 ? '#10b981' : '#ef4444' }}; font-weight:700;">
                                        {{ $m->cantidad >= 0 ? '+' : '' }}{{ rtrim(rtrim(number_format($m->cantidad, 2), '0'), '.') }}
                                    </td>
                                    <td style="text-align:right;"><strong>{{ rtrim(rtrim(number_format($m->stock_nuevo, 2), '0'), '.') }}</strong></td>
                                    <td>{{ $m->usuario->name ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            @if($salida->observaciones)
                <div class="panel">
                    <h3>📝 Observaciones</h3>
                    <p style="margin:0; color:#4b5563;">{{ $salida->observaciones }}</p>
                </div>
            @endif
        </div>

        <div>
            <div class="panel">
                <h3>Información</h3>
                <div class="kv">
                    <div class="item"><div class="l">Código</div><div class="v">{{ $salida->codigo }}</div></div>
                    <div class="item"><div class="l">Tipo</div><div class="v">{{ $salida->tipo_label }}</div></div>
                    <div class="item"><div class="l">Fecha salida</div><div class="v">{{ $salida->fecha_salida->format('d/m/Y H:i') }}</div></div>
                    <div class="item"><div class="l">Documento</div><div class="v">{{ $salida->numero_documento ?: '—' }}</div></div>
                    <div class="item"><div class="l">Paciente</div><div class="v">{{ $salida->paciente_id ? '#'.$salida->paciente_id : '—' }}</div></div>
                    <div class="item"><div class="l">Servicio</div><div class="v">{{ $salida->servicio_id ? '#'.$salida->servicio_id : '—' }}</div></div>
                    <div class="item"><div class="l">Bodega origen</div><div class="v">{{ $salida->bodega_origen_id ? '#'.$salida->bodega_origen_id : '—' }}</div></div>
                    <div class="item"><div class="l">Bodega destino</div><div class="v">{{ $salida->bodega_destino_id ? '#'.$salida->bodega_destino_id : '—' }}</div></div>
                    <div class="item"><div class="l">Registrado por</div><div class="v">{{ $salida->usuario->name ?? '—' }}</div></div>
                    <div class="item"><div class="l">Autorizado por</div><div class="v">{{ $salida->autorizador->name ?? '—' }}</div></div>
                </div>
            </div>

            <div class="panel">
                <h3>Resumen económico</h3>
                <div class="totals">
                    <div class="row"><span>Subtotal</span><strong>$ {{ number_format($salida->subtotal, 2) }}</strong></div>
                    <div class="row"><span>Impuestos</span><strong>$ {{ number_format($salida->impuestos, 2) }}</strong></div>
                    <div class="row big"><span>Total</span><strong>$ {{ number_format($salida->total, 2) }}</strong></div>
                </div>
            </div>

            <div class="panel">
                <h3>Flujo de la salida</h3>
                <ul class="timeline">
                    <li><b>1. Solicitud / orden</b><br><small>{{ $salida->fecha_salida->format('d/m/Y H:i') }} · {{ $salida->tipo_label }}</small></li>
                    <li><b>2. Validación FEFO</b><br><small>{{ $salida->detalles->count() }} ítems · {{ rtrim(rtrim(number_format($salida->detalles->sum('cantidad'), 2), '0'), '.') }} unidades</small></li>
                    <li><b>3. Autorización</b><br><small>{{ $salida->autorizador->name ?? 'Sin autorización registrada' }}</small></li>
                    <li><b>4. Descuento de inventario</b><br><small>{{ $salida->estado === 'CONFIRMADA' ? '✅ Stock descontado' : ($salida->estado === 'ANULADA' ? '⛔ Anulada' : '⏳ Pendiente de confirmación') }}</small></li>
                    <li><b>5. Kardex y trazabilidad</b><br><small>{{ $movimientos->count() }} movimientos · {{ $salida->paciente_id ? 'Paciente #'.$salida->paciente_id : 'Sin paciente asociado' }}</small></li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
