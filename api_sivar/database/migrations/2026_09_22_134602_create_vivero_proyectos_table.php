<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vivero_proyectos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vivero_id')->constrained('viveros')->onDelete('cascade');
            $table->integer('proyecto_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vivero_proyectos');
    }
};
