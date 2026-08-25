<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\Reservation;
use App\Models\RoomType;

class AdminOperationsTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    protected function roomType(): RoomType
    {
        return RoomType::where('slug', 'standard-king-room')->first();
    }

    public function test_reservations_index_lists_requests(): void
    {
        Reservation::create([
            'room_type_id' => $this->roomType()->id,
            'guest_name' => 'Ngozi Booking',
            'guest_phone' => '+2348099887766',
            'check_in' => now()->addDays(14)->toDateString(),
            'check_out' => now()->addDays(16)->toDateString(),
            'guests' => 2,
            'quoted_total' => 90000,
            'status' => Reservation::STATUS_PENDING,
        ]);

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/reservations')
                ->assertSee('Ngozi Booking');
        });
    }

    public function test_availability_calendar_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/availability-calendar')
                ->assertSee(now()->format('F Y'));
        });
    }

    public function test_audit_log_page_loads_with_entries(): void
    {
        $reservation = Reservation::create([
            'room_type_id' => $this->roomType()->id,
            'guest_name' => 'Audit Trail Guest',
            'guest_phone' => '+2348011223344',
            'check_in' => now()->addDays(20)->toDateString(),
            'check_out' => now()->addDays(21)->toDateString(),
            'guests' => 1,
            'quoted_total' => 45000,
            'status' => Reservation::STATUS_PENDING,
        ]);

        $this->assertNotNull(
            \Spatie\Activitylog\Models\Activity::forSubject($reservation)->first(),
            'Activity was not logged.'
        );

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/audit-log')
                ->assertSee('Booking request created');
        });
    }
}

