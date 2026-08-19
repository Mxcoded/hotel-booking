<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;

class FeedbackFormTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_feedback_page_loads(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/feedback')
                ->assertSee('Feedback')
                ->assertSee('Rating')
                ->assertSee('Message');
        });
    }

    public function test_feedback_form_can_be_submitted(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/feedback')
                ->waitFor('form', 5)
                ->select('rating', '5')
                ->type('message', 'Excellent hotel experience!')
                ->press('Submit')
                ->waitForReload();
        });

        $this->assertDatabaseHas('feedbacks', [
            'rating' => 5,
            'message' => 'Excellent hotel experience!',
        ]);
    }

    public function test_feedback_form_validates_rating(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/feedback')
                ->waitFor('form', 5)
                ->type('message', 'Test feedback')
                ->press('Submit')
                ->assertSee('required');
        });
    }

    public function test_feedback_form_optional_fields(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/feedback')
                ->waitFor('form', 5)
                ->select('rating', '4')
                ->type('message', 'Great stay!')
                ->press('Submit')
                ->waitForReload();
        });

        $this->assertDatabaseHas('feedbacks', [
            'rating' => 4,
            'message' => 'Great stay!',
            'name' => null,
            'email' => null,
        ]);
    }
}
