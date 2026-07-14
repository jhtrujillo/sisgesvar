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
        Schema::create('viveros', function (Blueprint $table) {
            $table->id();
            $table->string('identificador_unico')->unique();
            $table->string('nombre');
            $table->string('ingenio')->nullable();
            $table->string('hacienda')->nullable();
            $table->string('suerte')->nullable();
            $table->integer('proyecto_id')->nullable();
            $table->string('caracter')->nullable();
            $table->string('responsable_id')->nullable();
            $table->date('fecha_siembra');
            $table->integer('numero_corte')->default(1);
            $table->string('temporada_floracion')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viveros');
    }
};
