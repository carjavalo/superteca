<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Catálogo dinámico de Tipos de Administración (vías).
 *
 * Alimenta el campo «Tipo» del modal de Vías de Administración. El valor que se
 * guarda en `vias_administracion.tipo` es el «código» derivado del Detalle
 * (mayúsculas, sin acentos). Así, los 9 tipos sembrados coinciden exactamente
 * con las claves históricas (PARENTERAL, ENTERAL, …, VAGINAL) y no se rompe la
 * data ya registrada ni las etiquetas existentes.
 */
class TipoAdministracion extends Model
{
    protected $table = 'TipoAdministracion';

    /** La tabla sólo tiene id, Detalle y Observacion (sin timestamps). */
    public $timestamps = false;

    protected $fillable = ['Detalle', 'Observacion'];

    /** Código interno derivado del Detalle (lo que se persiste en vias_administracion.tipo). */
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
     * de vías sin generar consultas N+1 al listar.
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
     * Códigos válidos para validar vias_administracion.tipo: los del catálogo
     * dinámico más las claves legadas de ViaAdministracion::TIPOS.
     */
    public static function codigosDisponibles(): array
    {
        $delCatalogo = array_keys(static::mapaCodigoDetalle());
        $legados     = array_keys(ViaAdministracion::TIPOS);

        return array_values(array_unique(array_merge($delCatalogo, $legados)));
    }
}
