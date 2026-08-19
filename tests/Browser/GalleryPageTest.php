<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\Gallery;

class GalleryPageTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_gallery_page_loads(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/gallery')
                ->assertSee('Gallery');
        });
    }

    public function test_gallery_page_displays_images(): void
    {
        Gallery::factory()->count(5)->create();

        $this->browse(function ($browser) {
            $browser->visit('/gallery')
                ->assertSee('Gallery');
        });
    }

    public function test_gallery_page_has_back_to_home(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/gallery')
                ->assertSeeLink('Home');
        });
    }
}
