<x-app-layout>
<style>
    :root { --inst:#2e3a75; }
    .page-header { background: linear-gradient(135deg, #14b8a6 0%, var(--inst) 100%); color:#fff; padding:1.4rem 1.8rem; border-radius:12px; margin-bottom:1.2rem; }
    .page-header h1 { margin:0; font-size:1.3rem; }
    .card-box { background:#fff; padding:1.2rem 1.4rem; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,.07); margin-bottom:1rem; }
    .grid { display:grid; gap:.8rem; }
    .grid-3 { grid-template-columns:repeat(3,1fr); }
    .grid-4 { grid-template-columns:repeat(4,1fr); }
    @media (max-width:800px) { .grid-3,.grid-4 { grid-template-columns:1fr; } }
    label { font-size:.72rem; color:#475569; font-weight:600; display:block; margin-bottom:.25rem; }
    input,select,textarea { width:100%; padding:.55rem .7rem; border:1.5px solid #e2e8f0; border-radius:8px; font-size:.85rem; box-sizing:border-box; }
    .btn { padding:.55rem 1.1rem; border-radius:8px; text-decoration:none; font-size:.85rem; font-weight:600; border:none; cursor:pointer; }
    .btn-primary { background:#14b8a6; color:#fff; }
    .btn-secondary { background:#e2e8f0; color:#1e293b; }
</style>

<div class="page-header">
    <h1>Registrar Paciente</h1>
</div>

@if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:.7rem 1rem;border-radius:8px;margin-bottom:1rem">
        <ul style="margin:0 0 0 1.2rem">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif

<form method="POST" action="{{ route('admin.dispensacion.pacientes.store') }}">
    @csrf
    <div class="card-box">
        <h2 style="margin:0 0 1rem 0; font-size:1rem">Identificación</h2>
        <div class="grid grid-4">
            <div>
                <label>Tipo doc. *</label>
                <select name="tipo_documento" required>
                    @foreach(['CC'=>'Cédula','TI'=>'T. Identidad','RC'=>'R. Civil','CE'=>'Extranjería','PA'=>'Pasaporte'] as $k=>$v)
                        <option value="{{ $k }}" {{ old('tipo_documento','CC')==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <div><label>Documento *</label><input type="text" name="documento" required value="{{ old('documento') }}"></div>
            <div><label>Nombres *</label><input type="text" name="nombres" required value="{{ old('nombres') }}"></div>
            <div><label>Apellidos *</label><input type="text" name="apellidos" required value="{{ old('apellidos') }}"></div>
        </div>
    </div>

    <div class="card-box">
        <h2 style="margin:0 0 1rem 0; font-size:1rem">Datos clínicos</h2>
        <div class="grid grid-4">
            <div><label>Fecha nacimiento</label><input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento') }}"></div>
            <div><label>Sexo</label>
                <select name="sexo">
                    <option value="">—</option>
                    <option value="M" {{ old('sexo')=='M'?'selected':'' }}>Masculino</option>
                    <option value="F" {{ old('sexo')=='F'?'selected':'' }}>Femenino</option>
                    <option value="O" {{ old('sexo')=='O'?'selected':'' }}>Otro</option>
                </select>
            </div>
            <div><label>Peso (kg)</label><input type="number" step="0.01" name="peso" value="{{ old('peso') }}"></div>
            <div><label>Talla (cm)</label><input type="number" step="0.01" name="talla" value="{{ old('talla') }}"></div>
        </div>
        <div class="grid grid-3" style="margin-top:.7rem">
            <div><label>EPS</label><input type="text" name="eps" value="{{ old('eps') }}"></div>
            <div><label>Servicio</label>
                <select name="servicio_id">
                    <option value="">—</option>
                    @foreach($servicios as $s)
                        <option value="{{ $s->id }}" {{ old('servicio_id')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div><label>Cama</label><input type="text" name="cama" value="{{ old('cama') }}"></div>
        </div>
        <div class="grid grid-3" style="margin-top:.7rem">
            <div><label>Fecha ingreso</label><input type="datetime-local" name="fecha_ingreso" value="{{ old('fecha_ingreso', now()->format('Y-m-d\TH:i')) }}"></div>
            <div><label>Estado clínico *</label>
                <select name="estado_clinico" required>
                    @foreach(\App\Models\Paciente::ESTADOS_CLINICOS as $k=>$v)
                        <option value="{{ $k }}" {{ old('estado_clinico','ACTIVO')==$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="card-box">
        <h2 style="margin:0 0 1rem 0; font-size:1rem">Contacto</h2>
        <div class="grid grid-3">
            <div><label>Teléfono</label><input type="text" name="telefono" value="{{ old('telefono') }}"></div>
            <div><label>Correo</label><input type="email" name="correo" value="{{ old('correo') }}"></div>
            <div><label>Dirección</label><input type="text" name="direccion" value="{{ old('direccion') }}"></div>
        </div>
        <div style="margin-top:.7rem">
            <label>Observaciones</label>
            <textarea name="observaciones" rows="2">{{ old('observaciones') }}</textarea>
        </div>
    </div>

    <div style="display:flex;gap:.7rem;justify-content:flex-end">
        <a href="{{ route('admin.dispensacion.pacientes.index') }}" class="btn btn-secondary">Cancelar</a>
        <button class="btn btn-primary">Registrar</button>
    </div>
</form>
</x-app-layout>
