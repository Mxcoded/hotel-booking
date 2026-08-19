<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('room_media', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('type');
            $table->string('caption')->nullable()->after('sort_order');
            $table->string('alt_text')->nullable()->after('caption');
        });
    }

    public function down(): void
    {
        Schema::table('room_media', function (Blueprint $table) {
            $table->dropColumn(['sort_order', 'caption', 'alt_text']);
        });
    }
};