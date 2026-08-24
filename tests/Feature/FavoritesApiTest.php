<?php

namespace Tests\Feature;

use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesApiTest extends TestCase
{
    use RefreshDatabase;

    protected function createRoomType(array $overrides = []): RoomType
    {
        return RoomType::create(array_merge([
            'name' => 'Standard Room',
            'slug' => 'standard-room',
            'room_type' => 'standard',
            'price' => 20000,
            'description' => 'A nice room',
            'base_guests' => 2,
            'is_active' => true,
        ], $overrides));
    }

    public function test_returns_whitelisted_fields_with_mapped_image_url(): void
    {
        $room = $this->createRoomType([
            'slug' => 'with-image',
            'image' => 'room-types/pic.jpg',
            'weekend_price' => 25000,
        ]);

        $response = $this->postJson(route('api.favorites'), ['ids' => [$room->id]]);

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment([
                'id' => $room->id,
                'name' => 'Standard Room',
                'price' => 20000.0,
                'weekend_price' => 25000.0,
                'image_url' => asset('storage/room-types/pic.jpg'),
            ]);

        // Ensure sensitive/unneeded columns are not leaked
        $this->assertArrayNotHasKey('description', $response->json()[0]);
        $this->assertArrayNotHasKey('seasonal_pricing', $response->json()[0]);
    }

    public function test_null_image_maps_to_null_image_url(): void
    {
        $room = $this->createRoomType(['slug' => 'no-image']);

        $response = $this->postJson(route('api.favorites'), ['ids' => [$room->id]]);

        $response->assertOk()
            ->assertJsonFragment(['image_url' => null]);
    }

    public function test_normalizes_prefixed_roomtype_ids(): void
    {
        $room = $this->createRoomType();

        $response = $this->postJson(route('api.favorites'), ['ids' => ["roomtype-{$room->id}"]]);

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $room->id]);
    }

    public function test_excludes_inactive_room_types(): void
    {
        $active = $this->createRoomType(['slug' => 'active-one']);
        $inactive = $this->createRoomType(['slug' => 'inactive-one', 'is_active' => false]);

        $response = $this->postJson(route('api.favorites'), ['ids' => [$active->id, $inactive->id]]);

        $response->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => $active->id]);
    }

    public function test_empty_and_garbage_ids_return_empty_array(): void
    {
        $this->postJson(route('api.favorites'), ['ids' => []])
            ->assertOk()
            ->assertExactJson([]);

        $this->postJson(route('api.favorites'), ['ids' => ['roomtype-', 'abc', null]])
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_caps_results_at_fifty(): void
    {
        for ($i = 1; $i <= 55; $i++) {
            $this->createRoomType(['name' => "Room {$i}", 'slug' => "room-{$i}"]);
        }

        $ids = RoomType::pluck('id')->toArray();

        $this->postJson(route('api.favorites'), ['ids' => $ids])
            ->assertOk()
            ->assertJsonCount(50);
    }
}
