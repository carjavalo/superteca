<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'user_name', 'role_name', 'event',
        'model_type', 'model_id', 'description',
        'route', 'method', 'url', 'ip', 'user_agent',
        'changes', 'created_at',
    ];

    protected $casts = [
        'changes'    => 'array',
        'created_at' => 'datetime',
    ];

    public const EVENTOS = [
        'LOGIN'   => 'Inicio de sesión',
        'LOGOUT'  => 'Cierre de sesión',
        'CREATED' => 'Creación',
        'UPDATED' => 'Actualización',
        'DELETED' => 'Eliminación',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEventoLabelAttribute(): string
    {
        return self::EVENTOS[$this->event] ?? $this->event;
    }

    public function getModelShortAttribute(): ?string
    {
        return $this->model_type ? class_basename($this->model_type) : null;
    }
}
