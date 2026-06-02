<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoInteraccion extends Model
{
    protected $table = 'catalogo_interacciones';

    public const SEVERIDADES = ['LEVE'=>'Leve','MODERADA'=>'Moderada','GRAVE'=>'Grave'];

    protected $fillable = ['medicamento_1_id','medicamento_2_id','severidad','descripcion'];

    public function medicamento1() { return $this->belongsTo(Medicamento::class, 'medicamento_1_id'); }
    public function medicamento2() { return $this->belongsTo(Medicamento::class, 'medicamento_2_id'); }
}
