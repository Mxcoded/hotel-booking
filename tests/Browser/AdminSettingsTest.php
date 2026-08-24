<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;

class AdminSettingsTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_settings_index_lists_seeded_settings(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/settings')
                ->assertSee('Site Name');
        });
    }
}
