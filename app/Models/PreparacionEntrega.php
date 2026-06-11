<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreparacionEntrega extends Model
{
    protected $table = 'preparaciones_entrega';

    protected $fillable = [
        'preparacion_id','paciente_id','fecha_entrega',
        'usuario_entrega_id','servicio_destino_id','recibido_por','observaciones',
    ];

    protected $casts = [
        'fecha_entrega' => 'datetime',
    ];

    public function preparacion(): BelongsTo { return $this->belongsTo(Preparacion::class, 'preparacion_id'); }
    public function paciente(): BelongsTo { return $this->belongsTo(Paciente::class); }
    public function usuario(): BelongsTo { return $this->belongsTo(User::class, 'usuario_entrega_id'); }
    public function servicio(): BelongsTo { return $this->belongsTo(TipoServicios::class, 'servicio_destino_id'); }
}
