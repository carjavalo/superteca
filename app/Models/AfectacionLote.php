<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AfectacionLote extends Model
{
    protected $table = 'afectacion_lotes';

    protected $fillable = [
        'alerta_id','inventario_lote_id','estado','observaciones','usuario_id',
    ];

    public function alerta()         { return $this->belongsTo(AlertaCadenaFrio::class, 'alerta_id'); }
    public function inventarioLote() { return $this->belongsTo(InventarioLote::class, 'inventario_lote_id'); }
    public function usuario()        { return $this->belongsTo(\App\Models\User::class, 'usuario_id'); }
}
