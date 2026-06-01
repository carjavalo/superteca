<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    use HasFactory;

    protected $table = 'unidades_medida';

    public const TIPOS = [
        'PESO'          => 'Peso',
        'VOLUMEN'       => 'Volumen',
        'CONCENTRACION' => 'Concentración',
        'TEMPERATURA'   => 'Temperatura',
        'TIEMPO'        => 'Tiempo',
        'VELOCIDAD'     => 'Velocidad',
        'CANTIDAD'      => 'Cantidad',
        'SUPERFICIE'    => 'Superficie',
        'OTRO'          => 'Otro',
    ];

    public const COLOR_TIPO = [
        'PESO'          => '#3b82f6',
        'VOLUMEN'       => '#10b981',
        'CONCENTRACION' => '#a855f7',
        'TEMPERATURA'   => '#ef4444',
        'TIEMPO'        => '#f59e0b',
        'VELOCIDAD'     => '#06b6d4',
        'CANTIDAD'      => '#6b7280',
        'SUPERFICIE'    => '#84cc16',
        'OTRO'          => '#94a3b8',
    ];

    public const ICONO_TIPO = [
        'PESO'          => '⚖️',
        'VOLUMEN'       => '🧪',
        'CONCENTRACION' => '🧬',
        'TEMPERATURA'   => '🌡',
        'TIEMPO'        => '⏱',
        'VELOCIDAD'     => '💨',
        'CANTIDAD'      => '🔢',
        'SUPERFICIE'    => '📐',
        'OTRO'          => '🔧',
    ];

    protected $fillable = [
        'codigo',
        'nombre',
        'abreviatura',
        'tipo',
        'unidad_base_id',
        'factor_conversion',
        'simbolo',
        'precision_decimal',
        'permite_fracciones',
        'activa_calculos',
        'color_identificacion',
        'icono',
        'observaciones',
        'estado',
    ];

    protected $casts = [
        'factor_conversion'  => 'decimal:8',
        'precision_decimal'  => 'integer',
        'permite_fracciones' => 'boolean',
        'activa_calculos'    => 'boolean',
        'estado'             => 'boolean',
    ];

    public function unidadBase()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_base_id');
    }

    public function derivadas()
    {
        return $this->hasMany(UnidadMedida::class, 'unidad_base_id');
    }

    public function presentaciones()
    {
        return $this->hasMany(Presentacion::class, 'unidad_medida_id');
    }

    public function lotes()
    {
        return $this->hasMany(InventarioLote::class, 'unidad_medida_id');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? '—';
    }

    public function getColorTipoAttribute(): string
    {
        return $this->color_identificacion ?: (self::COLOR_TIPO[$this->tipo] ?? '#94a3b8');
    }

    public function getIconoFinalAttribute(): string
    {
        return $this->icono ?: (self::ICONO_TIPO[$this->tipo] ?? '🔧');
    }

    public function convertirA(UnidadMedida $destino, float $valor): ?float
    {
        if ($this->tipo !== $destino->tipo) return null;
        $base = $valor * (float) $this->factor_conversion;
        return $base / (float) $destino->factor_conversion;
    }
}
