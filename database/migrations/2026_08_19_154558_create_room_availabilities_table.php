<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('room_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['available', 'booked', 'blocked', 'maintenance'])->default('available');
            $table->decimal('price_override', 8, 2)->nullable();
            $table->text('note')->nullable(); // e.g., "Christmas block", "Maintenance"
            $table->timestamps();

            $table->unique(['room_id', 'date']);
            $table->index(['date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_availabilities');
    }
};