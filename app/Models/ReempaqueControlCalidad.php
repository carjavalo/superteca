<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReempaqueControlCalidad extends Model
{
    protected $table = 'reempaques_control_calidad';

    protected $fillable = [
        'reempaque_id','fecha_control','cantidad_verificada','etiquetado_correcto',
        'lote_visible','fecha_vencimiento_visible','cumple','observaciones','usuario_control_id',
    ];

    protected $casts = [
        'fecha_control' => 'datetime',
        'cantidad_verificada' => 'decimal:2',
        'etiquetado_correcto' => 'boolean',
        'lote_visible' => 'boolean',
        'fecha_vencimiento_visible' => 'boolean',
        'cumple' => 'boolean',
    ];

    public function reempaque() { return $this->belongsTo(Reempaque::class); }
    public function usuario() { return $this->belongsTo(User::class, 'usuario_control_id'); }
}
