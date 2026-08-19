<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_units', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('room_type_id')->constrained()->onDelete('cascade');
            $table->string('unit_number'); // e.g., "101", "202", "301"
            $table->unsignedInteger('floor')->nullable();
            $table->enum('status', ['available', 'occupied', 'maintenance', 'out_of_order', 'housekeeping'])->default('available');
            $table->text('notes')->nullable(); // unit-specific notes
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            
            $table->unique(['room_type_id', 'unit_number']);
            $table->index(['status', 'is_active']);
            $table->index(['floor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_units');
    }
};