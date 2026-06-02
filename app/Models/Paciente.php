<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Paciente extends Model
{
    protected $table = 'pacientes';

    public const ESTADOS_CLINICOS = [
        'ACTIVO'    => 'Activo',
        'EGRESADO'  => 'Egresado',
        'FALLECIDO' => 'Fallecido',
    ];

    protected $fillable = [
        'documento','tipo_documento','nombres','apellidos','fecha_nacimiento',
        'sexo','peso','talla','telefono','correo','direccion','eps',
        'cama','servicio_id','fecha_ingreso','fecha_egreso',
        'observaciones','estado','estado_clinico',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_ingreso'    => 'datetime',
        'fecha_egreso'     => 'datetime',
        'peso'             => 'decimal:2',
        'talla'            => 'decimal:2',
        'estado'           => 'boolean',
    ];

    public function servicio(): BelongsTo { return $this->belongsTo(ServicioHospitalario::class, 'servicio_id'); }
    public function alergias()       { return $this->hasMany(PacienteAlergia::class); }
    public function diagnosticos()   { return $this->hasMany(PacienteDiagnostico::class); }
    public function prescripciones() { return $this->hasMany(PacientePrescripcion::class); }
    public function tratamientos()   { return $this->hasMany(PacienteTratamiento::class); }
    public function dispensaciones() { return $this->hasMany(PacienteDispensacion::class); }
    public function preparaciones()  { return $this->hasMany(Preparacion::class); }
    public function entregas()       { return $this->hasMany(DispensacionEntrega::class); }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombres} {$this->apellidos}");
    }

    public function getEdadAttribute(): ?int
    {
        return $this->fecha_nacimiento?->age;
    }
}
