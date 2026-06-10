<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Crea el catálogo dinámico `TipoAdministracion`, que alimenta el campo «Tipo»
 * del modal de Vías de Administración.
 *
 * Estructura solicitada:
 *   id          BIGINT  auto-increment  PRIMARY KEY
 *   Detalle     VARCHAR(120)  -> nombre del tipo de administración
 *   Observacion VARCHAR(300)  -> objetivo / para qué se usa el tipo
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('TipoAdministracion', function (Blueprint $table) {
            $table->id();
            $table->string('Detalle', 120);
            $table->string('Observacion', 300)->nullable();
        });

        // Semilla: los tipos que hoy existen en el select «Tipo» del modal
        // (constante ViaAdministracion::TIPOS). El Detalle guarda la etiqueta
        // legible; el código interno (PARENTERAL, ENTERAL, …) se deriva de él en
        // el modelo, conservando la compatibilidad con las vías ya registradas.
        $tipos = [
            ['Detalle' => 'Parenteral',   'Observacion' => 'Administración mediante inyección o infusión (IV, IM, SC), evitando el tracto digestivo.'],
            ['Detalle' => 'Enteral',      'Observacion' => 'Administración a través del tracto gastrointestinal (oral, sonda, sublingual).'],
            ['Detalle' => 'Tópica',       'Observacion' => 'Aplicación local sobre la piel o mucosas, generalmente para efecto local.'],
            ['Detalle' => 'Respiratoria', 'Observacion' => 'Administración por vía inhalatoria hacia las vías respiratorias y los pulmones.'],
            ['Detalle' => 'Oftálmica',    'Observacion' => 'Aplicación en el ojo para tratamiento local oftalmológico.'],
            ['Detalle' => 'Ótica',        'Observacion' => 'Administración en el conducto auditivo para tratamiento del oído.'],
            ['Detalle' => 'Nasal',        'Observacion' => 'Administración en la mucosa nasal para efecto local o sistémico.'],
            ['Detalle' => 'Rectal',       'Observacion' => 'Administración por el recto (supositorios, enemas) para efecto local o sistémico.'],
            ['Detalle' => 'Vaginal',      'Observacion' => 'Administración intravaginal para tratamiento local ginecológico.'],
        ];

        foreach ($tipos as $t) {
            if (! DB::table('TipoAdministracion')->where('Detalle', $t['Detalle'])->exists()) {
                DB::table('TipoAdministracion')->insert($t);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('TipoAdministracion');
    }
};
