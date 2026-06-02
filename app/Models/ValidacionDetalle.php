<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidacionDetalle extends Model
{
    protected $table = 'validaciones_detalle';

    public const ESTADOS = [
        'VALIDO'    => 'Válido',
        'OBSERVADO' => 'Observado',
        'RECHAZADO' => 'Rechazado',
    ];

    protected $fillable = [
        'validacion_id','medicamento_id','dosis_prescrita','dosis_recomendada',
        'unidad_medida_id','via_administracion_id','observaciones','estado',
    ];

    public function validacion()        { return $this->belongsTo(Validacion::class); }
    public function medicamento()       { return $this->belongsTo(Medicamento::class); }
    public function unidadMedida()      { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id'); }
    public function viaAdministracion() { return $this->belongsTo(ViaAdministracion::class, 'via_administracion_id'); }
}
