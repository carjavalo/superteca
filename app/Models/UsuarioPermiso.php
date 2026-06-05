<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioPermiso extends Model
{
    protected $table = 'usuario_permisos';

    public $timestamps = false;

    protected $fillable = ['usuario_id', 'permiso_id', 'accion_id', 'permitido', 'created_at'];

    protected $casts = [
        'permitido'  => 'boolean',
        'created_at' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
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
