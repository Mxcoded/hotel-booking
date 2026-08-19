<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\User;
use App\Models\Room;
use App\Models\Gallery;
use App\Models\Setting;
use App\Models\Attraction;
use App\Models\Contact;
use App\Models\Feedback;
use App\Models\WhatsappLead;

class AdminDashboardTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    protected function loginAsAdmin($browser): void
    {
        $user = User::first();

        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', 'password')
            ->press('Log in')
            ->waitForReload();
    }

    public function test_admin_requires_authentication(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin')
                ->assertPathIs('/login');
        });
    }

    public function test_admin_dashboard_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin')
                ->assertSee('Dashboard');
        });
    }

    public function test_admin_dashboard_shows_stats(): void
    {
        Room::factory()->count(5)->create();
        Gallery::factory()->count(3)->create();
        Contact::factory()->count(2)->create();
        Feedback::factory()->count(4)->create();

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin')
                ->assertSee('Rooms')
                ->assertSee('Gallery');
        });
    }

    public function test_admin_sidebar_navigation(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin')
                ->assertSeeLink('Rooms')
                ->assertSeeLink('Gallery')
                ->assertSeeLink('Settings');
        });
    }
}
