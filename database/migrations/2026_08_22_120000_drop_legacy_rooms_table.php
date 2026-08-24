<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop the legacy `rooms` table, superseded by `room_types` (with
     * `room_units` and `room_availabilities`). Also removes the dead
     * `room_id` columns/FKs that pointed at it.
     */
    public function up(): void
    {
        Schema::table('room_availabilities', function (Blueprint $table) {
            if (Schema::hasColumn('room_availabilities', 'room_id')) {
                $table->dropForeign(['room_id']);
                $table->dropColumn('room_id');
            }
        });

        Schema::table('room_media', function (Blueprint $table) {
            if (Schema::hasColumn('room_media', 'room_id')) {
                $table->dropForeign(['room_id']);
                $table->dropColumn('room_id');
            }
        });

        Schema::dropIfExists('rooms');
    }

    /**
     * Restore the legacy `rooms` table. Previously dropped FK relations
     * from room_availabilities/room_media are intentionally not restored.
     */
    public function down(): void
    {
        if (Schema::hasTable('rooms')) {
            return;
        }

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 8, 2);
            $table->string('image');
            $table->json('features')->nullable(); // For storing features like bed type, wifi, etc.
            $table->integer('guests');
            $table->timestamps();
        });
    }
};
