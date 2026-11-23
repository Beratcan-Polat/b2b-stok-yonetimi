<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->string('name_en')->nullable();
        });

        $translations = [
            'Elektronik' => 'Electronics',
            'Ofis Ürünleri' => 'Office Supplies',
            'Temizlik Ürünleri' => 'Cleaning Supplies',
            'Spor Ekipmanları' => 'Sports Equipment',
        ];

        foreach ($translations as $name => $englishName) {
            DB::table('categories')->where('name', $name)->whereNull('name_en')->update(['name_en' => $englishName]);
        }
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('name_en');
        });
    }
};
