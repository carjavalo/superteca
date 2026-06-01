@php $color = $u->color_tipo; $icono = $u->icono_final; @endphp
<div class="u-card">
    <div class="accent" style="background:{{ $color }};"></div>
    <div class="u-head">
        <div class="u-icon" style="background:{{ $color }}1f; color:{{ $color }};">{{ $icono }}</div>
        <div class="u-title">
            <h4>{{ $u->nombre }} <span style="color:#6b7280;font-weight:600;">({{ $u->abreviatura }})</span></h4>
            <div class="sub"><strong>{{ $u->codigo }}</strong> · {{ $u->tipo_label }}</div>
        </div>
    </div>
    <div class="u-body">
        <div class="kv"><span>Símbolo</span><b>{{ $u->simbolo ?: '—' }}</b></div>
        <div class="kv"><span>Factor</span><b>{{ rtrim(rtrim(number_format((float)$u->factor_conversion, 8, '.', ''), '0'), '.') }}</b></div>
        <div class="kv"><span>Precisión</span><b>{{ $u->precision_decimal }} dec.</b></div>
        @if($u->unidadBase)
            <div class="kv"><span>Base</span><b>{{ $u->unidadBase->abreviatura }}</b></div>
        @else
            <div class="kv"><span>Base</span><b><span class="badge badge-base">UNIDAD BASE</span></b></div>
        @endif
    </div>
    <div class="u-foot">
        @if($u->estado)
            <span class="badge badge-active">● Activa</span>
        @else
            <span class="badge badge-inactive">● Inactiva</span>
        @endif
        <div style="display:flex; gap:6px;">
            <a href="{{ route('admin.unidades_medida.show', $u) }}" class="btn-icon btn-view" title="Ver">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z"/></svg>
            </a>
            <button type="button" class="btn-icon btn-edit" title="Editar" onclick='editUnidad(@json($u))'>
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
            </button>
            <form action="{{ route('admin.unidades_medida.destroy', $u) }}" method="POST" onsubmit="return confirm('¿Eliminar unidad?')" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="btn-icon btn-del" title="Eliminar">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/></svg>
                </button>
            </form>
        </div>
    </div>
</div>
