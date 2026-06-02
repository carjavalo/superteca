<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispensacionEntregaRecepcion extends Model
{
    protected $table = 'dispensacion_entregas_recepcion';

    public $timestamps = false;

    protected $fillable = [
        'entrega_id','fecha_recepcion','usuario_recibe_id','recibe_nombre',
        'recibe_documento','observaciones','recibido','created_at',
    ];

    protected $casts = [
        'fecha_recepcion' => 'datetime',
        'recibido'        => 'boolean',
        'created_at'      => 'datetime',
    ];

    public function entrega() { return $this->belongsTo(DispensacionEntrega::class, 'entrega_id'); }
    public function usuario() { return $this->belongsTo(User::class, 'usuario_recibe_id'); }
}
