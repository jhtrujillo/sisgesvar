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
        Schema::connection('sivar')->table('viveros', function (Blueprint $table) {
            $table->string('origen_ingenio')->nullable()->after('numero_corte');
            $table->string('origen_hacienda')->nullable()->after('origen_ingenio');
            $table->string('origen_suerte')->nullable()->after('origen_hacienda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sivar')->table('viveros', function (Blueprint $table) {
            $table->dropColumn(['origen_ingenio', 'origen_hacienda', 'origen_suerte']);
        });
    }
};
