<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidenteAccion extends Model
{
    protected $table = 'incidentes_acciones';

    protected $fillable = [
        'incidente_id','tipo_accion','descripcion','responsable_id',
        'fecha_compromiso','fecha_ejecucion','estado',
    ];

    protected $casts = [
        'fecha_compromiso' => 'date',
        'fecha_ejecucion'  => 'date',
    ];

    public function incidente(): BelongsTo
    {
        return $this->belongsTo(Incidente::class, 'incidente_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
