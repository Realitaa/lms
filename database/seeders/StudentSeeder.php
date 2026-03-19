<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // User::factory(10)->create();

    // First admin
    User::factory()->create([
      'name' => 'Student',
      'email' => env('STUDENT_EMAIL', 'student@example.com'),
      'email_verified_at' => now(),
      'password' => bcrypt(env('STUDENT_PASSWORD', '12345678')),
      'role' => 'user',
    ]);
  }
}
