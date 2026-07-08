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
        Schema::create('lab_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('area')->nullable(); // Molecular, Genómica, etc.
            $table->string('consumible')->nullable(); // Nombre corto, tipo
            $table->string('actividad')->nullable(); // Electroforesis, etc.
            $table->string('codigo_cg1')->nullable(); // Código interno
            $table->string('descripcion_item')->nullable(); // Descripción completa
            $table->string('marca')->nullable();
            $table->string('unidad')->nullable();
            $table->decimal('cantidad_en_stock', 10, 2)->default(0);
            $table->decimal('cantidad_critica', 10, 2)->default(0);
            // La "condicion" será un atributo calculado en el Modelo
            $table->string('ubicacion')->nullable();
            $table->string('solicitante')->nullable();
            $table->date('fecha_solicitud')->nullable();
            $table->date('fecha_ultima_revision')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_inventories');
    }
};
