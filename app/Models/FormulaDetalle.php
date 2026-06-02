<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaDetalle extends Model
{
    protected $table = 'formulas_detalle';

    protected $fillable = [
        'formula_id','medicamento_id','presentacion_id','dosis',
        'unidad_medida_id','orden_preparacion','obligatorio','observaciones'
    ];

    protected $casts = [
        'obligatorio' => 'boolean',
        'dosis' => 'decimal:4',
    ];

    public function formula()      { return $this->belongsTo(Formula::class); }
    public function medicamento()  { return $this->belongsTo(Medicamento::class); }
    public function presentacion() { return $this->belongsTo(Presentacion::class); }
    public function unidadMedida() { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }
}
