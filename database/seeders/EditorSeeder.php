<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EditorSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    // User::factory(10)->create();

    // First admin
    User::factory()->create([
      'name' => 'Editor',
      'email' => env('EDITOR_EMAIL', 'editor@example.com'),
      'email_verified_at' => now(),
      'password' => bcrypt(env('EDITOR_PASSWORD', '12345678')),
      'role' => 'editor',
    ]);
  }
}
