<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * Sincroniza el catálogo RBAC definido en config/permisos.php contra la base
 * de datos. La sincronización es ADITIVA: nunca borra ni modifica registros
 * existentes, solo agrega las acciones, módulos y vistas que falten.
 *
 * Gracias a esto, cuando se registra una nueva vista en config/permisos.php
 * aparece automáticamente en Gestión de Permisos sin programar nada más.
 */
class PermisosCatalogo
{
    /**
     * Inserta en BD las acciones y permisos del catálogo que aún no existan.
     *
     * @return array{acciones:int, permisos:int} Cantidad de registros nuevos.
     */
    public static function sync(): array
    {
        $config = config('permisos');
        $nuevasAcciones = 0;
        $nuevosPermisos = 0;

        // 1) Acciones
        $existentes = DB::table('acciones')->pluck('nombre')->all();
        foreach ($config['acciones'] ?? [] as $nombre) {
            if (! in_array($nombre, $existentes, true)) {
                DB::table('acciones')->insert(['nombre' => $nombre]);
                $existentes[] = $nombre;
                $nuevasAcciones++;
            }
        }

        // 2) Permisos (módulo → vistas)
        $existentes = DB::table('permisos')
            ->get(['modulo', 'vista'])
            ->map(fn ($p) => $p->modulo.'||'.$p->vista)
            ->all();

        $ahora = now();
        foreach ($config['modulos'] ?? [] as $moduloKey => $modulo) {
            foreach (array_values($modulo['vistas'] ?? []) as $orden => $vista) {
                $clave = $moduloKey.'||'.$vista;
                if (! in_array($clave, $existentes, true)) {
                    DB::table('permisos')->insert([
                        'modulo'      => $moduloKey,
                        'vista'       => $vista,
                        'descripcion' => null,
                        'orden'       => $orden,
                        'estado'      => 1,
                        'created_at'  => $ahora,
                        'updated_at'  => $ahora,
                    ]);
                    $existentes[] = $clave;
                    $nuevosPermisos++;
                }
            }
        }

        return ['acciones' => $nuevasAcciones, 'permisos' => $nuevosPermisos];
    }
}
