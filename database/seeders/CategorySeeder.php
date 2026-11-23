<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoriler = [
            [
                'name' => 'Elektronik',
                'name_en' => 'Electronics',
                'slug' => 'elektronik',
            ],
            [
                'name' => 'Ofis Ürünleri',
                'name_en' => 'Office Supplies',
                'slug' => 'ofis-urunleri',
            ],
            [
                'name' => 'Temizlik Ürünleri',
                'name_en' => 'Cleaning Supplies',
                'slug' => 'temizlik-urunleri',
            ],
            [
                'name' => 'Spor Ekipmanları',
                'name_en' => 'Sports Equipment',
                'slug' => 'spor-ekipmanlari',
            ],
        ];

        foreach ($kategoriler as $kategori) {
            Category::firstOrCreate(
                ['slug' => $kategori['slug']],
                ['name' => $kategori['name'], 'name_en' => $kategori['name_en']]
            );
        }
    }
}
