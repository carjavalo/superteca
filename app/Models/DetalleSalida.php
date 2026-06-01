<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleSalida extends Model
{
    use HasFactory;

    protected $table = 'detalle_salidas';

    protected $fillable = [
        'salida_id','inventario_lote_id','medicamento_id','presentacion_id',
        'lote','fecha_vencimiento','cantidad','unidad_medida_id',
        'costo_unitario','costo_total','motivo_salida','paciente_id',
        'numero_preparacion','observaciones',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'cantidad'          => 'decimal:2',
        'costo_unitario'    => 'decimal:2',
        'costo_total'       => 'decimal:2',
    ];

    public function salida()        { return $this->belongsTo(Salida::class); }
    public function lotInventario() { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
    public function medicamento()   { return $this->belongsTo(Medicamento::class); }
    public function presentacion()  { return $this->belongsTo(Presentacion::class); }
    public function unidadMedida()  { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }

    public function getSemaforoVencimientoAttribute(): string
    {
        if (! $this->fecha_vencimiento) return 'gray';
        $dias = now()->diffInDays($this->fecha_vencimiento, false);
        if ($dias < 0)   return 'red';
        if ($dias <= 90) return 'amber';
        return 'green';
    }
}
