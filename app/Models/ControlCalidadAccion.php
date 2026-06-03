<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlCalidadAccion extends Model
{
    protected $table = 'controles_calidad_acciones';

    protected $fillable = [
        'control_id','descripcion','responsable_id',
        'fecha_compromiso','fecha_cierre','estado',
    ];

    protected $casts = [
        'fecha_compromiso' => 'date',
        'fecha_cierre'     => 'date',
    ];

    public function control(): BelongsTo
    {
        return $this->belongsTo(ControlCalidad::class, 'control_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }
}
