<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaCadenaFrio extends Model
{
    protected $table = 'alertas_cadena_frio';

    protected $fillable = [
        'equipo_id','fecha_inicio','fecha_fin',
        'temperatura_registrada','temperatura_permitida_min','temperatura_permitida_max',
        'severidad','estado','observaciones','usuario_id',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin'    => 'datetime',
    ];

    public function equipo()       { return $this->belongsTo(EquipoCadenaFrio::class, 'equipo_id'); }
    public function usuario()      { return $this->belongsTo(\App\Models\User::class, 'usuario_id'); }
    public function afectaciones() { return $this->hasMany(AfectacionLote::class, 'alerta_id'); }
}
