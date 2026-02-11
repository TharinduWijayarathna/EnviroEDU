<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'grade_level',
        'video_url',
        'order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'grade_level' => 'integer',
            'order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->orderBy('title');
    }

    public function miniGames(): HasMany
    {
        return $this->hasMany(MiniGame::class)->with('gameTemplate')->orderBy('title');
    }
}
