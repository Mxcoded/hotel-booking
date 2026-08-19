<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\User;
use App\Models\Room;

class AdminRoomManagementTest extends DuskTestCase
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

    public function test_rooms_index_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/rooms')
                ->assertSee('Rooms');
        });
    }

    public function test_rooms_index_displays_rooms(): void
    {
        Room::factory()->count(3)->create();

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/rooms')
                ->assertSee('Create');
        });
    }

    public function test_create_room_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/rooms/create')
                ->assertSee('Create')
                ->assertSee('Name')
                ->assertSee('Price')
                ->assertSee('Description');
        });
    }

    public function test_can_create_room(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/rooms/create')
                ->type('name', 'Deluxe Suite')
                ->type('price', '50000')
                ->type('guests', '4')
                ->type('description', 'A beautiful deluxe suite with ocean view.')
                ->attach('image', base_path('tests/Fixtures/test-image.jpg'))
                ->press('Create')
                ->waitForReload();
        });

        $this->assertDatabaseHas('rooms', [
            'name' => 'Deluxe Suite',
            'price' => '50000.00',
            'guests' => 4,
        ]);
    }

    public function test_edit_room_page_loads(): void
    {
        $room = Room::factory()->create();

        $this->browse(function ($browser) use ($room) {
            $this->loginAsAdmin($browser);
            $browser->visit("/admin/rooms/{$room->id}/edit")
                ->assertSee('Edit')
                ->assertSee($room->name);
        });
    }

    public function test_can_update_room(): void
    {
        $room = Room::factory()->create(['name' => 'Old Name']);

        $this->browse(function ($browser) use ($room) {
            $this->loginAsAdmin($browser);
            $browser->visit("/admin/rooms/{$room->id}/edit")
                ->type('name', 'Updated Suite')
                ->press('Save')
                ->waitForReload();
        });

        $this->assertDatabaseHas('rooms', [
            'id' => $room->id,
            'name' => 'Updated Suite',
        ]);
    }

    public function test_can_delete_room(): void
    {
        $room = Room::factory()->create();

        $this->browse(function ($browser) use ($room) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/rooms')
                ->press("Delete")
                ->waitForReload();
        });

        $this->assertDatabaseMissing('rooms', [
            'id' => $room->id,
        ]);
    }
}
