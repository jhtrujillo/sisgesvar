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
        Schema::create('cruzamientos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable();
            $table->string('madre')->nullable();
            $table->string('padre')->nullable();
            $table->string('tipo_cruzamiento')->nullable();
            $table->date('fecha')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cruzamientos');
    }
};
