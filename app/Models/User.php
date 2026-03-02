<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'school_id',
        'grade_level',
        'is_approved',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => Role::class,
            'grade_level' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    public function hasRole(Role|string $role): bool
    {
        $value = $role instanceof Role ? $role->value : $role;

        return $this->role?->value === $value;
    }

    public function children(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_parent', 'parent_id', 'student_id')
            ->withTimestamps();
    }

    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'student_parent', 'student_id', 'parent_id')
            ->withTimestamps();
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'badge_user')
            ->withPivot('earned_at', 'source_type', 'source_id')
            ->withTimestamps();
    }

    public function getBadgeEarnedAt($badge): ?Carbon
    {
        $earnedAt = $badge->pivot?->earned_at;
        if (! $earnedAt) {
            return null;
        }

        return $earnedAt instanceof Carbon ? $earnedAt : Carbon::parse($earnedAt);
    }

    public function quizAttempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function miniGameAttempts(): HasMany
    {
        return $this->hasMany(MiniGameAttempt::class);
    }

    public function platformGameAttempts(): HasMany
    {
        return $this->hasMany(PlatformGameAttempt::class);
    }

    public function teachingClasses(): HasMany
    {
        return $this->hasMany(ClassRoom::class, 'user_id');
    }

    public function enrolledClasses(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class, 'class_room_user', 'user_id', 'class_room_id')
            ->withTimestamps();
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function isApproved(): bool
    {
        if ($this->role === Role::SchoolAdmin || $this->role === Role::Parent) {
            return true;
        }

        return (bool) $this->is_approved;
    }
}
