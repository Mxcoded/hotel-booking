<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            // ── Room Classification ──────────────────────────
            $table->string('room_type')->default('standard')->after('name');
            $table->string('bed_type')->nullable()->after('room_type');
            $table->unsignedInteger('room_size')->nullable()->after('bed_type'); // sqm

            // ── Capacity Tiers ──────────────────────────────
            $table->unsignedInteger('base_guests')->default(2)->after('guests');
            $table->unsignedInteger('max_guests')->nullable()->after('base_guests');
            $table->decimal('extra_guest_fee', 8, 2)->default(0)->after('max_guests');

            // ── Pricing Rules ───────────────────────────────
            $table->decimal('weekend_price', 8, 2)->nullable()->after('price');      // Fri/Sat override
            $table->decimal('holiday_price', 8, 2)->nullable()->after('weekend_price');
            $table->json('seasonal_pricing')->nullable()->after('holiday_price');     // [{start, end, price}]
            $table->decimal('discount_percent', 5, 2)->default(0)->after('seasonal_pricing');
            $table->date('discount_start')->nullable()->after('discount_percent');
            $table->date('discount_end')->nullable()->after('discount_start');

            // ── Availability / Booking Rules ────────────────
            $table->unsignedInteger('min_stay')->default(1)->after('discount_end');
            $table->unsignedInteger('max_stay')->nullable()->after('min_stay');
            $table->unsignedInteger('advance_booking_days')->default(365)->after('max_stay');
            $table->time('check_in_time')->default('14:00')->after('advance_booking_days');
            $table->time('check_out_time')->default('11:00')->after('check_in_time');

            // ── Status & Display ────────────────────────────
            $table->boolean('is_active')->default(true)->after('check_out_time');
            $table->boolean('is_featured')->default(false)->after('is_active');
            $table->unsignedInteger('sort_order')->default(0)->after('is_featured');
        });
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn([
                'room_type', 'bed_type', 'room_size',
                'base_guests', 'max_guests', 'extra_guest_fee',
                'weekend_price', 'holiday_price', 'seasonal_pricing',
                'discount_percent', 'discount_start', 'discount_end',
                'min_stay', 'max_stay', 'advance_booking_days',
                'check_in_time', 'check_out_time',
                'is_active', 'is_featured', 'sort_order',
            ]);
        });
    }
};