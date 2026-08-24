<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use App\Mail\FeedbackSubmitted;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class EmailNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('mail.from.address', 'fallback@example.test');

        // Staff address configured via the settings table
        Setting::create(['key' => 'email', 'value' => 'staff@brickspoint.test', 'type' => 'text']);
    }

    public function test_contact_submission_queues_staff_notification(): void
    {
        Mail::fake();

        $response = $this->post('/contact', [
            'name' => 'John Guest',
            'email' => 'john@example.com',
            'message' => 'Do you have airport pickup?',
        ]);

        $response->assertRedirect('/#contact');

        Mail::assertQueued(ContactMessageReceived::class, 1);

        Mail::assertQueued(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
            return $mail->hasTo('staff@brickspoint.test');
        });
    }

    public function test_feedback_submission_queues_staff_notification(): void
    {
        Mail::fake();

        $response = $this->postJson('/feedback', [
            'name' => 'Jane Guest',
            'email' => 'jane@example.com',
            'rating' => 5,
            'message' => 'Wonderful stay!',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        Mail::assertQueued(FeedbackSubmitted::class, 1);

        Mail::assertQueued(FeedbackSubmitted::class, function (FeedbackSubmitted $mail) {
            return $mail->hasTo('staff@brickspoint.test');
        });
    }

    public function test_falls_back_to_mail_from_address_when_setting_missing(): void
    {
        Setting::where('key', 'email')->delete();
        Mail::fake();

        $this->post('/contact', [
            'name' => 'No Setting',
            'email' => 'guest@example.com',
            'message' => 'Hello there',
        ])->assertRedirect('/#contact');

        Mail::assertQueued(ContactMessageReceived::class, function (ContactMessageReceived $mail) {
            return $mail->hasTo('fallback@example.test');
        });
    }

    public function test_honeypot_blocks_notification(): void
    {
        Mail::fake();

        $this->post('/contact', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'message' => 'spam',
            'honeypot' => 'gotcha',
        ])->assertStatus(302);

        $this->postJson('/feedback', [
            'rating' => 1,
            'message' => 'spam',
            'honeypot' => 'gotcha',
        ])->assertStatus(422);

        Mail::assertNothingQueued();
    }
}
