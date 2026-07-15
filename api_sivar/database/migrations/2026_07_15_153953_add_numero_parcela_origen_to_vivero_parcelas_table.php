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
        Schema::table('vivero_parcelas', function (Blueprint $table) {
            $table->integer('numero_parcela_origen')->nullable()->after('variedad_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vivero_parcelas', function (Blueprint $table) {
            $table->dropColumn('numero_parcela_origen');
        });
    }
};
