<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    public const TIPOS = [
        'DISTRIBUIDOR'       => 'Distribuidor',
        'LABORATORIO'        => 'Laboratorio',
        'OPERADOR_LOGISTICO' => 'Operador Logístico',
        'DROGUERIA'          => 'Droguería',
        'INSUMOS'            => 'Insumos',
        'SERVICIOS'          => 'Servicios',
    ];

    protected $fillable = [
        'codigo',
        'tipo_proveedor',
        'razon_social',
        'nombre_comercial',
        'nit',
        'digito_verificacion',
        'registro_invima',
        'habilitacion_salud',
        'direccion',
        'ciudad',
        'departamento',
        'pais',
        'telefono',
        'celular',
        'email',
        'sitio_web',
        'contacto_comercial',
        'telefono_contacto',
        'email_contacto',
        'contacto_farmacovigilancia',
        'contacto_logistica',
        'condiciones_pago',
        'dias_credito',
        'maneja_cadena_frio',
        'temperatura_min',
        'temperatura_max',
        'tiempo_entrega_horas',
        'horario_entrega',
        'certificaciones',
        'observaciones',
        'logo',
        'estado',
    ];

    protected $casts = [
        'maneja_cadena_frio' => 'boolean',
        'estado'             => 'boolean',
        'dias_credito'       => 'integer',
        'tiempo_entrega_horas' => 'integer',
        'temperatura_min'    => 'decimal:2',
        'temperatura_max'    => 'decimal:2',
    ];

    public function inventarioLotes()
    {
        return $this->hasMany(InventarioLote::class, 'proveedor_id');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo_proveedor] ?? '—';
    }

    public function getNitCompletoAttribute(): string
    {
        return $this->digito_verificacion
            ? "{$this->nit}-{$this->digito_verificacion}"
            : (string) $this->nit;
    }
}
