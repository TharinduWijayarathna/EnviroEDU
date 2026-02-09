<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->student()->create([
            'name' => 'Student User',
            'email' => 'student@example.com',
        ]);
        User::factory()->teacher()->create([
            'name' => 'Teacher User',
            'email' => 'teacher@example.com',
        ]);
        User::factory()->parent()->create([
            'name' => 'Parent User',
            'email' => 'parent@example.com',
        ]);

        $this->call([
            AchievementSeeder::class,
            VideoLessonSeeder::class,
        ]);
    }
}
