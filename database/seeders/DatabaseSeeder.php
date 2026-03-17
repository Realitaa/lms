<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // First admin
        User::factory()->create([
            'name' => 'Admin',
            'email' => env('ADMIN_EMAIL', 'admin@example.com'),
            'email_verified_at' => now(),
            'password' => bcrypt(env('ADMIN_PASSWORD', '12345678')),
            'role' => 'admin',
        ]);
    }
}
