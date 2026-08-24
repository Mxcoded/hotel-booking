<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\Gallery;

class AdminGalleryTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_gallery_index_lists_entries(): void
    {
        Gallery::create([
            'path' => 'gallery/lobby.jpg',
            'alt_text' => 'Hotel lobby',
        ]);

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/galleries')
                ->assertSee('Hotel lobby');
        });
    }

    public function test_gallery_create_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/galleries/create')
                ->assertSee('Create');
        });
    }
}
