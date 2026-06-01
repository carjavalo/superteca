<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoInventario extends Model
{
    use HasFactory;

    protected $table = 'movimientos_inventario';

    public $timestamps = false;

    protected $fillable = [
        'tipo_movimiento','referencia_tipo','referencia_id','inventario_lote_id',
        'cantidad','stock_anterior','stock_nuevo','fecha_movimiento','usuario_id','observacion',
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
        'cantidad'         => 'decimal:2',
        'stock_anterior'   => 'decimal:2',
        'stock_nuevo'      => 'decimal:2',
    ];

    public function lote()    { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
    public function usuario() { return $this->belongsTo(User::class, 'usuario_id'); }
}
