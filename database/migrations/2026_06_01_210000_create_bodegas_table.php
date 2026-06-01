<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bodegas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique();
            $table->string('nombre', 150);
            $table->enum('tipo', ['CENTRAL', 'FARMACIA', 'MEZCLAS', 'SERVICIO', 'NEVERA'])->default('CENTRAL');
            $table->string('ubicacion', 255)->nullable();
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();

            $table->foreign('responsable_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bodegas');
    }
};
