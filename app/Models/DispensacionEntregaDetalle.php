<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispensacionEntregaDetalle extends Model
{
    protected $table = 'dispensacion_entregas_detalle';

    protected $fillable = [
        'entrega_id','medicamento_id','presentacion_id','preparacion_id','mezcla_id',
        'cantidad','unidad_medida_id','costo_unitario','costo_total','observaciones',
    ];

    protected $casts = [
        'cantidad'       => 'decimal:2',
        'costo_unitario' => 'decimal:4',
        'costo_total'    => 'decimal:2',
    ];

    public function entrega()      { return $this->belongsTo(DispensacionEntrega::class, 'entrega_id'); }
    public function medicamento()  { return $this->belongsTo(Medicamento::class); }
    public function presentacion() { return $this->belongsTo(Presentacion::class); }
    public function preparacion()  { return $this->belongsTo(Preparacion::class); }
    public function mezcla()       { return $this->belongsTo(Mezcla::class); }
    public function unidadMedida() { return $this->belongsTo(UnidadMedida::class); }
    public function lotes()        { return $this->hasMany(DispensacionEntregaLote::class, 'entrega_detalle_id'); }
}
