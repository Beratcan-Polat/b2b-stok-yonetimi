<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserPasswordResetAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    private function userWithRole(string $roleName): User
    {
        $user = User::factory()->create();
        $user->roles()->sync([Role::where('name', $roleName)->firstOrFail()->id]);

        return $user;
    }

    private function managerWithEditPermission(): User
    {
        $manager = $this->userWithRole('depo');
        $permission = Permission::where('name', 'kullanici.duzenle')->firstOrFail();
        $manager->permissions()->sync([$permission->id => ['allowed' => true]]);

        return $manager;
    }

    public function test_manager_does_not_see_password_fields_on_edit_page(): void
    {
        $manager = $this->managerWithEditPermission();
        $target = $this->userWithRole('depo');

        $this->actingAs($manager)->get(route('kullanicilar.edit', $target))
            ->assertOk()
            ->assertDontSee('name="password"', false)
            ->assertDontSee('name="password_confirmation"', false);
    }

    public function test_manager_cannot_reset_another_users_password_with_a_forged_request(): void
    {
        $manager = $this->managerWithEditPermission();
        $target = $this->userWithRole('depo');
        $originalPassword = $target->password;

        $this->actingAs($manager)->put(route('kullanicilar.update', $target), [
            'name' => 'Yeni Ad',
            'email' => $target->email,
            'role_id' => $target->roles()->firstOrFail()->id,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasErrors(['password' => 'Başka bir kullanıcının parolasını yalnızca Süper Admin değiştirebilir.']);

        $this->assertSame($originalPassword, $target->fresh()->password);
        $this->assertSame($target->name, $target->fresh()->name);
    }

    public function test_super_admin_sees_password_fields_on_edit_page(): void
    {
        $admin = $this->userWithRole('super-admin');
        $target = $this->userWithRole('depo');

        $this->actingAs($admin)->get(route('kullanicilar.edit', $target))
            ->assertOk()
            ->assertSee('name="password"', false)
            ->assertSee('name="password_confirmation"', false);
    }

    public function test_super_admin_can_reset_another_users_password(): void
    {
        $admin = $this->userWithRole('super-admin');
        $target = $this->userWithRole('depo');

        $this->actingAs($admin)->put(route('kullanicilar.update', $target), [
            'name' => $target->name,
            'email' => $target->email,
            'role_id' => $target->roles()->firstOrFail()->id,
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertSessionHasNoErrors()->assertRedirect(route('kullanicilar.index'));

        $this->assertTrue(Hash::check('new-password-123', $target->fresh()->password));
    }

    public function test_manager_can_still_create_a_user_with_an_initial_password(): void
    {
        $manager = $this->userWithRole('depo');
        $permission = Permission::where('name', 'kullanici.ekle')->firstOrFail();
        $manager->permissions()->sync([$permission->id => ['allowed' => true]]);
        $role = Role::where('name', 'depo')->firstOrFail();

        $this->actingAs($manager)->post(route('kullanicilar.store'), [
            'name' => 'Yeni Kullanıcı',
            'email' => 'yeni-kullanici@example.test',
            'password' => 'initial-password-123',
            'password_confirmation' => 'initial-password-123',
            'role_mode' => 'existing',
            'role_id' => $role->id,
        ])->assertSessionHasNoErrors()->assertRedirect(route('anasayfa'));

        $createdUser = User::where('email', 'yeni-kullanici@example.test')->firstOrFail();
        $this->assertTrue(Hash::check('initial-password-123', $createdUser->password));
    }
}
