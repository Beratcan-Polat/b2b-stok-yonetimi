<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OrderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.locale' => 'tr']);
    }

    public static function outOfStockRequests(): array
    {
        return [
            'create with product list permission' => ['GET', true, 'urunler.index'],
            'create without product list permission' => ['GET', false, 'siparisler.urun-sec'],
            'store with product list permission' => ['POST', true, 'urunler.index'],
            'store without product list permission' => ['POST', false, 'siparisler.urun-sec'],
        ];
    }

    #[DataProvider('outOfStockRequests')]
    public function test_out_of_stock_requests_return_to_an_accessible_page_without_creating_an_order(string $method, bool $canListProducts, string $destination): void
    {
        $permissions = $canListProducts ? ['siparis.olustur', 'urun.listele'] : ['siparis.olustur'];
        $user = $this->userWithPermissions($permissions);
        $product = $this->product(0);
        $route = $method === 'GET' ? 'siparisler.create' : 'siparisler.store';

        $this->actingAs($user)->call($method, route($route, $product), ['customer_name' => 'Müşteri', 'quantity' => 1])
            ->assertRedirect(route($destination))
            ->assertSessionHas('error', 'Bu ürün stokta bulunmadığı için sipariş verilemez.');

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 0]);
        $this->get(route($destination))->assertOk();
    }

    public function test_order_creator_can_open_the_product_picker_from_the_order_list(): void
    {
        $user = $this->userWithPermissions(['siparis.listele', 'siparis.olustur']);

        $this->actingAs($user)->get(route('siparisler.index'))
            ->assertSee('href="'.route('siparisler.urun-sec').'" class="btn btn-primary"', false);

        $this->get(route('siparisler.urun-sec'))
            ->assertSee('href="'.route('siparisler.index').'" class="btn btn-secondary"', false);
    }

    public function test_read_only_order_viewer_does_not_receive_an_order_creation_link(): void
    {
        $user = $this->userWithPermissions(['siparis.listele']);

        $this->actingAs($user)->get(route('siparisler.index'))
            ->assertDontSee(route('siparisler.urun-sec'));

        $this->get(route('siparisler.urun-sec'))->assertForbidden();
    }

    public static function invalidQuantities(): array
    {
        return [
            'zero' => ['0', 'Sipariş adedi en az 1 olmalıdır.'],
            'fraction' => ['1.5', 'Sipariş adedi tam sayı olmalıdır.'],
            'above stock' => ['6', 'Sipariş adedi mevcut stoktan fazla olamaz.'],
        ];
    }

    #[DataProvider('invalidQuantities')]
    public function test_quantity_errors_preserve_the_entered_values_without_changing_stock(string $quantity, string $message): void
    {
        $user = $this->userWithPermissions(['siparis.olustur']);
        $product = $this->product();

        $this->actingAs($user)->from(route('siparisler.create', $product))->followingRedirects()
            ->post(route('siparisler.store', $product), ['customer_name' => 'Örnek Müşteri', 'quantity' => $quantity])
            ->assertSee('adminkit/css/app.css')
            ->assertSeeText($message)
            ->assertSee('value="Örnek Müşteri"', false)
            ->assertSee('value="'.$quantity.'"', false)
            ->assertSee('id="quantity-error" class="invalid-feedback"', false);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }

    public function test_customer_error_is_shown_next_to_the_field_and_the_quantity_is_preserved(): void
    {
        $user = $this->userWithPermissions(['siparis.olustur']);
        $product = $this->product();

        $this->actingAs($user)->from(route('siparisler.create', $product))->followingRedirects()
            ->post(route('siparisler.store', $product), ['customer_name' => '', 'quantity' => 2])
            ->assertSeeText('Müşteri adı zorunludur.')
            ->assertSee('id="customer-name-error" class="invalid-feedback"', false)
            ->assertSee('value="2"', false);

        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'stock' => 5]);
    }

    private function userWithPermissions(array $permissions): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $user->permissions()->sync(Permission::whereIn('name', $permissions)->get()->mapWithKeys(
            fn (Permission $permission): array => [$permission->id => ['allowed' => true]],
        )->all());

        return $user;
    }

    private function product(int $stock = 5): Product
    {
        $category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Klavye',
            'sku' => 'KL-001',
            'price' => 125.50,
            'stock' => $stock,
        ]);
    }
}
