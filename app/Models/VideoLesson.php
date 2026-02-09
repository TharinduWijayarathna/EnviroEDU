<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VideoLesson extends Model
{
    protected $fillable = [
        'title',
        'description',
        'video_path',
        'grade_level',
        'key_points',
    ];

    protected function casts(): array
    {
        return [
            'key_points' => 'array',
        ];
    }

    public function lessonCompletions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }
}
