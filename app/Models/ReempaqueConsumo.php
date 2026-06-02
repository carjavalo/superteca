<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReempaqueConsumo extends Model
{
    protected $table = 'reempaques_consumo';

    protected $fillable = [
        'reempaque_id','inventario_lote_id','medicamento_id','lote_origen','fecha_vencimiento',
        'cantidad_consumida','unidad_medida_id','costo_unitario','costo_total',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'cantidad_consumida' => 'decimal:2',
        'costo_unitario' => 'decimal:4',
        'costo_total' => 'decimal:2',
    ];

    public function reempaque() { return $this->belongsTo(Reempaque::class); }
    public function inventarioLote() { return $this->belongsTo(InventarioLote::class); }
    public function medicamento() { return $this->belongsTo(Medicamento::class); }
    public function unidadMedida() { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }
}
