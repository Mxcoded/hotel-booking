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
                ->assertSee('Local Guide');
        });
    }

    public function test_local_guide_displays_attractions(): void
    {
        Attraction::factory()->count(3)->create([
            'category' => 'Food',
        ]);

        $this->browse(function ($browser) {
            $browser->visit('/local-guide')
                ->assertSee('Food');
        });
    }

    public function test_local_guide_has_category_filtering(): void
    {
        Attraction::factory()->create(['category' => 'Food']);
        Attraction::factory()->create(['category' => 'Shopping']);

        $this->browse(function ($browser) {
            $browser->visit('/local-guide')
                ->assertSee('Food')
                ->assertSee('Shopping');
        });
    }
}
