<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            
            // ── Classification ──────────────────────────────
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('room_type'); // standard, deluxe, suite, executive, presidential
            $table->string('bed_type')->nullable(); // king, queen, twin, double
            $table->unsignedInteger('room_size')->nullable(); // sqm
            
            // ── Description ────────────────────────────────
            $table->text('description');
            $table->json('features')->nullable(); // [{name, icon}]
            
            // ── Capacity Tiers ─────────────────────────────
            $table->unsignedInteger('base_guests')->default(2);
            $table->unsignedInteger('max_guests')->nullable();
            $table->decimal('extra_guest_fee', 8, 2)->default(0);
            
            // ── Pricing ────────────────────────────────────
            $table->decimal('price', 8, 2); // base price/night
            $table->decimal('weekend_price', 8, 2)->nullable(); // Fri/Sat
            $table->decimal('holiday_price', 8, 2)->nullable();
            $table->json('seasonal_pricing')->nullable(); // [{period, start, end, price}]
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->date('discount_start')->nullable();
            $table->date('discount_end')->nullable();
            
            // ── Booking Rules ──────────────────────────────
            $table->unsignedInteger('min_stay')->default(1);
            $table->unsignedInteger('max_stay')->nullable();
            $table->unsignedInteger('advance_booking_days')->default(365);
            $table->time('check_in_time')->default('14:00');
            $table->time('check_out_time')->default('11:00');
            
            // ── Media ──────────────────────────────────────
            $table->string('image')->nullable(); // main image
            
            // ── Status & Display ───────────────────────────
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            
            $table->timestamps();
            
            $table->index(['room_type', 'is_active']);
            $table->index(['is_featured', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_types');
    }
};