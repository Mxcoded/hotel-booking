<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
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

    public function test_contacts_index_lists_messages(): void
    {
        Contact::create([
            'name' => 'Ada Obi',
            'email' => 'ada@example.com',
            'message' => 'Do you have airport pickup?',
        ]);

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/contacts')
                ->assertSee('Ada Obi');
        });
    }

    public function test_whatsapp_leads_index_lists_leads(): void
    {
        WhatsappLead::create([
            'name' => 'Walk-in guest',
            'phone' => '+2348012345678',
            'ip_address' => '203.0.113.9',
        ]);

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/whatsapp-leads')
                ->assertSee('+2348012345678');
        });
    }

    public function test_feedback_index_lists_submissions(): void
    {
        Feedback::create([
            'name' => 'Musa Idris',
            'email' => 'musa@example.com',
            'rating' => 5,
            'message' => 'Excellent service throughout our stay.',
            'is_approved' => false,
        ]);

        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/feedback')
                ->assertSee('Excellent service throughout our stay.');
        });
    }
}
