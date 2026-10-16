<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $birdenFazlaRolVar = DB::table('role_user')
            ->select('user_id')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if ($birdenFazlaRolVar) {
            throw new RuntimeException(
                'Birden fazla role sahip kullanıcı var. Önce rol bağlantılarını düzeltin.'
            );
        }

        Schema::table('role_user', function (Blueprint $table): void {
            $table->unique('user_id', 'role_user_user_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_user', function (Blueprint $table): void {
            $table->dropUnique('role_user_user_id_unique');
        });
    }
};
