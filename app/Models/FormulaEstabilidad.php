<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaEstabilidad extends Model
{
    protected $table = 'formulas_estabilidad';

    protected $fillable = ['formula_id','temperatura_min','temperatura_max','horas_estabilidad','observaciones'];

    public function formula() { return $this->belongsTo(Formula::class); }
}
