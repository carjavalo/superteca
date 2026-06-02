<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mezcla extends Model
{
    protected $table = 'mezclas';

    protected $fillable = [
        'codigo','formula_id','tipo_mezcla','fecha_programada','fecha_inicio','fecha_fin',
        'volumen_programado','unidad_volumen_id','cantidad_preparaciones','observaciones',
        'estado','costo_total','usuario_preparador_id','usuario_validador_id'
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'volumen_programado' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    public const ESTADOS = [
        'PROGRAMADA'      => 'Programada',
        'EN_PROCESO'      => 'En Proceso',
        'CONTROL_CALIDAD' => 'Control de Calidad',
        'LIBERADA'        => 'Liberada',
        'CANCELADA'       => 'Cancelada',
    ];

    public const TIPOS = [
        'NUTRICION_PARENTERAL' => 'Nutrición Parenteral',
        'ANTIBIOTICO'          => 'Antibiótico',
        'ONCOLOGIA'            => 'Oncología',
        'PEDIATRICA'           => 'Pediátrica',
        'MAGISTRAL'            => 'Magistral',
    ];

    protected static function booted()
    {
        static::creating(function ($m) {
            if (empty($m->codigo)) {
                $next = (static::max('id') ?? 0) + 1;
                $m->codigo = 'MZ-' . date('Y') . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function formula()         { return $this->belongsTo(Formula::class); }
    public function detalles()        { return $this->hasMany(MezclaDetalle::class)->orderBy('orden_preparacion'); }
    public function consumos()        { return $this->hasMany(MezclaConsumo::class); }
    public function controles()       { return $this->hasMany(MezclaControlCalidad::class); }
    public function productosFinales(){ return $this->hasMany(MezclaProductoFinal::class); }
    public function preparador()      { return $this->belongsTo(User::class, 'usuario_preparador_id'); }
    public function validador()       { return $this->belongsTo(User::class, 'usuario_validador_id'); }
    public function unidadVolumen()   { return $this->belongsTo(UnidadMedida::class, 'unidad_volumen_id'); }
}
