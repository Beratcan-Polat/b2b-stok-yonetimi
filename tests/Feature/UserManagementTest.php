<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserManagementTest extends TestCase
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
    public function test_update_returns_to_an_accessible_page_without_changing_the_password(bool $canList): void
    {
        $this->seed(RolePermissionSeeder::class);
        $manager = $this->managerWithPermissions($canList ? ['kullanici.duzenle', 'kullanici.listele'] : ['kullanici.duzenle']);
        $target = $this->userWithRole('depo');
        $password = $target->password;
        $roleId = $target->roles->sole()->id;
        $destination = $canList ? 'kullanicilar.index' : 'anasayfa';

        $this->actingAs($manager)->get(route('kullanicilar.edit', $target))
            ->assertSee('adminkit/css/app.css')
            ->assertSee('href="'.route($destination).'" class="btn btn-secondary"', false)
            ->assertDontSee('name="password"', false);

        $this->put(route('kullanicilar.update', $target), [
            'name' => 'Güncellenen Kullanıcı',
            'email' => 'guncel@example.test',
            'role_id' => $roleId,
        ])->assertRedirect(route($destination))
            ->assertSessionHas('success', 'Kullanıcı başarıyla güncellendi.');

        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => 'Güncellenen Kullanıcı', 'email' => 'guncel@example.test']);
        $this->assertSame($password, $target->fresh()->password);
        $this->assertDatabaseHas('role_user', ['user_id' => $target->id, 'role_id' => $roleId]);
    }

    #[DataProvider('listPermissions')]
    public function test_delete_returns_to_an_accessible_page(bool $canList): void
    {
        $this->seed(RolePermissionSeeder::class);
        $manager = $this->managerWithPermissions($canList ? ['kullanici.sil', 'kullanici.listele'] : ['kullanici.sil']);
        $target = $this->userWithRole('depo');
        $destination = $canList ? 'kullanicilar.index' : 'anasayfa';

        $this->actingAs($manager)->delete(route('kullanicilar.destroy', $target))
            ->assertRedirect(route($destination))
            ->assertSessionHas('success', 'Kullanıcı başarıyla silindi.');

        $this->assertModelMissing($target);
        $this->assertDatabaseMissing('role_user', ['user_id' => $target->id]);
    }

    public function test_self_delete_without_list_permission_returns_home_and_keeps_the_account(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $manager = $this->managerWithPermissions(['kullanici.sil']);

        $this->actingAs($manager)->delete(route('kullanicilar.destroy', $manager))
            ->assertRedirect(route('anasayfa'))
            ->assertSessionHas('error', 'Kendi hesabınızı silemezsiniz.');

        $this->assertModelExists($manager);
    }

    public function test_create_validation_preserves_the_role_and_empty_permission_selection_without_refilling_passwords(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->userWithRole('super-admin');
        $role = Role::where('name', 'depo')->firstOrFail();
        $existingUser = User::factory()->create();
        $permissionId = $role->permissions()->firstOrFail()->id;

        $response = $this->actingAs($admin)->from(route('kullanicilar.create'))->followingRedirects()
            ->post(route('kullanicilar.store'), [
                'name' => 'Yeni Kullanıcı',
                'email' => $existingUser->email,
                'password' => 'test-password-123',
                'password_confirmation' => 'test-password-123',
                'role_mode' => 'existing',
                'role_id' => $role->id,
                'user_permissions_present' => '1',
            ])->assertSeeText('Bu e-posta adresi daha önce kullanılmış.')
            ->assertSee('adminkit/css/app.css')
            ->assertSee('value="Yeni Kullanıcı"', false)
            ->assertSee('id="email-error" class="invalid-feedback"', false)
            ->assertDontSee('value="test-password-123"', false);

        $this->assertMatchesRegularExpression('/<option\s+value="'.$role->id.'"[^>]*\bselected\b/s', $response->getContent());
        $this->assertDoesNotMatchRegularExpression('/id="kullanici-izin-'.$permissionId.'"[^>]*\bchecked\b/', $response->getContent());
        $this->assertDatabaseCount('users', 2);
    }

    public function test_new_role_validation_preserves_the_mode_name_and_selected_permissions(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->userWithRole('super-admin');
        $permission = Permission::where('name', 'kategori.ekle')->firstOrFail();

        $response = $this->actingAs($admin)->from(route('kullanicilar.create'))->followingRedirects()
            ->post(route('kullanicilar.store'), [
                'name' => 'Yeni Kullanıcı',
                'email' => 'yeni@example.test',
                'password' => 'test-password-123',
                'password_confirmation' => 'test-password-123',
                'role_mode' => 'new',
                'new_role_name' => 'Depo',
                'new_role_permission_ids' => [$permission->id],
            ])->assertSeeText('Bu adla oluşturulan rol kodu zaten kullanılıyor.')
            ->assertSee('value="new" selected', false)
            ->assertSee('value="Depo"', false)
            ->assertSee('id="new-role-error" class="invalid-feedback"', false);

        $this->assertMatchesRegularExpression('/id="yeni-rol-izin-'.$permission->id.'"[^>]*\bchecked\b/', $response->getContent());
        $this->assertDatabaseMissing('users', ['email' => 'yeni@example.test']);
        $this->assertDatabaseMissing('roles', ['label' => 'Depo']);
    }

    public function test_edit_validation_preserves_entered_values_and_personal_permission_selections(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = $this->userWithRole('super-admin');
        $target = $this->userWithRole('depo');
        $roleId = $target->roles->sole()->id;
        $originalName = $target->name;
        $permission = Permission::where('name', 'kategori.ekle')->firstOrFail();

        $response = $this->actingAs($admin)->from(route('kullanicilar.edit', $target))->followingRedirects()
            ->put(route('kullanicilar.update', $target), [
                'name' => 'Hatalı Formdaki Ad',
                'email' => $admin->email,
                'role_id' => $roleId,
                'user_permissions_present' => '1',
                'user_permission_ids' => [$permission->id],
            ])->assertSeeText('Bu e-posta adresi daha önce kullanılmış.')
            ->assertSee('adminkit/css/app.css')
            ->assertSee('value="Hatalı Formdaki Ad"', false)
            ->assertSee('id="email-error" class="invalid-feedback"', false);

        $this->assertMatchesRegularExpression('/id="kullanici-izin-'.$permission->id.'"[^>]*\bchecked\b/', $response->getContent());
        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => $originalName, 'email' => $target->email]);
        $this->assertDatabaseMissing('permission_user', ['user_id' => $target->id]);
    }

    private function userWithRole(string $roleName): User
    {
        $user = User::factory()->create();
        $user->roles()->sync([Role::where('name', $roleName)->firstOrFail()->id]);

        return $user;
    }

    private function managerWithPermissions(array $permissions): User
    {
        $user = $this->userWithRole('depo');
        $user->permissions()->sync(Permission::whereIn('name', $permissions)->get()->mapWithKeys(
            fn (Permission $permission): array => [$permission->id => ['allowed' => true]],
        )->all());

        return $user;
    }
}
