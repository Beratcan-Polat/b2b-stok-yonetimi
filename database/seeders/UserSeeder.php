<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $kullanicilar = [
            ['name' => 'Süper Admin', 'email' => 'superadmin@b2b.test', 'rol' => 'super-admin'],
            ['name' => 'Depo Sorumlusu', 'email' => 'depo@b2b.test', 'rol' => 'depo'],
            ['name' => 'Satış Temsilcisi', 'email' => 'satis@b2b.test', 'rol' => 'satis'],
            ['name' => 'Örnek Bayi', 'email' => 'bayi@b2b.test', 'rol' => 'bayi'],
        ];

        foreach ($kullanicilar as $bilgi) {
            $kullanici = User::firstOrCreate(
                ['email' => $bilgi['email']],
                [
                    'name' => $bilgi['name'],
                    'password' => Hash::make('parola123'),
                ]
            );

            $rol = Role::where('name', $bilgi['rol'])->firstOrFail();

            if ($kullanici->wasRecentlyCreated || $rol->isSuperAdmin()) {
                $kullanici->roles()->sync([
                    $rol->id,
                ]);
            }
        }
    }
}
