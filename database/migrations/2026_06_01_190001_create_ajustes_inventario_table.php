<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ajustes_inventario', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->enum('tipo_ajuste', ['POSITIVO', 'NEGATIVO']);
            $table->dateTime('fecha_ajuste');
            $table->foreignId('motivo_ajuste_id')->constrained('ajuste_motivos');
            $table->text('observaciones')->nullable();
            $table->enum('estado', ['BORRADOR', 'APROBADO', 'ANULADO'])->default('BORRADOR');
            $table->unsignedBigInteger('usuario_solicita_id')->nullable();
            $table->unsignedBigInteger('usuario_aprueba_id')->nullable();
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->string('evidencia_path')->nullable();
            $table->decimal('valor_total', 14, 2)->default(0);
            $table->timestamps();

            $table->foreign('usuario_solicita_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('usuario_aprueba_id')->references('id')->on('users')->nullOnDelete();
            $table->index('tipo_ajuste');
            $table->index('estado');
            $table->index('fecha_ajuste');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ajustes_inventario');
    }
};
