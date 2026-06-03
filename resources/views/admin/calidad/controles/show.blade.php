<x-app-layout>
@include('admin.calidad.controles._styles')

<div style="padding:1.5rem;">
    <div class="page-header">
        <div>
            <h1>{{ $control->codigo }}</h1>
            <p>{{ \App\Models\ControlCalidad::TIPOS[$control->tipo_control] ?? $control->tipo_control }} · {{ $control->fecha_control->format('Y-m-d H:i') }}</p>
        </div>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <span class="badge b-{{ $control->resultado }}" style="font-size:.85rem;padding:.4rem .9rem;">{{ $control->resultado }}</span>
            <a href="{{ route('admin.calidad.controles.bandeja') }}" class="btn btn-secondary">← Volver</a>
        </div>
    </div>

    @include('admin.calidad.controles._tabs')

    @if(session('success'))<div class="card" style="background:#dcfce7;color:#166534;">{{ session('success') }}</div>@endif

    {{-- Tarjeta de calidad --}}
    @php $det = $control->detalle->first(); @endphp
    <div class="grid-2">
        <div>
            <div class="card">
                <h3>Tarjeta de calidad</h3>
                <table class="t">
                    <tr><th style="width:35%;">Código</th><td><strong>{{ $control->codigo }}</strong></td></tr>
                    <tr><th>Tipo</th><td>{{ \App\Models\ControlCalidad::TIPOS[$control->tipo_control] ?? $control->tipo_control }}</td></tr>
                    <tr><th>Fecha</th><td>{{ $control->fecha_control->format('Y-m-d H:i') }}</td></tr>
                    <tr><th>Responsable</th><td>{{ optional($control->usuario)->name ?? '—' }}</td></tr>
                    <tr><th>Resultado</th><td><span class="badge b-{{ $control->resultado }}">{{ $control->resultado }}</span></td></tr>
                    @if($det && $det->inventarioLote)
                        <tr><th>Lote</th><td>{{ $det->inventarioLote->lote }}</td></tr>
                        <tr><th>Producto</th><td>{{ optional($det->inventarioLote->medicamento)->nombre }}</td></tr>
                        <tr><th>Estado del lote</th><td><span class="badge b-{{ $det->inventarioLote->estado_calidad }}">{{ $det->inventarioLote->estado_calidad }}</span></td></tr>
                    @endif
                    @if($control->observaciones)
                        <tr><th>Observaciones</th><td>{{ $control->observaciones }}</td></tr>
                    @endif
                </table>
            </div>

            <div class="card">
                <h3>Resultados de parámetros</h3>
                @if($control->resultados->isEmpty())
                    <div style="color:#94a3b8;text-align:center;padding:1rem;">Sin resultados registrados.</div>
                @else
                    <table class="t">
                        <thead><tr><th>Parámetro</th><th>Unidad</th><th>Valor</th><th>Cumple</th><th>Observaciones</th></tr></thead>
                        <tbody>
                        @foreach($control->resultados as $res)
                            <tr>
                                <td>{{ optional($res->parametro)->nombre }}</td>
                                <td>{{ optional($res->parametro)->unidad_medida ?? '—' }}</td>
                                <td>{{ $res->valor_obtenido ?? '—' }}</td>
                                <td>
                                    @if($res->cumple)<span class="badge b-APROBADO">Sí</span>
                                    @else<span class="badge b-RECHAZADO">No</span>@endif
                                </td>
                                <td>{{ $res->observaciones ?? '—' }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="card">
                <h3>Acciones correctivas</h3>
                @if($control->acciones->isEmpty())
                    <div style="color:#94a3b8;padding:.5rem 0;">Sin acciones correctivas.</div>
                @else
                    <table class="t">
                        <thead><tr><th>Descripción</th><th>Responsable</th><th>Compromiso</th><th>Cierre</th><th>Estado</th><th></th></tr></thead>
                        <tbody>
                        @foreach($control->acciones as $a)
                            <tr>
                                <td>{{ $a->descripcion }}</td>
                                <td>{{ optional($a->responsable)->name ?? '—' }}</td>
                                <td>{{ optional($a->fecha_compromiso)->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ optional($a->fecha_cierre)->format('Y-m-d') ?? '—' }}</td>
                                <td><span class="badge b-{{ $a->estado }}">{{ $a->estado }}</span></td>
                                <td>
                                    <form method="POST" action="{{ route('admin.calidad.controles.acciones.update', $a) }}" style="display:flex;gap:.3rem;">
                                        @csrf @method('PATCH')
                                        <select name="estado" class="form-control" style="padding:.2rem;font-size:.7rem;">
                                            @foreach(['ABIERTA','EN_PROCESO','CERRADA'] as $st)
                                                <option value="{{ $st }}" @selected($a->estado===$st)>{{ $st }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-mini btn-primary">OK</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif

                <form method="POST" action="{{ route('admin.calidad.controles.acciones.store', $control) }}" style="margin-top:.8rem;border-top:1px dashed #e2e8f0;padding-top:.8rem;">
                    @csrf
                    <div class="form-row">
                        <div class="form-group" style="grid-column:span 2;"><label>Descripción</label><input type="text" name="descripcion" required></div>
                        <div class="form-group"><label>Fecha compromiso</label><input type="date" name="fecha_compromiso"></div>
                        <div class="form-group" style="display:flex;align-items:flex-end;"><button class="btn btn-primary">+ Acción</button></div>
                    </div>
                </form>
            </div>

            <div class="card">
                <h3>Evidencias</h3>
                @if($control->evidencias->isEmpty())
                    <div style="color:#94a3b8;padding:.5rem 0;">Sin evidencias adjuntas.</div>
                @else
                    <ul style="list-style:none;padding:0;margin:0;">
                    @foreach($control->evidencias as $ev)
                        <li style="padding:.4rem 0;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;gap:.5rem;">
                            <a href="{{ asset('storage/'.$ev->ruta_archivo) }}" target="_blank">{{ $ev->nombre_archivo }}</a>
                            <span style="font-size:.7rem;color:#94a3b8;">{{ $ev->tipo_archivo }}</span>
                            <form method="POST" action="{{ route('admin.calidad.controles.evidencias.destroy', $ev) }}" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-mini btn-danger">Eliminar</button>
                            </form>
                        </li>
                    @endforeach
                    </ul>
                @endif
                <form method="POST" action="{{ route('admin.calidad.controles.evidencias.store', $control) }}" enctype="multipart/form-data" style="margin-top:.8rem;border-top:1px dashed #e2e8f0;padding-top:.8rem;">
                    @csrf
                    <div class="form-row">
                        <div class="form-group" style="grid-column:span 2;"><label>Archivo (max 10 MB)</label><input type="file" name="archivo" required></div>
                        <div class="form-group"><label>Observaciones</label><input type="text" name="observaciones"></div>
                        <div class="form-group" style="display:flex;align-items:flex-end;"><button class="btn btn-primary">+ Adjuntar</button></div>
                    </div>
                </form>
            </div>
        </div>

        <div>
            <div class="card">
                <h3>Cambiar resultado</h3>
                <form method="POST" action="{{ route('admin.calidad.controles.resultado', $control) }}">
                    @csrf @method('PATCH')
                    <div class="form-group"><label>Resultado</label>
                        <select name="resultado">
                            @foreach(\App\Models\ControlCalidad::RESULTADOS as $k => $v)
                                <option value="{{ $k }}" @selected($control->resultado===$k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group"><label>Observaciones</label><textarea name="observaciones" rows="2">{{ $control->observaciones }}</textarea></div>
                    <button class="btn btn-primary" style="width:100%;">Actualizar</button>
                    <small style="display:block;margin-top:.5rem;color:#64748b;">El cambio se propaga al estado del lote vinculado (Aprobado→Liberado, Condicional→Cuarentena, Rechazado→Bloqueado).</small>
                </form>
            </div>

            @if($det && $det->inventario_lote_id)
            <div class="card">
                <h3>Trazabilidad del lote</h3>
                <a href="{{ route('admin.calidad.controles.trazabilidad', $det->inventario_lote_id) }}" class="btn btn-secondary" style="width:100%;justify-content:center;">Ver trazabilidad completa</a>
            </div>
            @endif

            <div class="card">
                <h3>Eliminar control</h3>
                <form method="POST" action="{{ route('admin.calidad.controles.destroy', $control) }}" onsubmit="return confirm('¿Eliminar definitivamente este control?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger" style="width:100%;">Eliminar control</button>
                </form>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
