<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MezclaControlCalidad extends Model
{
    protected $table = 'mezclas_control_calidad';

    protected $fillable = [
        'mezcla_id','fecha_control','aspecto_visual','volumen_verificado',
        'ph','osmolaridad','temperatura','cumple','observaciones','usuario_control_id'
    ];

    protected $casts = [
        'fecha_control' => 'datetime',
        'cumple' => 'boolean',
    ];

    public function mezcla() { return $this->belongsTo(Mezcla::class); }
    public function usuario(){ return $this->belongsTo(User::class, 'usuario_control_id'); }
}
