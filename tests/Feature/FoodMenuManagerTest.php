<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FoodMenuManagerTest extends TestCase
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

    protected function tearDown(): void
    {
        foreach (glob(storage_path('app/public/menus/*.pdf')) ?: [] as $file) {
            @unlink($file);
        }

        parent::tearDown();
    }

    // ── Helpers ───────────────────────────────────────────────

    protected function createPdfOnDisk(string $path): void
    {
        Storage::disk('public')->put($path, "%PDF-1.4\n% Test food menu\n");
    }

    // ── Access ────────────────────────────────────────────────

    public function test_food_menu_page_loads_for_admin(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/food-menu-manager')
            ->assertOk();
    }

    public function test_guest_cannot_access_food_menu_page(): void
    {
        $this->get('/admin/food-menu-manager')
            ->assertRedirect(filament()->getLoginUrl());
    }

    public function test_non_admin_user_cannot_access_food_menu_page(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get('/admin/food-menu-manager')
            ->assertForbidden();
    }

    // ── Upload / Replace ──────────────────────────────────────

    public function test_upload_stores_setting_and_clears_cache(): void
    {
        $this->createPdfOnDisk('menus/menu-a.pdf');

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\FoodMenuManager::class)
            ->set('data.menu_pdf', ['menus/menu-a.pdf'])
            ->call('submit')
            ->assertHasNoErrors();

        $setting = Setting::where('key', 'food_menu_pdf')->first();
        $this->assertNotNull($setting);
        $this->assertSame('menus/menu-a.pdf', $setting->value);
        $this->assertSame('file', $setting->type);

        Storage::disk('public')->assertExists('menus/menu-a.pdf');
    }

    public function test_upload_replaces_existing_pdf_and_deletes_old_file(): void
    {
        $this->createPdfOnDisk('menus/old-menu.pdf');
        $this->createPdfOnDisk('menus/new-menu.pdf');

        Setting::create(['key' => 'food_menu_pdf', 'value' => 'menus/old-menu.pdf', 'type' => 'file']);

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\FoodMenuManager::class)
            ->set('data.menu_pdf', ['menus/new-menu.pdf'])
            ->call('submit')
            ->assertHasNoErrors();

        $setting = Setting::where('key', 'food_menu_pdf')->first();
        $this->assertSame('menus/new-menu.pdf', $setting->value);

        Storage::disk('public')->assertExists('menus/new-menu.pdf');
        Storage::disk('public')->assertMissing('menus/old-menu.pdf');
    }

    public function test_submit_without_file_changes_nothing(): void
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\FoodMenuManager::class)
            ->call('submit');

        $this->assertSame(0, Setting::where('key', 'food_menu_pdf')->count());
    }

    // ── Remove ────────────────────────────────────────────────

    public function test_remove_deletes_file_and_nulls_setting_value(): void
    {
        $this->createPdfOnDisk('menus/removable.pdf');
        Setting::create(['key' => 'food_menu_pdf', 'value' => 'menus/removable.pdf', 'type' => 'file']);

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\FoodMenuManager::class)
            ->call('removeMenu')
            ->assertHasNoErrors();

        $setting = Setting::where('key', 'food_menu_pdf')->first();
        $this->assertNull($setting->value);
        Storage::disk('public')->assertMissing('menus/removable.pdf');
    }

    public function test_remove_without_existing_setting_is_safe(): void
    {
        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\FoodMenuManager::class)
            ->call('removeMenu')
            ->assertHasNoErrors();

        $this->assertSame(0, Setting::where('key', 'food_menu_pdf')->count());
    }

    // ── Public Site Integration ───────────────────────────────

    public function test_public_menu_page_reflects_uploaded_pdf(): void
    {
        $this->get('/menu')->assertOk();

        $this->createPdfOnDisk('menus/public-check.pdf');

        Livewire::actingAs($this->admin)
            ->test(\App\Filament\Pages\FoodMenuManager::class)
            ->set('data.menu_pdf', ['menus/public-check.pdf'])
            ->call('submit')
            ->assertHasNoErrors();

        $this->get('/menu')
            ->assertOk()
            ->assertSee('public-check.pdf', false);
    }
}
