<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformGameAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'platform_game_id',
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

    public function platformGame(): BelongsTo
    {
        return $this->belongsTo(PlatformGame::class, 'platform_game_id');
    }
}
