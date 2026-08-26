<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Models\RoomUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AvailabilitySyncTest extends TestCase
{
    use RefreshDatabase;

    protected RoomType $roomType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roomType = RoomType::create([
            'name' => 'Standard King Room',
            'slug' => 'standard-king-room',
            'room_type' => 'standard',
            'price' => 45000,
            'description' => 'Test room.',
            'base_guests' => 2,
            'min_stay' => 1,
            'max_stay' => 30,
            'advance_booking_days' => 365,
            'is_active' => true,
        ]);

        RoomUnit::create([
            'room_type_id' => $this->roomType->id,
            'unit_number' => '101',
            'floor' => 1,
            'is_active' => true,
        ]);

        RoomUnit::create([
            'room_type_id' => $this->roomType->id,
            'unit_number' => '102',
            'floor' => 1,
            'is_active' => true,
        ]);
    }

    protected function stayDates(int $inDays = 30): array
    {
        return [
            'check_in' => now()->addDays($inDays)->toDateString(),
            'check_out' => now()->addDays($inDays + 2)->toDateString(),
        ];
    }

    protected function makeReservation(array $dates, array $overrides = []): Reservation
    {
        return Reservation::create(array_merge([
            'room_type_id' => $this->roomType->id,
            'guest_name' => 'Sync Test Guest',
            'guest_phone' => '+2348011223344',
            'check_in' => $dates['check_in'],
            'check_out' => $dates['check_out'],
            'guests' => 1,
            'status' => Reservation::STATUS_PENDING,
            'source' => 'website',
        ], $overrides));
    }

    public function test_confirming_reservation_books_unit_nights(): void
    {
        $reservation = $this->makeReservation($this->stayDates());

        $reservation->markConfirmed();

        $reservation->refresh();

        \Log::info('Reservation confirmed, room_unit_id: ' . $reservation->room_unit_id);

        $bookedAll = RoomAvailability::query()
            ->where('room_unit_id', $reservation->room_unit_id)
            ->where('status', 'booked')
            ->orderBy('date')
            ->get();
        \Log::info('All booked nights for unit: ' . $bookedAll->count());
        foreach ($bookedAll as $b) {
            \Log::info('  Booked: ' . $b->date->toDateString() . ' status: ' . $b->status . ' note: ' . $b->note);
        }

        $this->assertNotNull($reservation->room_unit_id, 'A unit should be assigned on confirmation.');
        $this->assertEquals(Reservation::STATUS_CONFIRMED, $reservation->status);

        $booked = RoomAvailability::query()
            ->where('room_unit_id', $reservation->room_unit_id)
            ->where('status', 'booked')
            ->orderBy('date')
            ->get();

        $this->assertCount(2, $booked);
        $this->assertEquals($reservation->check_in->toDateString(), $booked[0]->date->toDateString());
        $this->assertEquals(
            $reservation->check_out->copy()->subDay()->toDateString(),
            $booked[1]->date->toDateString()
        );
        $this->assertSame("Booking #{$reservation->id}", $booked[0]->note);
    }

    public function test_cancelling_reservation_releases_nights(): void
    {
        $reservation = $this->makeReservation($this->stayDates());
        $reservation->markConfirmed();

        $unitId = $reservation->room_unit_id;
        $this->assertNotNull($unitId);

        $reservation->markCancelled();
        $reservation->refresh();

        $remaining = RoomAvailability::query()
            ->where('room_unit_id', $unitId)
            ->where('status', 'booked')
            ->where('note', "Booking #{$reservation->id}")
            ->count();

        $this->assertSame(0, $remaining, 'Cancelling must release the booked nights.');
    }

    public function test_overlapping_confirmed_reservations_get_different_units(): void
    {
        $first = $this->makeReservation($this->stayDates());
        $second = $this->makeReservation($this->stayDates(), ['guest_name' => 'Second Guest']);

        $first->markConfirmed();
        $second->markConfirmed();

        $this->assertNotNull($first->room_unit_id);
        $this->assertNotNull($second->room_unit_id);
        $this->assertNotSame($first->room_unit_id, $second->room_unit_id);
    }

    public function test_confirmation_fails_when_no_unit_is_free(): void
    {
        // Leave a single active unit for this type
        RoomUnit::where('room_type_id', $this->roomType->id)
            ->whereKeyNot(RoomUnit::query()->where('room_type_id', $this->roomType->id)->orderBy('id')->value('id'))
            ->update(['is_active' => false]);

        $first = $this->makeReservation($this->stayDates());
        $second = $this->makeReservation($this->stayDates(), ['guest_name' => 'Second Guest']);

        $this->assertTrue((bool) $first->markConfirmed());
        $this->assertFalse((bool) $second->markConfirmed(), 'Confirming without a free unit must fail.');
        $this->assertNull($second->fresh()->room_unit_id);
        $this->assertEquals(Reservation::STATUS_CONFIRMED, $second->fresh()->status);

        // The second reservation must NOT have blocked any nights
        $rows = RoomAvailability::query()
            ->where('note', "Booking #{$second->id}")
            ->count();

        $this->assertSame(0, $rows);
    }

    public function test_guest_availability_api_reflects_booked_nights(): void
    {
        [$in, $out] = array_values($this->stayDates());

        // Confirm one reservation per active unit so the whole type is booked.
        $unitCount = RoomUnit::query()->where('room_type_id', $this->roomType->id)->active()->count();
        $this->assertGreaterThan(0, $unitCount);

        for ($i = 0; $i < $unitCount; $i++) {
            $this->makeReservation(
                ['check_in' => $in, 'check_out' => $out],
                ['guest_name' => "Guest {$i}"]
            )->markConfirmed();
        }

        $response = $this->getJson("/api/availability?check_in={$in}&check_out={$out}&guests=1");

        $response->assertOk();

        $match = collect($response->json('results'))->firstWhere('id', $this->roomType->id);

        $this->assertNotNull($match);
        $this->assertSame('unavailable', $match['status']);
        $this->assertSame(0, $match['units_free']);
    }

    public function test_availability_search_runs_a_bounded_number_of_queries(): void
    {
        [$in, $out] = array_values($this->stayDates());

        DB::enableQueryLog();

        $this->getJson("/api/availability?check_in={$in}&check_out={$out}&guests=1")->assertOk();

        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // One index query + room types/units/settings — never per-unit-per-night.
        $this->assertLessThan(
            40,
            $queryCount,
            "Availability search issued {$queryCount} queries; the batched index is regressing."
        );
    }
}
