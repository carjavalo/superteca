<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ControlCalidad extends Model
{
    protected $table = 'controles_calidad';

    protected $fillable = [
        'codigo','tipo_control','fecha_control','usuario_control_id',
        'resultado','observaciones',
    ];

    protected $casts = [
        'fecha_control' => 'datetime',
    ];

    public const TIPOS = [
        'RECEPCION'         => 'Recepción',
        'ALMACENAMIENTO'    => 'Almacenamiento',
        'CADENA_FRIO'       => 'Cadena de Frío',
        'PRODUCCION'        => 'Producción',
        'PREPARACION'       => 'Preparación',
        'MEZCLA'            => 'Mezcla',
        'REEMPAQUE'         => 'Reempaque',
        'PRODUCTO_TERMINADO'=> 'Producto Terminado',
        'DISPENSACION'      => 'Dispensación',
    ];

    public const RESULTADOS = [
        'PENDIENTE'  => 'Pendiente',
        'APROBADO'   => 'Aprobado',
        'CONDICIONAL'=> 'Condicional',
        'RECHAZADO'  => 'Rechazado',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_control_id');
    }

    public function detalle(): HasMany
    {
        return $this->hasMany(ControlCalidadDetalle::class, 'control_id');
    }

    public function resultados(): HasMany
    {
        return $this->hasMany(ControlCalidadResultado::class, 'control_id');
    }

    public function acciones(): HasMany
    {
        return $this->hasMany(ControlCalidadAccion::class, 'control_id');
    }

    public function evidencias(): HasMany
    {
        return $this->hasMany(ControlCalidadEvidencia::class, 'control_id');
    }
}
