<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catálogo dinámico de Servicios (áreas hospitalarias).
 *
 * Alimenta el campo «Servicio» del registro de pacientes. A diferencia de los
 * catálogos de Tipos de Entrada / Administración (que guardan un código de
 * texto), aquí el select guarda el **id** de este catálogo en
 * `pacientes.servicio_id` (relación por clave foránea lógica).
 */
class TipoServicios extends Model
{
    protected $table = 'TipoServicios';

    /** La tabla sólo tiene id, Detalle y Observacion (sin timestamps). */
    public $timestamps = false;

    protected $fillable = ['Detalle', 'Observacion'];

    /**
     * Alias de compatibilidad: las vistas de pacientes (índice, crear, ficha)
     * referencian ->nombre del servicio. Se mapea al Detalle del catálogo.
     */
    public function getNombreAttribute(): ?string
    {
        return $this->Detalle;
    }
}
