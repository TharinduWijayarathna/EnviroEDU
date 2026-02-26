<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PlatformGame extends Model
{
    protected $fillable = [
        'slug',
        'title',
        'description',
        'order',
        'config',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(PlatformGameAttempt::class, 'platform_game_id');
    }
}
