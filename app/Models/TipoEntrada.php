<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Catálogo dinámico de Tipos de Entrada de inventario.
 *
 * Alimenta el campo «Tipo de entrada» del formulario de entradas. El valor que
 * se guarda en `entradas.tipo_entrada` es el «código» derivado del Detalle
 * (mayúsculas, sin acentos). Así, los 7 tipos sembrados coinciden exactamente
 * con las claves históricas (COMPRA, DONACION, …, ASIGNACION) y no se rompe la
 * data ya registrada ni la lógica de Asignación a paciente.
 */
class TipoEntrada extends Model
{
    protected $table = 'tipoEntrada';

    /** La tabla sólo tiene id, Detalle y Observacion (sin timestamps). */
    public $timestamps = false;

    protected $fillable = ['Detalle', 'Observacion'];

    /** Código interno derivado del Detalle (lo que se persiste en entradas.tipo_entrada). */
    public function getCodigoAttribute(): string
    {
        return static::codigoDesde($this->Detalle);
    }

    /** Normaliza un Detalle a un código estable: MAYÚSCULAS, sin acentos, sólo A-Z0-9 y "_". */
    public static function codigoDesde(?string $detalle): string
    {
        $ascii = Str::ascii((string) $detalle);
        $upper = mb_strtoupper(trim($ascii));
        $code  = preg_replace('/[^A-Z0-9]+/', '_', $upper);

        return trim($code, '_');
    }

    /**
     * Mapa [codigo => Detalle] cacheado por petición, para resolver etiquetas
     * de entradas sin generar consultas N+1 al listar.
     */
    public static function mapaCodigoDetalle(): array
    {
        static $cache = null;

        if ($cache === null) {
            try {
                $cache = static::query()->get(['Detalle'])
                    ->mapWithKeys(fn ($t) => [$t->codigo => $t->Detalle])
                    ->all();
            } catch (\Throwable $e) {
                // La tabla aún no existe (p. ej. antes de migrar): no romper la vista.
                $cache = [];
            }
        }

        return $cache;
    }

    /**
     * Códigos válidos para validar entradas.tipo_entrada: los del catálogo
     * dinámico más las claves legadas de Entrada::TIPOS (por compatibilidad).
     */
    public static function codigosDisponibles(): array
    {
        $delCatalogo = array_keys(static::mapaCodigoDetalle());
        $legados     = array_keys(Entrada::TIPOS);

        return array_values(array_unique(array_merge($delCatalogo, $legados)));
    }
}
