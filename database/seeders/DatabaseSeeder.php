<?php

namespace Database\Seeders;

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
        $this->call(GameTemplateSeeder::class);
        $this->call(BadgeSeeder::class);
        $this->call(PlatformGameSeeder::class);
        $this->call(MiniGameSeeder::class);
        $this->call(DemoSeeder::class);
    }
}
