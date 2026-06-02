<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreparacionDetalle extends Model
{
    protected $table = 'preparaciones_detalle';

    protected $fillable = [
        'preparacion_id','medicamento_id','presentacion_id',
        'dosis','unidad_medida_id','concentracion','observaciones',
    ];

    protected $casts = [
        'dosis'         => 'decimal:4',
        'concentracion' => 'decimal:4',
    ];

    public function preparacion(): BelongsTo { return $this->belongsTo(Preparacion::class, 'preparacion_id'); }
    public function medicamento(): BelongsTo { return $this->belongsTo(Medicamento::class); }
    public function presentacion(): BelongsTo { return $this->belongsTo(Presentacion::class); }
    public function unidadMedida(): BelongsTo { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }
}
