<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetalleTraslado extends Model
{
    use HasFactory;

    protected $table = 'detalle_traslados';

    protected $fillable = [
        'traslado_id', 'inventario_lote_id', 'medicamento_id', 'presentacion_id',
        'lote', 'fecha_vencimiento', 'cantidad', 'costo_unitario', 'observacion',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'cantidad'          => 'decimal:2',
        'costo_unitario'    => 'decimal:2',
    ];

    public function traslado()     { return $this->belongsTo(Traslado::class); }
    public function lote()         { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
    public function medicamento()  { return $this->belongsTo(Medicamento::class); }
    public function presentacion() { return $this->belongsTo(Presentacion::class); }
}
