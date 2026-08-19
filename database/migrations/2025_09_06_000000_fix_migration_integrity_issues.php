<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Sessions: Add proper foreign key constraint on user_id
        Schema::table('sessions', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 2. Rooms: Enforce unsigned values for price and guests
        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->unsigned()->change();
            $table->unsignedInteger('guests')->change();
        });

        // 3. Feedbacks: Enforce unsigned rating and add index on is_approved
        Schema::table('feedbacks', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating')->change();
            $table->index('is_approved');
        });

        // 4. WhatsApp Leads: Add unique constraint on phone
        Schema::table('whatsapp_leads', function (Blueprint $table) {
            $table->unique('phone');
        });

        // 5. Contacts: Add index on is_read for admin inbox filtering
        Schema::table('contacts', function (Blueprint $table) {
            $table->index('is_read');
        });

        // 6. Attractions: Add index on category for grouping queries
        Schema::table('attractions', function (Blueprint $table) {
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('rooms', function (Blueprint $table) {
            $table->decimal('price', 8, 2)->change();
            $table->integer('guests')->change();
        });

        Schema::table('feedbacks', function (Blueprint $table) {
            $table->tinyInteger('rating')->change();
            $table->dropIndex(['is_approved']);
        });

        Schema::table('whatsapp_leads', function (Blueprint $table) {
            $table->dropIndex(['phone']);
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropIndex(['is_read']);
        });

        Schema::table('attractions', function (Blueprint $table) {
            $table->dropIndex(['category']);
        });
    }
};
