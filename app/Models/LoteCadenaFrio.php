<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoteCadenaFrio extends Model
{
    protected $table = 'lotes_cadena_frio';

    protected $fillable = [
        'inventario_lote_id','equipo_id','fecha_ingreso','fecha_salida','observaciones',
    ];

    protected $casts = [
        'fecha_ingreso' => 'datetime',
        'fecha_salida'  => 'datetime',
    ];

    public function inventarioLote() { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
    public function equipo()         { return $this->belongsTo(EquipoCadenaFrio::class, 'equipo_id'); }
}
