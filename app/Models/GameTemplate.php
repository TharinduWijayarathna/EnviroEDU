<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameTemplate extends Model
{
    /** @use HasFactory<\Database\Factories\GameTemplateFactory> */
    use HasFactory;

    protected $fillable = [
        'slug',
        'name',
        'description',
        'config_schema',
    ];

    protected function casts(): array
    {
        return [
            'config_schema' => 'array',
        ];
    }

    public function miniGames(): HasMany
    {
        return $this->hasMany(MiniGame::class);
    }
}
