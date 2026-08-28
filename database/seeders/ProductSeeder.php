<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriIdleri = Category::pluck('id', 'slug');

        $urunler = [
            [
                'kategori_slug' => 'elektronik',
                'name' => 'Kablosuz Klavye',
                'sku' => 'ELK-001',
                'price' => 850.00,
                'stock' => 25,
            ],
            [
                'kategori_slug' => 'elektronik',
                'name' => 'USB-C Çoklayıcı',
                'sku' => 'ELK-002',
                'price' => 1250.00,
                'stock' => 12,
            ],
            [
                'kategori_slug' => 'elektronik',
                'name' => '24 İnç Monitör',
                'sku' => 'ELK-003',
                'price' => 5499.90,
                'stock' => 8,
            ],
            [
                'kategori_slug' => 'ofis-urunleri',
                'name' => "A4 Fotokopi Kağıdı 5'li Paket",
                'sku' => 'OFS-001',
                'price' => 720.00,
                'stock' => 40,
            ],
            [
                'kategori_slug' => 'ofis-urunleri',
                'name' => 'Masaüstü Evrak Rafı',
                'sku' => 'OFS-002',
                'price' => 285.50,
                'stock' => 18,
            ],
            [
                'kategori_slug' => 'ofis-urunleri',
                'name' => "Tükenmez Kalem 50'li Kutu",
                'sku' => 'OFS-003',
                'price' => 450.00,
                'stock' => 30,
            ],
            [
                'kategori_slug' => 'temizlik-urunleri',
                'name' => 'Sıvı Sabun 5 Litre',
                'sku' => 'TEM-001',
                'price' => 390.00,
                'stock' => 22,
            ],
            [
                'kategori_slug' => 'temizlik-urunleri',
                'name' => 'Yüzey Temizleyici 5 Litre',
                'sku' => 'TEM-002',
                'price' => 425.00,
                'stock' => 15,
            ],
            [
                'kategori_slug' => 'temizlik-urunleri',
                'name' => "Kağıt Havlu 12'li Paket",
                'sku' => 'TEM-003',
                'price' => 680.00,
                'stock' => 0,
            ],
            [
                'kategori_slug' => 'spor-ekipmanlari',
                'name' => 'Futbol Topu',
                'sku' => 'SPR-001',
                'price' => 950.00,
                'stock' => 14,
            ],
            [
                'kategori_slug' => 'spor-ekipmanlari',
                'name' => 'Egzersiz Matı',
                'sku' => 'SPR-002',
                'price' => 640.00,
                'stock' => 20,
            ],
            [
                'kategori_slug' => 'spor-ekipmanlari',
                'name' => 'Dambıl Seti',
                'sku' => 'SPR-003',
                'price' => 1850.00,
                'stock' => 6,
            ],
        ];

        foreach ($urunler as $urun) 
        {
            Product::firstOrCreate(
                ['sku' => $urun['sku']],
                [
                    'category_id' => $kategoriIdleri[$urun['kategori_slug']],
                    'name' => $urun['name'],
                    'price' => $urun['price'],
                    'stock' => $urun['stock'],
                    'image_path' => null,
                ]
            );
        }
    }
}
