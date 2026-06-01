<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;

    protected $table = 'salidas';

    public const TIPOS = [
        'PRODUCCION'           => 'Producción',
        'DISPENSACION'         => 'Dispensación',
        'VENCIMIENTO'          => 'Vencimiento',
        'DANO'                 => 'Daño / Ruptura',
        'DEVOLUCION_PROVEEDOR' => 'Devolución a proveedor',
        'TRASLADO'             => 'Traslado',
        'CONSUMO_INTERNO'      => 'Consumo interno',
        'AJUSTE_NEGATIVO'      => 'Ajuste negativo',
    ];

    public const ESTADOS = [
        'BORRADOR'   => 'Borrador',
        'CONFIRMADA' => 'Confirmada',
        'ANULADA'    => 'Anulada',
    ];

    protected $fillable = [
        'codigo','tipo_salida','fecha_salida','paciente_id','servicio_id',
        'bodega_origen_id','bodega_destino_id','numero_documento','observaciones',
        'subtotal','impuestos','total','estado','usuario_id','autorizado_por',
    ];

    protected $casts = [
        'fecha_salida' => 'datetime',
        'subtotal'     => 'decimal:2',
        'impuestos'    => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    public function usuario()     { return $this->belongsTo(User::class, 'usuario_id'); }
    public function autorizador() { return $this->belongsTo(User::class, 'autorizado_por'); }
    public function detalles()    { return $this->hasMany(DetalleSalida::class); }

    public function getTipoLabelAttribute(): string   { return self::TIPOS[$this->tipo_salida] ?? '—'; }
    public function getEstadoLabelAttribute(): string { return self::ESTADOS[$this->estado] ?? $this->estado; }
}
