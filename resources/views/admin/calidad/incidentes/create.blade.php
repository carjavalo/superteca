<x-app-layout>
@include('admin.calidad.incidentes._styles')

<div style="padding:1.5rem;">
    <div class="page-header">
        <div>
            <h1>Reportar Incidente</h1>
            <p>Registra un evento que afecta calidad, inventario, producción, dispensación o paciente</p>
        </div>
        <a href="{{ route('admin.calidad.incidentes.index') }}" class="btn" style="background:#fff;color:var(--inc);">← Volver</a>
    </div>

    @include('admin.calidad.incidentes._tabs')

    @if($errors->any())
        <div class="card" style="background:#fee2e2;color:#991b1b;">
            <strong>Revisa los datos:</strong>
            <ul style="margin:.4rem 0 0 1rem;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.calidad.incidentes.store') }}">
        @csrf
        <div class="card">
            <h3>1. Datos del incidente</h3>
            <div class="form-grid">
                <div>
                    <label>Fecha y hora *</label>
                    <input type="datetime-local" name="fecha_incidente" value="{{ old('fecha_incidente', now()->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div>
                    <label>Tipo *</label>
                    <select name="tipo_incidente" required>
                        @foreach(\App\Models\Incidente::TIPOS as $k=>$v)
                            <option value="{{ $k }}" @selected(old('tipo_incidente')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Clasificación *</label>
                    <select name="clasificacion" required>
                        @foreach(\App\Models\Incidente::CLASIFICACIONES as $k=>$v)
                            <option value="{{ $k }}" @selected(old('clasificacion')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label>Severidad *</label>
                    <select name="severidad" required>
                        @foreach(\App\Models\Incidente::SEVERIDADES as $k=>$v)
                            <option value="{{ $k }}" @selected(old('severidad', 'MEDIA')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="full">
                    <label>Descripción del evento *</label>
                    <textarea name="descripcion" rows="3" required placeholder="¿Qué ocurrió, dónde, cuándo, cómo se detectó?">{{ old('descripcion') }}</textarea>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>2. Lotes afectados (opcional)</h3>
            <p style="font-size:.8rem;color:#64748b;margin-top:-.5rem;">Selecciona los lotes que deben quedar trazados al incidente. Puedes bloquearlos automáticamente.</p>
            <div class="form-grid">
                <div class="full">
                    <label>Lotes</label>
                    <select name="lotes[]" multiple size="8" style="height:auto;">
                        @foreach($lotes as $l)
                            <option value="{{ $l->id }}">
                                {{ $l->lote }} · {{ optional($l->medicamento)->nombre ?? 'N/D' }}
                                @if(!is_null($l->cantidad_disponible)) · stock {{ $l->cantidad_disponible }} @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="full">
                    <label>Observaciones de afectación</label>
                    <textarea name="observaciones_afect" rows="2" placeholder="Cómo afectó el incidente a estos lotes">{{ old('observaciones_afect') }}</textarea>
                </div>
                <div class="full">
                    <label style="display:flex;align-items:center;gap:.5rem;font-weight:600;">
                        <input type="checkbox" name="bloquear_lotes" value="1" style="width:auto;">
                        Bloquear automáticamente los lotes seleccionados (estado_calidad → BLOQUEADO)
                    </label>
                </div>
            </div>
        </div>

        <div class="card" style="display:flex;justify-content:flex-end;gap:.5rem;">
            <a href="{{ route('admin.calidad.incidentes.index') }}" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-primary" type="submit">Registrar Incidente</button>
        </div>
    </form>
</div>
</x-app-layout>
