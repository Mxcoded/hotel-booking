<?php

namespace Tests\Feature;

use App\Filament\Pages\PaymentSettings;
use App\Models\Attraction;
use App\Models\Feedback;
use App\Models\Gallery;
use App\Models\RoomUnit;
use App\Models\RoomType;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class ExpandedAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::where('email', 'admin@brickspoint.ng')->first();
    }

    protected function roomType(): RoomType
    {
        return RoomType::create([
            'name' => 'Audit Room',
            'slug' => 'audit-room',
            'room_type' => 'standard',
            'price' => 30000,
            'description' => 'Test.',
            'base_guests' => 2,
            'min_stay' => 1,
            'advance_booking_days' => 365,
            'is_active' => true,
        ]);
    }

    public function test_room_unit_creation_is_logged(): void
    {
        Activity::query()->delete();

        $unit = RoomUnit::create([
            'room_type_id' => $this->roomType()->id,
            'unit_number' => '101',
            'floor' => 1,
            'status' => 'available',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'room_units',
            'event' => 'created',
            'subject_id' => $unit->id,
        ]);
    }

    public function test_gallery_create_and_delete_are_logged(): void
    {
        Activity::query()->delete();

        $gallery = Gallery::create(['path' => 'gallery/foo.jpg']);
        $gallery->delete();

        $this->assertCount(2, Activity::where('log_name', 'gallery')->get());
        $this->assertDatabaseHas('activity_log', ['log_name' => 'gallery', 'event' => 'deleted']);
    }

    public function test_attraction_update_is_logged(): void
    {
        Activity::query()->delete();

        $attraction = Attraction::create([
            'name' => 'Jabi Lake',
            'category' => 'Nature',
            'description' => 'A scenic lake.',
            'image' => 'attractions/jabi.jpg',
        ]);
        $attraction->update(['name' => 'Jabi Lake Park']);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'attractions',
            'event' => 'updated',
        ]);
    }

    public function test_feedback_creation_and_approval_are_logged(): void
    {
        Activity::query()->delete();

        $feedback = Feedback::create([
            'name' => 'Guest',
            'email' => 'guest@example.com',
            'rating' => 5,
            'message' => 'Lovely stay',
        ]);

        // @todo: toggle approval as an admin would via the resource
        $feedback->update(['is_approved' => true]);

        $this->assertDatabaseHas('activity_log', ['log_name' => 'feedback', 'event' => 'created']);
        $this->assertDatabaseHas('activity_log', ['log_name' => 'feedback', 'event' => 'updated']);
    }

    public function test_user_update_is_logged_without_password(): void
    {
        Activity::query()->delete();

        $this->actingAs($this->admin);
        $this->admin->update(['name' => 'Renamed Admin']);

        $activity = Activity::where('log_name', 'users')->latest()->first();
        $this->assertNotNull($activity);
        $this->assertArrayNotHasKey('password', $activity->attribute_changes['attributes'] ?? []);
        $this->assertEquals($this->admin->id, $activity->causer_id);
    }

    public function test_setting_update_is_logged_without_secret_value(): void
    {
        Activity::query()->delete();

        $setting = Setting::create([
            'key' => 'paystack_secret_key',
            'group' => 'payment',
            'value' => 'sk_test_abc123',
        ]);

        $setting->update(['value' => 'sk_test_updated']);

        $activity = Activity::where('log_name', 'settings')->latest()->first();
        $this->assertNotNull($activity);

        // value must never appear in the audit capture
        $this->assertArrayNotHasKey('value', $activity->attribute_changes['attributes'] ?? []);
    }

    public function test_payment_settings_page_renders_and_saves(): void
    {
        Livewire::actingAs($this->admin)
            ->test(PaymentSettings::class)
            ->assertSuccessful()
            ->assertFormFieldExists('paystack_enabled')
            ->fillForm([
                'paystack_enabled' => true,
                'paystack_mode' => 'test',
                'paystack_public_key' => 'pk_test_xyz',
                'paystack_secret_key' => 'sk_test_xyz',
                'paystack_currency' => 'NGN',
                'paystack_callback_url' => 'https://example.com/callback',
            ])
            ->call('submit')
            ->assertHasNoFormErrors();

        $this->assertTrue((bool) setting('paystack_enabled'));
        $this->assertEquals('pk_test_xyz', setting('paystack_public_key'));
        $this->assertEquals('sk_test_xyz', setting('paystack_secret_key'));
    }
}
