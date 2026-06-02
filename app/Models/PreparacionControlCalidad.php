<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreparacionControlCalidad extends Model
{
    protected $table = 'preparaciones_control_calidad';

    protected $fillable = [
        'preparacion_id','fecha_control','aspecto_visual','volumen_verificado',
        'ph','osmolaridad','cumple','observaciones','usuario_control_id',
    ];

    protected $casts = [
        'fecha_control'      => 'datetime',
        'volumen_verificado' => 'decimal:2',
        'ph'                 => 'decimal:2',
        'osmolaridad'        => 'decimal:2',
        'cumple'             => 'boolean',
    ];

    public function preparacion(): BelongsTo { return $this->belongsTo(Preparacion::class, 'preparacion_id'); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'usuario_control_id'); }
}
