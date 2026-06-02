<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PacientePrescripcion extends Model
{
    protected $table = 'pacientes_prescripciones';

    public const ESTADOS = ['ACTIVA'=>'Activa','SUSPENDIDA'=>'Suspendida','FINALIZADA'=>'Finalizada'];

    protected $fillable = ['codigo','paciente_id','medico_id','fecha_prescripcion','estado','observaciones'];

    protected $casts = ['fecha_prescripcion' => 'datetime'];

    protected static function booted()
    {
        static::creating(function ($p) {
            if (empty($p->codigo)) {
                $year = now()->year;
                $last = static::where('codigo', 'like', "RX-{$year}-%")->orderByDesc('id')->first();
                $n = 1;
                if ($last) { $parts = explode('-', $last->codigo); $n = (int) end($parts) + 1; }
                $p->codigo = sprintf('RX-%s-%05d', $year, $n);
            }
        });
    }

    public function paciente() { return $this->belongsTo(Paciente::class); }
    public function medico()   { return $this->belongsTo(User::class, 'medico_id'); }
    public function detalles() { return $this->hasMany(PacientePrescripcionDetalle::class, 'prescripcion_id'); }
}
