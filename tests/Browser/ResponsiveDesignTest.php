<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\User;

class ResponsiveDesignTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_home_page_mobile_view(): void
    {
        $this->browse(function ($browser) {
            $browser->resize(375, 812)
                ->visit('/')
                ->assertSee('Brickspoint')
                ->assertSee('Hotel');
        });
    }

    public function test_rooms_page_mobile_view(): void
    {
        $this->browse(function ($browser) {
            $browser->resize(375, 812)
                ->visit('/rooms')
                ->assertSee('Rooms');
        });
    }

    public function test_login_page_mobile_view(): void
    {
        $this->browse(function ($browser) {
            $browser->resize(375, 812)
                ->visit('/login')
                ->assertSee('Email')
                ->assertSee('Password');
        });
    }

    public function test_tablet_view(): void
    {
        $this->browse(function ($browser) {
            $browser->resize(768, 1024)
                ->visit('/')
                ->assertSee('Brickspoint');
        });
    }

    public function test_desktop_view(): void
    {
        $this->browse(function ($browser) {
            $browser->resize(1920, 1080)
                ->visit('/')
                ->assertSee('Brickspoint');
        });
    }
}
