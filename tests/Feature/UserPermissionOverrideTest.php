<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPermissionOverrideTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(string $name): User
    {
        $user = User::factory()->create();
        $user->roles()->sync([Role::where('name', $name)->firstOrFail()->id]);

        return $user;
    }

    private function override(string $name, string $state): array
    {
        return ['permission_id' => Permission::where('name', $name)->firstOrFail()->id, 'state' => $state];
    }

    private function updateUser(User $user, array $overrides)
    {
        return $this->put(route('kullanicilar.update', $user), [
            'name' => $user->name, 'email' => $user->email,
            'role_id' => $user->roles()->firstOrFail()->id,
            'permission_overrides' => $overrides,
        ]);
    }

    public function test_extra_permission_only_applies_to_ahmet_not_mehmet(): void
    {
        $admin = $this->userWithRole('super-admin');
        $ahmet = $this->userWithRole('depo');
        $mehmet = $this->userWithRole('depo');

        $this->actingAs($admin);
        $this->updateUser($ahmet, [$this->override('kullanici.ekle', 'allow')])->assertRedirect(route('kullanicilar.index'));
        $this->assertTrue($ahmet->hasPermissionTo('kullanici.ekle'));
        $this->assertFalse($ahmet->hasPermissionTo('kullanici.listele'));
        $this->assertFalse($mehmet->hasPermissionTo('kullanici.ekle'));
        $this->assertFalse($mehmet->hasPermissionTo('kullanici.listele'));
        $this->actingAs($ahmet)->get(route('kullanicilar.create'))->assertOk()->assertDontSee('Kullanıcıya Özel Yetkiler');
        $this->actingAs($mehmet)->get(route('kullanicilar.create'))->assertForbidden();
    }

    public function test_deny_overrides_role_and_inherit_restores_access(): void
    {
        $admin = $this->userWithRole('super-admin');
        $user = $this->userWithRole('depo');
        $this->actingAs($admin);
        $this->updateUser($user, [$this->override('urun.sil', 'deny')])->assertRedirect(route('kullanicilar.index'));
        $this->assertFalse($user->hasPermissionTo('urun.sil'));
        $this->assertTrue($user->hasPermissionTo('urun.duzenle'));

        $this->updateUser($user, [$this->override('urun.sil', 'inherit')])->assertRedirect(route('kullanicilar.index'));
        $this->assertTrue($user->hasPermissionTo('urun.sil'));
        $this->assertSame(0, $user->permissions()->count());
    }

    public function test_blocked_list_permission_does_not_block_create(): void
    {
        $user = $this->userWithRole('depo');
        $this->actingAs($this->userWithRole('super-admin'));
        $this->updateUser($user, [$this->override('urun.listele', 'deny')])->assertRedirect(route('kullanicilar.index'));
        $this->assertFalse($user->hasPermissionTo('urun.listele'));
        $this->assertTrue($user->hasPermissionTo('urun.ekle'));
        $this->actingAs($user)->get(route('urunler.create'))->assertOk();
        $this->get(route('urunler.index'))->assertForbidden();
    }

    public function test_normal_manager_cannot_write_personal_permissions(): void
    {
        $manager = $this->userWithRole('depo');
        $target = $this->userWithRole('depo');
        $manager->permissions()->sync([
            Permission::where('name', 'kullanici.duzenle')->firstOrFail()->id => ['allowed' => true],
            Permission::where('name', 'kullanici.listele')->firstOrFail()->id => ['allowed' => true],
        ]);
        $this->actingAs($manager);
        $this->updateUser($target, [$this->override('kategori.sil', 'allow')])->assertSessionHasErrors('permission_overrides');
        $this->assertSame(0, $target->permissions()->count());
    }

    public function test_normal_update_preserves_personal_permissions_when_field_is_absent(): void
    {
        $manager = $this->userWithRole('depo');
        $target = $this->userWithRole('depo');
        $manager->permissions()->sync([
            Permission::where('name', 'kullanici.duzenle')->firstOrFail()->id => ['allowed' => true],
            Permission::where('name', 'kullanici.listele')->firstOrFail()->id => ['allowed' => true],
        ]);
        $denied = Permission::where('name', 'urun.sil')->firstOrFail();
        $target->permissions()->sync([$denied->id => ['allowed' => false]]);
        $this->actingAs($manager)->put(route('kullanicilar.update', $target), [
            'name' => 'Güncel', 'email' => $target->email, 'role_id' => $target->roles()->firstOrFail()->id,
        ])->assertRedirect(route('kullanicilar.index'));
        $this->assertFalse($target->hasPermissionTo('urun.sil'));
        $this->assertSame(1, $target->permissions()->count());
    }

    public function test_super_admin_can_create_user_with_personal_permission(): void
    {
        $this->actingAs($this->userWithRole('super-admin'))->post(route('kullanicilar.store'), [
            'name' => 'Ahmet', 'email' => 'ahmet@example.test',
            'password' => 'test-password', 'password_confirmation' => 'test-password',
            'role_id' => Role::where('name', 'depo')->firstOrFail()->id,
            'permission_overrides' => [$this->override('kategori.ekle', 'allow')],
        ])->assertRedirect(route('kullanicilar.index'));
        $user = User::where('email', 'ahmet@example.test')->firstOrFail();
        $this->assertTrue($user->hasPermissionTo('kategori.ekle'));
        $this->assertFalse($user->hasPermissionTo('kategori.listele'));
        $this->assertFalse($user->hasPermissionTo('kategori.sil'));
    }

    public function test_personal_allow_and_list_deny_can_coexist(): void
    {
        $this->actingAs($this->userWithRole('super-admin'))->post(route('kullanicilar.store'), [
            'name' => 'Hata', 'email' => 'hata@example.test',
            'password' => 'test-password', 'password_confirmation' => 'test-password',
            'role_id' => Role::where('name', 'depo')->firstOrFail()->id,
            'permission_overrides' => [$this->override('kategori.ekle', 'allow'), $this->override('kategori.listele', 'deny')],
        ])->assertSessionHasNoErrors()->assertRedirect(route('kullanicilar.index'));
        $user = User::where('email', 'hata@example.test')->firstOrFail();
        $this->assertTrue($user->hasPermissionTo('kategori.ekle'));
        $this->assertFalse($user->hasPermissionTo('kategori.listele'));
    }

    public function test_forms_show_personal_selections_and_reject_invalid_payload(): void
    {
        $admin = $this->userWithRole('super-admin');
        $user = $this->userWithRole('depo');
        $permission = Permission::where('name', 'urun.sil')->firstOrFail();
        $user->permissions()->sync([$permission->id => ['allowed' => false]]);
        $this->actingAs($admin)->get(route('kullanicilar.create'))->assertOk()->assertSee('Kullanıcıya Özel Yetkiler');
        $html = $this->get(route('kullanicilar.edit', $user))->assertOk()->getContent();
        $this->assertMatchesRegularExpression('/id="kullanici-izin-'.$permission->id.'"[^>]*>/', $html);
        $this->assertDoesNotMatchRegularExpression('/id="kullanici-izin-'.$permission->id.'"[^>]*\bchecked\b/', $html);
        $this->from(route('kullanicilar.edit', $user))->put(route('kullanicilar.update', $user), [
            'name' => $user->name, 'email' => $user->email, 'role_id' => $user->roles()->firstOrFail()->id,
            'permission_overrides' => [['permission_id' => 999999, 'state' => 'invalid']],
        ])->assertSessionHasErrors(['permission_overrides.0.permission_id', 'permission_overrides.0.state']);
        $this->get(route('kullanicilar.edit', $user))->assertOk();
    }

    public function test_individual_category_deny_is_enforced_on_http_requests(): void
    {
        $role = Role::where('name', 'depo')->firstOrFail();
        $role->permissions()->syncWithoutDetaching(Permission::whereIn('name', ['kategori.listele', 'kategori.duzenle'])->pluck('id'));
        $user = $this->userWithRole('depo');
        $category = Category::create(['name' => 'Test', 'slug' => 'test']);
        $this->actingAs($this->userWithRole('super-admin'));
        $this->updateUser($user, [$this->override('kategori.duzenle', 'deny')])->assertRedirect(route('kullanicilar.index'));
        $this->actingAs($user)->put(route('kategoriler.update', $category), ['name' => 'Yeni'])->assertForbidden();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Test']);
    }

    public function test_personal_grants_do_not_open_role_management_or_super_admin_assignment(): void
    {
        $user = $this->userWithRole('depo');
        $this->actingAs($this->userWithRole('super-admin'));
        $this->updateUser($user, [$this->override('kullanici.ekle', 'allow')])->assertRedirect(route('kullanicilar.index'));
        $this->actingAs($user)->get(route('roller.index'))->assertForbidden();
        $this->post(route('kullanicilar.store'), [
            'name' => 'Yetkisiz', 'email' => 'yetkisiz@example.test',
            'password' => 'test-password', 'password_confirmation' => 'test-password',
            'role_id' => Role::where('name', 'super-admin')->firstOrFail()->id,
        ])->assertSessionHasErrors('role_id');
    }

    public function test_checkboxes_save_only_differences_from_role(): void
    {
        $ahmet = $this->userWithRole('depo');
        $mehmet = $this->userWithRole('depo');
        $role = $ahmet->roles()->firstOrFail();
        $extra = Permission::where('name', 'kullanici.ekle')->firstOrFail();
        $removed = Permission::where('name', 'urun.sil')->firstOrFail();
        $selected = $role->permissions()->where('permissions.id', '!=', $removed->id)->pluck('permissions.id')->push($extra->id)->all();

        $this->actingAs($this->userWithRole('super-admin'))->put(route('kullanicilar.update', $ahmet), [
            'name' => $ahmet->name, 'email' => $ahmet->email, 'role_id' => $role->id,
            'user_permissions_present' => '1', 'user_permission_ids' => $selected,
        ])->assertSessionHasNoErrors()->assertRedirect(route('kullanicilar.index'));

        $this->assertTrue($ahmet->hasPermissionTo('kullanici.ekle'));
        $this->assertFalse($ahmet->hasPermissionTo('kullanici.listele'));
        $this->assertFalse($ahmet->hasPermissionTo('urun.sil'));
        $this->assertFalse($mehmet->hasPermissionTo('kullanici.ekle'));
        $this->assertTrue($mehmet->hasPermissionTo('urun.sil'));
        $this->assertSame(2, $ahmet->permissions()->count());
    }

    public function test_all_unchecked_removes_role_access_for_only_target_user(): void
    {
        $user = $this->userWithRole('depo');
        $this->actingAs($this->userWithRole('super-admin'))->put(route('kullanicilar.update', $user), [
            'name' => $user->name, 'email' => $user->email, 'role_id' => $user->roles()->firstOrFail()->id,
            'user_permissions_present' => '1',
        ])->assertSessionHasNoErrors()->assertRedirect(route('kullanicilar.index'));
        $this->assertFalse($user->hasPermissionTo('urun.listele'));
        $this->assertFalse($user->hasPermissionTo('urun.ekle'));
    }

    public function test_unchanged_checkboxes_keep_dynamic_role_inheritance(): void
    {
        $user = $this->userWithRole('depo');
        $role = $user->roles()->firstOrFail();
        $this->actingAs($this->userWithRole('super-admin'))->put(route('kullanicilar.update', $user), [
            'name' => $user->name, 'email' => $user->email, 'role_id' => $role->id,
            'user_permissions_present' => '1', 'user_permission_ids' => $role->permissions()->pluck('permissions.id')->all(),
        ])->assertSessionHasNoErrors();
        $this->assertSame(0, $user->permissions()->count());
        $role->permissions()->syncWithoutDetaching(Permission::whereIn('name', ['kategori.listele', 'kategori.ekle'])->pluck('id'));
        $this->assertTrue($user->hasPermissionTo('kategori.ekle'));
    }

    public function test_non_super_admin_cannot_submit_checkboxes(): void
    {
        $manager = $this->userWithRole('depo');
        $target = $this->userWithRole('depo');
        $manager->permissions()->sync(Permission::whereIn('name', ['kullanici.listele', 'kullanici.duzenle'])->get()->mapWithKeys(fn ($permission) => [$permission->id => ['allowed' => true]])->all());
        $this->actingAs($manager)->put(route('kullanicilar.update', $target), [
            'name' => $target->name, 'email' => $target->email, 'role_id' => $target->roles()->firstOrFail()->id,
            'user_permissions_present' => '1', 'user_permission_ids' => [Permission::where('name', 'kategori.sil')->firstOrFail()->id],
        ])->assertSessionHasErrors(['user_permissions_present', 'user_permission_ids']);
        $this->assertSame(0, $target->permissions()->count());
    }

    public function test_create_with_checkboxes_and_role_defaults_render_checked(): void
    {
        $role = Role::where('name', 'depo')->firstOrFail();
        $ids = $role->permissions()->pluck('permissions.id')->push(Permission::where('name', 'kategori.ekle')->firstOrFail()->id)->all();
        $this->actingAs($this->userWithRole('super-admin'))->post(route('kullanicilar.store'), [
            'name' => 'Checkbox', 'email' => 'checkbox@example.test',
            'password' => 'test-password', 'password_confirmation' => 'test-password',
            'role_id' => $role->id, 'user_permissions_present' => '1', 'user_permission_ids' => $ids,
        ])->assertSessionHasNoErrors()->assertRedirect(route('kullanicilar.index'));
        $user = User::where('email', 'checkbox@example.test')->firstOrFail();
        $this->assertTrue($user->hasPermissionTo('kategori.ekle'));
        $this->assertFalse($user->hasPermissionTo('kategori.sil'));
        $html = $this->get(route('kullanicilar.edit', $user))->assertOk()->getContent();
        $id = Permission::where('name', 'urun.listele')->firstOrFail()->id;
        $this->assertMatchesRegularExpression('/id="kullanici-izin-'.$id.'"[^>]*\bchecked\b/', $html);
        $this->assertStringNotContainsString('Rolden geleni kullan</option>', $html);
    }

    public function test_super_admin_creates_user_and_new_role_in_one_request(): void
    {
        $admin = $this->userWithRole('super-admin');
        $permission = Permission::where('name', 'kategori.ekle')->firstOrFail();

        $this->actingAs($admin)->post(route('kullanicilar.store'), [
            'name' => 'Ayşe', 'email' => 'ayse@example.test',
            'password' => 'test-password', 'password_confirmation' => 'test-password',
            'role_mode' => 'new', 'new_role_name' => 'Depo Sorumlusu Özel',
            'new_role_permission_ids' => [$permission->id],
        ])->assertSessionHasNoErrors()->assertRedirect(route('kullanicilar.index'));

        $role = Role::where('name', 'depo-sorumlusu-ozel')->firstOrFail();
        $user = User::where('email', 'ayse@example.test')->firstOrFail();
        $this->assertSame('Depo Sorumlusu Özel', $role->label);
        $this->assertSame($role->id, $user->roles()->firstOrFail()->id);
        $this->assertTrue($user->hasPermissionTo('kategori.ekle'));
        $this->assertFalse($user->hasPermissionTo('kategori.listele'));
        $this->assertFalse($user->hasPermissionTo('kategori.sil'));
        $this->assertSame(0, $user->permissions()->count());
    }

    public function test_duplicate_generated_slug_does_not_create_user_or_role(): void
    {
        $this->actingAs($this->userWithRole('super-admin'))->post(route('kullanicilar.store'), [
            'name' => 'Yeni', 'email' => 'duplicate@example.test',
            'password' => 'test-password', 'password_confirmation' => 'test-password',
            'role_mode' => 'new', 'new_role_name' => 'Depo',
        ])->assertSessionHasErrors('new_role_slug');

        $this->assertDatabaseMissing('users', ['email' => 'duplicate@example.test']);
        $this->assertSame(1, Role::where('name', 'depo')->count());
    }

    public function test_normal_manager_cannot_create_new_role_from_user_form(): void
    {
        $manager = $this->userWithRole('depo');
        $manager->permissions()->sync(Permission::whereIn('name', ['kullanici.listele', 'kullanici.ekle'])->get()->mapWithKeys(fn ($permission) => [$permission->id => ['allowed' => true]])->all());

        $this->actingAs($manager)->post(route('kullanicilar.store'), [
            'name' => 'Yetkisiz', 'email' => 'unauthorized@example.test',
            'password' => 'test-password', 'password_confirmation' => 'test-password',
            'role_mode' => 'new', 'new_role_name' => 'Gizli Rol',
        ])->assertSessionHasErrors('role_mode');

        $this->assertDatabaseMissing('users', ['email' => 'unauthorized@example.test']);
        $this->assertDatabaseMissing('roles', ['name' => 'gizli-rol']);
    }

    public function test_create_form_offers_new_role_only_to_super_admin(): void
    {
        $admin = $this->userWithRole('super-admin');
        $this->actingAs($admin)->get(route('kullanicilar.create'))
            ->assertOk()->assertSee('Yeni rol oluştur')->assertSee('new_role_permission_ids[]');

        $manager = $this->userWithRole('depo');
        $manager->permissions()->sync(Permission::whereIn('name', ['kullanici.listele', 'kullanici.ekle'])->get()->mapWithKeys(fn ($permission) => [$permission->id => ['allowed' => true]])->all());
        $this->actingAs($manager)->get(route('kullanicilar.create'))
            ->assertOk()->assertDontSee('Yeni rol oluştur')->assertDontSee('new_role_permission_ids[]');
    }
}
