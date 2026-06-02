<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MezclaProductoFinal extends Model
{
    protected $table = 'mezclas_producto_final';

    protected $fillable = [
        'mezcla_id','lote_produccion','fecha_produccion','fecha_vencimiento',
        'volumen_final','unidad_volumen_id','cantidad_unidades','observaciones'
    ];

    protected $casts = [
        'fecha_produccion' => 'datetime',
        'fecha_vencimiento' => 'datetime',
    ];

    public function mezcla() { return $this->belongsTo(Mezcla::class); }
}
