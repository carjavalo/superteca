<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Traslado extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo', 'fecha_solicitud', 'fecha_envio', 'fecha_recepcion',
        'bodega_origen_id', 'bodega_destino_id', 'tipo_traslado', 'estado',
        'usuario_solicita_id', 'usuario_envia_id', 'usuario_recibe_id',
        'observaciones', 'valor_total',
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_envio'     => 'datetime',
        'fecha_recepcion' => 'datetime',
        'valor_total'     => 'decimal:2',
    ];

    public const TIPOS = [
        'INTERNO'      => 'Interno',
        'ENTRE_SEDES'  => 'Entre Sedes',
        'DEVOLUCION'   => 'Devolución',
        'REUBICACION'  => 'Reubicación',
    ];

    public const ESTADOS = [
        'BORRADOR'    => 'Borrador',
        'PENDIENTE'   => 'Pendiente',
        'EN_TRANSITO' => 'En Tránsito',
        'RECIBIDO'    => 'Recibido',
        'RECHAZADO'   => 'Rechazado',
        'ANULADO'     => 'Anulado',
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($m) {
            if (empty($m->codigo)) {
                $next = (self::max('id') ?? 0) + 1;
                $m->codigo = 'TR-' . date('Y') . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function bodegaOrigen()   { return $this->belongsTo(Bodega::class, 'bodega_origen_id'); }
    public function bodegaDestino()  { return $this->belongsTo(Bodega::class, 'bodega_destino_id'); }
    public function solicitante()    { return $this->belongsTo(User::class, 'usuario_solicita_id'); }
    public function enviador()       { return $this->belongsTo(User::class, 'usuario_envia_id'); }
    public function receptor()       { return $this->belongsTo(User::class, 'usuario_recibe_id'); }
    public function detalles()       { return $this->hasMany(DetalleTraslado::class, 'traslado_id'); }
    public function recepciones()    { return $this->hasMany(RecepcionTraslado::class, 'traslado_id'); }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo_traslado] ?? $this->tipo_traslado;
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }
}
