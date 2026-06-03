<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Incidente extends Model
{
    protected $table = 'incidentes';

    protected $fillable = [
        'codigo','fecha_incidente','tipo_incidente','clasificacion','severidad',
        'descripcion','usuario_reporta_id','fecha_reporte','estado',
    ];

    protected $casts = [
        'fecha_incidente' => 'datetime',
        'fecha_reporte'   => 'datetime',
    ];

    public const TIPOS = [
        'INVENTARIO'   => 'Inventario',
        'CADENA_FRIO'  => 'Cadena de Frío',
        'PRODUCCION'   => 'Producción',
        'PREPARACION'  => 'Preparación',
        'REEMPAQUE'    => 'Reempaque',
        'DISPENSACION' => 'Dispensación',
        'CALIDAD'      => 'Calidad',
        'PACIENTE'     => 'Paciente',
        'EQUIPO'       => 'Equipo',
        'AUDITORIA'    => 'Auditoría',
    ];

    public const CLASIFICACIONES = [
        'DESVIACION'    => 'Desviación',
        'NO_CONFORMIDAD'=> 'No conformidad',
        'EVENTO_ADVERSO'=> 'Evento adverso',
        'RIESGO'        => 'Riesgo',
        'HALLAZGO'      => 'Hallazgo',
    ];

    public const SEVERIDADES = [
        'BAJA'    => 'Baja',
        'MEDIA'   => 'Media',
        'ALTA'    => 'Alta',
        'CRITICA' => 'Crítica',
    ];

    public const ESTADOS = [
        'ABIERTO'           => 'Abierto',
        'INVESTIGACION'     => 'En investigación',
        'ACCION_CORRECTIVA' => 'Acción correctiva',
        'CERRADO'           => 'Cerrado',
    ];

    public function usuarioReporta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_reporta_id');
    }

    public function detalle(): HasOne
    {
        return $this->hasOne(IncidenteDetalle::class, 'incidente_id');
    }

    public function afectaciones(): HasMany
    {
        return $this->hasMany(IncidenteAfectacion::class, 'incidente_id');
    }

    public function acciones(): HasMany
    {
        return $this->hasMany(IncidenteAccion::class, 'incidente_id');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(IncidenteEvidencia::class, 'incidente_id');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(IncidenteSeguimiento::class, 'incidente_id');
    }
}
