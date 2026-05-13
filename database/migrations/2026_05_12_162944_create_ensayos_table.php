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
        Schema::create('ensayos', function (Blueprint $table) {
            $table->id();
            
            // Identificadores
            $table->string('nombre_ensayo')->nullable();
            $table->string('nombre_env')->nullable();
            
            // Descripciones Generales
            $table->string('proyecto')->nullable();
            $table->string('estado_seleccion')->nullable();
            $table->string('serie')->nullable();
            $table->string('amb_seleccion')->nullable(); // Clave para filtrado de ambiente
            $table->string('amb_evaluacion')->nullable();
            $table->string('objetivo')->nullable();
            
            // Localización / Hacienda
            $table->string('ingenio')->nullable();
            $table->string('hacienda')->nullable();
            $table->string('suerte')->nullable();
            $table->string('zona_agroecologia')->nullable();
            $table->string('consociacion')->nullable();
            
            // Datos Técnicos Numéricos y Métricas
            $table->string('corte')->nullable(); // Texto seguro para 1=plantilla etc.
            $table->integer('entradas')->nullable();
            $table->integer('testigos')->nullable();
            $table->integer('clones')->nullable();
            $table->integer('total_parcelas')->nullable();
            $table->string('diseno')->nullable();
            $table->integer('surcos')->nullable();
            $table->decimal('longitud_surco', 10, 2)->nullable();
            $table->decimal('longitud_callejon', 10, 2)->nullable();
            $table->decimal('distancia_surco', 10, 2)->nullable();
            $table->decimal('area_total', 12, 4)->nullable();
            
            // Tiempos y Clima
            $table->string('red_meteorologica')->nullable();
            $table->date('fecha_siembra')->nullable();
            $table->date('fecha_cosecha_final')->nullable();
            $table->date('fecha_evaluacion')->nullable();
            $table->integer('meses_evaluacion')->nullable();
            $table->date('fecha_cosecha_programada')->nullable();
            
            // Campos de Estado y Auditoría interna
            $table->string('estado_actual')->nullable();
            $table->integer('ano_siembra')->nullable();
            $table->integer('mes_siembra')->nullable();
            $table->string('tipo_cosecha')->nullable();
            $table->text('comentarios')->nullable();
            $table->string('ubicacion_fisica')->nullable();
            $table->string('nombre_reporte')->nullable();
            $table->string('investigador')->nullable();

            // Relación de autoría / Seguridad
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ensayos');
    }
};
