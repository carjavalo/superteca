<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $fillable = [
        'codigo',
        'nombre',
        'nombre_generico',
        'concentracion',
        'unidad_medida',
        'forma_farmaceutica',
        'via_administracion',
        'laboratorio_id',
        'laboratorio',
        'registro_invima',
        'requiere_refrigeracion',
        'fotoproteccion',
        'alto_riesgo',
        'controlado',
        'estabilidad_horas',
        'estado',
        'semaforo_sanitario'
    ];

    protected $casts = [
        'requiere_refrigeracion' => 'boolean',
        'fotoproteccion'         => 'boolean',
        'alto_riesgo'            => 'boolean',
        'controlado'             => 'boolean',
        'estado'                 => 'boolean',
    ];

    public function laboratorioModel()
    {
        return $this->belongsTo(Laboratorio::class, 'laboratorio_id');
    }

    public function presentaciones()
    {
        return $this->hasMany(Presentacion::class);
    }

    public function inventarioLotes()
    {
        return $this->hasMany(InventarioLote::class);
    }
}