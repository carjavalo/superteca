<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidenteAfectacion extends Model
{
    protected $table = 'incidentes_afectaciones';

    protected $fillable = [
        'incidente_id','inventario_lote_id','mezcla_id','preparacion_id',
        'reempaque_id','entrega_id','paciente_id','equipo_cadena_frio_id',
        'control_calidad_id','observaciones',
    ];

    public function incidente(): BelongsTo
    {
        return $this->belongsTo(Incidente::class, 'incidente_id');
    }

    public function inventarioLote(): BelongsTo
    {
        return $this->belongsTo(InventarioLote::class, 'inventario_lote_id');
    }

    public function equipoCadenaFrio(): BelongsTo
    {
        return $this->belongsTo(EquipoCadenaFrio::class, 'equipo_cadena_frio_id');
    }

    public function controlCalidad(): BelongsTo
    {
        return $this->belongsTo(ControlCalidad::class, 'control_calidad_id');
    }
}
