<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReempaqueDetalle extends Model
{
    protected $table = 'reempaques_detalle';

    protected $fillable = ['reempaque_id','insumo','cantidad','unidad_medida_id','costo_unitario','costo_total'];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo_unitario' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    public function reempaque() { return $this->belongsTo(Reempaque::class); }
    public function unidadMedida() { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }
}
