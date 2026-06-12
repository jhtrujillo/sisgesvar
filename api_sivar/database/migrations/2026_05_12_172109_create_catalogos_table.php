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
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();
            $table->string('categoria'); // e.g. 'PROYECTO', 'INGENIO', 'AMBIENTE'
            $table->string('valor');     // e.g. 'PIEDEMONTE'
            $table->string('alias')->nullable(); // Opcional, para búsquedas aproximadas futuras
            $table->timestamps();

            $table->unique(['categoria', 'valor']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalogos');
    }
};
