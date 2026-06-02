<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaRequerimiento extends Model
{
    protected $table = 'formulas_requerimientos';

    protected $fillable = ['formula_id','medicamento_id','cantidad_requerida','unidad_medida_id'];

    public function formula()      { return $this->belongsTo(Formula::class); }
    public function medicamento()  { return $this->belongsTo(Medicamento::class); }
    public function unidadMedida() { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }
}
