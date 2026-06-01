<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bodega extends Model
{
    use HasFactory;

    protected $fillable = ['codigo', 'nombre', 'tipo', 'ubicacion', 'responsable_id', 'estado'];

    protected $casts = ['estado' => 'boolean'];

    public const TIPOS = [
        'CENTRAL'  => 'Bodega Central',
        'FARMACIA' => 'Farmacia',
        'MEZCLAS'  => 'Central de Mezclas',
        'SERVICIO' => 'Servicio / Piso',
        'NEVERA'   => 'Nevera Especializada',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }
}
