<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;
use App\Models\Feedback;

class FeedbackFormTest extends DuskTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    public function test_guest_can_submit_feedback(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/feedback')
                ->type('name', 'Dusk Reviewer')
                ->type('email', 'reviewer@example.com')
                ->type('message', 'Wonderful stay, rooms were spotless.')
                ->script("document.getElementById('star5').checked = true;");

            try {
                $browser->press('Submit Feedback');
            } catch (\Facebook\WebDriver\Exception\WebDriverException $e) {
                usleep(800000);
                $browser->press('Submit Feedback');
            }

            $browser->pause(2500);
        });

        $feedback = Feedback::where('email', 'reviewer@example.com')->first();

        $this->assertNotNull($feedback, 'Feedback row was not persisted.');
        $this->assertEquals(5, (int) $feedback->rating);
    }
}
