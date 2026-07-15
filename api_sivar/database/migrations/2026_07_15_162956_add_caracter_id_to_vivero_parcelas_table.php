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
            $table->unsignedBigInteger('caracter_id')->nullable()->after('numero_parcela_origen');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vivero_parcelas', function (Blueprint $table) {
            $table->dropColumn('caracter_id');
        });
    }
};
