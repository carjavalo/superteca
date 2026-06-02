<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispensacionEntregaLote extends Model
{
    protected $table = 'dispensacion_entregas_lotes';

    protected $fillable = [
        'entrega_detalle_id','inventario_lote_id','lote','fecha_vencimiento',
        'cantidad_entregada','costo_unitario',
    ];

    protected $casts = [
        'fecha_vencimiento'   => 'date',
        'cantidad_entregada'  => 'decimal:2',
        'costo_unitario'      => 'decimal:4',
    ];

    public function detalle() { return $this->belongsTo(DispensacionEntregaDetalle::class, 'entrega_detalle_id'); }
    public function inventarioLote() { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
}
