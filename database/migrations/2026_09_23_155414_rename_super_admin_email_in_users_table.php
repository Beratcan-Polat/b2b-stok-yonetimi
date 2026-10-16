<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $yeniEmailKullaniliyor = DB::table('users')
            ->where('email', 'superadmin@b2b.test')
            ->exists();

        if (! $yeniEmailKullaniliyor) {
            DB::table('users')
                ->where('email', 'admin@b2b.test')
                ->update([
                    'email' => 'superadmin@b2b.test',
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $eskiEmailKullaniliyor = DB::table('users')
            ->where('email', 'admin@b2b.test')
            ->exists();

        if (! $eskiEmailKullaniliyor) {
            DB::table('users')
                ->where('email', 'superadmin@b2b.test')
                ->update([
                    'email' => 'admin@b2b.test',
                ]);
        }
    }
};
