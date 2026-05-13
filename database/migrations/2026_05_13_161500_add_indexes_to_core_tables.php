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
        Schema::table('ensayos', function (Blueprint $table) {
            $table->index('amb_seleccion');
            $table->index('ingenio');
            $table->index('ano_siembra');
            $table->index('proyecto');
            $table->index('serie');
        });

        Schema::table('cruzamientos', function (Blueprint $table) {
            $table->index('codigo');
            $table->index('madre');
            $table->index('padre');
            $table->index('fecha');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ensayos', function (Blueprint $table) {
            $table->dropIndex(['amb_seleccion']);
            $table->dropIndex(['ingenio']);
            $table->dropIndex(['ano_siembra']);
            $table->dropIndex(['proyecto']);
            $table->dropIndex(['serie']);
        });

        Schema::table('cruzamientos', function (Blueprint $table) {
            $table->dropIndex(['codigo']);
            $table->dropIndex(['madre']);
            $table->dropIndex(['padre']);
            $table->dropIndex(['fecha']);
        });
    }
};
