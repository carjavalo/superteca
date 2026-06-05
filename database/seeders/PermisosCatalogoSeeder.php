<?php

namespace Database\Seeders;

use App\Support\PermisosCatalogo;
use Illuminate\Database\Seeder;

class PermisosCatalogoSeeder extends Seeder
{
    /**
     * Sincroniza (de forma aditiva) el catálogo de acciones y permisos
     * definido en config/permisos.php hacia la base de datos `superteca`.
     */
    public function run(): void
    {
        $resultado = PermisosCatalogo::sync();

        $this->command?->info(
            "Catálogo RBAC sincronizado: {$resultado['acciones']} acciones nuevas, "
            ."{$resultado['permisos']} permisos nuevos."
        );
    }
}
