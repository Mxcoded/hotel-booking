<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamp('no_show_at')->nullable();

            $table->string('payment_status')->default('unpaid');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('paid_at')->nullable();

            $table->string('coupon_code')->nullable();
            $table->decimal('discount_amount', 10, 2)->default(0);
        });

        // The status column was originally a MySQL/SQLite ENUM restricted to
        // pending/confirmed/cancelled. Widen it to a plain string so the new
        // lifecycle statuses (checked_in/checked_out/no_show) are accepted.
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE reservations MODIFY status VARCHAR(32) NOT NULL DEFAULT 'pending'");
        } elseif ($driver === 'sqlite') {
            Schema::table('reservations', function (Blueprint $table) {
                $table->string('status')->default('pending')->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropColumn([
                'checked_in_at',
                'checked_out_at',
                'no_show_at',
                'payment_status',
                'amount_paid',
                'payment_method',
                'payment_reference',
                'paid_at',
                'coupon_code',
                'discount_amount',
            ]);
        });
    }
};
