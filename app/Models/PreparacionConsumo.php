<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreparacionConsumo extends Model
{
    protected $table = 'preparaciones_consumo';

    protected $fillable = [
        'preparacion_id','inventario_lote_id','medicamento_id',
        'lote','fecha_vencimiento','cantidad_consumida','unidad_medida_id',
        'costo_unitario','costo_total',
    ];

    protected $casts = [
        'fecha_vencimiento'  => 'date',
        'cantidad_consumida' => 'decimal:4',
        'costo_unitario'     => 'decimal:4',
        'costo_total'        => 'decimal:2',
    ];

    public function preparacion(): BelongsTo { return $this->belongsTo(Preparacion::class, 'preparacion_id'); }
    public function inventarioLote(): BelongsTo { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
    public function medicamento(): BelongsTo { return $this->belongsTo(Medicamento::class); }
    public function unidadMedida(): BelongsTo { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }
}
