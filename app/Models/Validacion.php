<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Validacion extends Model
{
    protected $table = 'validaciones';

    public const TIPOS = [
        'PRESCRIPCION' => 'Prescripción',
        'PREPARACION'  => 'Preparación',
        'MEZCLA'       => 'Mezcla',
        'DISPENSACION' => 'Dispensación',
    ];

    public const RESULTADOS = [
        'PENDIENTE' => 'Pendiente',
        'APROBADA'  => 'Aprobada',
        'OBSERVADA' => 'Observada',
        'RECHAZADA' => 'Rechazada',
    ];

    public const PRIORIDADES = [
        'BAJA'    => 'Baja',
        'NORMAL'  => 'Normal',
        'ALTA'    => 'Alta',
        'CRITICA' => 'Crítica',
    ];

    protected $fillable = [
        'codigo','paciente_id','prescripcion_id','preparacion_id','mezcla_id',
        'fecha_validacion','tipo_validacion','resultado','prioridad',
        'observaciones','farmaceutico_id',
    ];

    protected $casts = [
        'fecha_validacion' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($v) {
            if (empty($v->codigo)) {
                $year = now()->year;
                $last = static::where('codigo', 'like', "VAL-{$year}-%")->orderByDesc('id')->first();
                $n = 1;
                if ($last) { $parts = explode('-', $last->codigo); $n = (int) end($parts) + 1; }
                $v->codigo = sprintf('VAL-%s-%05d', $year, $n);
            }
        });
    }

    public function paciente()     { return $this->belongsTo(Paciente::class); }
    public function prescripcion() { return $this->belongsTo(PacientePrescripcion::class, 'prescripcion_id'); }
    public function preparacion()  { return $this->belongsTo(Preparacion::class); }
    public function mezcla()       { return $this->belongsTo(Mezcla::class); }
    public function farmaceutico() { return $this->belongsTo(User::class, 'farmaceutico_id'); }
    public function detalles()     { return $this->hasMany(ValidacionDetalle::class); }
    public function alertas()      { return $this->hasMany(ValidacionAlerta::class); }
    public function aprobaciones() { return $this->hasMany(ValidacionAprobacion::class); }
}
