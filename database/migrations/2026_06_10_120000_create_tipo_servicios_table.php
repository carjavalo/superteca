<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Crea el catálogo dinámico `TipoServicios`, que alimenta el campo «Servicio»
 * del registro de pacientes (/admin/dispensacion/pacientes/crear).
 *
 * Estructura solicitada:
 *   id          BIGINT  auto-increment  PRIMARY KEY
 *   Detalle     VARCHAR(120)  -> nombre del servicio
 *   Observacion VARCHAR(300)  -> objetivo / descripción del servicio
 *
 * Se siembra con los servicios que hoy alimentan el select (tabla compartida
 * `servicios_hospitalarios`). El campo «Servicio» de pacientes pasa a guardar el
 * id de ESTE catálogo (ver migración que libera la FK y el modelo Paciente).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TipoServicios', function (Blueprint $table) {
            $table->id();
            $table->string('Detalle', 120);
            $table->string('Observacion', 300)->nullable();
        });

        // Descripciones amigables para los servicios conocidos; el resto recibe
        // una observación genérica (editable luego desde el CRUD).
        $desc = [
            'UCI Adultos'      => 'Unidad de Cuidados Intensivos para pacientes adultos en estado crítico.',
            'UCI Pediátrica'   => 'Unidad de Cuidados Intensivos para pacientes pediátricos.',
            'UCI Neonatal'     => 'Unidad de Cuidados Intensivos para recién nacidos.',
            'Urgencias'        => 'Servicio de atención de urgencias y emergencias.',
            'Hospitalización'  => 'Servicio de hospitalización general de pacientes.',
            'Oncología'        => 'Servicio de atención y tratamiento oncológico.',
        ];

        // Copia los servicios actuales del catálogo compartido (lo que hoy ve el
        // usuario en el select). Si estuviera vacío, usa un set por defecto.
        $servicios = DB::table('servicios_hospitalarios')->orderBy('id')->pluck('nombre');
        if ($servicios->isEmpty()) {
            $servicios = collect(array_keys($desc));
        }

        foreach ($servicios as $nombre) {
            if (! DB::table('TipoServicios')->where('Detalle', $nombre)->exists()) {
                DB::table('TipoServicios')->insert([
                    'Detalle'     => $nombre,
                    'Observacion' => $desc[$nombre] ?? 'Servicio o área hospitalaria donde se encuentra ubicado el paciente.',
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('TipoServicios');
    }
};
