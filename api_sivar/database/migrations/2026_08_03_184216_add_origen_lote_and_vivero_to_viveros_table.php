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
            $table->unsignedBigInteger('origen_lote_id')->nullable()->after('origen_suerte');
            $table->unsignedBigInteger('origen_vivero_id')->nullable()->after('origen_lote_id');

            $table->foreign('origen_lote_id')->references('id')->on('lotes')->onDelete('set null');
            $table->foreign('origen_vivero_id')->references('id')->on('viveros')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sivar')->table('viveros', function (Blueprint $table) {
            $table->dropForeign(['origen_vivero_id']);
            $table->dropForeign(['origen_lote_id']);
            $table->dropColumn(['origen_vivero_id', 'origen_lote_id']);
        });
    }
};
