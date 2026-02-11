<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
    ];

    protected function casts(): array
    {
        return [
            'criteria_config' => 'array',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'badge_user')
            ->withPivot('earned_at', 'source_type', 'source_id')
            ->withTimestamps();
    }
}
