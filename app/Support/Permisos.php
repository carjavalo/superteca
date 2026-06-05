<?php

namespace App\Support;

use App\Models\RolPermiso;
use Illuminate\Support\Facades\Auth;

/**
 * Verificador de permisos RBAC.
 *
 * Consulta las asignaciones de la tabla `rol_permisos` (módulo → vista → acción)
 * para el rol del usuario autenticado. El Super Admin siempre tiene acceso total.
 */
class Permisos
{
    /** Cache por petición: conjunto de "vista|accion" concedidos al rol. */
    protected static ?array $cache = null;
    protected static ?int $rolId = null;

    /**
     * Indica si el usuario autenticado puede ejecutar una acción sobre una vista.
     */
    public static function puede(string $vista, string $accion = 'Ver'): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        $role = $user->role;
        if (! $role) {
            return false;
        }

        // El Super Admin tiene acceso total por diseño.
        if ($role->name === 'Super Admin') {
            return true;
        }

        $set = static::permisosDelRol($role->id);

        return isset($set[mb_strtolower($vista) . '|' . mb_strtolower($accion)]);
    }

    /**
     * ¿Puede ver (o ejecutar la acción) en al menos UNA de las vistas indicadas?
     * Útil para decidir si se muestra el encabezado de un grupo del menú.
     */
    public static function puedeCualquiera(array $vistas, string $accion = 'Ver'): bool
    {
        foreach ($vistas as $vista) {
            if (static::puede($vista, $accion)) {
                return true;
            }
        }

        return false;
    }

    /** Módulo (clave) al que pertenece una vista, según config/permisos.php. */
    public static function moduloDeVista(string $vista): ?string
    {
        foreach (config('permisos.modulos', []) as $clave => $modulo) {
            foreach ($modulo['vistas'] ?? [] as $v) {
                if (mb_strtolower($v) === mb_strtolower($vista)) {
                    return $clave;
                }
            }
        }

        return null;
    }

    /**
     * Devuelve la acción si es configurable (columna de la matriz) para el módulo
     * de la vista; en caso contrario la degrada a «Ver». Evita exigir una acción
     * que el administrador no puede conceder desde la pantalla de permisos.
     */
    public static function accionAplicable(string $vista, string $accion): string
    {
        $modulo = static::moduloDeVista($vista);
        $columnas = config("permisos.matriz.{$modulo}", ['Ver', 'Crear', 'Editar', 'Eliminar']);

        return in_array($accion, $columnas, true) ? $accion : 'Ver';
    }

    /**
     * Conjunto de permisos concedidos al rol, indexado por "vista|accion".
     */
    protected static function permisosDelRol(int $rolId): array
    {
        if (static::$cache !== null && static::$rolId === $rolId) {
            return static::$cache;
        }

        $rows = RolPermiso::query()
            ->where('rol_permisos.rol_id', $rolId)
            ->where('rol_permisos.permitido', 1)
            ->join('permisos', 'permisos.id', '=', 'rol_permisos.permiso_id')
            ->join('acciones', 'acciones.id', '=', 'rol_permisos.accion_id')
            ->get(['permisos.vista as vista', 'acciones.nombre as accion']);

        $set = [];
        foreach ($rows as $r) {
            $set[mb_strtolower($r->vista) . '|' . mb_strtolower($r->accion)] = true;
        }

        static::$cache = $set;
        static::$rolId = $rolId;

        return $set;
    }
}
