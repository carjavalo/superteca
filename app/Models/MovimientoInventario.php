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
        'medicamento_id','presentacion_id','lote_codigo','fecha_vencimiento',
        'bodega_origen_id','bodega_destino_id','proveedor_id',
        'cantidad','stock_anterior','stock_nuevo',
        'costo_unitario','costo_total',
        'fecha_movimiento','usuario_id','observacion',
    ];

    protected $casts = [
        'fecha_movimiento'  => 'datetime',
        'fecha_vencimiento' => 'date',
        'cantidad'          => 'decimal:2',
        'stock_anterior'    => 'decimal:2',
        'stock_nuevo'       => 'decimal:2',
        'costo_unitario'    => 'decimal:2',
        'costo_total'       => 'decimal:2',
    ];

    public const TIPOS = [
        'ENTRADA'           => ['label' => 'Entrada',           'color' => '#22c55e'],
        'SALIDA'            => ['label' => 'Salida',            'color' => '#ef4444'],
        'AJUSTE_POSITIVO'   => ['label' => 'Ajuste +',          'color' => '#f59e0b'],
        'AJUSTE_NEGATIVO'   => ['label' => 'Ajuste -',          'color' => '#f97316'],
        'AJUSTE'            => ['label' => 'Ajuste',            'color' => '#f59e0b'],
        'TRASLADO'          => ['label' => 'Traslado',          'color' => '#3b82f6'],
        'TRASLADO_ENTRADA'  => ['label' => 'Traslado entrada',  'color' => '#0ea5e9'],
        'TRASLADO_SALIDA'   => ['label' => 'Traslado salida',   'color' => '#3b82f6'],
        'DEVOLUCION'        => ['label' => 'Devolución',        'color' => '#14b8a6'],
        'VENCIMIENTO'       => ['label' => 'Vencimiento',       'color' => '#64748b'],
        'PRODUCCION'        => ['label' => 'Producción',        'color' => '#a855f7'],
    ];

    public function getCantidadEntradaAttribute(): float
    {
        return ((float) $this->cantidad) > 0 ? (float) $this->cantidad : 0;
    }

    public function getCantidadSalidaAttribute(): float
    {
        return ((float) $this->cantidad) < 0 ? abs((float) $this->cantidad) : 0;
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo_movimiento]['label'] ?? $this->tipo_movimiento;
    }

    public function getTipoColorAttribute(): string
    {
        return self::TIPOS[$this->tipo_movimiento]['color'] ?? '#6b7280';
    }

    public function lote()           { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
    public function usuario()        { return $this->belongsTo(User::class, 'usuario_id'); }
    public function medicamento()    { return $this->belongsTo(Medicamento::class); }
    public function presentacion()   { return $this->belongsTo(Presentacion::class); }
    public function proveedor()      { return $this->belongsTo(Proveedor::class); }
    public function bodegaOrigen()   { return $this->belongsTo(Bodega::class, 'bodega_origen_id'); }
    public function bodegaDestino()  { return $this->belongsTo(Bodega::class, 'bodega_destino_id'); }
}
