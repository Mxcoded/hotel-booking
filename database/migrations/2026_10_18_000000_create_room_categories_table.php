<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Room categories back the `room_types.room_type` classification column,
     * making the previously hard-coded list manageable from the admin panel.
     */
    public function up(): void
    {
        if (! Schema::hasTable('room_categories')) {
            Schema::create('room_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('color')->default('gray');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        $defaults = [
            ['name' => 'Standard', 'slug' => 'standard', 'color' => 'gray', 'sort_order' => 1],
            ['name' => 'Deluxe', 'slug' => 'deluxe', 'color' => 'info', 'sort_order' => 2],
            ['name' => 'Suite', 'slug' => 'suite', 'color' => 'warning', 'sort_order' => 3],
            ['name' => 'Executive', 'slug' => 'executive', 'color' => 'primary', 'sort_order' => 4],
            ['name' => 'Presidential', 'slug' => 'presidential', 'color' => 'danger', 'sort_order' => 5],
        ];

        foreach ($defaults as $default) {
            DB::table('room_categories')->updateOrInsert(
                ['slug' => $default['slug']],
                array_merge($default, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('room_categories');
    }
};
