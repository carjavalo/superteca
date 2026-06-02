<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reempaque extends Model
{
    use HasFactory;

    protected $table = 'reempaques';

    public const ESTADOS = [
        'PROGRAMADO' => 'Programado',
        'EN_PROCESO' => 'En Proceso',
        'CONTROL_CALIDAD' => 'Control de Calidad',
        'LIBERADO' => 'Liberado',
        'ANULADO' => 'Anulado',
    ];

    protected $fillable = [
        'codigo','fecha_programada','fecha_inicio','fecha_fin',
        'medicamento_origen_id','presentacion_origen_id','presentacion_destino_id',
        'unidad_medida_destino_id','factor_conversion','cantidad_esperada',
        'observaciones','estado','costo_total',
        'usuario_responsable_id','usuario_aprobador_id',
    ];

    protected $casts = [
        'fecha_programada' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'factor_conversion' => 'decimal:4',
        'cantidad_esperada' => 'decimal:2',
        'costo_total' => 'decimal:2',
    ];

    protected static function booted()
    {
        static::creating(function ($r) {
            if (empty($r->codigo)) {
                $year = now()->year;
                $last = static::where('codigo', 'like', "REP-{$year}-%")->orderByDesc('id')->first();
                $n = 1;
                if ($last) {
                    $parts = explode('-', $last->codigo);
                    $n = (int) end($parts) + 1;
                }
                $r->codigo = sprintf('REP-%s-%05d', $year, $n);
            }
        });
    }

    public function medicamentoOrigen() { return $this->belongsTo(Medicamento::class, 'medicamento_origen_id'); }
    public function presentacionOrigen() { return $this->belongsTo(Presentacion::class, 'presentacion_origen_id'); }
    public function presentacionDestino() { return $this->belongsTo(Presentacion::class, 'presentacion_destino_id'); }
    public function unidadDestino() { return $this->belongsTo(UnidadMedida::class, 'unidad_medida_destino_id'); }
    public function responsable() { return $this->belongsTo(User::class, 'usuario_responsable_id'); }
    public function aprobador() { return $this->belongsTo(User::class, 'usuario_aprobador_id'); }
    public function detalles() { return $this->hasMany(ReempaqueDetalle::class); }
    public function consumos() { return $this->hasMany(ReempaqueConsumo::class); }
    public function productosFinales() { return $this->hasMany(ReempaqueProductoFinal::class); }
    public function controles() { return $this->hasMany(ReempaqueControlCalidad::class); }
}
