<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidenteDetalle extends Model
{
    protected $table = 'incidentes_detalle';

    protected $fillable = [
        'incidente_id','causa_raiz','impacto','conclusion',
        'responsable_investigacion_id','fecha_cierre',
    ];

    protected $casts = [
        'fecha_cierre' => 'datetime',
    ];

    public function incidente(): BelongsTo
    {
        return $this->belongsTo(Incidente::class, 'incidente_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_investigacion_id');
    }
}
