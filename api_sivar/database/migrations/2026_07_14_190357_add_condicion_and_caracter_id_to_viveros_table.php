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
            $table->string('condicion')->nullable();
            $table->unsignedBigInteger('caracter_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viveros', function (Blueprint $table) {
            $table->dropColumn(['condicion', 'caracter_id']);
        });
    }
};
