<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditoriaPermiso extends Model
{
    protected $table = 'auditoria_permisos';

    public $timestamps = false;

    protected $fillable = ['usuario_id', 'rol_id', 'accion', 'descripcion', 'fecha', 'ip'];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
}
