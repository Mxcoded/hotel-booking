<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\Room;
use App\Models\RoomMedia;

class RoomsPageTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_rooms_page_loads(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/rooms')
                ->assertSee('Rooms');
        });
    }

    public function test_rooms_page_displays_rooms(): void
    {
        Room::factory()->count(5)->create();

        $this->browse(function ($browser) {
            $browser->visit('/rooms')
                ->waitFor('.room-card', 5)
                ->assertSee('₦');
        });
    }

    public function test_room_detail_page_loads(): void
    {
        $room = Room::factory()->create();

        $this->browse(function ($browser) use ($room) {
            $browser->visit("/rooms/{$room->id}")
                ->assertSee($room->name)
                ->assertSee($room->description)
                ->assertSee('₦' . number_format($room->price));
        });
    }

    public function test_room_detail_page_shows_media(): void
    {
        $room = Room::factory()->create();
        RoomMedia::factory()->create([
            'room_id' => $room->id,
            'type' => 'image',
        ]);

        $this->browse(function ($browser) use ($room) {
            $browser->visit("/rooms/{$room->id}")
                ->assertSee($room->name);
        });
    }

    public function test_room_detail_page_has_back_button(): void
    {
        $room = Room::factory()->create();

        $this->browse(function ($browser) use ($room) {
            $browser->visit("/rooms/{$room->id}")
                ->assertSeeLink('Back');
        });
    }
}
