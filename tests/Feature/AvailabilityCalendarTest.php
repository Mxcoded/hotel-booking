<?php

namespace Tests\Feature;

use App\Filament\Pages\AvailabilityCalendar;
use App\Models\Reservation;
use App\Models\RoomAvailability;
use App\Models\RoomType;
use App\Models\RoomUnit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AvailabilityCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);

        $this->admin = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->admin->assignRole('admin');
    }

    protected function unitWithRoomType(array $roomOverrides = []): RoomUnit
    {
        $roomType = RoomType::create(array_merge([
            'name' => 'Calendar Deluxe',
            'slug' => 'calendar-deluxe',
            'room_type' => 'deluxe',
            'price' => 45000,
            'description' => 'Test room.',
            'base_guests' => 2,
            'min_stay' => 1,
            'advance_booking_days' => 365,
            'is_active' => true,
        ], $roomOverrides));

        return RoomUnit::create([
            'room_type_id' => $roomType->id,
            'unit_number' => 'C101',
            'floor' => 3,
            'is_active' => true,
        ]);
    }

    // â”€â”€ Access â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function test_calendar_page_loads_for_admin(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/availability-calendar')
            ->assertOk();
    }

    public function test_guest_cannot_access_calendar(): void
    {
        $this->get('/admin/availability-calendar')
            ->assertRedirect(filament()->getLoginUrl());
    }

    // â”€â”€ Grid data â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function test_grid_shows_active_units_and_statuses(): void
    {
        $unit = $this->unitWithRoomType();

        RoomAvailability::create([
            'room_unit_id' => $unit->id,
            'date' => now()->addDays(5)->toDateString(),
            'status' => 'booked',
        ]);

        Livewire::actingAs($this->admin)
            ->withQueryParams(['month' => now()->month, 'year' => now()->year])
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->assertSee('Calendar Deluxe')
            ->assertSee('C101')
            ->assertSee('Booked');
    }

    public function test_inactive_units_excluded_from_grid(): void
    {
        RoomUnit::create([
            'room_type_id' => $this->unitWithRoomType()->room_type_id,
            'unit_number' => 'INACTIVE-1',
            'is_active' => false,
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->assertDontSee('INACTIVE-1');
    }

    // â”€â”€ Toggle behaviour â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€â”€

    public function test_toggle_blocks_a_free_date(): void
    {
        $unit = $this->unitWithRoomType();
        $future = now()->addDays(7)->toDateString();

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('handleCellClick', $unit->id, $future, false);

        $this->assertDatabaseHas('room_availabilities', [
            'room_unit_id' => $unit->id,
            'date' => $future,
            'status' => 'blocked',
        ]);
    }

    public function test_toggle_unblocks_a_blocked_date(): void
    {
        $unit = $this->unitWithRoomType();
        $future = now()->addDays(7)->toDateString();

        RoomAvailability::create([
            'room_unit_id' => $unit->id,
            'date' => $future,
            'status' => 'blocked',
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('handleCellClick', $unit->id, $future, false);

        $this->assertDatabaseMissing('room_availabilities', [
            'room_unit_id' => $unit->id,
            'date' => $future,
        ]);
    }

    public function test_cannot_toggle_booked_dates(): void
    {
        $unit = $this->unitWithRoomType();
        $future = now()->addDays(7)->toDateString();

        RoomAvailability::create([
            'room_unit_id' => $unit->id,
            'date' => $future,
            'status' => 'booked',
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('handleCellClick', $unit->id, $future, false);

        // Still booked â€” untouched
        $this->assertDatabaseHas('room_availabilities', [
            'room_unit_id' => $unit->id,
            'date' => $future,
            'status' => 'booked',
        ]);
    }

    public function test_cannot_toggle_past_dates(): void
    {
        $unit = $this->unitWithRoomType();

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('handleCellClick', $unit->id, now()->subDays(2)->toDateString(), false);

        $this->assertDatabaseMissing('room_availabilities', [
            'room_unit_id' => $unit->id,
        ]);
    }

    public function test_ignores_invalid_unit_or_date(): void
    {
        $this->unitWithRoomType();

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('handleCellClick', 999999, now()->addDays(3)->toDateString(), false)
            ->call('handleCellClick', 1, 'not-a-date', false);

        $this->assertDatabaseCount('room_availabilities', 0);
    }

    // ── Range selection ───────────────────────────────────────

    public function test_shift_click_blocks_a_whole_span(): void
    {
        $unit = $this->unitWithRoomType();
        $startDay = 4;
        $endDay = 8;

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('handleCellClick', $unit->id, now()->addDays($startDay)->toDateString(), false)
            ->call('handleCellClick', $unit->id, now()->addDays($endDay)->toDateString(), true);

        for ($i = $startDay; $i <= $endDay; $i++) {
            $this->assertDatabaseHas('room_availabilities', [
                'room_unit_id' => $unit->id,
                'date' => now()->addDays($i)->toDateString(),
                'status' => 'blocked',
            ]);
        }
    }

    public function test_shift_click_releases_a_whole_span(): void
    {
        $unit = $this->unitWithRoomType();

        for ($i = 2; $i <= 5; $i++) {
            RoomAvailability::create([
                'room_unit_id' => $unit->id,
                'date' => now()->addDays($i)->toDateString(),
                'status' => 'blocked',
            ]);
        }

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('handleCellClick', $unit->id, now()->addDays(2)->toDateString(), false)
            ->call('handleCellClick', $unit->id, now()->addDays(5)->toDateString(), true);

        $remaining = RoomAvailability::where('room_unit_id', $unit->id)->count();

        $this->assertSame(0, $remaining, 'Shift-click on an anchored release must clear the span.');
    }

    public function test_range_leaves_booked_dates_untouched(): void
    {
        $unit = $this->unitWithRoomType();
        $bookedDate = now()->addDays(3)->toDateString();

        RoomAvailability::create([
            'room_unit_id' => $unit->id,
            'date' => $bookedDate,
            'status' => 'booked',
            'note' => 'Booking #999',
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('handleCellClick', $unit->id, now()->addDays(2)->toDateString(), false)
            ->call('handleCellClick', $unit->id, now()->addDays(5)->toDateString(), true);

        // The booked row survives untouched.
        $this->assertDatabaseHas('room_availabilities', [
            'room_unit_id' => $unit->id,
            'date' => $bookedDate,
            'status' => 'booked',
        ]);

        // Days 2..5 minus the booked day get blocked (3 of them).
        $blocked = RoomAvailability::where('room_unit_id', $unit->id)->where('status', 'blocked')->count();

        $this->assertSame(3, $blocked);
    }

    public function test_past_anchor_click_does_not_start_a_range(): void
    {
        $unit = $this->unitWithRoomType();

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('handleCellClick', $unit->id, now()->subDays(1)->toDateString(), false)
            // No valid anchor exists, so this behaves as a single-day block.
            ->call('handleCellClick', $unit->id, now()->addDays(5)->toDateString(), true);

        $this->assertSame(0, RoomAvailability::where('room_unit_id', $unit->id)
            ->whereDate('date', now()->subDays(1)->toDateString())->count());

        $this->assertDatabaseHas('room_availabilities', [
            'room_unit_id' => $unit->id,
            'date' => now()->addDays(5)->toDateString(),
            'status' => 'blocked',
        ]);
}

    // ── Maintenance ───────────────────────────────────────────

    public function test_mark_maintenance_takes_unit_out_of_order_for_a_range(): void
    {
        $unit = $this->unitWithRoomType();

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('applyMaintenance', [
                'room_unit_id' => $unit->id,
                'start' => now()->addDays(3)->toDateString(),
                'end' => now()->addDays(6)->toDateString(),
                'note' => 'AC replacement',
            ]);

        for ($i = 3; $i <= 6; $i++) {
            $this->assertDatabaseHas('room_availabilities', [
                'room_unit_id' => $unit->id,
                'date' => now()->addDays($i)->toDateString(),
                'status' => 'maintenance',
                'note' => 'AC replacement',
            ]);
        }
    }

    public function test_maintenance_skips_booked_nights(): void
    {
        $unit = $this->unitWithRoomType();
        $bookedDate = now()->addDays(4)->toDateString();

        RoomAvailability::create([
            'room_unit_id' => $unit->id,
            'date' => $bookedDate,
            'status' => 'booked',
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('applyMaintenance', [
                'room_unit_id' => $unit->id,
                'start' => now()->addDays(3)->toDateString(),
                'end' => now()->addDays(5)->toDateString(),
            ]);

        $this->assertDatabaseHas('room_availabilities', [
            'room_unit_id' => $unit->id,
            'date' => $bookedDate,
            'status' => 'booked',
        ]);

        $maintenance = RoomAvailability::where('room_unit_id', $unit->id)
            ->where('status', 'maintenance')->count();

        $this->assertSame(2, $maintenance);
    }

    public function test_clear_maintenance_restores_only_maintenance_rows(): void
    {
        $unit = $this->unitWithRoomType();

        foreach ([3, 4, 5] as $offset) {
            RoomAvailability::create([
                'room_unit_id' => $unit->id,
                'date' => now()->addDays($offset)->toDateString(),
                'status' => 'maintenance',
            ]);
        }

        RoomAvailability::create([
            'room_unit_id' => $unit->id,
            'date' => now()->addDays(7)->toDateString(),
            'status' => 'blocked',
        ]);

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\AvailabilityCalendar::class)
            ->call('removeMaintenance', [
                'room_unit_id' => $unit->id,
                'start' => now()->addDays(2)->toDateString(),
                'end' => now()->addDays(8)->toDateString(),
            ]);

        $this->assertSame(0, RoomAvailability::where('room_unit_id', $unit->id)
            ->where('status', 'maintenance')->count());

        // Manual block outside the maintenance set must survive.
        $this->assertDatabaseHas('room_availabilities', [
            'room_unit_id' => $unit->id,
            'date' => now()->addDays(7)->toDateString(),
            'status' => 'blocked',
        ]);
    }
}

