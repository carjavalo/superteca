<x-app-layout>
@include('admin.calidad.incidentes._styles')

<div style="padding:1.5rem;">
    <div class="page-header">
        <div>
            <h1>{{ $incidente->codigo }}</h1>
            <p>{{ \App\Models\Incidente::TIPOS[$incidente->tipo_incidente] ?? '' }} · {{ \App\Models\Incidente::CLASIFICACIONES[$incidente->clasificacion] ?? '' }} · Severidad {{ $incidente->severidad }}</p>
        </div>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <span class="badge b-{{ $incidente->estado }}" style="font-size:.85rem;padding:.4rem .8rem;">{{ $incidente->estado }}</span>
            <a href="{{ route('admin.calidad.incidentes.bandeja') }}" class="btn" style="background:#fff;color:var(--inc);">← Volver</a>
        </div>
    </div>

    @if(session('success'))<div class="card" style="background:#dcfce7;color:#166534;">{{ session('success') }}</div>@endif
    @if($errors->any())
        <div class="card" style="background:#fee2e2;color:#991b1b;">
            @foreach($errors->all() as $e) <div>{{ $e }}</div> @endforeach
        </div>
    @endif

    {{-- Datos del incidente --}}
    <div class="card">
        <h3>Datos del incidente</h3>
        <table class="t">
            <tr><th style="width:25%;">Fecha del evento</th><td>{{ $incidente->fecha_incidente->format('Y-m-d H:i') }}</td></tr>
            <tr><th>Reportado por</th><td>{{ optional($incidente->usuarioReporta)->name ?? '—' }} · {{ optional($incidente->fecha_reporte)->format('Y-m-d H:i') }}</td></tr>
            <tr><th>Tipo</th><td>{{ \App\Models\Incidente::TIPOS[$incidente->tipo_incidente] ?? $incidente->tipo_incidente }}</td></tr>
            <tr><th>Clasificación</th><td>{{ \App\Models\Incidente::CLASIFICACIONES[$incidente->clasificacion] ?? $incidente->clasificacion }}</td></tr>
            <tr><th>Severidad</th><td><span class="badge b-{{ $incidente->severidad }}">{{ $incidente->severidad }}</span></td></tr>
            <tr><th>Descripción</th><td>{{ $incidente->descripcion }}</td></tr>
        </table>
    </div>

    {{-- Cambio de estado --}}
    <div class="card">
        <h3>Flujo del incidente</h3>
        <form method="POST" action="{{ route('admin.calidad.incidentes.estado', $incidente) }}" class="form-grid">
            @csrf @method('PATCH')
            <div>
                <label>Cambiar estado</label>
                <select name="estado" required>
                    @foreach(\App\Models\Incidente::ESTADOS as $k=>$v)
                        <option value="{{ $k }}" @selected($incidente->estado===$k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div class="full">
                <label>Comentario (opcional)</label>
                <input type="text" name="comentario" placeholder="Nota para el cambio de estado">
            </div>
            <div><button class="btn btn-primary" type="submit">Actualizar estado</button></div>
        </form>
    </div>

    {{-- Investigación --}}
    <div class="card">
        <h3>Investigación / Análisis Causa Raíz</h3>
        <form method="POST" action="{{ route('admin.calidad.incidentes.detalle', $incidente) }}" class="form-grid">
            @csrf @method('PATCH')
            <div class="full">
                <label>Causa raíz</label>
                <textarea name="causa_raiz" rows="3">{{ optional($incidente->detalle)->causa_raiz }}</textarea>
            </div>
            <div class="full">
                <label>Impacto</label>
                <textarea name="impacto" rows="2">{{ optional($incidente->detalle)->impacto }}</textarea>
            </div>
            <div class="full">
                <label>Conclusión</label>
                <textarea name="conclusion" rows="2">{{ optional($incidente->detalle)->conclusion }}</textarea>
            </div>
            <div><button class="btn btn-primary" type="submit">Guardar investigación</button></div>
        </form>
        @if($incidente->detalle && $incidente->detalle->fecha_cierre)
            <p style="margin-top:.5rem;color:#16a34a;font-weight:600;">Cerrado el {{ $incidente->detalle->fecha_cierre->format('Y-m-d H:i') }}</p>
        @endif
    </div>

    {{-- Afectaciones --}}
    <div class="card">
        <h3>Afectaciones (Trazabilidad 360°)</h3>
        @if($incidente->afectaciones->isEmpty())
            <p style="color:#94a3b8;font-size:.85rem;">Sin afectaciones registradas.</p>
        @else
            <table class="t">
                <thead><tr><th>Lote</th><th>Medicamento</th><th>Estado lote</th><th>Bloqueo</th><th>Observaciones</th><th></th></tr></thead>
                <tbody>
                @foreach($incidente->afectaciones as $a)
                    <tr>
                        <td>{{ optional($a->inventarioLote)->lote ?? '—' }}</td>
                        <td>{{ optional(optional($a->inventarioLote)->medicamento)->nombre ?? '—' }}</td>
                        <td>
                            @if($a->inventarioLote)
                                <span class="badge b-{{ $a->inventarioLote->estado_calidad }}">{{ $a->inventarioLote->estado_calidad ?? 'N/D' }}</span>
                            @endif
                        </td>
                        <td>
                            @if($a->inventarioLote && $a->inventarioLote->bloqueado_incidente)
                                <span class="badge b-CRITICA">BLOQUEADO</span>
                            @else
                                <span class="badge b-BAJA">LIBRE</span>
                            @endif
                        </td>
                        <td>{{ $a->observaciones }}</td>
                        <td>
                            @if($a->inventarioLote)
                                <form method="POST" action="{{ route('admin.calidad.incidentes.bloquearLote', $incidente) }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="inventario_lote_id" value="{{ $a->inventario_lote_id }}">
                                    <input type="hidden" name="bloquear" value="{{ $a->inventarioLote->bloqueado_incidente ? 0 : 1 }}">
                                    <button class="btn btn-mini {{ $a->inventarioLote->bloqueado_incidente ? 'btn-success' : 'btn-danger' }}" type="submit">
                                        {{ $a->inventarioLote->bloqueado_incidente ? 'Liberar' : 'Bloquear' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Acciones CAPA --}}
    <div class="card">
        <h3>Acciones Correctivas y Preventivas (CAPA)</h3>
        <form method="POST" action="{{ route('admin.calidad.incidentes.acciones.store', $incidente) }}" class="form-grid">
            @csrf
            <div>
                <label>Tipo</label>
                <select name="tipo_accion" required>
                    <option value="CORRECTIVA">Correctiva</option>
                    <option value="PREVENTIVA">Preventiva</option>
                </select>
            </div>
            <div>
                <label>Fecha compromiso</label>
                <input type="date" name="fecha_compromiso">
            </div>
            <div class="full">
                <label>Descripción</label>
                <textarea name="descripcion" rows="2" required></textarea>
            </div>
            <div><button class="btn btn-primary" type="submit">Agregar acción</button></div>
        </form>

        @if($incidente->acciones->isNotEmpty())
            <table class="t" style="margin-top:1rem;">
                <thead><tr><th>Tipo</th><th>Descripción</th><th>Compromiso</th><th>Ejecución</th><th>Estado</th><th></th></tr></thead>
                <tbody>
                @foreach($incidente->acciones as $ac)
                    <tr>
                        <td><span class="badge b-{{ $ac->tipo_accion }}">{{ $ac->tipo_accion }}</span></td>
                        <td>{{ $ac->descripcion }}</td>
                        <td>{{ optional($ac->fecha_compromiso)->format('Y-m-d') ?? '—' }}</td>
                        <td>{{ optional($ac->fecha_ejecucion)->format('Y-m-d') ?? '—' }}</td>
                        <td><span class="badge b-{{ $ac->estado }}">{{ $ac->estado }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('admin.calidad.incidentes.acciones.update', $ac) }}" style="display:flex;gap:.3rem;">
                                @csrf @method('PATCH')
                                <select name="estado">
                                    <option value="PENDIENTE"  @selected($ac->estado==='PENDIENTE')>Pendiente</option>
                                    <option value="EN_PROCESO" @selected($ac->estado==='EN_PROCESO')>En proceso</option>
                                    <option value="CERRADA"    @selected($ac->estado==='CERRADA')>Cerrada</option>
                                </select>
                                <button class="btn btn-mini btn-primary" type="submit">OK</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Evidencias --}}
    <div class="card">
        <h3>Evidencias</h3>
        <form method="POST" action="{{ route('admin.calidad.incidentes.evidencias.store', $incidente) }}" enctype="multipart/form-data" class="form-grid">
            @csrf
            <div><label>Archivo *</label><input type="file" name="archivo" required></div>
            <div class="full"><label>Observaciones</label><input type="text" name="observaciones"></div>
            <div><button class="btn btn-primary" type="submit">Subir evidencia</button></div>
        </form>
        @if($incidente->evidencias->isNotEmpty())
            <table class="t" style="margin-top:1rem;">
                <thead><tr><th>Archivo</th><th>Tipo</th><th>Observaciones</th><th>Fecha</th><th></th></tr></thead>
                <tbody>
                @foreach($incidente->evidencias as $ev)
                    <tr>
                        <td><a href="{{ asset('storage/'.$ev->ruta_archivo) }}" target="_blank">{{ $ev->nombre_archivo }}</a></td>
                        <td>{{ $ev->tipo_archivo }}</td>
                        <td>{{ $ev->observaciones }}</td>
                        <td>{{ $ev->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.calidad.incidentes.evidencias.destroy', $ev) }}" onsubmit="return confirm('¿Eliminar evidencia?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-mini btn-danger" type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    {{-- Bitácora / Línea de tiempo --}}
    <div class="card">
        <h3>Línea de tiempo</h3>
        <form method="POST" action="{{ route('admin.calidad.incidentes.seguimiento', $incidente) }}" class="form-grid" style="margin-bottom:1rem;">
            @csrf
            <div class="full"><label>Nuevo comentario</label><textarea name="comentario" rows="2" required></textarea></div>
            <div><button class="btn btn-primary" type="submit">Agregar</button></div>
        </form>
        <div class="timeline">
            @forelse($incidente->seguimientos->sortByDesc('fecha') as $s)
                <div class="tl-item">
                    <div class="tl-meta">{{ $s->fecha->format('Y-m-d H:i') }} · {{ optional($s->usuario)->name ?? '—' }}</div>
                    <div class="tl-text">{{ $s->comentario }}</div>
                </div>
            @empty
                <p style="color:#94a3b8;font-size:.85rem;">Sin actividad registrada.</p>
            @endforelse
        </div>
    </div>

    {{-- Eliminar --}}
    <div class="card" style="display:flex;justify-content:flex-end;">
        <form method="POST" action="{{ route('admin.calidad.incidentes.destroy', $incidente) }}" onsubmit="return confirm('¿Eliminar el incidente y todas sus relaciones?')">
            @csrf @method('DELETE')
            <button class="btn btn-danger" type="submit">Eliminar incidente</button>
        </form>
    </div>
</div>
</x-app-layout>
