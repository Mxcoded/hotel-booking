<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\User;
use App\Models\Visitor;
use App\Models\WhatsappLead;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BookingFunnelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected RoomType $roomType;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);

        $this->admin = User::factory()->create([
            'email_verified_at' => now(),
        ]);
        $this->admin->assignRole('admin');

        $this->roomType = $this->roomType();
    }

    protected function roomType(): RoomType
    {
        return RoomType::create([
            'name' => 'Funnel Suite',
            'slug' => 'funnel-suite',
            'room_type' => 'suite',
            'price' => 50000,
            'description' => 'Test.',
            'base_guests' => 2,
            'min_stay' => 1,
            'advance_booking_days' => 365,
            'is_active' => true,
        ]);
    }

    protected function makeReservation(string $status = Reservation::STATUS_PENDING, ?string $createdAt = null): Reservation
    {
        $reservation = Reservation::create([
            'room_type_id' => $this->roomType->id,
            'guest_name' => 'Guest ' . uniqid(),
            'guest_phone' => '+2348000000000',
            'check_in' => now()->addDays(10)->toDateString(),
            'check_out' => now()->addDays(12)->toDateString(),
            'guests' => 2,
            'quoted_total' => 100000,
            'status' => $status,
        ]);

        if ($status === Reservation::STATUS_CONFIRMED) {
            $reservation->markConfirmed();
        }

        if ($createdAt) {
            $reservation->forceFill(['created_at' => $createdAt])->save();
            $reservation->refresh();
        }

        return $reservation;
    }

    public function test_funnel_counts_within_period(): void
    {
        // Visitors: 2 unique IPs (one repeated across days)
        Visitor::create(['ip_address' => '10.0.0.1', 'visited_date' => now()->subDays(3)->toDateString()]);
        Visitor::create(['ip_address' => '10.0.0.1', 'visited_date' => now()->subDays(1)->toDateString()]);
        Visitor::create(['ip_address' => '10.0.0.2', 'visited_date' => now()->toDateString()]);

        WhatsappLead::create(['name' => 'L1', 'phone' => '111', 'ip_address' => '10.0.0.1']);
        WhatsappLead::create(['name' => 'L2', 'phone' => '222', 'ip_address' => '10.0.0.2']);

        $this->makeReservation(Reservation::STATUS_PENDING);
        $confirmed = $this->makeReservation(Reservation::STATUS_CONFIRMED);

        // Old data outside the 30-day window — must be excluded
        Visitor::create(['ip_address' => '9.9.9.9', 'visited_date' => now()->subDays(60)->toDateString()]);
        WhatsappLead::create(['name' => 'Old', 'phone' => '999', 'ip_address' => '9.9.9.9'])->forceFill(['created_at' => now()->subDays(60)])->save();
        $old = $this->makeReservation(Reservation::STATUS_PENDING, now()->subDays(60)->toDateString());
        $this->assertNotNull($old);

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Widgets\BookingFunnelWidget::class)
            ->assertSee('Unique Visitors')->assertSee('2')
            ->assertSee('WhatsApp Leads')->assertSee('2')
            ->assertSee('Booking Requests')->assertSee('2')
            ->assertSee('Confirmed Stays')->assertSee('1');
    }

    public function test_confirmed_reservation_appears_in_confirmed_step(): void
    {
        $reservation = $this->makeReservation(Reservation::STATUS_CONFIRMED);
        $this->assertEquals(Reservation::STATUS_CONFIRMED, $reservation->fresh()->status);
        $this->assertNotNull($reservation->fresh()->confirmed_at);

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Widgets\BookingFunnelWidget::class)
            ->assertSee('Confirmed Stays')->assertSee('1');
    }

    public function test_empty_funnel_renders_zeroes_without_errors(): void
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Widgets\BookingFunnelWidget::class)
            ->assertSuccessful();
    }

    public function test_dashboard_shows_funnel_widget_for_admin(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Booking Funnel')
            ->assertSee('Unique Visitors');
    }
}
