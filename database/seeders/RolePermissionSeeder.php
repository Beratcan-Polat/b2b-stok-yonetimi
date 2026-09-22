<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $izinler = [
            'kategori.listele' => 'Kategorileri listeleme',
            'kategori.ekle' => 'Kategori ekleme',
            'kategori.duzenle' => 'Kategori düzenleme',
            'kategori.sil' => 'Kategori silme',
            'urun.listele' => 'Ürünleri listeleme',
            'urun.ekle' => 'Ürün ekleme',
            'urun.duzenle' => 'Ürün düzenleme',
            'urun.sil' => 'Ürün silme',
            'urun.silinenler' => 'Silinen ürünleri görüntüleme',
            'urun.geriyukle' => 'Silinen ürünü geri yükleme',
            'siparis.listele' => 'Siparişleri listeleme',
            'siparis.olustur' => 'Sipariş oluşturma',
            'kullanici.listele' => 'Kullanıcıları listeleme',
            'kullanici.ekle' => 'Kullanıcı ekleme',
            'kullanici.duzenle' => 'Kullanıcı düzenleme',
            'kullanici.sil' => 'Kullanıcı silme',
        ];

        foreach ($izinler as $ad => $etiket) {
            Permission::firstOrCreate(['name' => $ad], ['label' => $etiket]);
        }

        $roller = [
            'admin' => [
                'label' => 'Sistem Yöneticisi',
                'izinler' => array_keys($izinler),
            ],
            'depo' => [
                'label' => 'Depo Sorumlusu',
                'izinler' => [
                    'urun.listele',
                    'urun.ekle',
                    'urun.duzenle',
                    'urun.sil',
                    'urun.silinenler',
                    'urun.geriyukle',
                ],
            ],
            'satis' => [
                'label' => 'Satış Temsilcisi',
                'izinler' => [
                    'urun.listele',
                    'siparis.listele',
                    'siparis.olustur',
                ],
            ],
            'bayi' => [
                'label' => 'Bayi',
                'izinler' => [
                    'urun.listele',
                    'siparis.olustur',
                ],
            ],
        ];

        foreach ($roller as $ad => $bilgi) {
            $rol = Role::firstOrCreate(['name' => $ad], ['label' => $bilgi['label']]);

            $rol->permissions()->sync(
                Permission::whereIn('name', $bilgi['izinler'])->pluck('id')
            );
        }
    }
}
