<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\User;
use App\Models\Gallery;

class AdminGalleryTest extends DuskTestCase
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

    public function test_gallery_index_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/gallery')
                ->assertSee('Gallery');
        });
    }

    public function test_can_upload_gallery_image(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/gallery')
                ->attach('image', base_path('tests/Fixtures/test-image.jpg'))
                ->press('Upload')
                ->waitForReload();
        });

        $this->assertDatabaseHas('galleries', [
            'alt_text' => '',
        ]);
    }

    public function test_can_delete_gallery_image(): void
    {
        $gallery = Gallery::factory()->create();

        $this->browse(function ($browser) use ($gallery) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/gallery')
                ->press("Delete")
                ->waitForReload();
        });

        $this->assertDatabaseMissing('galleries', [
            'id' => $gallery->id,
        ]);
    }
}
