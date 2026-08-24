<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;

class AdminRoomManagementTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_room_types_index_lists_seeded_rooms(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/room-types')
                ->assertSee('Standard King Room');
        });
    }

    public function test_room_type_create_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/room-types/create')
                ->assertSee('Create');
        });
    }

    public function test_room_units_index_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/room-units')
                ->assertSee('Room Units');
        });
    }
}

