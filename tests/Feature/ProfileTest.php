<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.locale' => 'tr']);
    }

    public function test_guests_are_redirected_from_profile_and_password_changes_to_login(): void
    {
        $this->get(route('profil.goster'))->assertRedirect(route('giris'));
        $this->patch(route('profil.sifre-guncelle'), [])->assertRedirect(route('giris'));
    }

    public function test_user_can_change_own_password_without_changing_another_account(): void
    {
        $user = User::factory()->create(['password' => 'old-password-123']);
        $other = User::factory()->create();
        $otherPassword = $other->password;

        $this->actingAs($user)->patch(route('profil.sifre-guncelle'), [
            'current_password' => 'old-password-123',
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ])->assertRedirect(route('profil.goster'))
            ->assertSessionHas('success', 'Şifre başarıyla güncellendi.');

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
        $this->assertSame($otherPassword, $other->fresh()->password);
    }

    public static function invalidPasswords(): array
    {
        return [
            'incorrect current password' => ['wrong-password', 'new-password-123', 'current-password-error', 'Mevcut şifre yanlış.'],
            'mismatched confirmation' => ['old-password-123', 'different-password-123', 'password-error', 'Yeni şifre ve onay alanı eşleşmiyor.'],
        ];
    }

    #[DataProvider('invalidPasswords')]
    public function test_password_errors_are_displayed_without_refilling_password_fields(string $currentPassword, string $confirmation, string $errorId, string $message): void
    {
        $user = User::factory()->create(['password' => 'old-password-123']);
        $originalPassword = $user->password;

        $this->actingAs($user)->from(route('profil.goster'))->followingRedirects()
            ->patch(route('profil.sifre-guncelle'), [
                'current_password' => $currentPassword,
                'password' => 'new-password-123',
                'password_confirmation' => $confirmation,
            ])->assertSee('adminkit/css/app.css')
            ->assertSeeText($message)
            ->assertSee('id="'.$errorId.'" class="invalid-feedback"', false)
            ->assertDontSee('value="'.$currentPassword.'"', false)
            ->assertDontSee('value="new-password-123"', false)
            ->assertDontSee('value="'.$confirmation.'"', false);

        $this->assertSame($originalPassword, $user->fresh()->password);
    }

    public function test_impersonated_sessions_cannot_change_passwords_and_show_the_return_button(): void
    {
        $user = User::factory()->create(['password' => 'old-password-123']);
        $admin = User::factory()->create();
        $originalPassword = $user->password;

        $this->actingAs($user)->withSession(['impersonator_id' => $admin->id])->get(route('profil.goster'))
            ->assertSeeText('Başka bir kullanıcı hesabındayken parola değiştirilemez.')
            ->assertSee('action="'.route('admin.impersonate.return').'"', false)
            ->assertDontSee('action="'.route('profil.sifre-guncelle').'"', false);

        $this->patch(route('profil.sifre-guncelle'), [
            'current_password' => 'old-password-123', 'password' => 'new-password-123', 'password_confirmation' => 'new-password-123',
        ])->assertForbidden();

        $this->assertSame($originalPassword, $user->fresh()->password);
    }
}
