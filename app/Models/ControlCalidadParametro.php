<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ControlCalidadParametro extends Model
{
    protected $table = 'controles_calidad_parametros';

    protected $fillable = [
        'nombre','tipo_control','unidad_medida',
        'valor_minimo','valor_maximo','obligatorio','estado',
    ];

    protected $casts = [
        'valor_minimo' => 'decimal:4',
        'valor_maximo' => 'decimal:4',
        'obligatorio'  => 'boolean',
        'estado'       => 'boolean',
    ];
}
