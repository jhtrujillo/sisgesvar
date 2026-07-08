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
        Schema::create('lab_inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_inventory_id')->constrained('lab_inventories')->onDelete('cascade');
            $table->string('tipo_movimiento'); // 'INGRESO' o 'EGRESO'
            $table->decimal('cantidad', 10, 2);
            $table->decimal('stock_anterior', 10, 2);
            $table->decimal('stock_nuevo', 10, 2);
            $table->string('user_id')->nullable();
            $table->string('user_name')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lab_inventory_movements');
    }
};
