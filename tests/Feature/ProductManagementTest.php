<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProductManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.locale' => 'tr']);
    }

    public static function listPermissions(): array
    {
        return ['with list permission' => [true], 'without list permission' => [false]];
    }

    #[DataProvider('listPermissions')]
    public function test_update_preserves_the_image_and_returns_to_an_accessible_page(bool $canList): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/eski.png', 'existing-image');
        $user = $this->userWithPermissions($canList ? ['urun.duzenle', 'urun.listele'] : ['urun.duzenle']);
        $product = $this->product(['image_path' => 'products/eski.png']);
        $destination = $canList ? 'urunler.index' : 'anasayfa';

        $this->actingAs($user)->get(route('urunler.edit', $product))
            ->assertSee('adminkit/css/app.css')
            ->assertSee(asset('storage/products/eski.png'))
            ->assertSee('enctype="multipart/form-data"', false)
            ->assertSee('href="'.route($destination).'" class="btn btn-secondary"', false);

        $this->put(route('urunler.update', $product), [
            'category_id' => $product->category_id, 'name' => 'Güncel Klavye', 'sku' => 'KL-002', 'price' => '150.25', 'stock' => 8,
        ])->assertRedirect(route($destination))
            ->assertSessionHas('success', 'Ürün başarıyla güncellendi.');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Güncel Klavye', 'sku' => 'KL-002', 'price' => 150.25, 'stock' => 8, 'image_path' => 'products/eski.png']);
        Storage::disk('public')->assertExists('products/eski.png');
    }

    #[DataProvider('listPermissions')]
    public function test_delete_is_reversible_and_returns_to_an_accessible_page(bool $canList): void
    {
        $user = $this->userWithPermissions($canList ? ['urun.sil', 'urun.listele'] : ['urun.sil']);
        $product = $this->product();
        $order = $product->orders()->create([
            'customer_name' => 'Müşteri', 'quantity' => 2, 'total_price' => 251, 'status' => 'Bekliyor',
        ]);

        $this->actingAs($user)->delete(route('urunler.destroy', $product))
            ->assertRedirect(route($canList ? 'urunler.index' : 'anasayfa'))
            ->assertSessionHas('success', 'Ürün başarıyla silindi.');

        $this->assertSoftDeleted($product);
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'product_id' => $product->id, 'total_price' => 251]);
    }

    public static function restoreDestinations(): array
    {
        return [
            'deleted product viewer' => [['urun.geriyukle', 'urun.silinenler'], 'urunler.silinenler'],
            'active product viewer' => [['urun.geriyukle', 'urun.listele'], 'urunler.index'],
            'restore only' => [['urun.geriyukle'], 'anasayfa'],
        ];
    }

    #[DataProvider('restoreDestinations')]
    public function test_restore_returns_to_a_page_the_user_can_access(array $permissions, string $destination): void
    {
        $user = $this->userWithPermissions($permissions);
        $product = $this->product(['image_path' => 'products/eski.png']);
        $product->delete();

        $this->actingAs($user)->patch(route('urunler.geri-yukle', $product->id))
            ->assertRedirect(route($destination))
            ->assertSessionHas('success', 'Ürün başarıyla geri yüklendi.');

        $this->assertNotSoftDeleted($product);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'image_path' => 'products/eski.png', 'stock' => 5]);
        $this->get(route($destination))->assertOk();
    }

    public function test_deleted_product_viewer_has_a_sidebar_link_and_an_accessible_return_link(): void
    {
        $user = $this->userWithPermissions(['urun.silinenler']);

        $this->actingAs($user)->get(route('urunler.silinenler'))
            ->assertSee('adminkit/css/app.css')
            ->assertSee('href="'.route('urunler.silinenler').'"', false)
            ->assertSee('href="'.route('anasayfa').'" class="btn btn-secondary"', false)
            ->assertDontSee('href="'.route('urunler.index').'"', false)
            ->assertDontSee('href="'.route('urunler.create').'"', false);

        $this->get(route('urunler.index'))->assertForbidden();
    }

    public function test_create_stores_an_uploaded_image_and_product_details(): void
    {
        Storage::fake('public');
        $user = $this->userWithPermissions(['urun.ekle']);
        $category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);
        $image = $this->image();

        $this->actingAs($user)->post(route('urunler.store'), [
            'category_id' => $category->id, 'name' => 'Klavye', 'sku' => 'KL-001', 'price' => '125.50', 'stock' => 5, 'image' => $image,
        ])->assertRedirect(route('anasayfa'))
            ->assertSessionHas('success', 'Ürün başarıyla eklendi.');

        $this->assertDatabaseHas('products', ['category_id' => $category->id, 'name' => 'Klavye', 'sku' => 'KL-001', 'price' => 125.50, 'stock' => 5, 'image_path' => 'products/'.$image->hashName()]);
        Storage::disk('public')->assertExists('products/'.$image->hashName());
    }

    public function test_update_replaces_the_uploaded_image_and_removes_the_previous_file(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/eski.png', 'existing-image');
        $user = $this->userWithPermissions(['urun.duzenle']);
        $product = $this->product(['image_path' => 'products/eski.png']);
        $image = $this->image();

        $this->actingAs($user)->put(route('urunler.update', $product), [
            'category_id' => $product->category_id, 'name' => $product->name, 'sku' => $product->sku, 'price' => $product->price, 'stock' => 5, 'image' => $image,
        ])->assertRedirect(route('anasayfa'))->assertSessionHasNoErrors();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'image_path' => 'products/'.$image->hashName()]);
        Storage::disk('public')->assertExists('products/'.$image->hashName());
        Storage::disk('public')->assertMissing('products/eski.png');
    }

    public function test_invalid_image_preserves_the_current_image_and_entered_values(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('products/eski.png', 'existing-image');
        $user = $this->userWithPermissions(['urun.duzenle']);
        $product = $this->product(['image_path' => 'products/eski.png']);

        $this->actingAs($user)->from(route('urunler.edit', $product))->followingRedirects()
            ->put(route('urunler.update', $product), [
                'category_id' => $product->category_id, 'name' => 'Güncellenen Ad', 'sku' => 'KL-002', 'price' => '200.50', 'stock' => 9,
                'image' => UploadedFile::fake()->create('notlar.txt', 1, 'text/plain'),
            ])->assertSeeText('Yüklenen dosya bir görsel olmalıdır.')
            ->assertSee('value="Güncellenen Ad"', false)
            ->assertSee('value="KL-002"', false)
            ->assertSee('value="200.50"', false)
            ->assertSee('value="9"', false)
            ->assertSee('id="image-error" class="invalid-feedback"', false)
            ->assertSee(asset('storage/products/eski.png'));

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Klavye', 'sku' => 'KL-001', 'stock' => 5, 'image_path' => 'products/eski.png']);
        Storage::disk('public')->assertExists('products/eski.png');
    }

    public function test_create_validation_preserves_the_category_and_escapes_entered_values(): void
    {
        $user = $this->userWithPermissions(['urun.ekle']);
        $product = $this->product();

        $this->actingAs($user)->from(route('urunler.create'))->followingRedirects()
            ->post(route('urunler.store'), [
                'category_id' => $product->category_id, 'name' => '<img src=x onerror=alert(1)>', 'sku' => 'KL-001', 'price' => '-1', 'stock' => '1.5',
            ])->assertSee('value="'.$product->category_id.'" selected', false)
            ->assertSee('value="&lt;img src=x onerror=alert(1)&gt;"', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false)
            ->assertSeeText('Bu SKU daha önce kullanılmış.')
            ->assertSeeText('Ürün fiyatı negatif olamaz.')
            ->assertSeeText('Stok adedi tam sayı olmalıdır.')
            ->assertSee('id="sku-error" class="invalid-feedback"', false)
            ->assertSee('id="price-error" class="invalid-feedback"', false)
            ->assertSee('id="stock-error" class="invalid-feedback"', false);

        $this->assertDatabaseCount('products', 1);
    }

    public function test_empty_categories_offer_category_creation_only_when_authorized(): void
    {
        $user = $this->userWithPermissions(['urun.ekle']);

        $this->actingAs($user)->get(route('urunler.create'))
            ->assertSeeText('Henüz kategori bulunmuyor.')
            ->assertDontSee('href="'.route('kategoriler.create').'"', false)
            ->assertSee('type="submit" class="btn btn-primary" disabled', false);

        $permission = Permission::where('name', 'kategori.ekle')->firstOrFail();
        $user->permissions()->syncWithoutDetaching([$permission->id => ['allowed' => true]]);

        $this->get(route('urunler.create'))
            ->assertSee('href="'.route('kategoriler.create').'" class="alert-link"', false);
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

    private function product(array $attributes = []): Product
    {
        $category = Category::firstOrCreate(['slug' => 'elektronik'], ['name' => 'Elektronik']);

        return Product::create(array_merge([
            'category_id' => $category->id, 'name' => 'Klavye', 'sku' => 'KL-001', 'price' => 125.50, 'stock' => 5,
        ], $attributes));
    }

    private function image(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent('urun.png', base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aS9sAAAAASUVORK5CYII=',
        ));
    }
}
