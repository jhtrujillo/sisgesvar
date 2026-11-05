<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vivero_parcelas', function (Blueprint $table) {
            $table->integer('proyecto_id')->nullable()->after('variedad_id');
        });

        // Poblar proyecto_id en parcelas existentes que tengan caracter_id
        try {
            DB::statement("
                UPDATE vivero_parcelas 
                SET proyecto_id = (
                    SELECT proyecto_id 
                    FROM proyecto_caracteres 
                    WHERE proyecto_caracteres.id = vivero_parcelas.caracter_id
                )
                WHERE caracter_id IS NOT NULL AND proyecto_id IS NULL
            ");
        } catch (\Exception $e) {
            // Ignorar si la subconsulta no aplica en el motor específico
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vivero_parcelas', function (Blueprint $table) {
            $table->dropColumn('proyecto_id');
        });
    }
};

