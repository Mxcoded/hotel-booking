<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\User;
use App\Models\Contact;
use App\Models\Feedback;
use App\Models\WhatsappLead;

class AdminCommunicationsTest extends DuskTestCase
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

    public function test_contacts_index_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/contacts')
                ->assertSee('Contact');
        });
    }

    public function test_contacts_displays_messages(): void
    {
        Contact::factory()->count(3)->create();

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/contacts')
                ->assertSee('Contact');
        });
    }

    public function test_feedback_index_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/feedback')
                ->assertSee('Feedback');
        });
    }

    public function test_feedback_displays_entries(): void
    {
        Feedback::factory()->count(5)->create();

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/feedback')
                ->assertSee('Feedback');
        });
    }

    public function test_whatsapp_leads_page_loads(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/whatsapp-leads')
                ->assertSee('WhatsApp');
        });
    }

    public function test_whatsapp_leads_displays_entries(): void
    {
        WhatsappLead::factory()->count(3)->create();

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);
            $browser->visit('/admin/whatsapp-leads')
                ->assertSee('WhatsApp');
        });
    }
}
