<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RecepcionTraslado extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'traslado_id', 'fecha_recepcion', 'usuario_id', 'observaciones', 'recibido_completo',
    ];

    protected $casts = [
        'fecha_recepcion'   => 'datetime',
        'recibido_completo' => 'boolean',
        'created_at'        => 'datetime',
    ];

    public function traslado() { return $this->belongsTo(Traslado::class); }
    public function usuario()  { return $this->belongsTo(User::class); }
}
