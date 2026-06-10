<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ViaAdministracion extends Model
{
    use HasFactory;

    protected $table = 'vias_administracion';

    public const TIPOS = [
        'PARENTERAL'    => 'Parenteral',
        'ENTERAL'       => 'Enteral',
        'TOPICA'        => 'Tópica',
        'RESPIRATORIA'  => 'Respiratoria',
        'OFTALMICA'     => 'Oftálmica',
        'OTICA'         => 'Ótica',
        'NASAL'         => 'Nasal',
        'RECTAL'        => 'Rectal',
        'VAGINAL'       => 'Vaginal',
    ];

    public const RIESGOS = [
        'BAJO'    => 'Bajo',
        'MEDIO'   => 'Medio',
        'ALTO'    => 'Alto',
        'CRITICO' => 'Crítico',
    ];

    protected $fillable = [
        'codigo',
        'nombre',
        'nombre_corto',
        'descripcion',
        'tipo',
        'esteril_requerido',
        'requiere_bomba_infusion',
        'requiere_filtro',
        'permite_bolo',
        'permite_infusion_continua',
        'velocidad_min_ml_h',
        'velocidad_max_ml_h',
        'osmolaridad_max',
        'fotosensible',
        'requiere_monitorizacion',
        'riesgo_clinico',
        'color_identificacion',
        'icono',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'esteril_requerido'         => 'boolean',
        'requiere_bomba_infusion'   => 'boolean',
        'requiere_filtro'           => 'boolean',
        'permite_bolo'              => 'boolean',
        'permite_infusion_continua' => 'boolean',
        'fotosensible'              => 'boolean',
        'requiere_monitorizacion'   => 'boolean',
        'estado'                    => 'boolean',
        'velocidad_min_ml_h'        => 'decimal:2',
        'velocidad_max_ml_h'        => 'decimal:2',
        'osmolaridad_max'           => 'decimal:2',
    ];

    public function presentaciones()
    {
        return $this->hasMany(Presentacion::class, 'via_administracion_id');
    }

    public function getTipoLabelAttribute(): string
    {
        // Prioriza el catálogo dinámico (tabla TipoAdministracion); si no, la
        // constante legada; y como último recurso, un guion.
        return TipoAdministracion::mapaCodigoDetalle()[$this->tipo]
            ?? (self::TIPOS[$this->tipo] ?? '—');
    }

    public function getRiesgoLabelAttribute(): string
    {
        return self::RIESGOS[$this->riesgo_clinico] ?? '—';
    }
}
