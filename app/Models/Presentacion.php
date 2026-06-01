<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presentacion extends Model
{
    use HasFactory;

    protected $table = 'presentaciones';

    protected $fillable = [
        'medicamento_id',
        'codigo',
        'nombre',
        'concentracion',
        'unidad_concentracion',
        'volumen',
        'unidad_volumen',
        'forma_farmaceutica',
        'forma_farmaceutica_id',
        'via_administracion',
        'via_administracion_id',
        'unidad_medida_id',
        'unidad_volumen_id',
        'tipo_envase',
        'requiere_refrigeracion',
        'foto',
        'estado',
        'semaforo_sanitario',
    ];

    protected $casts = [
        'concentracion' => 'decimal:2',
        'volumen' => 'decimal:2',
        'requiere_refrigeracion' => 'boolean',
        'estado' => 'boolean',
    ];

    public function medicamento()
    {
        return $this->belongsTo(Medicamento::class);
    }

    public function inventarioLotes()
    {
        return $this->hasMany(InventarioLote::class);
    }

    public function formaFarmaceutica()
    {
        return $this->belongsTo(FormaFarmaceutica::class, 'forma_farmaceutica_id');
    }

    public function viaAdministracion()
    {
        return $this->belongsTo(ViaAdministracion::class, 'via_administracion_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida_id');
    }

    public function unidadVolumen()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_volumen_id');
    }
}
