<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Rename current to backup
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('ambiente', 'ambiente_old');
        });

        // 2. Create the new JSONB column
        Schema::table('users', function (Blueprint $table) {
            $table->jsonb('ambiente')->nullable()->after('ambiente_old');
        });

        // 3. Migrate old data to new JSON format: "VALLE" becomes ["VALLE"]
        $users = DB::table('users')->whereNotNull('ambiente_old')->get();
        foreach ($users as $user) {
            if (!empty($user->ambiente_old)) {
                DB::table('users')
                  ->where('id', $user->id)
                  ->update(['ambiente' => json_encode([trim($user->ambiente_old)])]);
            }
        }

        // 4. Drop the old column
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ambiente_old');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ambiente_old')->nullable();
        });

        // Rollback simple: take first element of array
        $users = DB::table('users')->whereNotNull('ambiente')->get();
        foreach ($users as $user) {
             $arr = json_decode($user->ambiente, true);
             if (is_array($arr) && !empty($arr)) {
                  DB::table('users')->where('id', $user->id)->update(['ambiente_old' => $arr[0]]);
             }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ambiente');
            $table->renameColumn('ambiente_old', 'ambiente');
        });
    }
};
