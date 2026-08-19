<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('label')->nullable()->after('group');
            $table->text('description')->nullable()->after('label');
            $table->unsignedInteger('sort')->default(0)->after('description');
            $table->boolean('is_active')->default(true)->after('sort');
            $table->json('options')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['label', 'description', 'sort', 'is_active', 'options']);
        });
    }
};