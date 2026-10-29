<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ampliar el campo rol para soportar SUPER_ADMIN y asignar
     * permisos globales a los usuarios que antes tenían acceso hardcodeado.
     */
    public function up(): void
    {
        // Ampliar el campo rol de 20 a 30 caracteres para soportar SUPER_ADMIN
        Schema::connection('sivar')->table('proyecto_permisos', function (Blueprint $table) {
            $table->string('rol', 30)->default('VIEWER')->change();
        });

        // Obtener IDs de los usuarios que antes eran super-admin hardcodeados
        // para asignarles el rol SUPER_ADMIN en un proyecto "global" (proyecto_id = 0)
        $superAdminLogins = ['estuvar4', 'auxsisvar1'];
        $superAdminEmails = ['jhtrujillo@cenicana.org', 'srodriguez@cenicana.org'];

        $userIds = DB::connection('sivar')->table('usuario')
            ->where(function ($q) use ($superAdminLogins, $superAdminEmails) {
                $q->whereIn(DB::raw('LOWER(lgin)'), $superAdminLogins)
                  ->orWhereIn(DB::raw('LOWER(email)'), $superAdminEmails);
            })
            ->pluck('id_usrio');

        foreach ($userIds as $userId) {
            DB::connection('sivar')->table('proyecto_permisos')->updateOrInsert(
                [
                    'proyecto_id' => 0, // Proyecto global (super-admin)
                    'usuario_id' => $userId,
                ],
                [
                    'rol' => 'SUPER_ADMIN',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Eliminar registros de SUPER_ADMIN global
        DB::connection('sivar')->table('proyecto_permisos')
            ->where('proyecto_id', 0)
            ->where('rol', 'SUPER_ADMIN')
            ->delete();

        // Revertir el tamaño del campo
        Schema::connection('sivar')->table('proyecto_permisos', function (Blueprint $table) {
            $table->string('rol', 20)->default('VIEWER')->change();
        });
    }
};

