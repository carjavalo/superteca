<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presentaciones', function (Blueprint $table) {
            $table->unsignedBigInteger('via_administracion_id')->nullable()->after('via_administracion');
            $table->foreign('via_administracion_id')
                  ->references('id')->on('vias_administracion')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('presentaciones', function (Blueprint $table) {
            $table->dropForeign(['via_administracion_id']);
            $table->dropColumn('via_administracion_id');
        });
    }
};
