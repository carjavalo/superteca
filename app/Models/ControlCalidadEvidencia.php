<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlCalidadEvidencia extends Model
{
    protected $table = 'controles_calidad_evidencias';

    protected $fillable = [
        'control_id','nombre_archivo','ruta_archivo','tipo_archivo','observaciones',
    ];

    public function control(): BelongsTo
    {
        return $this->belongsTo(ControlCalidad::class, 'control_id');
    }
}
