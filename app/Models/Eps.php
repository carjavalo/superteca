<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo de EPS.
 *
 * Alimenta el campo «EPS» de pacientes (y cualquier otra vista que lo
 * diligencie). El select guarda el nombre (Detalle) de la EPS en el campo de
 * texto `pacientes.eps`. Las EPS con Estado=0 (Inactivo) NO se ofrecen en los
 * selects del sistema (scope activas()).
 */
class Eps extends Model
{
    protected $table = 'EPS';

    /** La tabla sólo tiene id, Detalle, Estado y Observacion (sin timestamps). */
    public $timestamps = false;

    protected $fillable = ['Detalle', 'Estado', 'Observacion'];

    protected $casts = ['Estado' => 'boolean'];

    /** Alias de compatibilidad por si alguna vista usa ->nombre. */
    public function getNombreAttribute(): ?string
    {
        return $this->Detalle;
    }

    /** Sólo las EPS activas (usables en el sistema). */
    public function scopeActivas($query)
    {
        return $query->where('Estado', 1);
    }
}
