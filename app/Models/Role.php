<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'descripcion'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function rolPermisos()
    {
        return $this->hasMany(RolPermiso::class, 'rol_id');
    }

    /** El Super Admin tiene acceso total al sistema por diseño. */
    public function esSuperAdmin(): bool
    {
        return $this->name === 'Super Admin';
    }
}
