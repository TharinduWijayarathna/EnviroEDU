<?php

namespace Database\Seeders;

use App\Models\Badge;
use App\Models\PlatformGame;
use Illuminate\Database\Seeder;

class PlatformGameSeeder extends Seeder
{
    /**
     * @return array<int, array{slug: string, title: string, description: string, order: int, badge_name: string, badge_slug: string, badge_icon: string, badge_desc: string}>
     */
    protected function games(): array
    {
        return [
            [
                'slug' => 'photosynthesis',
                'title' => 'Photosynthesis for Kids',
                'description' => 'Help a plant grow! Drag water to the plant, place the sun in the sky, and click the wind. Watch flowers bloom!',
                'order' => 1,
                'badge_name' => 'Plant Helper',
                'badge_slug' => 'badge-photosynthesis',
                'badge_icon' => '🌻',
                'badge_desc' => 'You completed the Photosynthesis game! You helped a plant get water, sunlight, and air.',
            ],
            [
                'slug' => 'seed-grow',
                'title' => 'How a Seed Grows',
                'description' => 'Watch a seed turn into a plant! Add soil, water, and sunlight and see each stage of growth.',
                'order' => 2,
                'badge_name' => 'Seed Expert',
                'badge_slug' => 'badge-seed-grow',
                'badge_icon' => '🌱',
                'badge_desc' => 'You learned how a seed grows with soil, water, and sun!',
            ],
            [
                'slug' => 'vine-growth',
                'title' => 'How a Vine Grows Around a Tree',
                'description' => 'See how vines climb and wrap around a tree to reach the sunlight.',
                'order' => 3,
                'badge_name' => 'Vine Explorer',
                'badge_slug' => 'badge-vine-growth',
                'badge_icon' => '🌿',
                'badge_desc' => 'You discovered how vines grow around trees!',
            ],
            [
                'slug' => 'star-patterns',
                'title' => 'Star Patterns',
                'description' => 'Learn about patterns of stars in the night sky and how people see pictures in them.',
                'order' => 4,
                'badge_name' => 'Star Gazer',
                'badge_slug' => 'badge-star-patterns',
                'badge_icon' => '⭐',
                'badge_desc' => 'You learned about star patterns in the sky!',
            ],
            [
                'slug' => 'rainbow',
                'title' => 'How a Rainbow is Made',
                'description' => 'Discover how sunlight and rain create a rainbow with beautiful colors.',
                'order' => 5,
                'badge_name' => 'Rainbow Scientist',
                'badge_slug' => 'badge-rainbow',
                'badge_icon' => '🌈',
                'badge_desc' => 'You learned how rainbows are made from light and water!',
            ],
            [
                'slug' => 'water-cycle',
                'title' => 'The Water Cycle',
                'description' => 'Follow water as it evaporates, forms clouds, and falls back as rain.',
                'order' => 6,
                'badge_name' => 'Water Cycle Pro',
                'badge_slug' => 'badge-water-cycle',
                'badge_icon' => '💧',
                'badge_desc' => 'You learned how water moves around Earth!',
            ],
            [
                'slug' => 'day-night',
                'title' => 'How Day and Night Work',
                'description' => 'See how Earth rotating around the Sun gives us day and night.',
                'order' => 7,
                'badge_name' => 'Day & Night Explorer',
                'badge_slug' => 'badge-day-night',
                'badge_icon' => '🌍',
                'badge_desc' => 'You learned why we have day and night!',
            ],
            [
                'slug' => 'solar-eclipse',
                'title' => 'How a Solar Eclipse Works',
                'description' => 'Watch the Moon pass in front of the Sun and see how a solar eclipse happens.',
                'order' => 8,
                'badge_name' => 'Eclipse Explorer',
                'badge_slug' => 'badge-solar-eclipse',
                'badge_icon' => '🌑',
                'badge_desc' => 'You learned how a solar eclipse works!',
            ],
            [
                'slug' => 'lunar-eclipse',
                'title' => 'How a Lunar Eclipse Works',
                'description' => 'See how the Earth blocks sunlight from the Moon during a lunar eclipse.',
                'order' => 9,
                'badge_name' => 'Moon Eclipse Expert',
                'badge_slug' => 'badge-lunar-eclipse',
                'badge_icon' => '🌒',
                'badge_desc' => 'You learned how a lunar eclipse works!',
            ],
        ];
    }

    public function run(): void
    {
        foreach ($this->games() as $data) {
            $game = PlatformGame::query()->firstOrCreate(
                ['slug' => $data['slug']],
                [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'order' => $data['order'],
                    'config' => null,
                ]
            );

            Badge::query()->firstOrCreate(
                ['slug' => $data['badge_slug']],
                [
                    'name' => $data['badge_name'],
                    'description' => $data['badge_desc'],
                    'icon' => $data['badge_icon'],
                    'criteria_type' => 'platform_game_complete',
                    'criteria_config' => ['platform_game_slug' => $game->slug],
                    'order' => $data['order'] + 10,
                ]
            );
        }
    }
}
