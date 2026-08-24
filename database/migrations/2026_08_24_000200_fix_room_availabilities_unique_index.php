<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // When the legacy room_id column was dropped, MySQL reduced this
        // index to (date) alone — silently allowing only ONE availability
        // row per date across all units. Restore correct semantics.
        Schema::table('room_availabilities', function (Blueprint $table) {
            $table->dropUnique('room_availabilities_room_id_date_unique');
            $table->unique(['room_unit_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('room_availabilities', function (Blueprint $table) {
            $table->dropUnique(['room_unit_id', 'date']);
            $table->unique('date');
        });
    }
};
