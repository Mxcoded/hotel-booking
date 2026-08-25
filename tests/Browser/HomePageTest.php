<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;

class HomePageTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function test_home_page_loads_with_branding(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->assertSee('Brickspoint')
                ->assertSee('Featured Rooms & Suites');
        });
    }

    public function test_home_page_has_navigation(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->assertSeeLink('Home')
                ->assertSeeLink('Rooms')
                ->assertSeeLink('Gallery')
                ->assertSeeLink('Menu')
                ->assertSeeLink('Explore Wuse II');
        });
    }

    public function test_home_page_displays_seeded_rooms(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->waitForText('Standard King Room', 15)
                ->assertSee('Standard King Room');
        });
    }

    public function test_home_page_has_contact_section(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->within('#contact', function ($browser) {
                    $browser->assertSee('Or Send Us a Message')
                        ->assertInputPresent('name')
                        ->assertInputPresent('email')
                        ->assertInputPresent('message')
                        ->assertButtonEnabled('Send Message');
                });
        });
    }

    public function test_home_page_has_whatsapp_button(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->assertSee('Book Now on WhatsApp');
        });
    }

    public function test_navigate_to_rooms_page(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->clickLink('Rooms')
                ->assertPathIs('/rooms')
                ->assertSee('Standard King Room');
        });
    }

    public function test_navigate_to_gallery_page(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->clickLink('Gallery')
                ->assertPathIs('/gallery')
                ->assertSee('Our Gallery');
        });
    }

    public function test_navigate_to_local_guide_page(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->clickLink('Explore Wuse II')
                ->assertPathIs('/local-guide')
                ->assertSee('Explore Wuse II');
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
                ->assertInputPresent('rating')
                ->assertInputPresent('name')
                ->assertInputPresent('email')
                ->assertInputPresent('message');
        });
    }
}

