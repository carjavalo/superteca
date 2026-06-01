<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presentaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('forma_farmaceutica_id')->nullable()->after('forma_farmaceutica');
            $table->foreign('forma_farmaceutica_id')
                  ->references('id')->on('formas_farmaceuticas')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('presentaciones', function (Blueprint $table) {
            $table->dropForeign(['forma_farmaceutica_id']);
            $table->dropColumn('forma_farmaceutica_id');
        });
    }
};
