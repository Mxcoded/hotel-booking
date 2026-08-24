<?php

namespace Tests\Feature;

use App\Mail\ReservationRequestReceived;
use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\RoomUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReservationApiTest extends TestCase
{
    use RefreshDatabase;

    protected array $range;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('mail.from.address', 'fallback@example.test');

        $checkIn = now()->addDays(10);
        $this->range = ['check_in' => $checkIn->toDateString(), 'check_out' => $checkIn->copy()->addDays(2)->toDateString()];
    }

    protected function roomTypeWithUnit(array $overrides = []): RoomType
    {
        $roomType = RoomType::create(array_merge([
            'name' => 'Bookable Deluxe',
            'slug' => 'bookable-deluxe',
            'room_type' => 'deluxe',
            'price' => 50000,
            'description' => 'A test room.',
            'base_guests' => 2,
            'max_guests' => 3,
            'extra_guest_fee' => 5000,
            'min_stay' => 1,
            'advance_booking_days' => 365,
            'is_active' => true,
        ], $overrides));

        RoomUnit::create(['room_type_id' => $roomType->id, 'unit_number' => '101', 'is_active' => true]);

        return $roomType;
    }

    protected function validPayload(RoomType $roomType, array $overrides = []): array
    {
        return array_merge([
            'room_type_id' => $roomType->id,
            'check_in' => $this->range['check_in'],
            'check_out' => $this->range['check_out'],
            'guests' => 2,
            'guest_name' => 'Ada Obi',
            'guest_phone' => '+2348012345678',
            'guest_email' => 'ada@example.com',
        ], $overrides);
    }

    public function test_creates_pending_reservation_with_server_computed_total(): void
    {
        Mail::fake();
        $roomType = $this->roomTypeWithUnit();

        // Client tries to cheat with a bogus total field; it must be ignored.
        $response = $this->postJson('/api/reservations', $this->validPayload($roomType, [
            'quoted_total' => 1,
        ]));

        $response->assertCreated()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 100000); // json_encode emits integral floats as ints

        $reservation = Reservation::first();
        $this->assertEquals(Reservation::STATUS_PENDING, $reservation->status);
        $this->assertEquals(100000.0, (float) $reservation->quoted_total); // 2 nights × 50k, server-computed
        $this->assertNull($reservation->confirmed_at);

        Mail::assertQueued(ReservationRequestReceived::class, function (ReservationRequestReceived $mail) {
            return $mail->hasTo('fallback@example.test');
        });
    }

    public function test_staff_email_setting_used_for_notification(): void
    {
        Mail::fake();
        \App\Models\Setting::create(['key' => 'email', 'value' => 'front-desk@brickspoint.test', 'type' => 'text']);
        $roomType = $this->roomTypeWithUnit();

        $this->postJson('/api/reservations', $this->validPayload($roomType))->assertCreated();

        Mail::assertQueued(ReservationRequestReceived::class, fn ($mail) => $mail->hasTo('front-desk@brickspoint.test'));
    }

    public function test_email_is_optional(): void
    {
        Mail::fake();
        $roomType = $this->roomTypeWithUnit();

        $payload = $this->validPayload($roomType);
        unset($payload['guest_email']);

        $this->postJson('/api/reservations', $payload)->assertCreated();

        $reservation = Reservation::first();
        $this->assertNull($reservation->guest_email);
    }

    public function test_validates_required_fields(): void
    {
        $this->postJson('/api/reservations', [])->assertStatus(422);

        $roomType = $this->roomTypeWithUnit();
        $payload = $this->validPayload($roomType);
        unset($payload['guest_name'], $payload['guest_phone']);

        $this->postJson('/api/reservations', $payload)->assertStatus(422);
    }

    public function test_honeypot_blocks_submission(): void
    {
        Mail::fake();
        $roomType = $this->roomTypeWithUnit();

        $this->postJson('/api/reservations', $this->validPayload($roomType, ['honeypot' => 'gotcha']))
            ->assertStatus(422);

        $this->assertDatabaseCount('reservations', 0);
        Mail::assertNothingQueued();
    }

    public function test_rejects_unavailable_dates(): void
    {
        Mail::fake();
        $roomType = $this->roomTypeWithUnit();

        // Book the unit's second night directly
        \App\Models\RoomAvailability::create([
            'room_unit_id' => \App\Models\RoomUnit::first()->id,
            'date' => now()->addDays(11)->toDateString(),
            'status' => 'booked',
        ]);

        $response = $this->postJson('/api/reservations', $this->validPayload($roomType));

        $response->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'no longer available'));

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_rejects_type_without_units(): void
    {
        $roomType = RoomType::create([
            'name' => 'Ghost Suite',
            'slug' => 'ghost-suite',
            'room_type' => 'suite',
            'price' => 50000,
            'description' => 'No units yet.',
            'base_guests' => 2,
            'min_stay' => 1,
            'advance_booking_days' => 365,
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/reservations', $this->validPayload($roomType));

        $response->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'contact us'));
    }

    public function test_rejects_occupancy_and_stay_violations(): void
    {
        $roomType = $this->roomTypeWithUnit(['max_guests' => 2]);

        $this->postJson('/api/reservations', $this->validPayload($roomType, ['guests' => 5]))
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'accommodates up to'));

        $strict = $this->roomTypeWithUnit(['name' => 'Strict', 'slug' => 'strict', 'min_stay' => 5]);
        $this->postJson('/api/reservations', $this->validPayload($strict))
            ->assertStatus(422)
            ->assertJsonPath('message', fn ($m) => str_contains($m, 'stay of'));
    }

    public function test_status_transitions(): void
    {
        $roomType = $this->roomTypeWithUnit();
        $reservation = Reservation::create($this->validPayload($roomType) + [
            'quoted_total' => 100000,
            'status' => Reservation::STATUS_PENDING,
        ]);

        $reservation->markConfirmed();
        $this->assertEquals(Reservation::STATUS_CONFIRMED, $reservation->fresh()->status);
        $this->assertNotNull($reservation->fresh()->confirmed_at);

        $reservation->markCancelled();
        $this->assertEquals(Reservation::STATUS_CANCELLED, $reservation->fresh()->status);
        $this->assertNull($reservation->fresh()->confirmed_at);
    }
}
