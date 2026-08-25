<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;

class AdminDashboardTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function test_guest_cannot_see_admin_dashboard(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin')
                ->assertDontSee('Booking Funnel');
        });
    }

    public function test_admin_dashboard_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin')
                ->assertSee('Dashboard')
                ->assertSee('Booking Funnel');
        });
    }

    public function test_admin_dashboard_shows_funnel_and_stats(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin')
                ->assertSee('Unique Visitors')
                ->assertSee('WhatsApp Leads')
                ->assertSee('Booking Requests')
                ->assertSee('Confirmed Stays');
        });
    }
}

