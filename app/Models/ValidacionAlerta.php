<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidacionAlerta extends Model
{
    protected $table = 'validaciones_alertas';

    public const TIPOS = [
        'ALERGIA'        => 'Alergia',
        'INTERACCION'    => 'Interacción',
        'DOSIS'          => 'Dosis',
        'VENCIMIENTO'    => 'Vencimiento',
        'STOCK'          => 'Stock',
        'COMPATIBILIDAD' => 'Compatibilidad',
        'DUPLICIDAD'     => 'Duplicidad',
    ];

    public const SEVERIDADES = [
        'BAJA'    => 'Baja',
        'MEDIA'   => 'Media',
        'ALTA'    => 'Alta',
        'CRITICA' => 'Crítica',
    ];

    protected $fillable = [
        'validacion_id','tipo_alerta','severidad','descripcion','resuelta',
    ];

    protected $casts = ['resuelta' => 'boolean'];

    public function validacion() { return $this->belongsTo(Validacion::class); }
}
