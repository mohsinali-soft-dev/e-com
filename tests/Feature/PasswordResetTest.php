<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_can_be_rendered(): void
    {
        $this->get(route('password.request'))->assertOk();
    }

    public function test_reset_link_can_be_requested(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.check-email'))
            ->assertSessionHasNoErrors()
            ->assertSessionHas('reset_email', $user->email);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_check_email_page_can_be_rendered(): void
    {
        $this->get(route('password.check-email'))
            ->assertOk()
            ->assertSee('Check your email');
    }

    public function test_password_can_be_reset_with_a_valid_token(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->post(route('password.email'), ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use ($user) {
            $this->get(route('password.reset', [
                'token' => $notification->token,
                'email' => $user->email,
            ]))
                ->assertOk()
                ->assertSee('readonly', false);

            $response = $this->post(route('password.update'), [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

            $response->assertRedirect(route('login'))->assertSessionHasNoErrors();

            return true;
        });

        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
    }
}
