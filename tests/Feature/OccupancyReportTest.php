<?php

namespace Tests\Feature;

use App\Filament\Pages\OccupancyReport;
use App\Models\Reservation;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Models\RoomUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OccupancyReportTest extends TestCase
{
    use RefreshDatabase;

    private function page(string $month): OccupancyReport
    {
        $page = new OccupancyReport;
        $page->data = ['month' => $month];

        return $page;
    }

    private function createRoomType(): RoomType
    {
        return RoomType::create([
            'name' => 'Deluxe King',
            'slug' => 'deluxe-king',
            'room_type' => 'deluxe',
            'price' => 20000,
            'description' => 'A deluxe room',
            'base_guests' => 2,
            'min_stay' => 1,
            'is_active' => true,
        ]);
    }

    private function createActiveUnit(RoomType $roomType): RoomUnit
    {
        return RoomUnit::create([
            'room_type_id' => $roomType->id,
            'unit_number' => '101',
            'floor' => 1,
            'status' => 'available',
            'is_active' => true,
        ]);
    }

    public function test_get_month_normalises_full_dates_from_the_date_picker(): void
    {
        $page = $this->page('2026-08-28');

        $this->assertSame('2026-08', $page->getMonth());
    }

    public function test_get_month_passes_clean_year_month_through(): void
    {
        $page = $this->page('2026-08');

        $this->assertSame('2026-08', $page->getMonth());
    }

    public function test_get_month_falls_back_to_current_month_when_empty(): void
    {
        $page = $this->page('');

        $this->assertSame(now()->format('Y-m'), $page->getMonth());
    }

    public function test_report_sums_occupancy_and_revenue_for_the_month(): void
    {
        $roomType = $this->createRoomType();
        $unit = $this->createActiveUnit($roomType);

        RoomAvailability::create([
            'room_unit_id' => $unit->id,
            'date' => '2026-08-10',
            'status' => RoomAvailability::STATUS_BOOKED,
        ]);

        Reservation::create([
            'room_type_id' => $roomType->id,
            'guest_name' => 'Test Guest',
            'guest_email' => 'guest@example.com',
            'guest_phone' => '+2348000000000',
            'check_in' => '2026-08-01',
            'check_out' => '2026-08-31',
            'guests' => 2,
            'quoted_total' => 620000,
            'status' => Reservation::STATUS_CONFIRMED,
            'payment_status' => Reservation::PAYMENT_PAID,
            'amount_paid' => 620000,
        ]);

        Reservation::create([
            'room_type_id' => $roomType->id,
            'guest_name' => 'Cancelled Guest',
            'guest_email' => 'cancelled@example.com',
            'guest_phone' => '+2348000000001',
            'check_in' => '2026-08-01',
            'check_out' => '2026-08-10',
            'guests' => 2,
            'quoted_total' => 200000,
            'status' => Reservation::STATUS_CANCELLED,
            'payment_status' => Reservation::PAYMENT_UNPAID,
            'amount_paid' => 0,
        ]);

        $report = $this->page('2026-08')->getReport();

        $this->assertSame('August 2026', $report['month_label']);
        $this->assertSame(1, $report['active_units']);
        $this->assertSame(31, $report['total_nights']);
        $this->assertSame(1, $report['booked_nights']);
        $this->assertSame(round((1 / 31) * 100, 1), $report['occupancy']);
        // Only the confirmed reservation is counted; the cancelled one is excluded.
        $this->assertSame(1, $report['bookings']);
        $this->assertSame(620000.0, $report['revenue_potential']);
        $this->assertSame(620000.0, $report['collected']);
        $this->assertSame('Deluxe King', $report['by_room_type']->keys()->first());
    }
}