<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidacionAprobacion extends Model
{
    protected $table = 'validaciones_aprobaciones';

    public const ACCIONES = [
        'APROBADA'  => 'Aprobada',
        'RECHAZADA' => 'Rechazada',
        'DEVUELTA'  => 'Devuelta',
        'OBSERVADA' => 'Observada',
    ];

    protected $fillable = [
        'validacion_id','usuario_id','fecha_aprobacion','accion','observaciones',
    ];

    protected $casts = ['fecha_aprobacion' => 'datetime'];

    public function validacion() { return $this->belongsTo(Validacion::class); }
    public function usuario()    { return $this->belongsTo(User::class, 'usuario_id'); }
}
