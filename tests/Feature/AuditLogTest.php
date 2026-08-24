<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditLogTest extends TestCase
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

    protected function roomType(): RoomType
    {
        return RoomType::create([
            'name' => 'Audit Suite',
            'slug' => 'audit-suite',
            'room_type' => 'suite',
            'price' => 50000,
            'description' => 'Test.',
            'base_guests' => 2,
            'min_stay' => 1,
            'advance_booking_days' => 365,
            'is_active' => true,
        ]);
    }

    protected function reservation(RoomType $roomType): Reservation
    {
        return Reservation::create([
            'room_type_id' => $roomType->id,
            'guest_name' => 'Ada Obi',
            'guest_email' => null,
            'guest_phone' => '+2348000000000',
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 2,
            'quoted_total' => 100000,
            'status' => Reservation::STATUS_PENDING,
        ]);
    }

    // ── Logging behaviour ─────────────────────────────────────

    public function test_reservation_creation_is_logged(): void
    {
        $reservation = $this->reservation($this->roomType());

        $activity = Activity::query()
            ->where('log_name', 'reservations')
            ->where('subject_id', $reservation->id)
            ->where('event', 'created')
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals('Booking request created', $activity->description);
        $this->assertNull($activity->causer_id); // guest submission
    }

    public function test_status_confirmation_logs_dirty_change(): void
    {
        $reservation = $this->reservation($this->roomType());

        $this->actingAs($this->admin);
        $reservation->markConfirmed();

        $activity = Activity::query()
            ->where('log_name', 'reservations')
            ->where('subject_id', $reservation->id)
            ->where('event', 'updated')
            ->latest()
            ->first();

        $this->assertNotNull($activity);
        $this->assertEquals(Reservation::STATUS_PENDING, $activity->attribute_changes['old']['status'] ?? null);
        $this->assertEquals(Reservation::STATUS_CONFIRMED, $activity->attribute_changes['attributes']['status'] ?? null);
        $this->assertEquals($this->admin->id, $activity->causer_id); // admin confirmed it

        // Only dirty fields logged, not untouched ones
        $this->assertArrayNotHasKey('guest_name', $activity->attribute_changes['attributes']);
    }

    public function test_room_type_update_and_delete_are_logged(): void
    {
        $roomType = $this->roomType();

        Activity::query()->delete(); // drop the 'created' entry for clarity

        $roomType->update(['price' => 65000]);
        $roomType->delete();

        $activities = Activity::query()->where('log_name', 'room_types')->orderBy('id')->get();
        $this->assertCount(2, $activities);

        $update = $activities->firstWhere('event', 'updated');
        $this->assertEquals(50000.0, (float) ($update->attribute_changes['old']['price']));
        $this->assertEquals(65000.0, (float) ($update->attribute_changes['attributes']['price']));

        $delete = $activities->firstWhere('event', 'deleted');
        $this->assertEquals('Room type deleted', $delete->description);
    }

    public function test_clean_update_logs_nothing(): void
    {
        $roomType = $this->roomType();
        Activity::query()->delete();

        $roomType->update(['price' => $roomType->price]); // no actual change

        $this->assertSame(0, Activity::query()->where('log_name', 'room_types')->count());
    }

    // ── Viewer access ─────────────────────────────────────────

    public function test_audit_page_loads_for_admin(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/audit-log')
            ->assertOk();
    }

    public function test_guest_cannot_access_audit_page(): void
    {
        $this->get('/admin/audit-log')
            ->assertRedirect(filament()->getLoginUrl());
    }
}
