<x-app-layout>
@include('admin.calidad.controles._styles')

<div style="padding:1.5rem;">
    <div class="page-header">
        <div><h1>Nuevo Control de Calidad</h1><p>Checklist dinámico de parámetros parametrizados</p></div>
        <a href="{{ route('admin.calidad.controles.bandeja') }}" class="btn btn-secondary">← Volver</a>
    </div>

    @include('admin.calidad.controles._tabs')

    @if($errors->any())
        <div class="card" style="background:#fee2e2;color:#991b1b;">
            <ul style="margin:0;padding-left:1rem;">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Selector de tipo (recarga la página con parámetros propios del tipo) --}}
    <div class="card">
        <form method="GET">
            <div class="form-row">
                <div class="form-group">
                    <label>Tipo de control</label>
                    <select name="tipo" onchange="this.form.submit()">
                        @foreach(\App\Models\ControlCalidad::TIPOS as $k => $v)
                            <option value="{{ $k }}" @selected($tipoSel===$k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="display:flex;align-items:flex-end;">
                    <small style="color:#64748b;">Al cambiar el tipo se cargan los parámetros configurados en el catálogo.</small>
                </div>
            </div>
        </form>
    </div>

    <form method="POST" action="{{ route('admin.calidad.controles.store') }}">
        @csrf
        <input type="hidden" name="tipo_control" value="{{ $tipoSel }}">

        <div class="card">
            <h3>Datos generales</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Fecha y hora</label>
                    <input type="datetime-local" name="fecha_control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="form-group">
                    <label>Resultado</label>
                    <select name="resultado" required>
                        @foreach(\App\Models\ControlCalidad::RESULTADOS as $k => $v)
                            <option value="{{ $k }}" @selected($k==='PENDIENTE')>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Lote de inventario (opcional)</label>
                    <select name="inventario_lote_id">
                        <option value="">— Ninguno —</option>
                        @foreach($lotes as $lote)
                            <option value="{{ $lote->id }}">
                                {{ $lote->lote }} · {{ optional($lote->medicamento)->nombre }} (Estado: {{ $lote->estado_calidad ?? 'N/D' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Observaciones</label>
                <textarea name="observaciones" rows="2" placeholder="Notas generales del control"></textarea>
            </div>
        </div>

        <div class="card">
            <h3>Checklist dinámico ({{ $parametros->count() }} parámetros para "{{ \App\Models\ControlCalidad::TIPOS[$tipoSel] ?? $tipoSel }}")</h3>
            @if($parametros->isEmpty())
                <div style="color:#94a3b8;text-align:center;padding:1.2rem;">
                    No hay parámetros configurados para este tipo. Configúralos en
                    <a href="{{ route('admin.calidad.controles.parametros.index') }}">Parámetros</a>.
                </div>
            @else
                <table class="t">
                    <thead>
                        <tr>
                            <th>Parámetro</th><th>Unidad</th><th>Rango</th>
                            <th>Valor obtenido</th><th>Cumple</th><th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($parametros as $i => $p)
                        <tr>
                            <td>
                                <strong>{{ $p->nombre }}</strong>
                                @if($p->obligatorio) <span class="badge b-RECHAZADO">obligatorio</span>@endif
                                <input type="hidden" name="parametros[{{ $i }}][parametro_id]" value="{{ $p->id }}">
                            </td>
                            <td>{{ $p->unidad_medida ?? '—' }}</td>
                            <td>
                                @if(!is_null($p->valor_minimo) || !is_null($p->valor_maximo))
                                    {{ $p->valor_minimo ?? '—' }} a {{ $p->valor_maximo ?? '—' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td><input type="text" name="parametros[{{ $i }}][valor_obtenido]" placeholder="Valor"></td>
                            <td style="text-align:center;">
                                <input type="hidden" name="parametros[{{ $i }}][cumple]" value="0">
                                <input type="checkbox" name="parametros[{{ $i }}][cumple]" value="1" checked>
                            </td>
                            <td><input type="text" name="parametros[{{ $i }}][observaciones]" placeholder="—"></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div style="display:flex;gap:.5rem;justify-content:flex-end;">
            <a href="{{ route('admin.calidad.controles.bandeja') }}" class="btn btn-secondary">Cancelar</a>
            <button class="btn btn-primary">Guardar Control</button>
        </div>
    </form>
</div>
</x-app-layout>
