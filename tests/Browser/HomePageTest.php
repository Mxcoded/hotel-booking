<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\Room;
use App\Models\Gallery;
use App\Models\Setting;
use App\Models\Attraction;
use App\Models\Feedback;

class HomePageTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_home_page_loads(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->assertSee('Brickspoint')
                ->assertSee('Hotel')
                ->assertSee('Rooms');
        });
    }

    public function test_home_page_has_navigation(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->assertSeeLink('Home')
                ->assertSeeLink('Rooms')
                ->assertSeeLink('Gallery')
                ->assertSeeLink('Local Guide');
        });
    }

    public function test_home_page_displays_featured_rooms(): void
    {
        Room::factory()->count(3)->create();

        $this->browse(function ($browser) {
            $browser->visit('/')
                ->waitFor('.room-card', 5)
                ->assertSee('₦');
        });
    }

    public function test_home_page_has_contact_section(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->assertSee('Contact')
                ->assertSee('Send')
                ->assertSee('Message');
        });
    }

    public function test_home_page_has_whatsapp_button(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->assertSee('WhatsApp');
        });
    }

    public function test_navigate_to_rooms_page(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->clickLink('Rooms')
                ->assertPathIs('/rooms')
                ->assertSee('Rooms');
        });
    }

    public function test_navigate_to_gallery_page(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->clickLink('Gallery')
                ->assertPathIs('/gallery')
                ->assertSee('Gallery');
        });
    }

    public function test_navigate_to_local_guide_page(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->clickLink('Local Guide')
                ->assertPathIs('/local-guide')
                ->assertSee('Local Guide');
        });
    }

    public function test_navigate_to_favorites_page(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/favorites')
                ->assertSee('Favorites');
        });
    }

    public function test_navigate_to_feedback_page(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/feedback')
                ->assertSee('Feedback')
                ->assertSee('Rating');
        });
    }
}
