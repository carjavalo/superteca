<?php

namespace App\Http\Middleware;

use App\Support\Permisos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Enforcement RBAC a nivel de ruta.
 *
 * Bloquea el acceso a una ruta si el rol del usuario no tiene la acción «Ver»
 * sobre la vista mapeada en config('permisos.rutas'). Reglas:
 *   - El Super Admin nunca se bloquea (acceso total por diseño).
 *   - Las rutas sin mapeo (perfil, dashboard, etc.) quedan permitidas.
 *   - Se usa el prefijo de ruta más largo que coincida.
 */
class VerificarPermiso
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Sin usuario: que lo resuelva el middleware de autenticación.
        if (! $user) {
            return $next($request);
        }

        // Super Admin: acceso total.
        if (optional($user->role)->name === 'Super Admin') {
            return $next($request);
        }

        $routeName = optional($request->route())->getName();
        if (! $routeName) {
            return $next($request);
        }

        $vista = $this->vistaDeRuta($routeName);

        // Ruta no mapeada: permitida.
        if ($vista === null) {
            return $next($request);
        }

        // Acción exigida según el último segmento del nombre de ruta
        // (index→Ver, store→Crear, update→Editar, destroy→Eliminar,
        //  exportar→Exportar, confirm/liberar→Aprobar, anular→Anular, …).
        $sufijo = (string) \Illuminate\Support\Str::afterLast($routeName, '.');
        $accion = config("permisos.acciones_ruta.{$sufijo}", 'Ver');
        $accion = Permisos::accionAplicable($vista, $accion);

        if (Permisos::puede($vista, $accion)) {
            return $next($request);
        }

        abort(403, "No tienes permiso para «{$accion}» en «{$vista}». Contacta al administrador del sistema.");
    }

    /** Devuelve la vista asociada a la ruta usando el prefijo más largo que coincida. */
    private function vistaDeRuta(string $routeName): ?string
    {
        $mejorVista = null;
        $mejorLongitud = -1;

        foreach (config('permisos.rutas', []) as $prefijo => $vista) {
            if ($routeName === $prefijo || str_starts_with($routeName, $prefijo . '.')) {
                if (strlen($prefijo) > $mejorLongitud) {
                    $mejorVista = $vista;
                    $mejorLongitud = strlen($prefijo);
                }
            }
        }

        return $mejorVista;
    }
}
