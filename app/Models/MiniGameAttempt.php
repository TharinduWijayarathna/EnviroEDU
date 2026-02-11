<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiniGameAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'mini_game_id',
        'completed',
        'details',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
            'details' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function miniGame(): BelongsTo
    {
        return $this->belongsTo(MiniGame::class, 'mini_game_id');
    }
}
