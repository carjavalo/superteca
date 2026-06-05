<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolPermiso extends Model
{
    protected $table = 'rol_permisos';

    public $timestamps = false;

    protected $fillable = ['rol_id', 'permiso_id', 'accion_id', 'permitido', 'created_at'];

    protected $casts = [
        'permitido'  => 'boolean',
        'created_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'permiso_id');
    }

    public function accion()
    {
        return $this->belongsTo(Accion::class, 'accion_id');
    }
}
