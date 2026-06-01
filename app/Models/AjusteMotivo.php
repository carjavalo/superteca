<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjusteMotivo extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'nombre',
        'tipo', // POSITIVO, NEGATIVO, AMBOS
        'requiere_observacion',
        'requiere_aprobacion',
        'estado'
    ];
}
