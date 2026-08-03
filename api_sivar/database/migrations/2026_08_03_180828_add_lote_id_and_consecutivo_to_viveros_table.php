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
            $table->foreignId('lote_id')->nullable()->constrained('lotes')->onDelete('set null');
            $table->integer('consecutivo_vivero_ingenio')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viveros', function (Blueprint $table) {
            $table->dropForeign(['lote_id']);
            $table->dropColumn(['lote_id', 'consecutivo_vivero_ingenio']);
        });
    }
};
