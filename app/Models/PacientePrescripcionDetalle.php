<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacientePrescripcionDetalle extends Model
{
    protected $table = 'pacientes_prescripciones_detalle';

    protected $fillable = [
        'prescripcion_id','medicamento_id','dosis','unidad_medida_id',
        'frecuencia','duracion_dias','via_administracion_id','observaciones',
    ];

    protected $casts = ['dosis' => 'decimal:4'];

    public function prescripcion()       { return $this->belongsTo(PacientePrescripcion::class, 'prescripcion_id'); }
    public function medicamento()        { return $this->belongsTo(Medicamento::class); }
    public function unidadMedida()       { return $this->belongsTo(UnidadMedida::class); }
    public function viaAdministracion()  { return $this->belongsTo(ViaAdministracion::class); }
}
