<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;

class ContactFormTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_contact_form_exists_on_homepage(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->assertSee('Contact')
                ->assertSee('Name')
                ->assertSee('Email')
                ->assertSee('Message');
        });
    }

    public function test_contact_form_can_be_submitted(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->scrollTo('#contact')
                ->waitFor('#contact-form', 5)
                ->type('name', 'Test User')
                ->type('email', 'test@example.com')
                ->type('message', 'This is a test message from Dusk.')
                ->press('Send')
                ->waitForReload()
                ->assertPathIs('/');
        });

        $this->assertDatabaseHas('contacts', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'This is a test message from Dusk.',
        ]);
    }

    public function test_contact_form_validates_required_fields(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                ->scrollTo('#contact')
                ->waitFor('#contact-form', 5)
                ->press('Send')
                ->assertSee('required');
        });
    }
}
