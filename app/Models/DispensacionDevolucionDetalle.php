<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispensacionDevolucionDetalle extends Model
{
    protected $table = 'dispensacion_devoluciones_detalle';

    protected $fillable = [
        'devolucion_id','entrega_lote_id','inventario_lote_id','cantidad',
        'costo_unitario','costo_total','reingresa_stock','observaciones',
    ];

    protected $casts = [
        'cantidad'        => 'decimal:2',
        'costo_unitario'  => 'decimal:4',
        'costo_total'     => 'decimal:2',
        'reingresa_stock' => 'boolean',
    ];

    public function devolucion()     { return $this->belongsTo(DispensacionDevolucion::class, 'devolucion_id'); }
    public function entregaLote()    { return $this->belongsTo(DispensacionEntregaLote::class, 'entrega_lote_id'); }
    public function inventarioLote() { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
}
