<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjusteInventario extends Model
{
    use HasFactory;

    protected $table = 'ajustes_inventario';

    protected $fillable = [
        'codigo',
        'tipo_ajuste',
        'fecha_ajuste',
        'motivo_ajuste_id',
        'observaciones',
        'estado',
        'usuario_solicita_id',
        'usuario_aprueba_id',
        'fecha_aprobacion',
        'evidencia_path',
        'valor_total',
    ];

    protected $casts = [
        'fecha_ajuste' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'valor_total' => 'decimal:2',
    ];

    public const TIPOS = [
        'POSITIVO' => 'Positivo (sumar stock)',
        'NEGATIVO' => 'Negativo (restar stock)',
    ];

    public const ESTADOS = [
        'BORRADOR' => 'Borrador',
        'APROBADO' => 'Aprobado',
        'ANULADO'  => 'Anulado',
    ];

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            if (empty($model->codigo)) {
                $last = self::orderByDesc('id')->first();
                $next = $last ? $last->id + 1 : 1;
                $model->codigo = 'AJ-' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function motivo()
    {
        return $this->belongsTo(AjusteMotivo::class, 'motivo_ajuste_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleAjusteInventario::class, 'ajuste_id');
    }

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'usuario_solicita_id');
    }

    public function aprobador()
    {
        return $this->belongsTo(User::class, 'usuario_aprueba_id');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo_ajuste] ?? $this->tipo_ajuste;
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }
}
