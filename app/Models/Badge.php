<?php

namespace App\Models;

use App\Enums\BadgeAwardFor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'criteria_type',
        'criteria_config',
        'order',
        'topic_id',
        'award_for',
    ];

    protected function casts(): array
    {
        return [
            'criteria_config' => 'array',
            'award_for' => BadgeAwardFor::class,
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'badge_user')
            ->withPivot('earned_at', 'source_type', 'source_id')
            ->withTimestamps();
    }
}
