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
        Schema::table('viveros', function (Blueprint $table) {
            $table->integer('origen_anio')->nullable()->after('origen_suerte');
            $table->string('origen_parcela')->nullable()->after('origen_anio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viveros', function (Blueprint $table) {
            $table->dropColumn(['origen_anio', 'origen_parcela']);
        });
    }
};
