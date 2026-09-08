<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
        $this->user = User::where('email', 'admin@brickspoint.ng')->first();
    }

    public function test_user_resource_create_form_renders(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserResource\Pages\CreateUser::class)
            ->assertSuccessful()
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('email')
            ->assertFormFieldExists('roles')
            ->assertFormFieldDoesNotExist('perm_contact_messages');
    }

    public function test_creating_user_assigns_role_without_direct_permissions(): void
    {
        $adminRole = Role::where('name', 'admin')->first();

        Livewire::actingAs($this->user)
            ->test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'New Staff',
                'email' => 'staff@brickspoint.ng',
                'password' => 'secret123',
                'roles' => [$adminRole->id],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $staff = User::where('email', 'staff@brickspoint.ng')->first();
        $this->assertNotNull($staff);
        $this->assertTrue($staff->hasRole('admin'));
        $this->assertSame(0, $staff->getDirectPermissions()->count());
    }

    public function test_editing_user_changes_role(): void
    {
        $staff = User::factory()->create(['email' => 'staff2@brickspoint.ng']);
        $staff->assignRole('admin');

        $receptionistRole = Role::where('name', 'receptionist')->first();

        Livewire::actingAs($this->user)
            ->test(UserResource\Pages\EditUser::class, ['record' => $staff->getKey()])
            ->fillForm([
                'roles' => [$receptionistRole->id],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $staff->refresh();
        $this->assertTrue($staff->hasRole('receptionist'));
        $this->assertFalse($staff->hasRole('admin'));
    }
}
