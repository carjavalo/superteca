<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidenteEvidencia extends Model
{
    protected $table = 'incidentes_evidencias';

    protected $fillable = [
        'incidente_id','nombre_archivo','ruta_archivo','tipo_archivo','observaciones',
    ];

    public function incidente(): BelongsTo
    {
        return $this->belongsTo(Incidente::class, 'incidente_id');
    }
}
