<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MiniGame extends Model
{
    /** @use HasFactory<\Database\Factories\MiniGameFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'topic_id',
        'game_template_id',
        'title',
        'description',
        'prompt',
        'config',
        'grade_level',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function gameTemplate(): BelongsTo
    {
        return $this->belongsTo(GameTemplate::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(MiniGameAttempt::class, 'mini_game_id');
    }
}
