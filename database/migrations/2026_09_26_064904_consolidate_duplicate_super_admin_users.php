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
        DB::transaction(function (): void {
            if (! DB::table('users')->where('email', 'admin@b2b.test')->exists()) {
                return;
            }

            $asilKullanici = DB::table('users')
                ->where('id', 1)
                ->lockForUpdate()
                ->first();

            $tekrarKullanici = DB::table('users')
                ->where('id', 6)
                ->lockForUpdate()
                ->first();

            if (
                ! $asilKullanici
                || $asilKullanici->email !== 'admin@b2b.test'
                || ! $tekrarKullanici
                || $tekrarKullanici->email !== 'superadmin@b2b.test'
            ) {
                throw new RuntimeException(
                    'Kullanıcı kayıtları beklenen durumda değil. İşlem yapılmadı.'
                );
            }

            $superAdminRolId = DB::table('roles')
                ->where('name', 'super-admin')
                ->where('is_super_admin', true)
                ->value('id');

            if (! $superAdminRolId) {
                throw new RuntimeException(
                    'Korumalı süper admin rolü bulunamadı.'
                );
            }

            foreach ([1, 6] as $kullaniciId) {
                $rolIdleri = DB::table('role_user')
                    ->where('user_id', $kullaniciId)
                    ->pluck('role_id');

                if (
                    $rolIdleri->count() !== 1
                    || (int) $rolIdleri->first() !== (int) $superAdminRolId
                ) {
                    throw new RuntimeException(
                        'Kullanıcıların rol bağlantıları beklenen durumda değil.'
                    );
                }
            }

            DB::table('sessions')
                ->whereIn('user_id', [1, 6])
                ->delete();

            DB::table('role_user')
                ->where('user_id', 6)
                ->delete();

            DB::table('users')
                ->where('id', 6)
                ->delete();

            DB::table('password_reset_tokens')
                ->whereIn('email', [
                    'admin@b2b.test',
                    'superadmin@b2b.test',
                ])
                ->delete();

            DB::table('users')
                ->where('id', 1)
                ->update([
                    'name' => 'Süper Admin',
                    'email' => 'superadmin@b2b.test',
                    'updated_at' => now(),
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new RuntimeException(
            'Bu veri düzeltmesi otomatik geri alınamaz. SQL yedeğini kullanın.'
        );
    }
};
