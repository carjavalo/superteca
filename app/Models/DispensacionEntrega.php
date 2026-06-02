<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DispensacionEntrega extends Model
{
    use HasFactory;

    protected $table = 'dispensacion_entregas';

    public const ESTADOS = [
        'PENDIENTE' => 'Pendiente',
        'ENTREGADA' => 'Entregada',
        'PARCIAL'   => 'Parcial',
        'DEVUELTA'  => 'Devuelta',
        'ANULADA'   => 'Anulada',
    ];

    public const TIPOS = [
        'PACIENTE'          => 'Paciente',
        'SERVICIO'          => 'Servicio',
        'ENFERMERIA'        => 'Enfermería',
        'QUIROFANO'         => 'Quirófano',
        'UCI'               => 'UCI',
        'URGENCIAS'         => 'Urgencias',
        'HOSPITALIZACION'   => 'Hospitalización',
        'FARMACIA_SATELITE' => 'Farmacia Satélite',
    ];

    protected $fillable = [
        'codigo','tipo_entrega','paciente_id','servicio_id','fecha_entrega',
        'usuario_dispensador_id','usuario_recibe_id','recibe_nombre','recibe_documento',
        'observaciones','estado','costo_total',
    ];

    protected $casts = [
        'fecha_entrega' => 'datetime',
        'costo_total'   => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($e) {
            if (empty($e->codigo)) {
                $year = now()->year;
                $last = static::where('codigo', 'like', "DSP-{$year}-%")->orderByDesc('id')->first();
                $n = 1;
                if ($last) {
                    $parts = explode('-', $last->codigo);
                    $n = (int) end($parts) + 1;
                }
                $e->codigo = sprintf('DSP-%s-%05d', $year, $n);
            }
        });
    }

    public function paciente()    { return $this->belongsTo(Paciente::class); }
    public function servicio()    { return $this->belongsTo(ServicioHospitalario::class, 'servicio_id'); }
    public function dispensador() { return $this->belongsTo(User::class, 'usuario_dispensador_id'); }
    public function recibe()      { return $this->belongsTo(User::class, 'usuario_recibe_id'); }
    public function detalles()    { return $this->hasMany(DispensacionEntregaDetalle::class, 'entrega_id'); }
    public function recepciones() { return $this->hasMany(DispensacionEntregaRecepcion::class, 'entrega_id'); }
    public function devoluciones(){ return $this->hasMany(DispensacionDevolucion::class, 'entrega_id'); }

    public function getDestinoAttribute(): string
    {
        if ($this->paciente) return $this->paciente->nombre_completo ?? trim(($this->paciente->nombres ?? '').' '.($this->paciente->apellidos ?? ''));
        if ($this->servicio) return $this->servicio->nombre ?? '';
        return self::TIPOS[$this->tipo_entrega] ?? $this->tipo_entrega;
    }
}
