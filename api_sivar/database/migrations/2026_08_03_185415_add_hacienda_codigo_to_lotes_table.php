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
        Schema::connection('sivar')->table('lotes', function (Blueprint $table) {
            $table->string('hacienda_codigo')->nullable()->after('ingenio_codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sivar')->table('lotes', function (Blueprint $table) {
            $table->dropColumn(['hacienda_codigo']);
        });
    }
};
