<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\Contact;

class ContactFormTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

    }

    public function test_guest_can_submit_contact_form(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->within('#contact', function ($browser) {
                    $browser->type('name', 'Dusk Visitor')
                        ->type('email', 'dusk@example.com')
                        ->type('message', 'Hello from the Dusk suite.')
                        ->press('Send Message');
                })
                ->pause(2500);
        });

        $contact = Contact::where('email', 'dusk@example.com')->first();

        $this->assertNotNull($contact, 'Contact row was not persisted.');
        $this->assertEquals('Dusk Visitor', $contact->name);
    }
}

