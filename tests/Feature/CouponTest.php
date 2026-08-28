<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\RoomUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    protected RoomType $roomType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roomType = RoomType::create([
            'name' => 'Premier Room',
            'slug' => 'premier-room',
            'room_type' => 'suite',
            'price' => 100000,
            'description' => 'Test.',
            'base_guests' => 2,
            'min_stay' => 1,
            'max_stay' => 30,
            'advance_booking_days' => 365,
            'is_active' => true,
        ]);

        RoomUnit::create(['room_type_id' => $this->roomType->id, 'unit_number' => '401', 'floor' => 4, 'is_active' => true]);
    }

    protected function bookingPayload(array $overrides = []): array
    {
        return array_merge([
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 1,
            'guest_name' => 'Coupon Guest',
            'guest_phone' => '+2348088776655',
        ], $overrides);
    }

    public function test_percent_coupon_reduces_total(): void
    {
        Coupon::create([
            'code' => 'SAVE10',
            'discount_type' => Coupon::TYPE_PERCENT,
            'discount_value' => 10,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/reservations', $this->bookingPayload(['coupon_code' => 'SAVE10']));

        $response->assertStatus(201);

        $reservation = Reservation::first();
        $this->assertEquals('SAVE10', $reservation->coupon_code);
        // 2 nights @ 100,000 each = 200,000, minus 10% = 180,000
        $this->assertEquals(180000, (float) $reservation->quoted_total);
        $this->assertEquals(20000, (float) $reservation->discount_amount);
    }

    public function test_invalid_coupon_rejects_booking(): void
    {
        $response = $this->postJson('/api/reservations', $this->bookingPayload(['coupon_code' => 'NOPE']));
        $response->assertStatus(422);
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_expired_coupon_rejects_booking(): void
    {
        Coupon::create([
            'code' => 'OLDD',
            'discount_type' => Coupon::TYPE_PERCENT,
            'discount_value' => 5,
            'valid_until' => now()->subDay(),
            'max_uses' => 100,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/reservations', $this->bookingPayload(['coupon_code' => 'OLDD']));
        $response->assertStatus(422);
        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_usage_limit_enforced(): void
    {
        Coupon::create([
            'code' => 'USEONCE',
            'discount_type' => Coupon::TYPE_FIXED,
            'discount_value' => 5000,
            'max_uses' => 1,
            'is_active' => true,
        ]);

        $this->postJson('/api/reservations', $this->bookingPayload(['coupon_code' => 'USEONCE']))->assertStatus(201);
        $this->postJson('/api/reservations', $this->bookingPayload([
            'coupon_code' => 'USEONCE',
            'guest_name' => 'Second Guest',
        ]))->assertStatus(422);

        $this->assertDatabaseCount('reservations', 1);
    }
}
