<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.locale' => 'tr']);
    }

    public function test_failed_login_preserves_email_and_remember_selection_without_refilling_the_password(): void
    {
        $user = User::factory()->create(['password' => 'correct-password-123']);

        $this->from(route('giris'))->followingRedirects()->post(route('giris.gonder'), [
            'email' => $user->email, 'password' => 'wrong-password-123', 'remember' => '1',
        ])->assertSee('adminkit/css/app.css')
            ->assertSeeText('Girdiğiniz bilgilere göre kullanıcı bulunamadı.')
            ->assertSee('value="'.$user->email.'"', false)
            ->assertSee('id="email-error" class="invalid-feedback"', false)
            ->assertSee('class="form-check-input" checked', false)
            ->assertDontSee('value="wrong-password-123"', false)
            ->assertDontSee('id="sidebar"', false);

        $this->assertGuest();
    }

    public function test_successful_login_returns_to_the_intended_page_and_keeps_the_language(): void
    {
        $user = User::factory()->create(['password' => 'correct-password-123']);

        $this->withSession(['locale' => 'en'])->get(route('profil.goster'))
            ->assertRedirect(route('giris'));

        $this->post(route('giris.gonder'), ['email' => $user->email, 'password' => 'correct-password-123', 'remember' => '1'])
            ->assertRedirect(route('profil.goster'))
            ->assertSessionHas('locale', 'en');

        $this->assertAuthenticatedAs($user);
        $this->get(route('profil.goster'))->assertSeeText('My Profile');
    }

    public function test_authenticated_users_are_redirected_from_login_to_home(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('giris'))->assertRedirect(route('anasayfa'));
    }

    public function test_user_without_management_permissions_sees_the_profile_card_and_escaped_name(): void
    {
        $user = User::factory()->create(['name' => '<script>alert(1)</script>']);

        $this->actingAs($user)->get(route('anasayfa'))
            ->assertSee('adminkit/css/app.css')
            ->assertSee('href="'.route('profil.goster').'" class="btn btn-primary mt-auto align-self-start"', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('href="'.route('kategoriler.index').'"', false)
            ->assertDontSee('href="'.route('urunler.index').'"', false)
            ->assertDontSee('href="'.route('siparisler.index').'"', false)
            ->assertDontSee('href="'.route('kullanicilar.index').'"', false)
            ->assertDontSee('href="'.route('roller.index').'"', false);
    }

    public function test_deleted_product_viewer_can_reach_the_deleted_list_from_the_home_card(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create();
        $permission = Permission::where('name', 'urun.silinenler')->firstOrFail();
        $user->permissions()->attach($permission->id, ['allowed' => true]);

        $this->actingAs($user)->get(route('anasayfa'))
            ->assertSee('href="'.route('urunler.silinenler').'" class="btn btn-primary mt-auto align-self-start"', false)
            ->assertDontSee('href="'.route('urunler.index').'"', false)
            ->assertDontSee('href="'.route('urunler.create').'"', false);
    }
}
