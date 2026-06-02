<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReempaqueProductoFinal extends Model
{
    protected $table = 'reempaques_producto_final';

    protected $fillable = [
        'reempaque_id','medicamento_id','presentacion_id','lote_reempaque','lote_origen',
        'fecha_reempaque','fecha_vencimiento','cantidad_generada','unidad_medida_id',
        'costo_unitario','codigo_barras','inventario_lote_generado_id','observaciones',
    ];

    protected $casts = [
        'fecha_reempaque' => 'datetime',
        'fecha_vencimiento' => 'date',
        'cantidad_generada' => 'decimal:2',
        'costo_unitario' => 'decimal:4',
    ];

    public function reempaque() { return $this->belongsTo(Reempaque::class); }
    public function medicamento() { return $this->belongsTo(Medicamento::class); }
    public function presentacion() { return $this->belongsTo(Presentacion::class); }
    public function unidadMedida() { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }
    public function inventarioLoteGenerado() { return $this->belongsTo(InventarioLote::class, 'inventario_lote_generado_id'); }
}
