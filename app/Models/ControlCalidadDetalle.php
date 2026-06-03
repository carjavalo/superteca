<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlCalidadDetalle extends Model
{
    protected $table = 'controles_calidad_detalle';

    protected $fillable = [
        'control_id','inventario_lote_id','mezcla_id','preparacion_id',
        'reempaque_id','entrega_id','entrada_id',
    ];

    public function control(): BelongsTo
    {
        return $this->belongsTo(ControlCalidad::class, 'control_id');
    }

    public function inventarioLote(): BelongsTo
    {
        return $this->belongsTo(InventarioLote::class, 'inventario_lote_id');
    }
}
