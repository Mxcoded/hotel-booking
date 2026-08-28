<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('housekeeping_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_unit_id')->constrained()->cascadeOnDelete();
            $table->string('status')->default('dirty'); // dirty | clean | inspected
            $table->text('note')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('cleaned_at')->nullable();
            $table->timestamps();

            $table->index(['room_unit_id', 'status']);
        });

        // Track each unit's current housekeeping status directly for easy filtering.
        Schema::table('room_units', function (Blueprint $table) {
            $table->string('housekeeping_status')->default('clean');
        });
    }

    public function down(): void
    {
        Schema::table('room_units', function (Blueprint $table) {
            $table->dropColumn('housekeeping_status');
        });

        Schema::dropIfExists('housekeeping_logs');
    }
};
