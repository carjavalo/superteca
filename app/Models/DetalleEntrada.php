<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleEntrada extends Model
{
    use HasFactory;

    protected $table = 'detalle_entradas';

    protected $fillable = [
        'entrada_id','medicamento_id','presentacion_id','laboratorio_id','proveedor_id',
        'lote','fecha_vencimiento','fecha_fabricacion','cantidad','unidad_medida_id',
        'costo_unitario','costo_total','temperatura_min','temperatura_max',
        'ubicacion','registro_invima','observaciones',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_fabricacion' => 'date',
        'cantidad'          => 'decimal:2',
        'costo_unitario'    => 'decimal:2',
        'costo_total'       => 'decimal:2',
        'temperatura_min'   => 'decimal:2',
        'temperatura_max'   => 'decimal:2',
    ];

    public function entrada()       { return $this->belongsTo(Entrada::class); }
    public function medicamento()   { return $this->belongsTo(Medicamento::class); }
    public function presentacion()  { return $this->belongsTo(Presentacion::class); }
    public function laboratorio()   { return $this->belongsTo(Laboratorio::class); }
    public function proveedor()     { return $this->belongsTo(Proveedor::class); }
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
