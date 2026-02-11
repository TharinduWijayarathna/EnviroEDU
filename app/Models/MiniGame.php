<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MiniGame extends Model
{
    /** @use HasFactory<\Database\Factories\MiniGameFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'game_template_id',
        'title',
        'description',
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

    public function gameTemplate(): BelongsTo
    {
        return $this->belongsTo(GameTemplate::class);
    }
}
