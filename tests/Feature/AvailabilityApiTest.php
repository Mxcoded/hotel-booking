<?php

namespace Tests\Feature;

use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Models\RoomUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvailabilityApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Build an evergreen stay range starting N days from today.
     *
     * @return array{0: string, 1: string} [check_in, check_out]
     */
    protected function stayRange(int $startOffsetDays = 10, int $nights = 3): array
    {
        $checkIn = now()->addDays($startOffsetDays);
        return [$checkIn->toDateString(), $checkIn->copy()->addDays($nights)->toDateString()];
    }

    protected function createRoomType(array $overrides = []): RoomType
    {
        return RoomType::create(array_merge([
            'name' => 'Test Deluxe',
            'slug' => 'test-deluxe',
            'room_type' => 'deluxe',
            'price' => 40000,
            'description' => 'A test room.',
            'base_guests' => 2,
            'max_guests' => 3,
            'extra_guest_fee' => 5000,
            'min_stay' => 1,
            'advance_booking_days' => 365,
            'is_active' => true,
        ], $overrides));
    }

    public function test_requires_valid_dates(): void
    {
        $this->getJson('/api/availability')->assertStatus(422);

        [$checkIn, $checkOut] = $this->stayRange(10, -1);
        $this->getJson("/api/availability?check_in={$checkIn}&check_out={$checkOut}")->assertStatus(422);

        $past = now()->subDay()->toDateString();
        $future = now()->addDays(2)->toDateString();
        $this->getJson("/api/availability?check_in={$past}&check_out={$future}")->assertStatus(422);
        $this->getJson("/api/availability?check_in=not-a-date&check_out={$future}")->assertStatus(422);
    }

    public function test_returns_active_room_types_with_pricing(): void
    {
        $roomType = $this->createRoomType(['weekend_price' => 50000]);
        RoomUnit::create(['room_type_id' => $roomType->id, 'unit_number' => '101', 'is_active' => true]);

        // Pick a start date whose 3-night window covers at least one Fri/Sat
        // so weekend pricing kicks in: total = price + 2 × weekend_price.
        $checkIn = now()->addDays(10);
        while (!in_array($checkIn->copy()->addDay()->dayOfWeek, [5, 6])) {
            $checkIn->addDay();
        }
        $checkOut = $checkIn->copy()->addDays(3); // nights: start, +1, +2 → ≥1 weekend night guaranteed

        $response = $this->getJson("/api/availability?check_in={$checkIn->toDateString()}&check_out={$checkOut->toDateString()}");

        $expectedTotal = 40000 + 2 * 50000; // base + two weekend nights (Fri & Sat)
        $response->assertOk()
            ->assertJsonPath('nights', 3)
            ->assertJsonPath('results.0.id', $roomType->id)
            ->assertJsonPath('results.0.status', 'available')
            ->assertJsonPath('results.0.units_free', 1)
            ->assertJsonPath('results.0.total', $expectedTotal);
    }

    public function test_excludes_types_below_guest_capacity(): void
    {
        $small = $this->createRoomType(['name' => 'Small', 'slug' => 'small', 'max_guests' => 2, 'base_guests' => 1]);
        $big = $this->createRoomType(['name' => 'Big', 'slug' => 'big', 'max_guests' => 4, 'base_guests' => 2]);

        [$checkIn, $checkOut] = $this->stayRange();

        $response = $this->getJson("/api/availability?check_in={$checkIn}&check_out={$checkOut}&guests=3");

        $ids = collect($response->json('results'))->pluck('id');
        $this->assertFalse($ids->contains($small->id));
        $this->assertTrue($ids->contains($big->id));
    }

    public function test_fully_booked_range_reports_unavailable(): void
    {
        $roomType = $this->createRoomType();
        $unit = RoomUnit::create(['room_type_id' => $roomType->id, 'unit_number' => '101', 'is_active' => true]);

        [$checkIn, $checkOut] = $this->stayRange();
        $bookedNight = now()->addDays(11)->toDateString(); // second night of the range

        RoomAvailability::create([
            'room_unit_id' => $unit->id,
            'date' => $bookedNight,
            'status' => 'booked',
        ]);

        $response = $this->getJson("/api/availability?check_in={$checkIn}&check_out={$checkOut}");

        $response->assertOk()
            ->assertJsonPath('results.0.status', 'unavailable')
            ->assertJsonPath('results.0.units_free', 0)
            // Price is still quoted so the guest sees what it would cost
            ->assertJsonPath('results.0.total', 120000);
    }

    public function test_second_unit_still_available_when_first_booked(): void
    {
        $roomType = $this->createRoomType();
        $u1 = RoomUnit::create(['room_type_id' => $roomType->id, 'unit_number' => '101', 'is_active' => true]);
        RoomUnit::create(['room_type_id' => $roomType->id, 'unit_number' => '102', 'is_active' => true]);

        RoomAvailability::create([
            'room_unit_id' => $u1->id,
            'date' => now()->addDays(11)->toDateString(),
            'status' => 'booked',
        ]);

        [$checkIn, $checkOut] = $this->stayRange();

        $this->getJson("/api/availability?check_in={$checkIn}&check_out={$checkOut}")
            ->assertOk()
            ->assertJsonPath('results.0.units_free', 1)
            ->assertJsonPath('results.0.status', 'available');
    }

    public function test_type_without_units_says_contact_us(): void
    {
        $this->createRoomType();

        [$checkIn, $checkOut] = $this->stayRange();

        $this->getJson("/api/availability?check_in={$checkIn}&check_out={$checkOut}")
            ->assertOk()
            ->assertJsonPath('results.0.status', 'contact_us');
    }

    public function test_inactive_rooms_excluded_and_min_stay_error_reported(): void
    {
        $active = $this->createRoomType(['name' => 'Active One', 'slug' => 'active-one']);
        $this->createRoomType(['name' => 'Inactive', 'slug' => 'inactive', 'is_active' => false]);
        RoomUnit::create(['room_type_id' => $active->id, 'unit_number' => '101', 'is_active' => true]);

        $strict = $this->createRoomType([
            'name' => 'Strict Suite',
            'slug' => 'strict-suite',
            'min_stay' => 5,
        ]);
        RoomUnit::create(['room_type_id' => $strict->id, 'unit_number' => '201', 'is_active' => true]);

        [$checkIn, $checkOut] = $this->stayRange(10, 2);

        $response = $this->getJson("/api/availability?check_in={$checkIn}&check_out={$checkOut}");

        $names = collect($response->json('results'))->pluck('name');
        $this->assertTrue($names->contains('Active One'));
        $this->assertFalse($names->contains('Inactive'));
        $this->assertTrue($names->contains('Strict Suite'));

        $strictResult = collect($response->json('results'))->firstWhere('id', $strict->id);
        $this->assertStringContainsString('Minimum stay', $strictResult['errors'][0]);
    }
}
