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
        Schema::connection('sivar')->create('proyecto_permisos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('proyecto_id'); // ID del proyecto en remote_pg_sipro (id_prycto)
            $table->bigInteger('usuario_id');  // ID del usuario en tabla usuario (id_usrio)
            $table->string('rol', 20)->default('VIEWER'); // ADMIN, EDITOR, VIEWER
            $table->bigInteger('asignado_por')->nullable();
            $table->timestamps();

            $table->unique(['proyecto_id', 'usuario_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('sivar')->dropIfExists('proyecto_permisos');
    }
};
