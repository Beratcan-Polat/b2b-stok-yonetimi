<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataTableListsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    private function superAdmin(): User
    {
        $admin = User::factory()->create();
        $admin->roles()->sync([Role::where('is_super_admin', true)->firstOrFail()->id]);

        return $admin;
    }

    private function category(string $name, string $slug): Category
    {
        return Category::create(['name' => $name, 'slug' => $slug]);
    }

    private function product(Category $category, string $name, string $sku, int $stock): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'name' => $name,
            'sku' => $sku,
            'price' => 125.50,
            'stock' => $stock,
        ]);
    }

    public function test_all_table_data_routes_require_their_view_permission(): void
    {
        $routes = [
            'kategoriler.veri',
            'kullanicilar.veri',
            'roller.veri',
            'urunler.veri',
            'urunler.silinenler.veri',
            'siparisler.veri',
            'siparisler.urun-sec.veri',
        ];

        foreach ($routes as $routeName) {
            $this->get(route($routeName))->assertRedirect(route('giris'));
        }

        $this->actingAs(User::factory()->create());

        foreach ($routes as $routeName) {
            $this->getJson(route($routeName))->assertForbidden();
        }
    }

    public function test_data_routes_return_table_rows_and_keep_actions_available(): void
    {
        $admin = $this->superAdmin();
        $employee = User::factory()->create();
        $employee->roles()->sync([Role::where('name', 'depo')->firstOrFail()->id]);
        $category = $this->category('Elektronik', 'elektronik');
        $product = $this->product($category, 'Klavye', 'KL-001', 3);
        $deletedProduct = $this->product($category, 'Eski fare', 'FA-001', 0);
        $deletedProduct->delete();

        $order = Order::create([
            'product_id' => $product->id,
            'customer_name' => 'Örnek Müşteri',
            'quantity' => 2,
            'total_price' => 251,
            'status' => 'Bekliyor',
        ]);

        $this->actingAs($admin);

        $this->getJson(route('kategoriler.veri'))
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Elektronik');

        $this->getJson(route('kullanicilar.veri'))
            ->assertOk()
            ->assertJsonFragment(['email' => $employee->email, 'roles' => 'Depo Sorumlusu']);

        $this->getJson(route('roller.veri'))
            ->assertOk()
            ->assertJsonFragment(['label' => 'Süper Admin', 'is_super_admin' => true]);

        $productsResponse = $this->getJson(route('urunler.veri'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Klavye')
            ->assertJsonPath('data.0.category_name', 'Elektronik');

        $this->assertStringContainsString('Hızlı Sipariş Ver', $productsResponse->json('data.0.actions'));

        $deletedProductsResponse = $this->getJson(route('urunler.silinenler.veri'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Eski fare');

        $this->assertStringContainsString('Geri Yükle', $deletedProductsResponse->json('data.0.actions'));

        $this->getJson(route('siparisler.veri'))
            ->assertOk()
            ->assertJsonPath('data.0.id', $order->id)
            ->assertJsonPath('data.0.product_name', 'Klavye')
            ->assertJsonPath('data.0.customer_name', 'Örnek Müşteri');

        $pickerResponse = $this->getJson(route('siparisler.urun-sec.veri'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.sku', 'KL-001');

        $this->assertStringContainsString('Sipariş Oluştur', $pickerResponse->json('data.0.actions'));
    }

    public function test_product_data_returns_all_records_for_client_side_filtering(): void
    {
        $admin = $this->superAdmin();
        $firstCategory = $this->category('Elektronik', 'elektronik');
        $secondCategory = $this->category('Mobilya', 'mobilya');

        for ($index = 1; $index <= 11; $index++) {
            $this->product($firstCategory, 'Klavye '.$index, 'KL-'.$index, 2);
        }

        $this->product($secondCategory, 'Masa', 'MASA-001', 2);

        $this->actingAs($admin)
            ->getJson(route('urunler.veri'))
            ->assertOk()
            ->assertJsonCount(12, 'data');
    }
}
