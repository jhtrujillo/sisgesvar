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
        Schema::create('vivero_parcelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vivero_id')->constrained('viveros')->onDelete('cascade');
            $table->integer('numero_parcela');
            $table->string('variedad_id')->nullable();
            $table->string('id_plot_origen')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vivero_parcelas');
    }
};
