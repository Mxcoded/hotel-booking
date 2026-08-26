<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Feedback;
use App\Models\Gallery;
use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\RoomUnit;
use App\Models\RoomMedia;
use App\Models\Attraction;
use App\Models\Setting;
use App\Models\WhatsappLead;
use Illuminate\Database\Seeder;

class DuskTestDataSeeder extends Seeder
{
    public function run(): void
    {
        // Contacts
        Contact::create([
            'name' => 'Ada Obi',
            'email' => 'ada@example.com',
            'message' => 'Do you have airport pickup?',
        ]);
        Contact::create([
            'name' => 'Chinedu Okonkwo',
            'email' => 'chinedu@example.com',
            'message' => 'What are your check-in times?',
        ]);

        // Feedback
        Feedback::create([
            'name' => 'Ngozi Eze',
            'email' => 'ngozi@example.com',
            'rating' => 5,
            'message' => 'Excellent service throughout our stay.',
            'is_approved' => false,
        ]);
        Feedback::create([
            'name' => 'Emeka Okafor',
            'email' => 'emeka@example.com',
            'rating' => 4,
            'message' => 'Great location and friendly staff.',
            'is_approved' => true,
        ]);

        // Gallery
        Gallery::create([
            'path' => 'gallery/hotel-lobby.jpg',
            'alt_text' => 'Hotel lobby',
        ]);
        Gallery::create([
            'path' => 'gallery/room-view.jpg',
            'alt_text' => 'Room view',
        ]);

        // Reservations (need RoomType and RoomUnit)
        $roomType = RoomType::first();
        $roomUnit = RoomUnit::where('room_type_id', $roomType->id)->first();
        
        if ($roomType && $roomUnit) {
            Reservation::create([
                'room_type_id' => $roomType->id,
                'room_unit_id' => $roomUnit->id,
                'guest_name' => 'Ngozi Booking',
                'guest_email' => 'ngozi.booking@example.com',
                'guest_phone' => '+2348012345678',
                'check_in' => now()->addDays(5)->toDateString(),
                'check_out' => now()->addDays(8)->toDateString(),
                'guests' => 2,
                'status' => \App\Models\Reservation::STATUS_CONFIRMED,
                'source' => 'website',
            ]);
        }

        // WhatsApp Leads
        WhatsappLead::create([
            'name' => 'Test Lead',
            'phone' => '+2348012345679',
        ]);

        // Settings (already seeded by SettingsSeeder)
    }
}