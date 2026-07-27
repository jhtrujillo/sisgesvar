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
        Schema::connection('sivar')->table('ponderados_valor_merito', function (Blueprint $table) {
            $table->string('ambiente')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('sivar')->table('ponderados_valor_merito', function (Blueprint $table) {
            $table->dropColumn('ambiente');
        });
    }
};
