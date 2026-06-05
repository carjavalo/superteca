<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    use HasFactory;

    protected $table = 'entradas';

    public const TIPOS = [
        'COMPRA'      => 'Compra',
        'DONACION'    => 'Donación',
        'DEVOLUCION'  => 'Devolución',
        'TRASLADO'    => 'Traslado',
        'AJUSTE'      => 'Ajuste',
        'PRODUCCION'  => 'Producción',
        'ASIGNACION'  => 'Asignación',
    ];

    public const ESTADOS = [
        'BORRADOR'   => 'Borrador',
        'CONFIRMADA' => 'Confirmada',
        'ANULADA'    => 'Anulada',
    ];

    protected $fillable = [
        'codigo','tipo_entrada','proveedor_id','paciente_id','numero_factura','numero_remision',
        'fecha_entrada','fecha_documento','observaciones','subtotal','impuestos','total',
        'estado','usuario_id','bodega_destino_id',
    ];

    protected $casts = [
        'fecha_entrada'   => 'datetime',
        'fecha_documento' => 'date',
        'subtotal'        => 'decimal:2',
        'impuestos'       => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    public function proveedor() { return $this->belongsTo(Proveedor::class); }
    public function paciente()  { return $this->belongsTo(Paciente::class, 'paciente_id'); }
    public function usuario()   { return $this->belongsTo(User::class, 'usuario_id'); }
    public function detalles()  { return $this->hasMany(DetalleEntrada::class); }

    public function getEsAsignacionAttribute(): bool { return $this->tipo_entrada === 'ASIGNACION'; }

    public function getTipoLabelAttribute(): string   { return self::TIPOS[$this->tipo_entrada] ?? '—'; }
    public function getEstadoLabelAttribute(): string { return self::ESTADOS[$this->estado] ?? $this->estado; }
}
