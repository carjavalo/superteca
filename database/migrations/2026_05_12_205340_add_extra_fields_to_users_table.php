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
        Schema::table('users', function (Blueprint $table) {
            $table->string('apellido1', 100)->nullable()->after('name');
            $table->string('apellido2', 100)->nullable()->after('apellido1');
            $table->string('cedula', 30)->nullable()->unique()->after('apellido2');
            $table->string('contacto', 30)->nullable()->after('cedula');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['apellido1', 'apellido2', 'cedula', 'contacto']);
        });
    }
};
