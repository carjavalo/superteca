<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipoCadenaFrio extends Model
{
    protected $table = 'equipos_cadena_frio';

    protected $fillable = [
        'codigo','nombre','tipo','ubicacion',
        'temperatura_min','temperatura_max','humedad_min','humedad_max',
        'fabricante','modelo','serial',
        'fecha_calibracion','proxima_calibracion','estado',
    ];

    protected $casts = [
        'fecha_calibracion'   => 'date',
        'proxima_calibracion' => 'date',
        'estado'              => 'boolean',
    ];

    public function sensores()    { return $this->hasMany(SensorTemperatura::class, 'equipo_id'); }
    public function monitoreos()  { return $this->hasMany(MonitoreoTemperatura::class, 'equipo_id'); }
    public function alertas()     { return $this->hasMany(AlertaCadenaFrio::class, 'equipo_id'); }
    public function lotes()       { return $this->hasMany(LoteCadenaFrio::class, 'equipo_id'); }

    public function ultimoMonitoreo()
    {
        return $this->hasOne(MonitoreoTemperatura::class, 'equipo_id')->latestOfMany('fecha_hora');
    }

    public function getSemaforoAttribute(): string
    {
        $u = $this->ultimoMonitoreo;
        if (!$u) return 'GRIS';
        $t = (float) $u->temperatura;
        $min = (float) $this->temperatura_min;
        $max = (float) $this->temperatura_max;
        if ($t < $min || $t > $max) return 'ROJO';
        $margen = ($max - $min) * 0.10;
        if ($t <= $min + $margen || $t >= $max - $margen) return 'AMARILLO';
        return 'VERDE';
    }
}
