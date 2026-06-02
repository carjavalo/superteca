<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoCompatibilidad extends Model
{
    protected $table = 'catalogo_compatibilidades';

    protected $fillable = ['medicamento_1_id','medicamento_2_id','compatible','observaciones'];

    protected $casts = ['compatible' => 'boolean'];

    public function medicamento1() { return $this->belongsTo(Medicamento::class, 'medicamento_1_id'); }
    public function medicamento2() { return $this->belongsTo(Medicamento::class, 'medicamento_2_id'); }
}
