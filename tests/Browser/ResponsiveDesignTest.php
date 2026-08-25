<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;

class ResponsiveDesignTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function test_home_page_renders_on_mobile(): void
    {
        $this->browse(function ($browser) {
            $browser->resize(375, 812)
                ->visit('/')
                ->assertSee('Brickspoint')
                ->assertSee('Book Now on WhatsApp')
                ->resize(1920, 1080);
        });
    }

    public function test_rooms_page_renders_on_mobile(): void
    {
        $this->browse(function ($browser) {
            $browser->resize(375, 812)
                ->visit('/rooms')
                ->assertSee('Standard King Room')
                ->resize(1920, 1080);
        });
    }
}

