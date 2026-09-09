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
        Schema::create('vivero_caracteres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vivero_id');
            $table->unsignedBigInteger('caracter_id');
            $table->timestamps();

            $table->foreign('vivero_id')->references('id')->on('viveros')->onDelete('cascade');
            $table->foreign('caracter_id')->references('id')->on('proyecto_caracteres')->onDelete('cascade');
        });

        // Copy existing data from viveros to the pivot table
        \DB::statement('INSERT INTO vivero_caracteres (vivero_id, caracter_id, created_at, updated_at) SELECT id, caracter_id, created_at, updated_at FROM viveros WHERE caracter_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vivero_caracteres');
    }
};
