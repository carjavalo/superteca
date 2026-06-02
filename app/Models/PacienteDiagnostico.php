<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacienteDiagnostico extends Model
{
    protected $table = 'pacientes_diagnosticos';

    protected $fillable = ['paciente_id','codigo_cie10','descripcion','principal','fecha_diagnostico'];

    protected $casts = [
        'principal' => 'boolean',
        'fecha_diagnostico' => 'date',
    ];

    public function paciente() { return $this->belongsTo(Paciente::class); }
}
