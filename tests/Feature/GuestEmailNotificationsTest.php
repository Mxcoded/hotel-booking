<?php

namespace Tests\Feature;

use App\Mail\GuestBookingConfirmed;
use App\Mail\GuestBookingRequestReceived;
use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\RoomUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class GuestEmailNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected RoomType $roomType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roomType = RoomType::create([
            'name' => 'Deluxe King Room',
            'slug' => 'deluxe-king-room',
            'room_type' => 'standard',
            'price' => 60000,
            'description' => 'Test room.',
            'base_guests' => 2,
            'min_stay' => 1,
            'max_stay' => 30,
            'advance_booking_days' => 365,
            'is_active' => true,
        ]);

        RoomUnit::create([
            'room_type_id' => $this->roomType->id,
            'unit_number' => '201',
            'floor' => 2,
            'is_active' => true,
        ]);
    }

    public function test_store_reservation_queues_acknowledgement_to_guest(): void
    {
        Mail::fake();

        $checkIn = now()->addDays(10)->toDateString();
        $checkOut = now()->addDays(12)->toDateString();

        $response = $this->postJson('/api/reservations', [
            'room_type_id' => $this->roomType->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 1,
            'guest_name' => 'Ada Guest',
            'guest_phone' => '+2348011223344',
            'guest_email' => 'ada@example.com',
        ]);

        $response->assertStatus(201);

        Mail::assertQueued(GuestBookingRequestReceived::class, function (GuestBookingRequestReceived $mail) {
            return $mail->hasTo('ada@example.com');
        });
    }

    public function test_store_reservation_skips_ack_when_no_email(): void
    {
        Mail::fake();

        $response = $this->postJson('/api/reservations', [
            'room_type_id' => $this->roomType->id,
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 1,
            'guest_name' => 'No Email Guest',
            'guest_phone' => '+2348011223344',
        ]);

        $response->assertStatus(201);

        Mail::assertNotQueued(GuestBookingRequestReceived::class);
    }

    public function test_confirming_reservation_queues_confirmation_to_guest(): void
    {
        Mail::fake();

        $reservation = Reservation::create([
            'room_type_id' => $this->roomType->id,
            'guest_name' => 'Tunde Guest',
            'guest_email' => 'tunde@example.com',
            'guest_phone' => '+2348022334455',
            'check_in' => now()->addDays(15)->toDateString(),
            'check_out' => now()->addDays(17)->toDateString(),
            'guests' => 1,
            'status' => Reservation::STATUS_PENDING,
            'source' => 'website',
        ]);

        $this->assertTrue($reservation->markConfirmed());

        Mail::assertQueued(GuestBookingConfirmed::class, function (GuestBookingConfirmed $mail) {
            return $mail->hasTo('tunde@example.com');
        });
    }

    public function test_confirmation_email_not_sent_when_confirm_fails(): void
    {
        Mail::fake();

        RoomUnit::query()->update(['is_active' => false]);

        $reservation = Reservation::create([
            'room_type_id' => $this->roomType->id,
            'guest_name' => 'Blocked Guest',
            'guest_email' => 'blocked@example.com',
            'guest_phone' => '+2348033445566',
            'check_in' => now()->addDays(15)->toDateString(),
            'check_out' => now()->addDays(17)->toDateString(),
            'guests' => 1,
            'status' => Reservation::STATUS_PENDING,
            'source' => 'website',
        ]);

        $this->assertFalse($reservation->markConfirmed());

        Mail::assertNotQueued(GuestBookingConfirmed::class);
    }
}
