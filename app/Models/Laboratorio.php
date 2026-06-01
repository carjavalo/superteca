<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{
    protected $table = 'laboratorios';

    protected $fillable = [
        'codigo',
        'nombre',
        'nit',
        'registro_invima',
        'pais_origen',
        'ciudad',
        'direccion',
        'telefono',
        'email',
        'sitio_web',
        'contacto_comercial',
        'contacto_farmacovigilancia',
        'requiere_cadena_frio',
        'estado',
        'semaforo_sanitario',
        'observaciones',
        'logo',
    ];

    protected $casts = [
        'requiere_cadena_frio' => 'boolean',
        'estado'               => 'boolean',
    ];

    public function medicamentos()
    {
        return $this->hasMany(Medicamento::class);
    }
}
