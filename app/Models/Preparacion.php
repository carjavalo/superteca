<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Preparacion extends Model
{
    protected $table = 'preparaciones';

    protected $fillable = [
        'codigo','paciente_id','tipo_preparacion_id','servicio_id',
        'fecha_programada','fecha_inicio','fecha_fin',
        'volumen_final','unidad_volumen_id','observaciones','estado',
        'costo_total','usuario_preparador_id','usuario_validador_id',
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
        'fecha_inicio'     => 'datetime',
        'fecha_fin'        => 'datetime',
        'volumen_final'    => 'decimal:2',
        'costo_total'      => 'decimal:2',
    ];

    public const ESTADOS = [
        'PROGRAMADA'      => 'Programada',
        'EN_PROCESO'      => 'En proceso',
        'CONTROL_CALIDAD' => 'Control de calidad',
        'LIBERADA'        => 'Liberada',
        'ENTREGADA'       => 'Entregada',
        'ANULADA'         => 'Anulada',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $p) {
            if (!$p->codigo) {
                $p->codigo = 'PREP-' . now()->format('Ymd') . '-' . str_pad((string)(self::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function tipo(): BelongsTo { return $this->belongsTo(TipoPreparacion::class, 'tipo_preparacion_id'); }
    public function servicio(): BelongsTo { return $this->belongsTo(ServicioHospitalario::class, 'servicio_id'); }
    public function unidadVolumen(): BelongsTo { return $this->belongsTo(UnidadMedida::class, 'unidad_volumen_id'); }
    public function preparador(): BelongsTo { return $this->belongsTo(User::class, 'usuario_preparador_id'); }
    public function validador(): BelongsTo { return $this->belongsTo(User::class, 'usuario_validador_id'); }

    public function detalles(): HasMany { return $this->hasMany(PreparacionDetalle::class, 'preparacion_id'); }
    public function consumos(): HasMany { return $this->hasMany(PreparacionConsumo::class, 'preparacion_id'); }
    public function controles(): HasMany { return $this->hasMany(PreparacionControlCalidad::class, 'preparacion_id'); }
    public function entrega(): HasOne { return $this->hasOne(PreparacionEntrega::class, 'preparacion_id'); }
}
