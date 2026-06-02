<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paciente extends Model
{
    protected $table = 'pacientes';

    protected $fillable = [
        'documento','tipo_documento','nombres','apellidos','fecha_nacimiento',
        'sexo','peso','talla','cama','servicio_id','observaciones','estado',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'peso'             => 'decimal:2',
        'talla'            => 'decimal:2',
        'estado'           => 'boolean',
    ];

    public function servicio(): BelongsTo { return $this->belongsTo(ServicioHospitalario::class, 'servicio_id'); }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }
}
