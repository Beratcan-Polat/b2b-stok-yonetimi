<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImpersonateUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     */
    public function test_non_admin_user_cannot_access_impersonate_page()
    {
        $this->withExceptionHandling();

        $this->get('/admin/impersonate')
            ->assertRedirect('/giris');

        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get('/admin/impersonate')
            ->assertStatus(403);
    }
}
