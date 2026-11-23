<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function locales(): array
    {
        return ['turkish' => ['tr'], 'english' => ['en']];
    }

    #[DataProvider('locales')]
    public function test_guests_can_select_the_login_language(string $locale): void
    {
        $this->from(route('giris'))
            ->post(route('dil.degistir'), ['locale' => $locale])
            ->assertRedirect(route('giris'))
            ->assertSessionHas('locale', $locale);

        $this->get(route('giris'))
            ->assertSee('<html lang="'.$locale.'">', false)
            ->assertSeeText($locale === 'tr' ? 'Giriş Yap' : 'Log In');

        $this->post(route('giris.gonder'), [])
            ->assertInvalid(['email' => $locale === 'tr' ? 'E-posta adresi zorunludur.' : 'Email address is required.']);
    }

    public function test_unsupported_language_does_not_replace_the_selected_language(): void
    {
        $this->withSession(['locale' => 'en'])
            ->post(route('dil.degistir'), ['locale' => 'de'])
            ->assertInvalid(['locale'])
            ->assertSessionHas('locale', 'en');
    }

    #[DataProvider('locales')]
    public function test_panel_pages_render_in_the_selected_language(string $locale): void
    {
        $admin = $this->superAdmin();
        $category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);
        $product = $this->product($category);
        $role = Role::where('name', 'depo')->firstOrFail();
        $employee = User::factory()->create();
        $employee->roles()->sync([$role->id]);

        $pages = [
            ['anasayfa', [], 'Yönetim Paneli', 'Admin Dashboard'],
            ['kategoriler.index', [], 'Kategori Yönetimi', 'Categories Management'],
            ['kategoriler.create', [], 'Yeni Kategori Ekle', 'Add New Category'],
            ['kategoriler.edit', [$category], 'Kategori Düzenle', 'Edit Category'],
            ['urunler.index', [], 'Ürün Yönetimi', 'Products Management'],
            ['urunler.create', [], 'Yeni Ürün Ekle', 'Add New Product'],
            ['urunler.edit', [$product], 'Ürün Düzenle', 'Edit Product'],
            ['urunler.silinenler', [], 'Silinen Ürünler', 'Deleted Products'],
            ['siparisler.index', [], 'Siparişler', 'Orders'],
            ['siparisler.urun-sec', [], 'Sipariş İçin Ürün Seç', 'Select a Product to Order'],
            ['siparisler.create', [$product], 'Hızlı Sipariş Ver', 'Quick Order'],
            ['kullanicilar.index', [], 'Kullanıcı Yönetimi', 'Users Management'],
            ['kullanicilar.create', [], 'Yeni Kullanıcı Ekle', 'Add New User'],
            ['kullanicilar.edit', [$employee], 'Kullanıcıyı Düzenle', 'Edit User'],
            ['roller.index', [], 'Roller ve Yetkiler', 'Roles and Permissions'],
            ['roller.create', [], 'Yeni Rol Ekle', 'Add New Role'],
            ['roller.edit', [$role], 'Rolü Düzenle', 'Edit Role'],
            ['profil.goster', [], 'Profilim', 'My Profile'],
        ];

        $this->actingAs($admin)->withSession(['locale' => $locale]);

        foreach ($pages as [$route, $parameters, $turkish, $english]) {
            $this->get(route($route, $parameters))
                ->assertSee('<html lang="'.$locale.'">', false)
                ->assertSeeText($locale === 'tr' ? $turkish : $english)
                ->assertDontSee('panel.validation.')
                ->assertDontSee('panel.datatable');
        }

        $this->get(route('roller.create'))
            ->assertSeeText($locale === 'tr' ? 'Ürünleri listeleme' : 'List products');

        $this->get(route('siparisler.create', $product))
            ->assertSeeText($locale === 'tr' ? 'En fazla 5 adet' : 'You can order up to 5 units')
            ->assertSeeText($locale === 'tr' ? '1.250,50' : '1,250.50');
    }

    #[DataProvider('locales')]
    public function test_ajax_actions_and_order_status_are_translated_without_changing_records(string $locale): void
    {
        $admin = $this->superAdmin();
        $category = Category::create(['name' => 'Elektronik', 'name_en' => 'Electronics', 'slug' => 'elektronik']);
        $product = $this->product($category);
        $order = Order::create([
            'product_id' => $product->id,
            'customer_name' => 'Örnek Müşteri',
            'quantity' => 1,
            'total_price' => 1250.50,
            'status' => 'Bekliyor',
        ]);

        $this->actingAs($admin)->withSession(['locale' => $locale]);

        $response = $this->getJson(route('urunler.veri'))
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Türkçe Ürün')
            ->assertJsonPath('data.0.category_name', $locale === 'tr' ? 'Elektronik' : 'Electronics');

        $this->assertStringContainsString($locale === 'tr' ? 'Hızlı Sipariş Ver' : 'Quick Order', $response->json('data.0.actions'));
        $this->assertStringContainsString($locale === 'tr' ? 'Görsel yok' : 'No image', $response->json('data.0.image'));
        $this->assertStringContainsString($locale === 'tr' ? 'Bu ürünü silmek' : 'Are you sure you want to delete this product?', $response->json('data.0.actions'));

        $this->getJson(route('kategoriler.veri'))
            ->assertJsonPath('data.0.name', $locale === 'tr' ? 'Elektronik' : 'Electronics');

        $this->getJson(route('siparisler.veri'))
            ->assertOk()
            ->assertJsonPath('data.0.status', $locale === 'tr' ? 'Bekliyor' : 'Pending')
            ->assertJsonPath('data.0.customer_name', 'Örnek Müşteri');

        $this->assertSame('Bekliyor', $order->fresh()->status);
    }

    #[DataProvider('locales')]
    public function test_form_errors_use_the_selected_language(string $locale): void
    {
        $admin = $this->superAdmin();
        $category = Category::create(['name' => 'Elektronik', 'slug' => 'elektronik']);
        $product = $this->product($category);

        $this->actingAs($admin)->withSession(['locale' => $locale]);

        $this->post(route('kategoriler.store'), [])
            ->assertInvalid(['name' => $locale === 'tr' ? 'Kategori adı zorunludur.' : 'Category name is required.']);
        $this->post(route('urunler.store'), [])
            ->assertInvalid(['name' => $locale === 'tr' ? 'Ürün adı zorunludur.' : 'Product name is required.']);
        $this->post(route('siparisler.store', $product), [])
            ->assertInvalid(['customer_name' => $locale === 'tr' ? 'Müşteri adı zorunludur.' : 'Customer name is required.']);
        $this->post(route('kullanicilar.store'), [])
            ->assertInvalid(['name' => $locale === 'tr' ? 'Ad soyad zorunludur.' : 'Full name is required.']);
        $this->post(route('roller.store'), [])
            ->assertInvalid(['label' => $locale === 'tr' ? 'Rol adı zorunludur.' : 'Role name is required.']);
        $this->patch(route('profil.sifre-guncelle'), [])
            ->assertInvalid(['current_password' => $locale === 'tr' ? 'Mevcut şifre alanı zorunludur.' : 'Current password is required.']);

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('users', 1);
    }

    #[DataProvider('locales')]
    public function test_success_messages_and_logout_preserve_the_selected_language(string $locale): void
    {
        $admin = $this->superAdmin();

        $this->actingAs($admin)->withSession(['locale' => $locale])
            ->post(route('kategoriler.store'), ['name' => 'Elektronik'])
            ->assertRedirect(route('kategoriler.index'))
            ->assertSessionHas('success', $locale === 'tr' ? 'Kategori başarıyla eklendi.' : 'Category added successfully.');

        $this->assertDatabaseHas('categories', ['name' => 'Elektronik', 'slug' => 'elektronik']);

        $this->post(route('cikis'))
            ->assertRedirect(route('giris'))
            ->assertSessionHas('locale', $locale)
            ->assertSessionHas('success', $locale === 'tr' ? 'Çıkış yapıldı.' : 'You have been logged out.');

        $this->assertGuest();

        $this->get(route('giris'))
            ->assertSee('<html lang="'.$locale.'">', false);
    }

    private function superAdmin(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->roles()->sync([Role::where('is_super_admin', true)->firstOrFail()->id]);

        return $admin;
    }

    private function product(Category $category): Product
    {
        return Product::create([
            'category_id' => $category->id,
            'name' => 'Türkçe Ürün',
            'sku' => 'TR-001',
            'price' => 1250.50,
            'stock' => 5,
        ]);
    }
}
