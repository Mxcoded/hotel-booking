<?php

namespace Tests\Browser;

use Tests\Browser\DuskTestCase;

class AdminPaymentsAuditTest extends DuskTestCase
{
    public function test_payment_settings_page_renders(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/payment-settings')
                ->assertSee('Paystack Gateway')
                ->assertSee('Enable Paystack Payments')
                ->assertSee('Save Payment Settings');
        });
    }

    public function test_audit_log_page_loads_with_columns(): void
    {
        $this->browse(function ($browser) {
            $this->loginAsAdmin($browser);

            $browser->visit('/admin/audit-log')
                ->assertSee('When')
                ->assertSee('By')
                ->assertSee('Changes');
        });
    }
}
