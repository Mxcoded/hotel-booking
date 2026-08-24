<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\RoomType;
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
                ->assertSee('Standard King Room')
                ->assertSee('Deluxe');
        });
    }

    public function test_room_cards_link_to_detail_page(): void
    {
        $room = RoomType::where('slug', 'standard-king-room')->first();

        $this->browse(function ($browser) use ($room) {
            $browser->visit('/rooms')
                ->clickLink($room->name)
                ->assertPathIs('/rooms/' . $room->id)
                ->assertSee($room->name);
        });
    }

    public function test_room_detail_page_loads(): void
    {
        $room = RoomType::where('slug', 'standard-king-room')->first();

        $this->browse(function ($browser) use ($room) {
            $browser->visit('/rooms/' . $room->id)
                ->assertSee($room->name)
                ->assertSee('King Bed')
                ->assertSee('Guest(s)');
        });
    }

    public function test_room_detail_page_shows_media(): void
    {
        $room = RoomType::where('slug', 'standard-king-room')->first();

        RoomMedia::create([
            'room_type_id' => $room->id,
            'file_path' => 'room-media/seeded-detail.jpg',
            'type' => 'image',
        ]);

        $this->browse(function ($browser) use ($room) {
            $browser->visit('/rooms/' . $room->id)
                ->assertSourceHas('room-media/seeded-detail.jpg');
        });
    }
}
