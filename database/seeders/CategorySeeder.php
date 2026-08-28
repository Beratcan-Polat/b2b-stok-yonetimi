<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

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
                'slug' => 'elektronik',
            ],
            [
                'name' => 'Ofis Ürünleri',
                'slug' => 'ofis-urunleri',
            ],
            [
                'name' => 'Temizlik Ürünleri',
                'slug' => 'temizlik-urunleri',
            ],
            [
                'name' => 'Spor Ekipmanları',
                'slug' => 'spor-ekipmanlari',
            ],
        ];

        foreach ($kategoriler as $kategori)
            {
                Category::firstOrCreate(
                    ['slug' => $kategori['slug']],
                    ['name' => $kategori['name']]
                );
            }
    }
}
