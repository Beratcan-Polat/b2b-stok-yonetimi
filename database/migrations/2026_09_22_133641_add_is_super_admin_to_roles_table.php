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
        Schema::table('roles', function (Blueprint $table): void {
            $table->boolean('is_super_admin')->default(false)->after('label');
        });

        DB::table('roles')
            ->where('name', 'admin')
            ->update([
                'name' => 'super-admin',
                'label' => 'Süper Admin',
                'is_super_admin' => true,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropColumn('is_super_admin');
        });
    }
};
