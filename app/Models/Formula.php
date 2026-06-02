<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Formula extends Model
{
    protected $table = 'formulas';

    protected $fillable = [
        'codigo','nombre','tipo_formula','descripcion','volumen_final',
        'unidad_volumen_id','tiempo_estabilidad_horas','temperatura_min',
        'temperatura_max','requiere_refrigeracion','observaciones','estado'
    ];

    protected $casts = [
        'requiere_refrigeracion' => 'boolean',
        'estado' => 'boolean',
        'volumen_final' => 'decimal:2',
        'temperatura_min' => 'decimal:2',
        'temperatura_max' => 'decimal:2',
    ];

    public const TIPOS = [
        'NUTRICION_PARENTERAL' => 'Nutrición Parenteral',
        'ANTIBIOTICO'          => 'Antibiótico',
        'ONCOLOGIA'            => 'Oncología',
        'PEDIATRIA'            => 'Pediatría',
        'MAGISTRAL'            => 'Magistral',
        'ESTANDAR'             => 'Estándar',
    ];

    protected static function booted()
    {
        static::creating(function ($f) {
            if (empty($f->codigo)) {
                $next = (static::max('id') ?? 0) + 1;
                $f->codigo = 'FOR-' . date('Y') . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    public function detalles()        { return $this->hasMany(FormulaDetalle::class)->orderBy('orden_preparacion'); }
    public function compatibilidades(){ return $this->hasMany(FormulaCompatibilidad::class); }
    public function estabilidades()   { return $this->hasMany(FormulaEstabilidad::class); }
    public function diluyentes()      { return $this->hasMany(FormulaDiluyente::class); }
    public function requerimientos()  { return $this->hasMany(FormulaRequerimiento::class); }
    public function unidadVolumen()   { return $this->belongsTo(UnidadMedida::class, 'unidad_volumen_id'); }
}
