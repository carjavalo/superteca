<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidenteSeguimiento extends Model
{
    protected $table = 'incidentes_seguimiento';

    protected $fillable = [
        'incidente_id','fecha','comentario','usuario_id',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function incidente(): BelongsTo
    {
        return $this->belongsTo(Incidente::class, 'incidente_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
