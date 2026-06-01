<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('presentaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicamento_id')->constrained('medicamentos')->onDelete('cascade');
            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 255);
            $table->decimal('concentracion', 12, 2)->nullable();
            $table->string('unidad_concentracion', 20)->nullable();
            $table->decimal('volumen', 12, 2)->nullable();
            $table->string('unidad_volumen', 20)->nullable();
            $table->string('forma_farmaceutica', 100)->nullable();
            $table->string('via_administracion', 100)->nullable();
            $table->string('tipo_envase', 100)->nullable();
            $table->boolean('requiere_refrigeracion')->default(0);
            $table->string('foto', 255)->nullable();
            $table->boolean('estado')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presentaciones');
    }
};
