<?php

namespace Tests\Feature;

use App\Models\HousekeepingLog;
use App\Models\RoomType;
use App\Models\RoomUnit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HousekeepingTest extends TestCase
{
    use RefreshDatabase;

    protected RoomUnit $unit;

    protected function setUp(): void
    {
        parent::setUp();

        $roomType = RoomType::create([
            'name' => 'Standard Room',
            'slug' => 'standard-room',
            'room_type' => 'standard',
            'price' => 40000,
            'description' => 'Test.',
            'base_guests' => 1,
            'min_stay' => 1,
            'max_stay' => 30,
            'advance_booking_days' => 365,
            'is_active' => true,
        ]);

        $this->unit = RoomUnit::create([
            'room_type_id' => $roomType->id,
            'unit_number' => '101',
            'floor' => 1,
            'status' => 'occupied',
            'is_active' => true,
        ]);
    }

    public function test_mark_dirty_creates_log_and_updates_status(): void
    {
        $this->unit->markDirty('Spill on floor');

        $this->unit->refresh();
        $this->assertEquals(HousekeepingLog::STATUS_DIRTY, $this->unit->housekeeping_status);

        $log = $this->unit->housekeepingLogs()->first();
        $this->assertEquals(HousekeepingLog::STATUS_DIRTY, $log->status);
        $this->assertEquals('Spill on floor', $log->note);
    }

    public function test_mark_clean_sets_unit_to_available_and_logs(): void
    {
        $this->unit->markClean('Cleaned and turned');

        $this->unit->refresh();
        $this->assertEquals(HousekeepingLog::STATUS_CLEAN, $this->unit->housekeeping_status);
        $this->assertEquals('available', $this->unit->status);

        $log = $this->unit->housekeepingLogs()->first();
        $this->assertEquals(HousekeepingLog::STATUS_CLEAN, $log->status);
        $this->assertNotNull($log->cleaned_at);
    }

    public function test_mark_inspected_creates_log(): void
    {
        $this->unit->markInspected();

        $this->unit->refresh();
        $this->assertEquals(HousekeepingLog::STATUS_INSPECTED, $this->unit->housekeeping_status);

        $this->assertSame(1, $this->unit->housekeepingLogs()->count());
    }
}
