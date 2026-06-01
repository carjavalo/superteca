<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleAjusteInventario extends Model
{
    use HasFactory;

    protected $table = 'detalle_ajustes_inventario';

    protected $fillable = [
        'ajuste_id',
        'inventario_lote_id',
        'medicamento_id',
        'lote',
        'fecha_vencimiento',
        'stock_sistema',
        'stock_fisico',
        'diferencia',
        'costo_unitario',
        'valor_ajuste',
        'observacion',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'stock_sistema'  => 'decimal:2',
        'stock_fisico'   => 'decimal:2',
        'diferencia'     => 'decimal:2',
        'costo_unitario' => 'decimal:2',
        'valor_ajuste'   => 'decimal:2',
    ];

    public function ajuste()
    {
        return $this->belongsTo(AjusteInventario::class, 'ajuste_id');
    }

    public function lote()
    {
        return $this->belongsTo(InventarioLote::class, 'inventario_lote_id');
    }

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class, 'medicamento_id');
    }
}
