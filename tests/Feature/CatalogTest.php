<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.locale' => 'tr']);
    }

    public function test_guests_see_prices_and_exact_stock_including_out_of_stock_products_but_not_deleted_products(): void
    {
        $this->product(['name' => '<script>urun</script>', 'stock' => 37, 'price' => 1250.50]);
        $this->product(['name' => 'Stoksuz Ürün', 'sku' => 'STOK-0', 'stock' => 0]);
        $this->product(['name' => 'Silinen Ürün', 'sku' => 'SIL-1'])->delete();

        $this->get(route('katalog.index'))
            ->assertSee('&lt;script&gt;urun&lt;/script&gt;', false)
            ->assertDontSee('<script>urun</script>', false)
            ->assertSeeText(['1.250,50', '37', 'Stoksuz Ürün', 'Stokta Yok', 'Görsel yok'])
            ->assertDontSeeText('Silinen Ürün');

        $this->assertGuest();
    }

    public function test_category_and_sku_search_filters_are_applied_together(): void
    {
        $elektronik = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);
        $ofis = Category::create(['name' => 'Ofis', 'slug' => 'ofis']);
        $this->product(['category_id' => $elektronik->id, 'name' => 'Kablosuz Klavye', 'sku' => 'ARA-101']);
        $this->product(['category_id' => $ofis->id, 'name' => 'Ofis Klavyesi', 'sku' => 'ARA-102']);
        $this->product(['category_id' => $elektronik->id, 'name' => 'Fare', 'sku' => 'FAR-1']);

        $this->get(route('katalog.index', ['arama' => 'ARA-', 'kategori' => $elektronik->id]))
            ->assertSeeText('Kablosuz Klavye')
            ->assertDontSeeText(['Ofis Klavyesi', 'Fare'])
            ->assertSee('value="ARA-"', false)
            ->assertViewHas('kategoriId', (string) $elektronik->id);
    }

    public function test_products_can_be_searched_by_name(): void
    {
        $this->product(['name' => 'Kablosuz Klavye', 'sku' => 'KL-1']);
        $this->product(['name' => 'Kablolu Fare', 'sku' => 'FR-1']);

        $this->get(route('katalog.index', ['arama' => 'Kablosuz']))
            ->assertSeeText('Kablosuz Klavye')
            ->assertDontSeeText('Kablolu Fare');
    }

    public function test_unknown_category_redirects_to_the_catalog_with_a_validation_error(): void
    {
        $this->get(route('katalog.index', ['kategori' => 999]))
            ->assertRedirect(route('katalog.index'))
            ->assertSessionHasErrors('kategori');
    }

    public function test_pagination_keeps_search_category_and_sorting_filters(): void
    {
        $kategori = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);

        for ($index = 1; $index <= 13; $index++) {
            $this->product(['category_id' => $kategori->id, 'name' => 'Klavye '.$index, 'sku' => 'KL-'.$index]);
        }

        $this->get(route('katalog.index', ['arama' => 'Klavye', 'kategori' => $kategori->id, 'siralama' => 'ad-az']))
            ->assertSee('href="'.e(route('katalog.index', ['arama' => 'Klavye', 'kategori' => $kategori->id, 'siralama' => 'ad-az', 'page' => 2])).'"', false)
            ->assertSeeText('13 ürünün 1–12 arası gösteriliyor');
    }

    #[DataProvider('sortingOptions')]
    public function test_products_are_sorted_by_the_selected_option(?string $siralama, array $beklenenSira): void
    {
        $bravo = $this->product(['name' => 'Bravo', 'sku' => 'BR-1', 'price' => 5]);
        $alpha = $this->product(['name' => 'Alpha', 'sku' => 'AL-1', 'price' => 30]);
        $charlie = $this->product(['name' => 'Charlie', 'sku' => 'CH-1', 'price' => 100]);
        $bravo->forceFill(['created_at' => '2026-01-01 12:00:00'])->save();
        $alpha->forceFill(['created_at' => '2026-01-03 12:00:00'])->save();
        $charlie->forceFill(['created_at' => '2026-01-02 12:00:00'])->save();

        $this->get(route('katalog.index', ['siralama' => $siralama]))
            ->assertSeeTextInOrder($beklenenSira)
            ->assertSee('value="'.($siralama ?? 'varsayilan').'" selected', false);
    }

    public static function sortingOptions(): array
    {
        return [
            'default newest first' => [null, ['Alpha', 'Charlie', 'Bravo']],
            'explicit default' => ['varsayilan', ['Alpha', 'Charlie', 'Bravo']],
            'price ascending' => ['fiyat-artan', ['Bravo', 'Alpha', 'Charlie']],
            'price descending' => ['fiyat-azalan', ['Charlie', 'Alpha', 'Bravo']],
            'oldest first' => ['tarih-eski', ['Bravo', 'Charlie', 'Alpha']],
            'newest first' => ['tarih-yeni', ['Alpha', 'Charlie', 'Bravo']],
            'name ascending' => ['ad-az', ['Alpha', 'Bravo', 'Charlie']],
            'name descending' => ['ad-za', ['Charlie', 'Bravo', 'Alpha']],
        ];
    }

    public function test_unknown_sorting_option_redirects_to_the_catalog_with_a_validation_error(): void
    {
        $this->get(route('katalog.index', ['siralama' => 'price desc']))
            ->assertRedirect(route('katalog.index'))
            ->assertSessionHasErrors('siralama');
    }

    #[DataProvider('categoryLocales')]
    public function test_category_names_follow_the_language_in_both_the_filter_and_product_cards(string $locale, string $expectedName, string $otherName): void
    {
        $category = Category::create(['name' => 'Özel Donanım', 'name_en' => 'Custom Equipment', 'slug' => 'ozel-donanim']);
        $this->product(['category_id' => $category->id]);

        $this->withSession(['locale' => $locale])->get(route('katalog.index'))
            ->assertSeeText($expectedName)
            ->assertDontSeeText($otherName);
    }

    public static function categoryLocales(): array
    {
        return [
            'turkish' => ['tr', 'Özel Donanım', 'Custom Equipment'],
            'english' => ['en', 'Custom Equipment', 'Özel Donanım'],
        ];
    }

    public function test_all_seeded_categories_have_english_names_in_the_catalog(): void
    {
        $this->seed(CategorySeeder::class);

        foreach (Category::all() as $category) {
            $this->product(['category_id' => $category->id, 'sku' => 'KAT-'.$category->id]);
        }

        $this->withSession(['locale' => 'en'])->get(route('katalog.index'))
            ->assertSeeText(['Electronics', 'Office Supplies', 'Cleaning Supplies', 'Sports Equipment'])
            ->assertDontSeeText(['Elektronik', 'Ofis Ürünleri', 'Temizlik Ürünleri', 'Spor Ekipmanları']);
    }

    public function test_categories_without_an_english_name_fall_back_to_the_original_name(): void
    {
        $this->product();

        $this->withSession(['locale' => 'en'])->get(route('katalog.index'))
            ->assertSeeText('Elektronik');
    }

    private function product(array $attributes = []): Product
    {
        $kategori = Category::firstOrCreate(['slug' => 'elektronik'], ['name' => 'Elektronik']);

        return Product::create(array_merge([
            'category_id' => $kategori->id,
            'name' => 'Klavye',
            'sku' => 'KL-001',
            'price' => 125.50,
            'stock' => 5,
        ], $attributes));
    }
}
