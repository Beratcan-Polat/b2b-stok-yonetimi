<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(Role $role): User
    {
        $user = User::factory()->create();
        $user->roles()->sync([$role->id]);

        return $user;
    }

    private function superAdmin(): User
    {
        return $this->userWithRole(Role::where('is_super_admin', true)->firstOrFail());
    }

    public function test_only_super_admin_can_manage_roles(): void
    {
        $depo = $this->userWithRole(Role::where('name', 'depo')->firstOrFail());
        $this->actingAs($depo)->get(route('roller.index'))->assertForbidden();
        $this->post(route('roller.store'), ['name' => 'yeni-rol', 'label' => 'Yeni Rol'])->assertForbidden();
        $this->actingAs($this->superAdmin())->get(route('roller.index'))->assertOk();
    }

    public function test_role_permissions_are_independent_and_can_be_removed(): void
    {
        $admin = $this->superAdmin();
        $permission = Permission::where('name', 'kategori.ekle')->firstOrFail();
        $this->actingAs($admin)->post(route('roller.store'), [
            'name' => 'kategori-test',
            'label' => 'Kategori Test',
            'permission_ids' => [$permission->id],
        ])->assertRedirect(route('roller.index'));

        $role = Role::where('name', 'kategori-test')->firstOrFail();
        $this->assertEqualsCanonicalizing(['kategori.ekle'], $role->permissions()->pluck('name')->all());
        $user = $this->userWithRole($role);
        $this->assertTrue($user->hasPermissionTo('kategori.ekle'));
        $this->assertFalse($user->hasPermissionTo('kategori.listele'));
        $this->actingAs($user)->get(route('kategoriler.create'))->assertOk();
        $this->get(route('kategoriler.index'))->assertForbidden();

        $this->actingAs($admin);
        $this->put(route('roller.update', $role), ['name' => $role->name, 'label' => $role->label])
            ->assertRedirect(route('roller.index'));
        $this->assertFalse($user->hasPermissionTo('kategori.ekle'));
        $this->assertSame(0, $role->permissions()->count());
    }

    public function test_category_creator_cannot_edit_or_delete_categories(): void
    {
        $role = Role::create(['name' => 'kategori-test', 'label' => 'Kategori Test']);
        $role->permissions()->sync(Permission::whereIn('name', ['kategori.ekle', 'kategori.listele'])->pluck('id'));
        $user = $this->userWithRole($role);
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);

        $this->actingAs($user)->get(route('kategoriler.create'))->assertOk();
        $this->get(route('kategoriler.edit', $category))->assertForbidden();
        $this->put(route('kategoriler.update', $category), ['name' => 'Yeni'])->assertForbidden();
        $this->delete(route('kategoriler.destroy', $category))->assertForbidden();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Test']);
    }

    public function test_protected_role_and_assigned_roles_cannot_be_deleted(): void
    {
        $admin = $this->superAdmin();
        $protected = $admin->roles()->firstOrFail();
        $depo = Role::where('name', 'depo')->firstOrFail();
        $this->userWithRole($depo);

        $this->actingAs($admin)->put(route('roller.update', $protected), ['name' => 'degistir', 'label' => 'Değiştir'])->assertForbidden();
        $this->delete(route('roller.destroy', $protected))->assertForbidden();
        $this->delete(route('roller.destroy', $depo))->assertSessionHasErrors('rol');
        $this->assertDatabaseHas('roles', ['id' => $depo->id]);
    }

    public function test_normal_user_manager_cannot_assign_or_change_super_admin(): void
    {
        $role = Role::create(['name' => 'kullanici-yoneticisi', 'label' => 'Kullanıcı Yöneticisi']);
        $role->permissions()->sync(Permission::whereIn('name', ['kullanici.listele', 'kullanici.ekle', 'kullanici.duzenle', 'kullanici.sil'])->pluck('id'));
        $manager = $this->userWithRole($role);
        $admin = $this->superAdmin();
        $protected = $admin->roles()->firstOrFail();

        $this->actingAs($manager)->post(route('kullanicilar.store'), [
            'name' => 'Yetkisiz', 'email' => 'yetkisiz@example.test',
            'password' => 'test-password', 'password_confirmation' => 'test-password',
            'role_id' => $protected->id,
        ])->assertSessionHasErrors('role_id');
        $this->assertDatabaseMissing('users', ['email' => 'yetkisiz@example.test']);
        $this->put(route('kullanicilar.update', $admin), [])->assertForbidden();
        $this->delete(route('kullanicilar.destroy', $admin))->assertForbidden();
    }

    public function test_malformed_permission_list_returns_validation_error_and_form_still_renders(): void
    {
        $this->actingAs($this->superAdmin())->from(route('roller.create'))->post(route('roller.store'), [
            'name' => 'hata-test', 'label' => 'Hata Test',
            'izin_formu' => '1', 'permission_ids' => 'invalid',
        ])->assertSessionHasErrors('permission_ids');
        $this->get(route('roller.create'))->assertOk();
    }

    public function test_seeding_does_not_overwrite_custom_role_permissions(): void
    {
        $role = Role::where('name', 'depo')->firstOrFail();
        $ids = Permission::whereIn('name', ['kategori.listele', 'kategori.ekle'])->pluck('id');
        $role->permissions()->sync($ids);
        $this->seed(RolePermissionSeeder::class);
        $this->assertEqualsCanonicalizing($ids->all(), $role->permissions()->pluck('permissions.id')->all());
    }

    public function test_fresh_install_can_seed_one_super_admin(): void
    {
        $this->seed(UserSeeder::class);
        $this->seed(UserSeeder::class);
        $this->assertSame(1, User::whereHas('roles', fn ($query) => $query->where('is_super_admin', true))->count());
        $this->assertTrue(User::where('email', 'superadmin@b2b.test')->firstOrFail()->isSuperAdmin());
    }

    public function test_user_cannot_have_two_roles(): void
    {
        $user = $this->userWithRole(Role::where('name', 'depo')->firstOrFail());
        $otherRole = Role::where('name', 'satis')->firstOrFail();
        $this->expectException(QueryException::class);
        $user->roles()->attach($otherRole->id);
    }

    public function test_last_super_admin_cannot_remove_own_role_or_delete_own_account(): void
    {
        $admin = $this->superAdmin();
        $normalRole = Role::where('name', 'depo')->firstOrFail();
        $this->actingAs($admin)->put(route('kullanicilar.update', $admin), [
            'name' => $admin->name, 'email' => $admin->email, 'role_id' => $normalRole->id,
        ])->assertRedirect(route('kullanicilar.index'));
        $this->assertTrue($admin->fresh()->isSuperAdmin());
        $this->delete(route('kullanicilar.destroy', $admin))->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_restore_permission_does_not_add_product_views(): void
    {
        $permission = Permission::where('name', 'urun.geriyukle')->firstOrFail();
        $this->actingAs($this->superAdmin())->post(route('roller.store'), [
            'name' => 'geri-yukleme', 'label' => 'Geri Yükleme', 'permission_ids' => [$permission->id],
        ])->assertRedirect(route('roller.index'));
        $role = Role::where('name', 'geri-yukleme')->firstOrFail();
        $this->assertEqualsCanonicalizing(['urun.geriyukle'], $role->permissions()->pluck('name')->all());
    }

    public function test_super_admin_flag_cannot_be_submitted_and_unused_role_can_be_deleted(): void
    {
        $this->actingAs($this->superAdmin())->post(route('roller.store'), [
            'name' => 'sahte-admin', 'label' => 'Sahte Admin', 'is_super_admin' => true,
        ])->assertSessionHasErrors('is_super_admin');
        $this->assertDatabaseMissing('roles', ['name' => 'sahte-admin']);

        $role = Role::create(['name' => 'bos-rol', 'label' => 'Boş Rol']);
        $this->delete(route('roller.destroy', $role))->assertRedirect(route('roller.index'));
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_product_create_without_list_permission_is_reachable_and_saves(): void
    {
        $role = Role::create(['name' => 'urun-ekleyen', 'label' => 'Ürün Ekleyen']);
        $role->permissions()->sync([Permission::where('name', 'urun.ekle')->firstOrFail()->id]);
        $user = $this->userWithRole($role);
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);

        $this->actingAs($user)->get(route('anasayfa'))->assertOk()->assertSee(route('urunler.create'));
        $this->get(route('urunler.create'))->assertOk();
        $this->get(route('urunler.index'))->assertForbidden();
        $this->post(route('urunler.store'), [
            'category_id' => $category->id, 'name' => 'Yeni Ürün', 'sku' => 'YENI-001',
            'price' => '100', 'stock' => 5,
        ])->assertSessionHasNoErrors()->assertRedirect(route('anasayfa'));
        $this->assertDatabaseHas('products', ['sku' => 'YENI-001']);
    }

    public function test_order_create_without_product_or_order_list_permission_has_picker(): void
    {
        $role = Role::create(['name' => 'siparis-veren', 'label' => 'Sipariş Veren']);
        $role->permissions()->sync([Permission::where('name', 'siparis.olustur')->firstOrFail()->id]);
        $user = $this->userWithRole($role);
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $product = Product::create([
            'category_id' => $category->id, 'name' => 'Sipariş Ürünü', 'sku' => 'SIP-001',
            'price' => 100, 'stock' => 5,
        ]);

        $this->actingAs($user)->get(route('anasayfa'))->assertOk()->assertSee(route('siparisler.urun-sec'));
        $this->get(route('siparisler.index'))->assertForbidden();
        $this->get(route('urunler.index'))->assertForbidden();
        $this->get(route('siparisler.urun-sec'))->assertOk();
        $this->getJson(route('siparisler.urun-sec.veri'))
            ->assertJsonPath('data.0.name', 'Sipariş Ürünü');
        $this->get(route('siparisler.create', $product))->assertOk();
        $this->post(route('siparisler.store', $product), ['customer_name' => 'Müşteri', 'quantity' => 2])
            ->assertSessionHasNoErrors()->assertRedirect(route('siparisler.urun-sec'));
        $this->assertDatabaseHas('orders', ['product_id' => $product->id, 'quantity' => 2]);
        $this->assertSame(3, $product->fresh()->stock);
    }
}
