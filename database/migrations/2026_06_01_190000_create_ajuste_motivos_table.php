<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ajuste_motivos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->string('nombre', 150);
            $table->enum('tipo', ['POSITIVO', 'NEGATIVO', 'AMBOS'])->default('AMBOS');
            $table->boolean('requiere_observacion')->default(true);
            $table->boolean('requiere_aprobacion')->default(true);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajuste_motivos');
    }
};
