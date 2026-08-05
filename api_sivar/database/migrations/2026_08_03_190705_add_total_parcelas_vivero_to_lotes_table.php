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
            $table->integer('total_parcelas_vivero')->default(10)->after('capacidad_maxima');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sivar')->table('lotes', function (Blueprint $table) {
            $table->dropColumn(['total_parcelas_vivero']);
        });
    }
};
