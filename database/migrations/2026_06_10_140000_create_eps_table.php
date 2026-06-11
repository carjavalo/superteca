<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Crea el catálogo `EPS`, que alimenta el campo «EPS» del registro de pacientes
 * y de cualquier otra vista del sistema donde se diligencie la EPS.
 *
 * Estructura solicitada:
 *   id          BIGINT  auto-increment  PRIMARY KEY
 *   Detalle     VARCHAR(120)  -> nombre de la EPS
 *   Estado      BOOLEAN       -> 1=Activo, 0=Inactivo (las inactivas NO se
 *                                ofrecen en los selects del sistema)
 *   Observacion VARCHAR(300)  -> nota / objetivo
 *
 * Como el campo `pacientes.eps` es texto libre, el select guarda el nombre
 * (Detalle) de la EPS; no se cambia el esquema de pacientes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('EPS', function (Blueprint $table) {
            $table->id();
            $table->string('Detalle', 120);
            $table->boolean('Estado')->default(1);   // 1=Activo, 0=Inactivo
            $table->string('Observacion', 300)->nullable();
        });

        // Semilla con EPS de uso común en Colombia (Valle del Cauca incluido),
        // todas activas. El usuario puede agregar, editar o inactivar desde el CRUD.
        $eps = [
            'Nueva EPS',
            'EPS Sura',
            'EPS Sanitas',
            'Salud Total EPS',
            'Coomeva EPS',
            'S.O.S. - Servicio Occidental de Salud',
            'Coosalud EPS',
            'Asmet Salud EPS',
            'Emssanar EPS',
            'Famisanar EPS',
            'Compensar EPS',
            'Aliansalud EPS',
            'Mutual Ser EPS',
            'Cajacopi EPS',
        ];

        foreach ($eps as $nombre) {
            if (! DB::table('EPS')->where('Detalle', $nombre)->exists()) {
                DB::table('EPS')->insert([
                    'Detalle'     => $nombre,
                    'Estado'      => 1,
                    'Observacion' => null,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('EPS');
    }
};
