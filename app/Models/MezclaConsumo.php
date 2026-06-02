<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MezclaConsumo extends Model
{
    protected $table = 'mezclas_consumo';

    protected $fillable = [
        'mezcla_id','inventario_lote_id','medicamento_id','presentacion_id',
        'lote','fecha_vencimiento','cantidad_consumida','unidad_medida_id',
        'costo_unitario','costo_total'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'cantidad_consumida' => 'decimal:4',
        'costo_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    public function mezcla()      { return $this->belongsTo(Mezcla::class); }
    public function lote()        { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
    public function medicamento() { return $this->belongsTo(Medicamento::class); }
}
