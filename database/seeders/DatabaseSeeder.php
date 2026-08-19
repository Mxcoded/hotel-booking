<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            SettingsSeeder::class,
            RoomSeeder::class,
        ]);

        // Create admin role
        Role::create(['name' => 'admin']);

        // Create default admin user and assign role
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@brickspoint.ng',
        ])->assignRole('admin');
    }
}
