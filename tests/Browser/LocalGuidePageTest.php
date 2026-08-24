<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\Attraction;

class LocalGuidePageTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_local_guide_page_loads(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/local-guide')
                ->assertSee('Explore Wuse II');
        });
    }

    public function test_local_guide_displays_attractions(): void
    {
        Attraction::create([
            'name' => 'Jabi Lake Mall',
            'category' => 'shopping',
            'description' => 'Lakeside shopping mall ten minutes away.',
            'image' => 'attractions/jabi-lake.jpg',
        ]);

        $this->browse(function ($browser) {
            $browser->visit('/local-guide')
                ->assertSee('Jabi Lake Mall')
                ->assertSee('Lakeside shopping mall ten minutes away.');
        });
    }
}
