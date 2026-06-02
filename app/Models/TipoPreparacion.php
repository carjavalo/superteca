<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoPreparacion extends Model
{
    protected $table = 'tipo_preparaciones';

    protected $fillable = ['nombre','codigo','descripcion','color','estado'];

    protected $casts = ['estado' => 'boolean'];
}
