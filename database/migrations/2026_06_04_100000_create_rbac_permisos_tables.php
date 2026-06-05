<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| RBAC — Centro de Gestión de Permisos
|--------------------------------------------------------------------------
| Crea las tablas del control de acceso basado en roles:
|   acciones · permisos · rol_permisos · usuario_permisos · auditoria_permisos
|
| Cada bloque está protegido con Schema::hasTable() para ser IDEMPOTENTE:
| en la base `superteca` actual las tablas ya existen (con datos), por lo
| que la migración se registra sin recrearlas; en una instalación nueva las
| crea desde cero replicando la estructura real.
*/
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('acciones')) {
            Schema::create('acciones', function (Blueprint $table) {
                $table->id();
                $table->string('nombre', 60)->unique();
            });
        }

        if (! Schema::hasTable('permisos')) {
            Schema::create('permisos', function (Blueprint $table) {
                $table->id();
                $table->string('modulo', 60)->index();
                $table->string('vista', 100);
                $table->string('descripcion', 150)->nullable();
                $table->integer('orden')->default(0);
                $table->boolean('estado')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('rol_permisos')) {
            Schema::create('rol_permisos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('rol_id');
                $table->unsignedBigInteger('permiso_id');
                $table->unsignedBigInteger('accion_id');
                $table->boolean('permitido')->default(false);
                $table->timestamp('created_at')->nullable();

                $table->unique(['rol_id', 'permiso_id', 'accion_id'], 'rol_permiso_accion_unique');
                $table->foreign('rol_id')->references('id')->on('roles')->cascadeOnDelete();
                $table->foreign('permiso_id')->references('id')->on('permisos')->cascadeOnDelete();
                $table->foreign('accion_id')->references('id')->on('acciones')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('usuario_permisos')) {
            Schema::create('usuario_permisos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('usuario_id');
                $table->unsignedBigInteger('permiso_id');
                $table->unsignedBigInteger('accion_id');
                $table->boolean('permitido')->default(false);
                $table->timestamp('created_at')->nullable();

                $table->unique(['usuario_id', 'permiso_id', 'accion_id'], 'usuario_permiso_accion_unique');
                $table->foreign('usuario_id')->references('id')->on('users')->cascadeOnDelete();
                $table->foreign('permiso_id')->references('id')->on('permisos')->cascadeOnDelete();
                $table->foreign('accion_id')->references('id')->on('acciones')->cascadeOnDelete();
            });
        }

        if (! Schema::hasTable('auditoria_permisos')) {
            Schema::create('auditoria_permisos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('usuario_id')->nullable();
                $table->unsignedBigInteger('rol_id')->nullable();
                $table->string('accion', 100);
                $table->text('descripcion')->nullable();
                $table->dateTime('fecha');
                $table->string('ip', 45)->nullable();

                $table->index('usuario_id');
                $table->index('rol_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria_permisos');
        Schema::dropIfExists('usuario_permisos');
        Schema::dropIfExists('rol_permisos');
        Schema::dropIfExists('permisos');
        Schema::dropIfExists('acciones');
    }
};
