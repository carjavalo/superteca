<x-app-layout>
    <x-slot name="header"><h2>Ajuste {{ $ajuste->codigo }}</h2></x-slot>

    <style>
        :root { --inst:#2e3a75; --ajuste:#7c3aed; }
        .st-alert.success { background:#d1fae5; color:#065f46; border-left:5px solid #10b981; padding:14px 18px; border-radius:10px; margin-bottom:20px; }
        .st-alert.error   { background:#fee2e2; color:#991b1b; border-left:5px solid #ef4444; padding:14px 18px; border-radius:10px; margin-bottom:20px; }

        .hero { background:linear-gradient(135deg,#2e3a75 0%, #7c3aed 100%); color:#fff; border-radius:16px; padding:24px 28px; margin-bottom:18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:18px;}
        .hero h1 { margin:0; font-size:1.4rem; font-weight:800;}
        .hero p  { margin:4px 0 0; opacity:.85;}
        .est-tag { padding:6px 14px; border-radius:20px; background:#fff; color:var(--inst); font-weight:700;}
        .card { background:#fff; border-radius:14px; padding:20px 22px; box-shadow:0 4px 12px rgba(0,0,0,.05); margin-bottom:18px; }
        .card h3 { margin:0 0 12px; color:var(--inst); font-size:1rem; font-weight:800; border-bottom:2px solid #f3f4f6; padding-bottom:6px;}
        .grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:14px; }
        .item { padding:8px 0; }
        .item .lbl { font-size:.72rem; color:#6b7280; text-transform:uppercase; letter-spacing:.04em; font-weight:700; }
        .item .val { color:#111827; font-size:.92rem; margin-top:3px;}

        table { width:100%; border-collapse:collapse; font-size:.88rem; }
        th { background:#f9fafb; padding:10px; text-align:left; font-size:.74rem; text-transform:uppercase; color:#374151; }
        td { padding:10px; border-top:1px solid #f3f4f6; }
        .diff-pos { color:#059669; font-weight:700;}
        .diff-neg { color:#dc2626; font-weight:700;}

        .actions { display:flex; gap:10px; justify-content:flex-end; margin-top:16px; }
        .btn-primary { background:#10b981; color:#fff; border:none; padding:11px 22px; border-radius:8px; font-weight:700; cursor:pointer;}
        .btn-primary:hover{ background:#059669;}
        .btn-danger  { background:#ef4444; color:#fff; border:none; padding:11px 22px; border-radius:8px; font-weight:700; cursor:pointer;}
        .btn-danger:hover{ background:#b91c1c;}
        .btn-outline { background:#fff; color:#374151; border:1.5px solid #d1d5db; padding:11px 18px; border-radius:8px; font-weight:600; text-decoration:none;}
        .badge-tipo { padding:4px 12px; border-radius:20px; font-weight:700; font-size:.78rem; }
        .b-POSITIVO { background:#d1fae5; color:#065f46;}
        .b-NEGATIVO { background:#fee2e2; color:#991b1b;}
    </style>

    @if(session('success')) <div class="st-alert success">{{ session('success') }}</div> @endif
    @if(session('error'))   <div class="st-alert error">{{ session('error') }}</div> @endif

    <div class="hero">
        <div>
            <h1>{{ $ajuste->codigo }}</h1>
            <p>{{ optional($ajuste->fecha_ajuste)->format('d/m/Y H:i') }} · <span class="badge-tipo b-{{ $ajuste->tipo_ajuste }}" style="background:#fff;">{{ $ajuste->tipo_ajuste }}</span></p>
        </div>
        <div class="est-tag">● {{ $ajuste->estado_label }}</div>
    </div>

    <div class="card">
        <h3>Información general</h3>
        <div class="grid">
            <div class="item"><div class="lbl">Motivo</div><div class="val">{{ $ajuste->motivo->codigo ?? '' }} · {{ $ajuste->motivo->nombre ?? '—' }}</div></div>
            <div class="item"><div class="lbl">Solicitante</div><div class="val">{{ $ajuste->solicitante->name ?? '—' }}</div></div>
            <div class="item"><div class="lbl">Aprobador</div><div class="val">{{ $ajuste->aprobador->name ?? '— (pendiente)' }}</div></div>
            <div class="item"><div class="lbl">Fecha aprobación</div><div class="val">{{ $ajuste->fecha_aprobacion ? $ajuste->fecha_aprobacion->format('d/m/Y H:i') : '—' }}</div></div>
            <div class="item" style="grid-column:1/-1;"><div class="lbl">Observaciones</div><div class="val">{{ $ajuste->observaciones ?: '—' }}</div></div>
        </div>
    </div>

    <div class="card">
        <h3>Detalle de lotes</h3>
        <table>
            <thead>
                <tr>
                    <th>Medicamento</th>
                    <th>Lote</th>
                    <th>Vence</th>
                    <th style="text-align:right;">Sistema</th>
                    <th style="text-align:right;">Físico</th>
                    <th style="text-align:right;">Diferencia</th>
                    <th style="text-align:right;">Costo unit.</th>
                    <th style="text-align:right;">Valor</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ajuste->detalles as $d)
                    <tr>
                        <td>{{ $d->medicamento->nombre ?? '—' }}</td>
                        <td><strong>{{ $d->lote }}</strong></td>
                        <td>{{ $d->fecha_vencimiento ? $d->fecha_vencimiento->format('d/m/Y') : '—' }}</td>
                        <td style="text-align:right;">{{ number_format($d->stock_sistema, 2) }}</td>
                        <td style="text-align:right;">{{ number_format($d->stock_fisico, 2) }}</td>
                        <td style="text-align:right;" class="{{ $d->diferencia>0?'diff-pos':($d->diferencia<0?'diff-neg':'') }}">
                            {{ $d->diferencia>0?'+':'' }}{{ number_format($d->diferencia, 2) }}
                        </td>
                        <td style="text-align:right;">$ {{ number_format($d->costo_unitario, 2) }}</td>
                        <td style="text-align:right;"><strong>$ {{ number_format($d->valor_ajuste, 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background:#faf5ff;">
                    <td colspan="7" style="text-align:right; padding:14px;"><strong>Valor total del ajuste:</strong></td>
                    <td style="text-align:right; padding:14px;"><strong style="color:var(--ajuste); font-size:1.1rem;">$ {{ number_format($ajuste->valor_total, 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="actions">
        <a href="{{ route('admin.ajustes.index') }}" class="btn-outline">← Volver al listado</a>
        @if($ajuste->estado === 'BORRADOR')
            <form action="{{ route('admin.ajustes.approve', $ajuste) }}" method="POST" onsubmit="return confirm('Confirmar aprobación. Esta acción modifica el inventario y crea movimientos en el kardex.');">
                @csrf
                <button type="submit" class="btn-primary">✅ Aprobar y aplicar</button>
            </form>
        @endif
        @if($ajuste->estado !== 'ANULADO')
            <form action="{{ route('admin.ajustes.annul', $ajuste) }}" method="POST" onsubmit="return confirm('¿Anular este ajuste? Si ya estaba aprobado, se revertirá el inventario.');">
                @csrf
                <button type="submit" class="btn-danger">⛔ Anular</button>
            </form>
        @endif
    </div>
</x-app-layout>
