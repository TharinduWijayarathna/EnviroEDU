<?php

namespace Database\Seeders;

use App\Models\VideoLesson;
use Illuminate\Database\Seeder;

class VideoLessonSeeder extends Seeder
{
    public function run(): void
    {
        VideoLesson::firstOrCreate(
            ['title' => 'Plant Parts'],
            [
                'description' => 'Learn about the parts of a plant and their functions.',
                'grade_level' => 'grade4',
                'video_path' => null,
                'key_points' => [
                    'Roots absorb water.',
                    'The stem supports the plant.',
                    'Leaves make food.',
                ],
            ]
        );
    }
}
