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

class CategoryDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleting_category_deletes_products_and_preserves_orders(): void
    {
        $this->seed(RolePermissionSeeder::class);

        $admin = User::factory()->create();
        $admin->roles()->sync([Role::where('name', 'super-admin')->firstOrFail()->id]);

        $category = Category::create([
            'name' => 'Test kategori',
            'slug' => 'test-kategori',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Test ürün',
            'sku' => 'TEST-001',
            'price' => 100,
            'stock' => 10,
        ]);

        $order = Order::create([
            'product_id' => $product->id,
            'customer_name' => 'Test müşteri',
            'quantity' => 1,
            'total_price' => 100,
            'status' => 'Bekliyor',
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('kategoriler.destroy', $category));

        $response->assertRedirect(route('kategoriler.index'))
            ->assertSessionHas('success', 'Kategori ve bağlı ürünler başarıyla silindi.');

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'product_id' => null,
        ]);

        $this->actingAs($admin)
            ->getJson(route('siparisler.veri'))
            ->assertJsonPath('data.0.product_name', 'Silinmiş ürün');
    }
}
