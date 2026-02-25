<?php

namespace App\Enums;

enum BadgeAwardFor: string
{
    case Quiz = 'quiz';
    case Game = 'game';
    case Both = 'both';

    public function label(): string
    {
        return match ($this) {
            self::Quiz => 'Quizzes only',
            self::Game => 'Games only',
            self::Both => 'Quizzes or games',
        };
    }
}
