<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\User;

class AuthTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_login_page_loads(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                ->assertSee('Email')
                ->assertSee('Password')
                ->assertSee('Log in');
        });
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'email' => 'dusk@example.com',
            'password' => 'password',
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Log in')
                ->waitForReload()
                ->assertPathIs('/admin');
        });
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'email' => 'dusk@example.com',
            'password' => 'password',
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'wrong-password')
                ->press('Log in')
                ->assertSee('These credentials do not match our records');
        });
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'dusk@example.com',
            'password' => 'password',
        ]);

        $this->browse(function ($browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Log in')
                ->waitForReload()
                ->visit('/admin')
                ->clickLink('Logout')
                ->waitForReload()
                ->assertPathIs('/');
        });
    }

    public function test_register_page_loads(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/register')
                ->assertSee('Name')
                ->assertSee('Email')
                ->assertSee('Password')
                ->assertSee('Register');
        });
    }

    public function test_user_can_register(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/register')
                ->type('name', 'New User')
                ->type('email', 'newuser@example.com')
                ->type('password', 'Password123!')
                ->type('password_confirmation', 'Password123!')
                ->press('Register')
                ->waitForReload();
        });

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_forgot_password_page_loads(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/forgot-password')
                ->assertSee('Email')
                ->assertSee('Send');
        });
    }
}
