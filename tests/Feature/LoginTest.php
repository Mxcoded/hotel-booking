<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_returns_ok(): void
    {
        $this->get('/login')
            ->assertOk();
    }

    public function test_login_page_renders_form_fields(): void
    {
        $this->get('/login')
            ->assertSee('Welcome back')
            ->assertSee('Sign in to your admin account to continue')
            ->assertSeeInOrder(['Email address', 'Password', 'Remember me', 'Log in']);
    }

    public function test_login_page_has_honeypot_field(): void
    {
        $this->get('/login')
            ->assertSee('name="honeypot"', false)
            ->assertSee('tabindex="-1"', false);
    }

    public function test_login_page_has_password_toggle(): void
    {
        $this->get('/login')
            ->assertSee('togglePassword', false)
            ->assertSee('pw-show', false)
            ->assertSee('pw-hide', false);
    }

    public function test_login_page_has_back_to_website_link(): void
    {
        $this->get('/login')
            ->assertSee('Back to website');
    }

    public function test_login_page_displays_branding(): void
    {
        $this->get('/login')
            ->assertSee('Brickspoint Hotel')
            ->assertSee('Admin Portal');
    }

    public function test_login_page_shows_showcase_features(): void
    {
        $this->get('/login')
            ->assertSee('Secure & Private', false)
            ->assertSee('Room & Booking Management', false)
            ->assertSee('Real-time Dashboard', false);
    }

    public function test_login_page_has_register_link(): void
    {
        $this->get('/login')
            ->assertSee('Create a new account');
    }

    public function test_login_page_has_forgot_password_link(): void
    {
        $this->get('/login')
            ->assertSee('Forgot password?')
            ->assertSee(route('password.request'));
    }

    public function test_unauthenticated_user_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')
            ->assertRedirect('/login');
    }

    public function test_already_authenticated_user_redirected_from_login(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/login')
            ->assertRedirect('/');
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->postLogin([])
            ->assertSessionHasErrors(['email', 'password']);
    }

    public function test_login_requires_valid_email_format(): void
    {
        $this->postLogin([
            'email' => 'not-an-email',
            'password' => 'password',
        ])
            ->assertSessionHasErrors(['email']);
    }

    public function test_login_with_nonexistent_user_fails(): void
    {
        $this->postLogin([
            'email' => 'nobody@example.com',
            'password' => 'password',
        ])
            ->assertSessionHasErrors(['email']);
    }

    public function test_login_with_wrong_password_fails(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $this->postLogin([
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ])
            ->assertSessionHasErrors(['email']);
    }

    public function test_login_with_valid_credentials_succeeds(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@brickspoint.ng',
            'email_verified_at' => now(),
        ]);

        $this->postLogin([
            'email' => 'admin@brickspoint.ng',
            'password' => 'password',
        ])
            ->assertRedirect('/admin/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_remember_me_creates_persistent_session(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@brickspoint.ng',
            'email_verified_at' => now(),
        ]);

        $response = $this->postLogin([
            'email' => 'admin@brickspoint.ng',
            'password' => 'password',
            'remember' => 'on',
        ]);

        $response->assertRedirect('/admin/dashboard');

        $this->assertAuthenticatedAs($user);

        $cookieNames = collect($response->headers->getCookies())
            ->map(fn ($cookie) => $cookie->getName())
            ->values()
            ->all();

        $hasRememberCookie = collect($cookieNames)
            ->contains(fn ($name) => str_starts_with($name, 'remember'));

        $this->assertTrue($hasRememberCookie, 'Expected remember_me cookie to be set. Cookies: ' . implode(', ', $cookieNames));
    }

    public function test_unverified_user_cannot_access_admin(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->get('/admin/dashboard')
            ->assertRedirect('/verify-email');
    }

    public function test_login_with_empty_email_returns_validation_error(): void
    {
        $this->postLogin([
            'email' => '',
            'password' => 'password',
        ])
            ->assertSessionHasErrors(['email']);
    }

    public function test_login_with_empty_password_returns_validation_error(): void
    {
        $user = User::factory()->create();

        $this->postLogin([
            'email' => $user->email,
            'password' => '',
        ])
            ->assertSessionHasErrors(['password']);
    }

    public function test_honeypot_field_submission_is_rejected(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->postLogin([
            'email' => $user->email,
            'password' => 'password',
            'honeypot' => 'bot-filled-this',
        ])
            ->assertSessionHasErrors();
    }

    public function test_login_page_has_split_layout_structure(): void
    {
        $this->get('/login')
            ->assertSee('auth-split', false)
            ->assertSee('auth-showcase', false)
            ->assertSee('auth-form-panel', false)
            ->assertSee('auth-card', false);
    }

    public function test_login_page_has_input_icons(): void
    {
        $this->get('/login')
            ->assertSee('input-icon', false)
            ->assertSee('auth-input-group', false);
    }

    private function postLogin(array $data)
    {
        $this->get('/login');

        return $this->post('/login', array_merge([
            '_token' => $this->app['session.store']->token(),
        ], $data));
    }
}
