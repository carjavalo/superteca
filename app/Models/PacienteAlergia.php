<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacienteAlergia extends Model
{
    protected $table = 'pacientes_alergias';

    public const SEVERIDADES = ['LEVE'=>'Leve','MODERADA'=>'Moderada','SEVERA'=>'Severa'];

    protected $fillable = ['paciente_id','medicamento_id','descripcion','severidad','observaciones'];

    public function paciente()    { return $this->belongsTo(Paciente::class); }
    public function medicamento() { return $this->belongsTo(Medicamento::class); }
}
