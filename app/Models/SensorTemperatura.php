<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorTemperatura extends Model
{
    protected $table = 'sensores_temperatura';

    protected $fillable = ['equipo_id','codigo_sensor','marca','modelo','estado'];

    protected $casts = ['estado' => 'boolean'];

    public function equipo() { return $this->belongsTo(EquipoCadenaFrio::class, 'equipo_id'); }
}
