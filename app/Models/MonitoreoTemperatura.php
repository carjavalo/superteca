<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonitoreoTemperatura extends Model
{
    protected $table = 'monitoreo_temperatura';

    protected $fillable = [
        'equipo_id','sensor_id','fecha_hora','temperatura','humedad',
        'usuario_id','origen','fuera_rango','observaciones',
    ];

    protected $casts = [
        'fecha_hora'  => 'datetime',
        'fuera_rango' => 'boolean',
    ];

    public function equipo()  { return $this->belongsTo(EquipoCadenaFrio::class, 'equipo_id'); }
    public function sensor()  { return $this->belongsTo(SensorTemperatura::class, 'sensor_id'); }
    public function usuario() { return $this->belongsTo(\App\Models\User::class, 'usuario_id'); }
}
