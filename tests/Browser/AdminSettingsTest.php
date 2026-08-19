<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\User;
use App\Models\Setting;

class AdminSettingsTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    protected function loginAsAdmin($browser): void
    {
        $user = User::first();

        $browser->visit('/login')
            ->type('email', $user->email)
            ->type('password', 'password')
            ->press('Log in')
            ->waitForReload();
    }

    public function test_settings_index_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/settings')
                ->assertSee('Settings');
        });
    }

    public function test_create_setting_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/settings/create')
                ->assertSee('Create')
                ->assertSee('Key')
                ->assertSee('Value');
        });
    }

    public function test_can_create_text_setting(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/settings/create')
                ->type('key', 'hotel_name')
                ->select('type', 'text')
                ->type('value', 'Brickspoint Hotel')
                ->press('Create')
                ->waitForReload();
        });

        $this->assertDatabaseHas('settings', [
            'key' => 'hotel_name',
            'value' => 'Brickspoint Hotel',
            'type' => 'text',
        ]);
    }

    public function test_can_update_setting(): void
    {
        $setting = Setting::create([
            'key' => 'test_key',
            'value' => 'old_value',
            'type' => 'text',
        ]);

        $this->browse(function ($browser) use ($setting) {
            $this->loginAsAdmin($browser);
            $browser->visit("/admin/settings/{$setting->id}/edit")
                ->type('value', 'new_value')
                ->press('Save')
                ->waitForReload();
        });

        $this->assertDatabaseHas('settings', [
            'id' => $setting->id,
            'value' => 'new_value',
        ]);
    }

    public function test_can_delete_setting(): void
    {
        $setting = Setting::create([
            'key' => 'delete_me',
            'value' => 'value',
            'type' => 'text',
        ]);

        $this->browse(function ($browser) use ($setting) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/settings')
                ->press("Delete")
                ->waitForReload();
        });

        $this->assertDatabaseMissing('settings', [
            'id' => $setting->id,
        ]);
    }
}
