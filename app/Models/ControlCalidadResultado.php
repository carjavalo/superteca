<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlCalidadResultado extends Model
{
    protected $table = 'controles_calidad_resultados';

    protected $fillable = [
        'control_id','parametro_id','valor_obtenido','cumple','observaciones',
    ];

    protected $casts = [
        'cumple' => 'boolean',
    ];

    public function control(): BelongsTo
    {
        return $this->belongsTo(ControlCalidad::class, 'control_id');
    }

    public function parametro(): BelongsTo
    {
        return $this->belongsTo(ControlCalidadParametro::class, 'parametro_id');
    }
}
