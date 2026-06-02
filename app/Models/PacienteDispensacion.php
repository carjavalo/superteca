<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacienteDispensacion extends Model
{
    protected $table = 'pacientes_dispensaciones';

    protected $fillable = [
        'paciente_id','entrega_id','entrega_detalle_id','medicamento_id',
        'inventario_lote_id','lote','fecha_vencimiento','cantidad',
        'fecha_entrega','costo_total',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_entrega'     => 'datetime',
        'cantidad'          => 'decimal:2',
        'costo_total'       => 'decimal:2',
    ];

    public function paciente()       { return $this->belongsTo(Paciente::class); }
    public function entrega()        { return $this->belongsTo(DispensacionEntrega::class, 'entrega_id'); }
    public function detalle()        { return $this->belongsTo(DispensacionEntregaDetalle::class, 'entrega_detalle_id'); }
    public function medicamento()    { return $this->belongsTo(Medicamento::class); }
    public function inventarioLote() { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
}
