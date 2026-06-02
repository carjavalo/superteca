<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormulaDiluyente extends Model
{
    protected $table = 'formulas_diluyentes';

    protected $fillable = ['formula_id','medicamento_id','volumen','unidad_medida_id','observacion'];

    public function formula()      { return $this->belongsTo(Formula::class); }
    public function medicamento()  { return $this->belongsTo(Medicamento::class); }
    public function unidadMedida() { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }
}
