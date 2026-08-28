<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\RoomUnit;
use App\Services\Paystack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaystackTest extends TestCase
{
    use RefreshDatabase;

    protected RoomType $roomType;

    protected function setUp(): void
    {
        parent::setUp();

        \App\Models\Setting::create(['key' => 'paystack_enabled', 'value' => '1', 'type' => 'toggle']);
        \App\Models\Setting::create(['key' => 'paystack_secret_key', 'value' => 'sk_test_abc', 'type' => 'password']);
        \App\Models\Setting::create(['key' => 'paystack_public_key', 'value' => 'pk_test_abc', 'type' => 'text']);
        \App\Models\Setting::create(['key' => 'paystack_currency', 'value' => 'NGN', 'type' => 'select']);

        $this->roomType = RoomType::create([
            'name' => 'Suite Room',
            'slug' => 'suite-room',
            'room_type' => 'suite',
            'price' => 100000,
            'description' => 'Test.',
            'base_guests' => 2,
            'min_stay' => 1,
            'max_stay' => 30,
            'advance_booking_days' => 365,
            'is_active' => true,
        ]);
    }

    public function test_service_reports_disabled_when_not_enabled(): void
    {
        \App\Models\Setting::where('key', 'paystack_enabled')->update(['value' => '0']);

        $service = new Paystack();
        $this->assertFalse($service->enabled());

        $result = $service->initialize(['email' => 'a@b.com', 'amount' => 1000]);
        $this->assertFalse($result['status']);
    }

    public function test_initialize_requests_authorization_url(): void
    {
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'message' => 'Authorization URL created',
                'data' => [
                    'authorization_url' => 'https://checkout.paystack.com/abc123',
                    'access_code' => 'abc123',
                    'reference' => 'BR-00001-123456-7890',
                ],
            ], 200),
        ]);

        $service = new Paystack();
        $result = $service->initialize(['email' => 'a@b.com', 'amount' => 50000]);

        $this->assertTrue($result['status']);
        $this->assertEquals('https://checkout.paystack.com/abc123', $result['authorization_url']);

        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.paystack.co/transaction/initialize'
                && $request['amount'] === 5000000  // 50000 naira * 100 = 5,000,000 kobo
                && $request['currency'] === 'NGN';
        });
    }

    public function test_verify_confirms_successful_transaction(): void
    {
        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'Verification successful',
                'data' => [
                    'status' => 'success',
                    'reference' => 'BR-00001-123456-7890',
                    'amount' => 5000000,
                ],
            ], 200),
        ]);

        $service = new Paystack();
        $result = $service->verify('BR-00001-123456-7890');

        $this->assertTrue($result['status']);
        $this->assertTrue($result['paid']);
        $this->assertEquals(50000, $result['amount']);
    }

    public function test_callback_records_payment_on_reservation(): void
    {
        $unit = RoomUnit::create(['room_type_id' => $this->roomType->id, 'unit_number' => '501', 'floor' => 5, 'is_active' => true]);

        $reservation = Reservation::create([
            'room_type_id' => $this->roomType->id,
            'room_unit_id' => $unit->id,
            'guest_name' => 'Pay Guest',
            'guest_email' => 'pay@example.com',
            'guest_phone' => '+2348099991111',
            'check_in' => now()->addDays(3)->toDateString(),
            'check_out' => now()->addDays(4)->toDateString(),
            'guests' => 1,
            'quoted_total' => 50000,
            'status' => Reservation::STATUS_CONFIRMED,
            'payment_reference' => 'BR-00001-123456-7890',
            'source' => 'website',
        ]);

        Http::fake([
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'message' => 'OK',
                'data' => ['status' => 'success', 'reference' => 'BR-00001-123456-7890', 'amount' => 5000000],
            ], 200),
        ]);

        $response = $this->get('/pay/callback?reference=BR-00001-123456-7890');
        $response->assertOk();

        $reservation->refresh();
        $this->assertEquals(Reservation::PAYMENT_PAID, $reservation->payment_status);
        $this->assertEquals(50000, (float) $reservation->amount_paid);
    }
}
