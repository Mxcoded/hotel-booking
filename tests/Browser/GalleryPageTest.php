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
                ->assertSee('Our Gallery');
        });
    }

    public function test_gallery_displays_images(): void
    {
        Gallery::create([
            'path' => 'gallery/poolside.jpg',
            'alt_text' => 'Poolside at dusk',
        ]);

        $this->browse(function ($browser) {
            $browser->visit('/gallery')
                ->assertSourceHas('gallery/poolside.jpg')
                ->assertSourceHas('Poolside at dusk');
        });
    }
}
