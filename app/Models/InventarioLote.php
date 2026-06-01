<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventarioLote extends Model
{
    use HasFactory;

    protected $table = 'inventario_lotes';

    protected $fillable = [
        'medicamento_id',
        'presentacion_id',
        'lote',
        'fecha_vencimiento',
        'fecha_ingreso',
        'cantidad_inicial',
        'cantidad_actual',
        'unidad_medida',
        'unidad_medida_id',
        'costo_unitario',
        'ubicacion',
        'temperatura_min',
        'temperatura_max',
        'proveedor_id',
        'estado',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'fecha_ingreso' => 'date',
        'cantidad_inicial' => 'decimal:2',
        'cantidad_actual' => 'decimal:2',
        'costo_unitario' => 'decimal:2',
        'temperatura_min' => 'decimal:2',
        'temperatura_max' => 'decimal:2',
        'estado' => 'boolean',
    ];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }

    public function presentacion()
    {
        return $this->belongsTo(Presentacion::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class, 'proveedor_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }
}
