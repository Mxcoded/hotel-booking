<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    public function test_user_resource_create_form_renders_with_grouped_permissions(): void
    {
        Livewire::actingAs($this->user)
            ->test(UserResource\Pages\CreateUser::class)
            ->assertSuccessful()
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('email')
            ->assertFormFieldExists('roles');
    }

    public function test_creating_user_syncs_grouped_permissions(): void
    {
        $contactPermIds = \Spatie\Permission\Models\Permission::where('name', 'like', '%contact%')->pluck('id');

        Livewire::actingAs($this->user)
            ->test(UserResource\Pages\CreateUser::class)
            ->fillForm([
                'name' => 'New Staff',
                'email' => 'staff@brickspoint.ng',
                'password' => 'secret123',
                'perm_contact-messages' => $contactPermIds,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $staff = User::where('email', 'staff@brickspoint.ng')->first();
        $this->assertNotNull($staff);
        $this->assertSameSize($contactPermIds, $staff->permissions()->where('name', 'like', '%contact%')->get());
    }

    public function test_editing_user_hydrates_and_persists_grouped_permissions(): void
    {
        $contactPermIds = \Spatie\Permission\Models\Permission::where('name', 'like', '%contact%')->pluck('id');

        $staff = User::factory()->create(['email' => 'staff2@brickspoint.ng']);
        $staff->syncPermissions(
            \Spatie\Permission\Models\Permission::whereIn('id', $contactPermIds)->pluck('name')->all()
        );

        $feedbackPermIds = \Spatie\Permission\Models\Permission::where('name', 'like', '%feedback%')->pluck('id');

        Livewire::actingAs($this->user)
            ->test(UserResource\Pages\EditUser::class, ['record' => $staff->getKey()])
            ->fillForm([
                'perm_contact-messages' => $contactPermIds,
                'perm_guest-feedback' => $feedbackPermIds,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $staff->refresh();
        $this->assertSame(
            $feedbackPermIds->count(),
            $staff->permissions()->whereIn('id', $feedbackPermIds)->count()
        );

        $this->assertSame(
            $contactPermIds->count(),
            $staff->permissions()->whereIn('id', $contactPermIds)->count()
        );
    }
}
