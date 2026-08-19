<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_media', function (Blueprint $table) {
            $table->foreignId('room_type_id')->nullable()->constrained()->onDelete('cascade')->after('room_id');
        });
        
        // Copy data from room_id to room_type_id via rooms table
        // This will be handled in a data migration/seeder
    }

    public function down(): void
    {
        Schema::table('room_media', function (Blueprint $table) {
            $table->dropConstrainedForeignId('room_type_id');
        });
    }
};