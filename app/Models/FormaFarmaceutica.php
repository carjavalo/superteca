<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormaFarmaceutica extends Model
{
    use HasFactory;

    protected $table = 'formas_farmaceuticas';

    public const TIPOS = [
        'SOLIDO'     => 'Sólido',
        'LIQUIDO'    => 'Líquido',
        'SEMISOLIDO' => 'Semisólido',
        'GASEOSO'    => 'Gaseoso',
        'ESTERIL'    => 'Estéril',
    ];

    public const RIESGOS = [
        'BAJO'  => 'Bajo',
        'MEDIO' => 'Medio',
        'ALTO'  => 'Alto',
    ];

    protected $fillable = [
        'codigo',
        'nombre',
        'nombre_corto',
        'descripcion',
        'tipo',
        'requiere_reconstitucion',
        'requiere_dilucion',
        'esteril',
        'multidosis',
        'reutilizable',
        'requiere_cadena_frio',
        'temperatura_min',
        'temperatura_max',
        'tiempo_estabilidad_horas',
        'permite_fraccionamiento',
        'riesgo_contaminacion',
        'color_identificacion',
        'icono',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'requiere_reconstitucion' => 'boolean',
        'requiere_dilucion'       => 'boolean',
        'esteril'                 => 'boolean',
        'multidosis'              => 'boolean',
        'reutilizable'            => 'boolean',
        'requiere_cadena_frio'    => 'boolean',
        'permite_fraccionamiento' => 'boolean',
        'estado'                  => 'boolean',
        'temperatura_min'         => 'decimal:2',
        'temperatura_max'         => 'decimal:2',
        'tiempo_estabilidad_horas' => 'integer',
    ];

    public function presentaciones()
    {
        return $this->hasMany(Presentacion::class, 'forma_farmaceutica_id');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? '—';
    }

    public function getRiesgoLabelAttribute(): string
    {
        return self::RIESGOS[$this->riesgo_contaminacion] ?? '—';
    }
}
