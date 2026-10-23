<?php

namespace Tests\Feature;

use App\Filament\Resources\SettingResource\Pages\EditSetting;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SettingResourceTest extends TestCase
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

        Storage::fake('public');
    }

    protected function makeSetting(string $value): Setting
    {
        return Setting::create([
            'key' => 'hero_media',
            'type' => 'image',
            'group' => 'hero',
            'label' => 'Hero Media',
            'value' => $value,
        ]);
    }

    public function test_public_disk_urls_are_relative_so_uploads_load_on_any_host(): void
    {
        $this->assertSame('/storage', config('filesystems.disks.public.url'));
    }

    public function test_edit_page_hydrates_existing_image_for_preview(): void
    {
        Storage::disk('public')->put('settings/hero.jpg', 'fake');

        $setting = $this->makeSetting('settings/hero.jpg');

        Livewire::actingAs($this->admin)
            ->test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->assertOk()
            ->assertFormSet(['file_upload' => 'settings/hero.jpg']);
    }

    public function test_edit_page_skips_hydration_when_file_is_missing(): void
    {
        $setting = $this->makeSetting('settings/missing.jpg');

        Livewire::actingAs($this->admin)
            ->test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->assertOk()
            ->assertFormSet(['file_upload' => null]);
    }

    public function test_replacing_image_updates_value_and_deletes_old_file(): void
    {
        Storage::disk('public')->put('settings/old.jpg', 'fake');
        Storage::disk('public')->put('settings/new.jpg', 'fake');

        $setting = $this->makeSetting('settings/old.jpg');

        Livewire::actingAs($this->admin)
            ->test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->fillForm(['file_upload' => ['settings/new.jpg']])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame('settings/new.jpg', $setting->fresh()->value);
        Storage::disk('public')->assertExists('settings/new.jpg');
        Storage::disk('public')->assertMissing('settings/old.jpg');
    }

    public function test_uploading_new_image_stores_file_and_updates_value(): void
    {
        Storage::disk('public')->put('settings/old.jpg', 'fake');

        $setting = $this->makeSetting('settings/old.jpg');

        Livewire::actingAs($this->admin)
            ->test(EditSetting::class, ['record' => $setting->getRouteKey()])
            ->set('data.file_upload', UploadedFile::fake()->image('hero.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $value = $setting->fresh()->value;

        $this->assertNotSame('settings/old.jpg', $value);
        $this->assertStringStartsWith('settings/', $value);
        Storage::disk('public')->assertExists($value);
        Storage::disk('public')->assertMissing('settings/old.jpg');
    }
}
