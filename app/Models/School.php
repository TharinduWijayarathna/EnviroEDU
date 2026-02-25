<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'admin_id',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'school_id');
    }

    public function teachers(): HasMany
    {
        return $this->users()->where('role', \App\Enums\Role::Teacher);
    }

    public function students(): HasMany
    {
        return $this->users()->where('role', \App\Enums\Role::Student);
    }

    public function pendingTeachers(): HasMany
    {
        return $this->teachers()->where('is_approved', false);
    }

    public function pendingStudents(): HasMany
    {
        return $this->students()->where('is_approved', false);
    }
}
