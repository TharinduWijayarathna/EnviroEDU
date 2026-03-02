<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Badge;
use App\Models\ClassRoom;
use App\Models\GameTemplate;
use App\Models\MiniGame;
use App\Models\MiniGameAttempt;
use App\Models\PlatformGame;
use App\Models\PlatformGameAttempt;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\QuizQuestionOption;
use App\Models\School;
use App\Models\Topic;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    private const PASSWORD = '123123123';

    private School $school;

    private User $admin;

    private User $teacher;

    private User $demoStudent;

    private User $parent;

    /** @var array<int, User> */
    private array $students = [];

    /** @var array<int, ClassRoom> */
    private array $classRooms = [];

    /** @var array<int, Topic> */
    private array $topics = [];

    /** @var array<int, Quiz> */
    private array $quizzes = [];

    /** @var array<int, MiniGame> */
    private array $miniGames = [];

    public function run(): void
    {
        $this->createSchoolAndUsers();
        $this->createClasses();
        $this->createTopics();
        $this->createQuizzes();
        $this->createMiniGames();
        $this->createStudentActivity();
    }

    private function createSchoolAndUsers(): void
    {
        $this->school = School::query()->firstOrCreate(
            ['slug' => 'test-1'],
            ['name' => 'Demo Test School']
        );

        $this->admin = User::query()->firstOrCreate(
            ['email' => 'school@gmail.com'],
            [
                'name' => 'School Admin',
                'password' => Hash::make(self::PASSWORD),
                'role' => Role::SchoolAdmin,
                'school_id' => $this->school->id,
                'is_approved' => true,
            ]
        );
        $this->school->update(['admin_id' => $this->admin->id]);

        $this->teacher = User::query()->firstOrCreate(
            ['email' => 'teacher@gmail.com'],
            [
                'name' => 'Demo Teacher',
                'password' => Hash::make(self::PASSWORD),
                'role' => Role::Teacher,
                'school_id' => $this->school->id,
                'is_approved' => true,
            ]
        );

        $this->demoStudent = User::query()->firstOrCreate(
            ['email' => 'student@gmail.com'],
            [
                'name' => 'Demo Student',
                'password' => Hash::make(self::PASSWORD),
                'role' => Role::Student,
                'school_id' => $this->school->id,
                'grade_level' => 4,
                'is_approved' => true,
            ]
        );

        $this->parent = User::query()->firstOrCreate(
            ['email' => 'parent@gmail.com'],
            [
                'name' => 'Demo Parent',
                'password' => Hash::make(self::PASSWORD),
                'role' => Role::Parent,
                'school_id' => null,
                'is_approved' => true,
            ]
        );

        $this->parent->children()->syncWithoutDetaching([$this->demoStudent->id]);

        $this->students[] = $this->demoStudent;

        for ($i = 1; $i <= 99; $i++) {
            $gradeLevel = $i % 2 === 0 ? 4 : 5;
            $student = User::query()->firstOrCreate(
                ['email' => "student{$i}@demo.enviroedu.local"],
                [
                    'name' => "Demo Student {$i}",
                    'password' => Hash::make(self::PASSWORD),
                    'role' => Role::Student,
                    'school_id' => $this->school->id,
                    'grade_level' => $gradeLevel,
                    'is_approved' => true,
                ]
            );
            $this->students[] = $student;
        }

        $this->parent->children()->syncWithoutDetaching(
            collect($this->students)->take(3)->pluck('id')->toArray()
        );
    }

    private function createClasses(): void
    {
        $classes = [
            ['name' => 'Grade 4A', 'grade_level' => 4, 'description' => 'Grade 4 Section A'],
            ['name' => 'Grade 4B', 'grade_level' => 4, 'description' => 'Grade 4 Section B'],
            ['name' => 'Grade 5A', 'grade_level' => 5, 'description' => 'Grade 5 Section A'],
            ['name' => 'Grade 5B', 'grade_level' => 5, 'description' => 'Grade 5 Section B'],
        ];

        foreach ($classes as $data) {
            $classRoom = ClassRoom::query()->firstOrCreate(
                [
                    'user_id' => $this->teacher->id,
                    'name' => $data['name'],
                ],
                [
                    'school_id' => $this->school->id,
                    'description' => $data['description'],
                    'grade_level' => $data['grade_level'],
                ]
            );
            $this->classRooms[] = $classRoom;
        }

        foreach ($this->students as $index => $student) {
            $classIndex = (int) floor($index / 25) % 4;
            $this->classRooms[$classIndex]->students()->syncWithoutDetaching([$student->id]);
        }
    }

    private function createTopics(): void
    {
        $topicsData = [
            ['title' => 'Living vs Non-Living', 'grade_level' => 4, 'order' => 1],
            ['title' => 'Animals and Habitats', 'grade_level' => 4, 'order' => 2],
            ['title' => 'Plants and Growth', 'grade_level' => 4, 'order' => 3],
            ['title' => 'The Water Cycle', 'grade_level' => 5, 'order' => 4],
            ['title' => 'Soil and Environment', 'grade_level' => 5, 'order' => 5],
            ['title' => 'Weather and Climate', 'grade_level' => 5, 'order' => 6],
            ['title' => 'Recycling and Waste', 'grade_level' => 4, 'order' => 7],
            ['title' => 'Energy and Resources', 'grade_level' => 5, 'order' => 8],
        ];

        foreach ($topicsData as $data) {
            $topic = Topic::query()->firstOrCreate(
                [
                    'user_id' => $this->teacher->id,
                    'title' => $data['title'],
                ],
                [
                    'description' => "Learn about {$data['title']}.",
                    'grade_level' => $data['grade_level'],
                    'order' => $data['order'],
                    'is_published' => true,
                ]
            );
            $this->topics[] = $topic;
        }
    }

    private function createQuizzes(): void
    {
        $quizData = $this->getQuizData();

        foreach ($quizData as $index => $data) {
            $topic = $this->topics[$index % count($this->topics)] ?? $this->topics[0];
            $quiz = Quiz::query()->firstOrCreate(
                [
                    'user_id' => $this->teacher->id,
                    'title' => $data['title'],
                ],
                [
                    'topic_id' => $topic->id,
                    'description' => $data['description'] ?? null,
                    'grade_level' => $data['grade_level'],
                    'is_published' => true,
                ]
            );
            $this->quizzes[] = $quiz;

            foreach ($data['questions'] as $qOrder => $q) {
                $question = QuizQuestion::query()->firstOrCreate(
                    [
                        'quiz_id' => $quiz->id,
                        'question_text' => $q['text'],
                    ],
                    ['order' => $qOrder + 1]
                );
                foreach ($q['options'] as $oOrder => $opt) {
                    QuizQuestionOption::query()->firstOrCreate(
                        [
                            'quiz_question_id' => $question->id,
                            'option_text' => $opt['text'],
                        ],
                        [
                            'is_correct' => $opt['correct'],
                            'order' => $oOrder + 1,
                        ]
                    );
                }
            }
        }
    }

    /**
     * @return array<int, array{title: string, description?: string, grade_level: int, questions: array<int, array{text: string, options: array<int, array{text: string, correct: bool}>}>}>
     */
    private function getQuizData(): array
    {
        return [
            [
                'title' => 'Living vs Non-Living Quiz',
                'grade_level' => 4,
                'questions' => [
                    ['text' => 'Which is a living thing?', 'options' => [['text' => 'Rock', 'correct' => false], ['text' => 'Tree', 'correct' => true], ['text' => 'Chair', 'correct' => false]]],
                    ['text' => 'What do living things need?', 'options' => [['text' => 'Food and water', 'correct' => true], ['text' => 'Nothing', 'correct' => false]]],
                    ['text' => 'Is water a living thing?', 'options' => [['text' => 'Yes', 'correct' => false], ['text' => 'No', 'correct' => true]]],
                ],
            ],
            [
                'title' => 'Habitats Basics',
                'grade_level' => 4,
                'questions' => [
                    ['text' => 'Where do fish live?', 'options' => [['text' => 'In water', 'correct' => true], ['text' => 'In trees', 'correct' => false]]],
                    ['text' => 'What is a habitat?', 'options' => [['text' => 'A home for animals', 'correct' => true], ['text' => 'A type of food', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Plants Need',
                'grade_level' => 4,
                'questions' => [
                    ['text' => 'What do plants need to grow?', 'options' => [['text' => 'Sunlight', 'correct' => true], ['text' => 'Darkness only', 'correct' => false]]],
                    ['text' => 'Where do plants get water?', 'options' => [['text' => 'From soil through roots', 'correct' => true], ['text' => 'From the sky only', 'correct' => false]]],
                    ['text' => 'What process do plants use to make food?', 'options' => [['text' => 'Photosynthesis', 'correct' => true], ['text' => 'Respiration', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Water Cycle Steps',
                'grade_level' => 5,
                'questions' => [
                    ['text' => 'What is evaporation?', 'options' => [['text' => 'Water turning into vapour', 'correct' => true], ['text' => 'Rain falling', 'correct' => false]]],
                    ['text' => 'What is condensation?', 'options' => [['text' => 'Clouds forming', 'correct' => true], ['text' => 'Water boiling', 'correct' => false]]],
                    ['text' => 'What is precipitation?', 'options' => [['text' => 'Rain or snow falling', 'correct' => true], ['text' => 'Water evaporating', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Soil Importance',
                'grade_level' => 5,
                'questions' => [
                    ['text' => 'Why is soil important?', 'options' => [['text' => 'Plants grow in it', 'correct' => true], ['text' => 'Only for building', 'correct' => false]]],
                    ['text' => 'How can we protect soil?', 'options' => [['text' => 'Reduce waste and avoid pollution', 'correct' => true], ['text' => 'Use more chemicals', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Weather and Environment',
                'grade_level' => 5,
                'questions' => [
                    ['text' => 'What is good for the environment?', 'options' => [['text' => 'Recycling', 'correct' => true], ['text' => 'Littering', 'correct' => false]]],
                    ['text' => 'What causes pollution?', 'options' => [['text' => 'Throwing trash in rivers', 'correct' => true], ['text' => 'Planting trees', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Recycling Basics',
                'grade_level' => 4,
                'questions' => [
                    ['text' => 'What can be recycled?', 'options' => [['text' => 'Paper and plastic', 'correct' => true], ['text' => 'Food waste only', 'correct' => false]]],
                    ['text' => 'Why is recycling important?', 'options' => [['text' => 'Reduces waste and saves resources', 'correct' => true], ['text' => 'It is not important', 'correct' => false]]],
                    ['text' => 'What are the 3 Rs?', 'options' => [['text' => 'Reduce, Reuse, Recycle', 'correct' => true], ['text' => 'Run, Rest, Relax', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Energy Sources',
                'grade_level' => 5,
                'questions' => [
                    ['text' => 'Which is a renewable energy source?', 'options' => [['text' => 'Solar power', 'correct' => true], ['text' => 'Coal', 'correct' => false]]],
                    ['text' => 'What does renewable mean?', 'options' => [['text' => 'Can be replaced naturally', 'correct' => true], ['text' => 'Never runs out', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Ecosystem Balance',
                'grade_level' => 5,
                'questions' => [
                    ['text' => 'What is an ecosystem?', 'options' => [['text' => 'Living and non-living things together', 'correct' => true], ['text' => 'Only plants', 'correct' => false]]],
                    ['text' => 'Why are bees important?', 'options' => [['text' => 'They pollinate plants', 'correct' => true], ['text' => 'They make honey only', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Food Chains',
                'grade_level' => 4,
                'questions' => [
                    ['text' => 'What starts a food chain?', 'options' => [['text' => 'Plants (producers)', 'correct' => true], ['text' => 'Lions', 'correct' => false]]],
                    ['text' => 'What do herbivores eat?', 'options' => [['text' => 'Plants', 'correct' => true], ['text' => 'Other animals only', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Conservation Quiz',
                'grade_level' => 5,
                'questions' => [
                    ['text' => 'What is conservation?', 'options' => [['text' => 'Protecting natural resources', 'correct' => true], ['text' => 'Using more resources', 'correct' => false]]],
                    ['text' => 'How can we save water?', 'options' => [['text' => 'Turn off taps when not in use', 'correct' => true], ['text' => 'Leave water running', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Climate Basics',
                'grade_level' => 5,
                'questions' => [
                    ['text' => 'What is climate?', 'options' => [['text' => 'Weather over a long time', 'correct' => true], ['text' => 'Today\'s weather', 'correct' => false]]],
                    ['text' => 'What affects climate?', 'options' => [['text' => 'Sun, oceans, and land', 'correct' => true], ['text' => 'Only the moon', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Pollution Types',
                'grade_level' => 4,
                'questions' => [
                    ['text' => 'What is air pollution?', 'options' => [['text' => 'Harmful substances in the air', 'correct' => true], ['text' => 'Clean air', 'correct' => false]]],
                    ['text' => 'What causes water pollution?', 'options' => [['text' => 'Trash and chemicals in water', 'correct' => true], ['text' => 'Fish swimming', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Biodiversity',
                'grade_level' => 5,
                'questions' => [
                    ['text' => 'What is biodiversity?', 'options' => [['text' => 'Variety of life on Earth', 'correct' => true], ['text' => 'One type of animal', 'correct' => false]]],
                    ['text' => 'Why is biodiversity important?', 'options' => [['text' => 'Keeps ecosystems healthy', 'correct' => true], ['text' => 'It is not important', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Composting',
                'grade_level' => 4,
                'questions' => [
                    ['text' => 'What is composting?', 'options' => [['text' => 'Turning waste into soil', 'correct' => true], ['text' => 'Burning trash', 'correct' => false]]],
                    ['text' => 'What can be composted?', 'options' => [['text' => 'Fruit and vegetable scraps', 'correct' => true], ['text' => 'Plastic', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Endangered Species',
                'grade_level' => 5,
                'questions' => [
                    ['text' => 'What does endangered mean?', 'options' => [['text' => 'At risk of extinction', 'correct' => true], ['text' => 'Very common', 'correct' => false]]],
                    ['text' => 'How can we help endangered animals?', 'options' => [['text' => 'Protect their habitats', 'correct' => true], ['text' => 'Hunt them', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Natural Resources',
                'grade_level' => 4,
                'questions' => [
                    ['text' => 'What is a natural resource?', 'options' => [['text' => 'Something from nature we use', 'correct' => true], ['text' => 'Something we make', 'correct' => false]]],
                    ['text' => 'Which is a natural resource?', 'options' => [['text' => 'Water', 'correct' => true], ['text' => 'Plastic', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Oceans and Seas',
                'grade_level' => 5,
                'questions' => [
                    ['text' => 'Why are oceans important?', 'options' => [['text' => 'Provide oxygen and food', 'correct' => true], ['text' => 'Only for swimming', 'correct' => false]]],
                    ['text' => 'What harms ocean life?', 'options' => [['text' => 'Plastic pollution', 'correct' => true], ['text' => 'Fish swimming', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Forests',
                'grade_level' => 4,
                'questions' => [
                    ['text' => 'Why are forests important?', 'options' => [['text' => 'Provide oxygen and habitat', 'correct' => true], ['text' => 'Only for wood', 'correct' => false]]],
                    ['text' => 'What is deforestation?', 'options' => [['text' => 'Cutting down forests', 'correct' => true], ['text' => 'Planting trees', 'correct' => false]]],
                ],
            ],
            [
                'title' => 'Environmental Heroes',
                'grade_level' => 4,
                'questions' => [
                    ['text' => 'What can you do to help the environment?', 'options' => [['text' => 'Reduce, reuse, recycle', 'correct' => true], ['text' => 'Use more plastic', 'correct' => false]]],
                    ['text' => 'Who can be an environmental hero?', 'options' => [['text' => 'Anyone who helps the planet', 'correct' => true], ['text' => 'Only scientists', 'correct' => false]]],
                ],
            ],
        ];
    }

    private function createMiniGames(): void
    {
        $templates = GameTemplate::query()->get()->keyBy('slug');

        $games = [
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
            [
                'title' => 'Recycle or Trash?',
                'grade_level' => 4,
                'template_slug' => 'drag_drop',
                'config' => [
                    'categories' => [
                        ['id' => 'recycle', 'label' => 'Recycle'],
                        ['id' => 'trash', 'label' => 'Trash'],
                    ],
                    'items' => [
                        ['label' => 'Paper', 'category_id' => 'recycle'],
                        ['label' => 'Plastic bottle', 'category_id' => 'recycle'],
                        ['label' => 'Food scraps', 'category_id' => 'trash'],
                        ['label' => 'Glass jar', 'category_id' => 'recycle'],
                    ],
                ],
            ],
            [
                'title' => 'Renewable vs Non-Renewable',
                'grade_level' => 5,
                'template_slug' => 'matching',
                'config' => [
                    'pairs' => [
                        ['left' => 'Solar', 'right' => 'Renewable'],
                        ['left' => 'Wind', 'right' => 'Renewable'],
                        ['left' => 'Coal', 'right' => 'Non-Renewable'],
                        ['left' => 'Oil', 'right' => 'Non-Renewable'],
                    ],
                ],
            ],
            [
                'title' => 'Ecosystem Connections',
                'grade_level' => 5,
                'template_slug' => 'multiple_choice',
                'config' => [
                    'questions' => [
                        [
                            'question_text' => 'What do producers do in an ecosystem?',
                            'options' => [
                                ['text' => 'Make food from sunlight', 'is_correct' => true],
                                ['text' => 'Eat other animals', 'is_correct' => false],
                            ],
                        ],
                        [
                            'question_text' => 'What is a food chain?',
                            'options' => [
                                ['text' => 'How energy moves from plants to animals', 'is_correct' => true],
                                ['text' => 'A chain you wear', 'is_correct' => false],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'title' => 'Protect Our Planet',
                'grade_level' => 4,
                'template_slug' => 'drag_drop',
                'config' => [
                    'categories' => [
                        ['id' => 'help', 'label' => 'Helps the planet'],
                        ['id' => 'harm', 'label' => 'Harms the planet'],
                    ],
                    'items' => [
                        ['label' => 'Plant trees', 'category_id' => 'help'],
                        ['label' => 'Use reusable bags', 'category_id' => 'help'],
                        ['label' => 'Throw trash on ground', 'category_id' => 'harm'],
                        ['label' => 'Waste water', 'category_id' => 'harm'],
                    ],
                ],
            ],
        ];

        foreach ($games as $game) {
            $template = $templates->get($game['template_slug']);
            if (! $template) {
                continue;
            }

            $topic = $this->topics[array_rand($this->topics)] ?? null;
            $miniGame = MiniGame::query()->firstOrCreate(
                [
                    'user_id' => $this->teacher->id,
                    'title' => $game['title'],
                    'grade_level' => $game['grade_level'],
                ],
                [
                    'game_template_id' => $template->id,
                    'topic_id' => $topic?->id,
                    'description' => 'Demo game for Grade '.$game['grade_level'].'.',
                    'config' => $game['config'],
                    'is_published' => true,
                ]
            );
            $this->miniGames[] = $miniGame;
        }
    }

    private function createStudentActivity(): void
    {
        $platformGames = PlatformGame::query()->get();
        $badges = Badge::query()->get();
        $baseDate = Carbon::now()->subDays(30);

        foreach ($this->students as $studentIndex => $student) {
            $attemptCount = 0;

            foreach ($this->quizzes as $quizIndex => $quiz) {
                if ($attemptCount >= 15 && $studentIndex > 10) {
                    break;
                }
                if (random_int(0, 100) < 70) {
                    $totalQuestions = $quiz->questions()->count();
                    $score = (int) min($totalQuestions, max(0, $totalQuestions - random_int(0, 2)));
                    $answers = [];
                    foreach ($quiz->questions as $q) {
                        $correct = $q->options()->where('is_correct', true)->first();
                        $answers[] = [
                            'question_id' => $q->id,
                            'correct' => $correct && random_int(0, 100) < 80,
                        ];
                    }
                    $completedAt = $baseDate->copy()->addDays(random_int(0, 28))->addMinutes(random_int(1, 120));

                    QuizAttempt::query()->firstOrCreate(
                        [
                            'user_id' => $student->id,
                            'quiz_id' => $quiz->id,
                        ],
                        [
                            'score' => $score,
                            'total_questions' => $totalQuestions,
                            'answers' => $answers,
                            'completed_at' => $completedAt,
                        ]
                    );
                    $attemptCount++;
                }
            }

            foreach ($this->miniGames as $miniGameIndex => $miniGame) {
                if (random_int(0, 100) < 60) {
                    $completedAt = $baseDate->copy()->addDays(random_int(0, 28))->addMinutes(random_int(1, 90));
                    MiniGameAttempt::query()->firstOrCreate(
                        [
                            'user_id' => $student->id,
                            'mini_game_id' => $miniGame->id,
                        ],
                        [
                            'completed' => true,
                            'details' => ['progress' => 100],
                            'completed_at' => $completedAt,
                        ]
                    );
                }
            }

            $gamesToPlay = $platformGames->random(min(5, $platformGames->count()));
            foreach ($gamesToPlay as $platformGame) {
                if (random_int(0, 100) < 75) {
                    $completedAt = $baseDate->copy()->addDays(random_int(0, 28))->addMinutes(random_int(1, 60));
                    PlatformGameAttempt::query()->firstOrCreate(
                        [
                            'user_id' => $student->id,
                            'platform_game_id' => $platformGame->id,
                        ],
                        [
                            'completed' => true,
                            'details' => [],
                            'completed_at' => $completedAt,
                        ]
                    );
                }
            }

            $badgesToAward = $badges->random(min(3, $badges->count()));
            foreach ($badgesToAward as $badge) {
                $earnedAt = $baseDate->copy()->addDays(random_int(0, 25));
                $student->badges()->syncWithoutDetaching([
                    $badge->id => [
                        'earned_at' => $earnedAt,
                        'source_type' => 'seeder',
                        'source_id' => null,
                    ],
                ]);
            }
        }
    }
}
