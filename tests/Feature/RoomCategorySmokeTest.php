<?php

namespace Tests\Feature;

use App\Filament\Resources\RoomCategoryResource;
use App\Filament\Resources\RoomTypeResource;
use App\Models\RoomCategory;
use App\Models\RoomType;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RoomCategorySmokeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->admin = User::where('email', 'admin@brickspoint.ng')->first();
    }

    public function test_room_type_create_form_renders_with_dynamic_categories(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RoomTypeResource\Pages\CreateRoomType::class)
            ->assertSuccessful()
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('room_type');
    }

    public function test_room_type_edit_form_renders(): void
    {
        $roomType = RoomType::create([
            'name' => 'Temp Suite',
            'slug' => 'temp-suite',
            'room_type' => 'suite',
            'bed_type' => 'king',
            'description' => 'Temp',
            'price' => 50000,
            'base_guests' => 2,
            'min_stay' => 1,
            'advance_booking_days' => 30,
            'is_active' => true,
        ]);

        Livewire::actingAs($this->admin)
            ->test(RoomTypeResource\Pages\EditRoomType::class, ['record' => $roomType->getKey()])
            ->assertSuccessful()
            ->assertFormFieldExists('room_type');
    }

    public function test_can_create_room_type_with_category(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RoomTypeResource\Pages\CreateRoomType::class)
            ->fillForm([
                'name' => 'Garden Villa',
                'room_type' => 'deluxe',
                'bed_type' => 'king',
                'description' => 'A garden villa.',
                'price' => 45000,
                'base_guests' => 2,
                'min_stay' => 1,
                'advance_booking_days' => 30,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('room_types', [
            'slug' => 'garden-villa',
            'room_type' => 'deluxe',
        ]);
    }

    public function test_room_category_resource_pages_render(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RoomCategoryResource\Pages\ListRoomCategories::class)
            ->assertSuccessful();

        Livewire::actingAs($this->admin)
            ->test(RoomCategoryResource\Pages\CreateRoomCategory::class)
            ->assertSuccessful()
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('slug')
            ->assertFormFieldExists('color');
    }

    public function test_can_create_room_category(): void
    {
        Livewire::actingAs($this->admin)
            ->test(RoomCategoryResource\Pages\CreateRoomCategory::class)
            ->fillForm([
                'name' => 'Penthouse',
                'slug' => 'penthouse',
                'color' => 'success',
                'sort_order' => 6,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('room_categories', ['slug' => 'penthouse']);

        $this->assertArrayHasKey('penthouse', RoomCategory::options());
    }
}
