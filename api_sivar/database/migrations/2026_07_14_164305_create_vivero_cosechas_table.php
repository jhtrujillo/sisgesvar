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
        Schema::create('vivero_cosechas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vivero_id')->constrained('viveros')->onDelete('cascade');
            $table->date('fecha_cosecha');
            $table->date('nueva_fecha_siembra');
            $table->integer('numero_corte_anterior');
            $table->string('ambiente')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vivero_cosechas');
    }
};
