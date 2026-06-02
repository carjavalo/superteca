<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacienteTratamiento extends Model
{
    protected $table = 'pacientes_tratamientos';

    public const ESTADOS = ['ACTIVO'=>'Activo','SUSPENDIDO'=>'Suspendido','FINALIZADO'=>'Finalizado'];

    protected $fillable = ['paciente_id','preparacion_id','mezcla_id','fecha_inicio','fecha_fin','estado','observaciones'];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin'    => 'datetime',
    ];

    public function paciente()    { return $this->belongsTo(Paciente::class); }
    public function preparacion() { return $this->belongsTo(Preparacion::class); }
    public function mezcla()      { return $this->belongsTo(Mezcla::class); }
}
