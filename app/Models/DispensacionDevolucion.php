<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispensacionDevolucion extends Model
{
    protected $table = 'dispensacion_devoluciones';

    public const ESTADOS = [
        'PENDIENTE' => 'Pendiente',
        'APROBADA'  => 'Aprobada',
        'RECHAZADA' => 'Rechazada',
    ];

    protected $fillable = [
        'codigo','entrega_id','fecha_devolucion','motivo','usuario_id',
        'estado','cantidad_devuelta','costo_total',
    ];

    protected $casts = [
        'fecha_devolucion'  => 'datetime',
        'cantidad_devuelta' => 'decimal:2',
        'costo_total'       => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($d) {
            if (empty($d->codigo)) {
                $year = now()->year;
                $last = static::where('codigo', 'like', "DEV-{$year}-%")->orderByDesc('id')->first();
                $n = 1;
                if ($last) { $parts = explode('-', $last->codigo); $n = (int) end($parts) + 1; }
                $d->codigo = sprintf('DEV-%s-%05d', $year, $n);
            }
        });
    }

    public function entrega() { return $this->belongsTo(DispensacionEntrega::class, 'entrega_id'); }
    public function usuario() { return $this->belongsTo(User::class, 'usuario_id'); }
    public function detalles() { return $this->hasMany(DispensacionDevolucionDetalle::class, 'devolucion_id'); }
}
