<x-app-layout>
    <x-slot name="header">
        <h2>Inventario por Lotes (Kardex)</h2>
    </x-slot>

    <!-- ESTADÍSTICAS DEL KARDEX -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); gap:20px; margin-bottom: 24px;">
        <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 2px 4px rgba(0,0,0,0.05); border-left:4px solid #3b82f6;">
            <p style="margin:0; font-size:0.85rem; color:#6b7280; font-weight:600; text-transform:uppercase;">Total Lotes</p>
            <h3 style="margin:10px 0 0 0; font-size:2rem; color:#1e3a8a;">{{ $stats['total_lotes'] }}</h3>
        </div>
        <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 2px 4px rgba(0,0,0,0.05); border-left:4px solid #10b981;">
            <p style="margin:0; font-size:0.85rem; color:#6b7280; font-weight:600; text-transform:uppercase;">Bajo Stock</p>
            <h3 style="margin:10px 0 0 0; font-size:2rem; color:#064e3b;">{{ $stats['bajo_stock'] }}</h3>
        </div>
        <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 2px 4px rgba(0,0,0,0.05); border-left:4px solid #f59e0b;">
            <p style="margin:0; font-size:0.85rem; color:#6b7280; font-weight:600; text-transform:uppercase;">Próximos a Vencer (30 Días)</p>
            <h3 style="margin:10px 0 0 0; font-size:2rem; color:#78350f;">{{ $stats['proximos'] }}</h3>
        </div>
        <div style="background:#fff; border-radius:10px; padding:20px; box-shadow:0 2px 4px rgba(0,0,0,0.05); border-left:4px solid #ef4444;">
            <p style="margin:0; font-size:0.85rem; color:#6b7280; font-weight:600; text-transform:uppercase;">Lotes Vencidos</p>
            <h3 style="margin:10px 0 0 0; font-size:2rem; color:#7f1d1d; display:flex; align-items:center; gap:8px;">
                {{ $stats['vencidos'] }}
                @if($stats['vencidos'] > 0)
                    <span style="background:#fee2e2; color:#ef4444; font-size:0.7rem; padding:2px 6px; border-radius:4px; font-weight:bold;">¡Alerta!</span>
                @endif
            </h3>
        </div>
    </div>

    <!-- BUSCADOR Y FILTROS -->
    <div style="background:#fff; padding:24px; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.1); margin-bottom:24px;">
        <form method="GET" action="{{ route('admin.inventarios.lotes') }}" style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end;">
            <div style="flex:1; min-width:300px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#374151; margin-bottom:6px;">Búsqueda (Medicamento o Lote)</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Buscar nombre, código o lote..." style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:10px; outline:none;">
            </div>
            <div style="width:220px;">
                <label style="display:block; font-size:0.85rem; font-weight:600; color:#374151; margin-bottom:6px;">Estado Vencimiento</label>
                <select name="estado" style="width:100%; border:1px solid #d1d5db; border-radius:6px; padding:10px; outline:none; background:#fff;">
                    <option value="">Todos los estados</option>
                    <option value="activo" {{ $estado == 'activo' ? 'selected' : '' }}>En regla (Activo)</option>
                    <option value="proximo" {{ $estado == 'proximo' ? 'selected' : '' }}>Próximo a Vencer</option>
                    <option value="vencido" {{ $estado == 'vencido' ? 'selected' : '' }}>Vencido</option>
                </select>
            </div>
            <div>
                <button type="submit" style="background:#2e3a75; color:#fff; border:none; padding:11px 24px; border-radius:6px; font-weight:600; cursor:pointer;">
                    🔍 Filtrar Kardex
                </button>
            </div>
        </form>
    </div>

    <!-- TABLA KARDEX -->
    <div style="background:#fff; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,0.1); overflow:hidden;">
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse; min-width:900px; text-align:left;">
                <thead>
                    <tr style="background:#f9fafb; border-bottom:1px solid #e5e7eb;">
                        <th style="padding:14px 20px; font-size:0.85rem; color:#374151;">MEDICAMENTO</th>
                        <th style="padding:14px 20px; font-size:0.85rem; color:#374151;">LOTE</th>
                        <th style="padding:14px 20px; font-size:0.85rem; color:#374151;">ESTADO</th>
                        <th style="padding:14px 20px; font-size:0.85rem; color:#374151;">EXISTENCIAS</th>
                        <th style="padding:14px 20px; font-size:0.85rem; color:#374151;">UBICACIÓN</th>
                        <th style="padding:14px 20px; font-size:0.85rem; color:#374151;">TEMP.</th>
                        <th style="padding:14px 20px; font-size:0.85rem; color:#374151; text-align:right;">INGRESO</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lotes as $lote)
                        @php
                            $hoy = \Carbon\Carbon::now();
                            $vencido = $lote->fecha_vencimiento && $lote->fecha_vencimiento->lt($hoy);
                            $proximo = $lote->fecha_vencimiento && $lote->fecha_vencimiento->between($hoy, $hoy->copy()->addDays(30));
                            
                            $pctStock = $lote->cantidad_inicial > 0 ? ($lote->cantidad_actual / $lote->cantidad_inicial) * 100 : 0;
                        @endphp
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <td style="padding:14px 20px;">
                                <div style="font-weight:600; color:#111827;">{{ $lote->medicamento->nombre ?? 'N/A' }}</div>
                                <div style="font-size:0.8rem; color:#6b7280;">{{ $lote->medicamento->codigo ?? 'N/A' }} {{ $lote->medicamento->concentracion ? '· '.$lote->medicamento->concentracion : '' }}</div>
                            </td>
                            <td style="padding:14px 20px;">
                                <div style="font-weight:600; color:#374151;">{{ $lote->lote }}</div>
                                <div style="font-size:0.8rem; color:#6b7280;">Venc: {{ $lote->fecha_vencimiento ? $lote->fecha_vencimiento->format('d/m/Y') : 'N/A' }}</div>
                            </td>
                            <td style="padding:14px 20px;">
                                @if($vencido)
                                    <span style="display:inline-block; padding:4px 8px; background:#fee2e2; color:#dc2626; border-radius:12px; font-size:0.75rem; font-weight:600;">Vencido</span>
                                @elseif($proximo)
                                    <span style="display:inline-block; padding:4px 8px; background:#fef3c7; color:#d97706; border-radius:12px; font-size:0.75rem; font-weight:600;">Por Vencer</span>
                                @else
                                    <span style="display:inline-block; padding:4px 8px; background:#d1fae5; color:#059669; border-radius:12px; font-size:0.75rem; font-weight:600;">En Regla</span>
                                @endif
                            </td>
                            <td style="padding:14px 20px;">
                                <div style="font-weight:600; color: {{ $pctStock < 20 ? '#dc2626' : '#111827' }};">{{ number_format($lote->cantidad_actual, 0) }} / {{ number_format($lote->cantidad_inicial, 0) }}</div>
                                <div style="font-size:0.8rem; color:#6b7280;">{{ $lote->unidad_medida }}</div>
                                <div style="width:100%; height:4px; background:#e5e7eb; border-radius:2px; margin-top:4px;">
                                    <div style="height:100%; width:{{ min(100, $pctStock) }}%; background:{{ $pctStock < 20 ? '#ef4444' : ($pctStock < 50 ? '#f59e0b' : '#10b981') }}; border-radius:2px;"></div>
                                </div>
                            </td>
                            <td style="padding:14px 20px;">
                                <span style="font-size:0.9rem; color:#374151;">{{ $lote->ubicacion ?: 'No asignada' }}</span>
                            </td>
                            <td style="padding:14px 20px;">
                                @if($lote->temperatura_min || $lote->temperatura_max)
                                    <span style="font-size:0.85rem; color:#4b5563; background:#e0f2fe; padding:3px 8px; border-radius:4px;">
                                        ❄️ {{ $lote->temperatura_min }}°C - {{ $lote->temperatura_max }}°C
                                    </span>
                                @else
                                    <span style="font-size:0.85rem; color:#9ca3af;">Ambiente</span>
                                @endif
                            </td>
                            <td style="padding:14px 20px; text-align:right; font-size:0.9rem; color:#374151;">
                                {{ $lote->fecha_ingreso ? $lote->fecha_ingreso->format('d/m/Y') : '--' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="padding:30px; text-align:center; color:#6b7280;">
                                No se encontraron lotes registrados en el Kardex.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="padding:16px 24px; border-top:1px solid #e5e7eb; background:#f9fafb;">
            {{ $lotes->links() }}
        </div>
    </div>
</x-app-layout>