<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaCompatibilidad extends Model
{
    protected $table = 'formulas_compatibilidades';

    protected $fillable = ['formula_id','medicamento_id','compatible','observacion'];

    protected $casts = ['compatible' => 'boolean'];

    public function formula()     { return $this->belongsTo(Formula::class); }
    public function medicamento() { return $this->belongsTo(Medicamento::class); }
}
