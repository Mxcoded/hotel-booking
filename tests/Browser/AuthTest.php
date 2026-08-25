<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\User;

class AuthTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function test_login_page_loads(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                ->assertInputPresent('email')
                ->assertInputPresent('password')
                ->assertSourceHas('Sign in to your admin account');
        });
    }

    public function test_user_can_login(): void
    {
        $user = User::where('email', 'admin@brickspoint.ng')->first();

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('#login-btn');

            $browser->waitUntil(
                'window.location.pathname === "/admin" || window.location.pathname === "/dashboard"',
                30
            );

            $browser->assertPathIs('/admin');
        });
    }

    public function test_login_rejects_bad_credentials(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                ->type('email', 'admin@brickspoint.ng')
                ->type('password', 'wrong-password')
                ->press('#login-btn');

            $browser->waitUntil('document.querySelector(".auth-error") !== null', 15);

            $browser->assertPathIs('/login');
        });
    }

    public function test_user_can_register(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/register')
                ->type('name', 'Dusk Newbie')
                ->type('email', 'newbie@example.com')
                ->type('password', 'super-secret-123')
                ->type('password_confirmation', 'super-secret-123')
                ->check('terms')
                ->press('#register-btn');

            $browser->waitUntil(
                'window.location.pathname !== "/register"',
                30
            );
        });

        $user = User::where('email', 'newbie@example.com')->first();

        $this->assertNotNull($user, 'Registered user was not persisted.');
    }
}

