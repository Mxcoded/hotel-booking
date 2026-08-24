<?php

namespace Tests\Feature;

use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class StayQuoteApiTest extends TestCase
{
    use RefreshDatabase;

    protected function createRoomType(array $overrides = []): RoomType
    {
        return RoomType::create(array_merge([
            'name' => 'Deluxe King',
            'slug' => 'deluxe-king',
            'room_type' => 'deluxe',
            'price' => 20000,
            'description' => 'A deluxe room',
            'base_guests' => 2,
            'min_stay' => 1,
            'is_active' => true,
        ], $overrides));
    }

    protected function quoteUrl(RoomType $roomType, string $checkIn, string $checkOut, ?int $guests = null): string
    {
        return route('api.stay-quote', array_filter([
            'room_type_id' => $roomType->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => $guests,
        ]));
    }

    public function test_applies_weekend_rate_on_friday_and_saturday_nights(): void
    {
        // No units configured -> availability should be "contact_us"
        $room = $this->createRoomType(['weekend_price' => 30000]);

        // Next Thursday (standard night) followed by Friday (weekend night)
        $checkIn = now()->startOfDay()->next(Carbon::THURSDAY);
        $checkOut = $checkIn->copy()->addDays(2);

        $response = $this->getJson($this->quoteUrl($room, $checkIn->toDateString(), $checkOut->toDateString()));

        $response->assertOk()
            ->assertJsonPath('nights', 2)
            ->assertJsonPath('availability', 'contact_us');

        $body = $response->json();
        $this->assertEquals(50000, $body['subtotal']); // 20000 + 30000
        $this->assertEquals(50000, $body['total']);

        $nightly = $body['nightly'];
        $this->assertEquals(20000, $nightly[0]['price']); // Thursday
        $this->assertEquals(30000, $nightly[1]['price']); // Friday
    }

    public function test_applies_holiday_rate_on_christmas(): void
    {
        $room = $this->createRoomType(['holiday_price' => 60000]);

        // Next upcoming Dec 25 (must be today or later for validation).
        // Nights are checkout-exclusive: staying Dec 25 -> 26 covers the holiday night.
        $christmas = Carbon::create(now()->year, 12, 25)->startOfDay();
        if ($christmas->lt(now())) {
            $christmas->addYear();
        }
        $checkIn = $christmas->copy();
        $checkOut = $christmas->copy()->addDay();

        $response = $this->getJson($this->quoteUrl($room, $checkIn->toDateString(), $checkOut->toDateString()));

        $response->assertOk()
            ->assertJsonPath('nights', 1);

        $this->assertEquals(60000, $response->json('total'));
    }

    public function test_applies_discount_within_discount_period(): void
    {
        $start = now()->startOfDay()->addDays(10);
        $room = $this->createRoomType([
            'discount_percent' => 10,
            'discount_start' => $start->toDateString(),
            'discount_end' => $start->copy()->addDays(30)->toDateString(),
        ]);

        $checkIn = $start->copy()->addDay(); // avoid edge ambiguity
        $checkOut = $checkIn->copy()->addDays(2);

        $response = $this->getJson($this->quoteUrl($room, $checkIn->toDateString(), $checkOut->toDateString()));

        $response->assertOk();
        $this->assertEquals(36000, $response->json('subtotal')); // 2 x (20000 * 0.9)
    }

    public function test_adds_extra_guest_fee_and_caps_at_max_occupancy(): void
    {
        $room = $this->createRoomType([
            'base_guests' => 2,
            'max_guests' => 4,
            'extra_guest_fee' => 5000,
        ]);

        $checkIn = now()->startOfDay()->addDays(3);
        $checkOut = $checkIn->copy()->addDay();

        // Within capacity: 2 extra guests
        $within = $this->getJson($this->quoteUrl($room, $checkIn->toDateString(), $checkOut->toDateString(), 4))
            ->assertOk()
            ->assertJsonPath('errors', []);

        $this->assertEquals(10000, $within->json('extra_guest_fee'));
        $this->assertEquals(30000, $within->json('total'));

        // Beyond capacity: fee capped at max-base guests AND an error returned
        $beyond = $this->getJson($this->quoteUrl($room, $checkIn->toDateString(), $checkOut->toDateString(), 9))
            ->assertOk();

        $this->assertEquals(10000, $beyond->json('extra_guest_fee'));

        $errors = $beyond->json('errors');
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('up to 4 guest(s)', $errors[0]);
    }

    public function test_reports_minimum_stay_violation(): void
    {
        $room = $this->createRoomType(['min_stay' => 3]);

        $checkIn = now()->startOfDay()->addDays(5);
        $checkOut = $checkIn->copy()->addDay();

        $response = $this->getJson($this->quoteUrl($room, $checkIn->toDateString(), $checkOut->toDateString()));

        $response->assertOk()
            ->assertJsonPath('nights', 1);

        $errors = $response->json('errors');
        $this->assertCount(1, $errors);
        $this->assertStringContainsString('Minimum stay', $errors[0]);
    }

    public function test_returns_404_for_unknown_or_inactive_room_types(): void
    {
        $inactive = $this->createRoomType(['is_active' => false]);
        $checkIn = now()->startOfDay()->addDays(5);
        $checkOut = $checkIn->copy()->addDay();

        $this->getJson($this->quoteUrl($inactive, $checkIn->toDateString(), $checkOut->toDateString()))
            ->assertNotFound();

        $this->getJson(route('api.stay-quote', [
            'room_type_id' => 999999,
            'check_in' => $checkIn->toDateString(),
            'check_out' => $checkOut->toDateString(),
        ]))->assertNotFound();
    }

    public function test_validates_dates_and_required_parameters(): void
    {
        $room = $this->createRoomType();
        $checkIn = now()->startOfDay()->addDays(5);

        // Missing parameters
        $this->getJson(route('api.stay-quote'))
            ->assertUnprocessable();

        // Check-out before check-in
        $this->getJson($this->quoteUrl(
            $room,
            $checkIn->toDateString(),
            $checkIn->copy()->subDay()->toDateString()
        ))->assertUnprocessable();

        // Check-in in the past
        $this->getJson($this->quoteUrl(
            $room,
            now()->subDays(3)->toDateString(),
            $checkIn->toDateString()
        ))->assertUnprocessable();

        // Malformed date format
        $this->getJson($this->quoteUrl($room, '25/12/2026', $checkIn->toDateString()))
            ->assertUnprocessable();
    }
}
