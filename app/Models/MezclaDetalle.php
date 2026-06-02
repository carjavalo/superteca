<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MezclaDetalle extends Model
{
    protected $table = 'mezclas_detalle';

    protected $fillable = [
        'mezcla_id','medicamento_id','presentacion_id','dosis_requerida',
        'unidad_medida_id','orden_preparacion','observaciones'
    ];

    public function mezcla()      { return $this->belongsTo(Mezcla::class); }
    public function medicamento() { return $this->belongsTo(Medicamento::class); }
    public function presentacion(){ return $this->belongsTo(Presentacion::class); }
    public function unidadMedida(){ return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }
}
