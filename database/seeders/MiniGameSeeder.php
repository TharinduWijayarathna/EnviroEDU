<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\GameTemplate;
use App\Models\MiniGame;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MiniGameSeeder extends Seeder
{
    public function run(): void
    {
        $teacher = User::query()->firstOrCreate(
            ['email' => 'seed-teacher@enviroedu.local'],
            [
                'name' => 'EnviroEDU Seed Teacher',
                'password' => Hash::make('password'),
                'role' => Role::Teacher,
            ]
        );

        $templates = GameTemplate::query()->get()->keyBy('slug');

        $games = [
            // Grade 4 – Living vs Non-Living
            [
                'title' => 'Living vs Non-Living Things',
                'grade_level' => 4,
                'template_slug' => 'drag_drop',
                'config' => [
                    'categories' => [
                        ['id' => 'living', 'label' => 'Living'],
                        ['id' => 'nonliving', 'label' => 'Non-Living'],
                    ],
                    'items' => [
                        ['label' => '🌳 Tree', 'category_id' => 'living'],
                        ['label' => '🪨 Rock', 'category_id' => 'nonliving'],
                        ['label' => '🐦 Bird', 'category_id' => 'living'],
                        ['label' => '💧 Water', 'category_id' => 'nonliving'],
                        ['label' => '🌸 Flower', 'category_id' => 'living'],
                        ['label' => '🪑 Chair', 'category_id' => 'nonliving'],
                    ],
                ],
            ],
            // Grade 4 – Where do they live?
            [
                'title' => 'Animals and Their Habitats',
                'grade_level' => 4,
                'template_slug' => 'matching',
                'config' => [
                    'pairs' => [
                        ['left' => 'Fish', 'right' => 'Water'],
                        ['left' => 'Bird', 'right' => 'Nest / Trees'],
                        ['left' => 'Rabbit', 'right' => 'Burrow'],
                        ['left' => 'Bear', 'right' => 'Forest'],
                    ],
                ],
            ],
            // Grade 4 – Plants
            [
                'title' => 'What Plants Need',
                'grade_level' => 4,
                'template_slug' => 'multiple_choice',
                'config' => [
                    'questions' => [
                        [
                            'question_text' => 'What do plants need to make their food?',
                            'options' => [
                                ['text' => 'Sunlight', 'is_correct' => true],
                                ['text' => 'Darkness', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'Where do plants get water from?',
                            'options' => [
                                ['text' => 'The soil (roots)', 'is_correct' => true],
                                ['text' => 'The sky only', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],
            // Grade 5 – Water
            [
                'title' => 'The Water Cycle',
                'grade_level' => 5,
                'template_slug' => 'matching',
                'config' => [
                    'pairs' => [
                        ['left' => 'Evaporation', 'right' => 'Water turns into vapour'],
                        ['left' => 'Condensation', 'right' => 'Clouds form'],
                        ['left' => 'Precipitation', 'right' => 'Rain or snow'],
                        ['left' => 'Collection', 'right' => 'Water in rivers and seas'],
                    ],
                ],
            ],
            // Grade 5 – Soil
            [
                'title' => 'Why Soil Matters',
                'grade_level' => 5,
                'template_slug' => 'multiple_choice',
                'config' => [
                    'questions' => [
                        [
                            'question_text' => 'Why is soil important for the environment?',
                            'options' => [
                                ['text' => 'Plants grow in it and it filters water', 'is_correct' => true],
                                ['text' => 'It is only for building', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'What can we do to protect soil?',
                            'options' => [
                                ['text' => 'Reduce waste and avoid pollution', 'is_correct' => true],
                                ['text' => 'Use more chemicals', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],
            // Grade 5 – Weather & protection
            [
                'title' => 'Weather and the Environment',
                'grade_level' => 5,
                'template_slug' => 'drag_drop',
                'config' => [
                    'categories' => [
                        ['id' => 'good', 'label' => 'Good for the environment'],
                        ['id' => 'bad', 'label' => 'Bad for the environment'],
                    ],
                    'items' => [
                        ['label' => 'Recycling', 'category_id' => 'good'],
                        ['label' => 'Littering', 'category_id' => 'bad'],
                        ['label' => 'Saving water', 'category_id' => 'good'],
                        ['label' => 'Polluting rivers', 'category_id' => 'bad'],
                    ],
                ],
            ],
        ];

        foreach ($games as $game) {
            $template = $templates->get($game['template_slug']);
            if (! $template) {
                continue;
            }

            MiniGame::query()->updateOrCreate(
                [
                    'user_id' => $teacher->id,
                    'title' => $game['title'],
                    'grade_level' => $game['grade_level'],
                ],
                [
                    'game_template_id' => $template->id,
                    'description' => 'Seed game for Grade '.$game['grade_level'].'.',
                    'config' => $game['config'],
                    'is_published' => true,
                ]
            );
        }
    }
}
