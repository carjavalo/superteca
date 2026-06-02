<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioHospitalario extends Model
{
    protected $table = 'servicios_hospitalarios';

    protected $fillable = ['nombre','codigo','estado'];

    protected $casts = ['estado' => 'boolean'];
}
