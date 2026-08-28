<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Models\RoomUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected RoomType $roomType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roomType = RoomType::create([
            'name' => 'Executive King Room',
            'slug' => 'executive-king-room',
            'room_type' => 'executive',
            'price' => 80000,
            'description' => 'Test room.',
            'base_guests' => 2,
            'min_stay' => 1,
            'max_stay' => 30,
            'advance_booking_days' => 365,
            'is_active' => true,
        ]);

        RoomUnit::create([
            'room_type_id' => $this->roomType->id,
            'unit_number' => '301',
            'floor' => 3,
            'is_active' => true,
        ]);
    }

    public function test_full_lifecycle_pending_to_checked_out(): void
    {
        $reservation = Reservation::create([
            'room_type_id' => $this->roomType->id,
            'guest_name' => 'Lifecycle Guest',
            'guest_phone' => '+2348011223344',
            'check_in' => now()->addDays(5)->toDateString(),
            'check_out' => now()->addDays(7)->toDateString(),
            'guests' => 1,
            'status' => Reservation::STATUS_PENDING,
            'source' => 'website',
        ]);

        $this->assertEquals(Reservation::STATUS_PENDING, $reservation->status);

        $reservation->markConfirmed();
        $reservation->refresh();
        $this->assertEquals(Reservation::STATUS_CONFIRMED, $reservation->status);
        $this->assertNotNull($reservation->room_unit_id);

        // Nights booked while confirmed.
        $this->assertSame(2, RoomAvailability::query()
            ->where('room_unit_id', $reservation->room_unit_id)
            ->where('status', 'booked')->count());

        $reservation->markCheckedIn();
        $reservation->refresh();
        $this->assertEquals(Reservation::STATUS_CHECKED_IN, $reservation->status);
        $this->assertNotNull($reservation->checked_in_at);

        // Unit occupied on front desk.
        $this->assertEquals('occupied', $reservation->roomUnit->status);

        $reservation->markCheckedOut();
        $reservation->refresh();
        $this->assertEquals(Reservation::STATUS_CHECKED_OUT, $reservation->status);
        $this->assertNotNull($reservation->checked_out_at);
        $this->assertEquals('available', $reservation->roomUnit->fresh()->status);
    }

    public function test_no_show_releases_nights(): void
    {
        $reservation = Reservation::create([
            'room_type_id' => $this->roomType->id,
            'guest_name' => 'No Show Guest',
            'guest_phone' => '+2348022334455',
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(4)->toDateString(),
            'guests' => 1,
            'status' => Reservation::STATUS_CONFIRMED,
            'source' => 'website',
        ]);

        $reservation->refresh();
        $this->assertEquals(Reservation::STATUS_CONFIRMED, $reservation->status);
        $unitId = $reservation->room_unit_id;
        $this->assertNotNull($unitId);

        $reservation->markNoShow();
        $reservation->refresh();
        $this->assertEquals(Reservation::STATUS_NO_SHOW, $reservation->status);

        // Nights released once no-show.
        $this->assertSame(0, RoomAvailability::query()
            ->where('room_unit_id', $unitId)
            ->where('status', 'booked')
            ->where('note', "Booking #{$reservation->id}")->count());
    }

    public function test_payment_recording_updates_status_after_full_payment(): void
    {
        $reservation = Reservation::create([
            'room_type_id' => $this->roomType->id,
            'guest_name' => 'Pay Guest',
            'guest_phone' => '+2348033445566',
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(4)->toDateString(),
            'guests' => 1,
            'quoted_total' => 50000,
            'status' => Reservation::STATUS_CONFIRMED,
            'source' => 'website',
        ]);

        $this->assertEquals(Reservation::PAYMENT_UNPAID, $reservation->payment_status);

        $reservation->recordPayment(20000, 'paystack', 'REF-1');
        $reservation->refresh();
        $this->assertEquals(Reservation::PAYMENT_PARTIAL, $reservation->payment_status);
        $this->assertEquals(30000, (float) $reservation->balanceDue());

        $reservation->recordPayment(30000, 'paystack', 'REF-2');
        $reservation->refresh();
        $this->assertEquals(Reservation::PAYMENT_PAID, $reservation->payment_status);
        $this->assertEquals(0, (float) $reservation->balanceDue());
    }
}
