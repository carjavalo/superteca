<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    protected $table = 'permisos';

    protected $fillable = ['modulo', 'vista', 'descripcion', 'orden', 'estado'];

    protected $casts = [
        'estado' => 'boolean',
        'orden'  => 'integer',
    ];

    public function rolPermisos()
    {
        return $this->hasMany(RolPermiso::class, 'permiso_id');
    }
}
