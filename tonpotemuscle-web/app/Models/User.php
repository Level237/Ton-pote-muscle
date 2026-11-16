<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'username',
    'email',
    'phone',
    'password',
    'role',
    'gender',
    'height_cm',
    'weight_kg',
    'activity_level',
    'fitness_level',
    'main_goal',
    'motivations',
    'trains_with_equipment',
    'equipment_notes',
    'hydration_goal_glasses',
    'deactivated_at',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'deactivated_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'motivations' => 'array',
            'weight_kg' => 'decimal:2',
            'hydration_goal_glasses' => 'integer',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isMember(): bool
    {
        return $this->role === UserRole::Member;
    }

    public function isDeactivated(): bool
    {
        return $this->deactivated_at !== null;
    }

    /**
     * @return HasMany<ProgramEnrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(ProgramEnrollment::class);
    }

    /**
     * @return HasMany<MemberPlanItem, $this>
     */
    public function planItems(): HasMany
    {
        return $this->hasMany(MemberPlanItem::class)->orderBy('planned_on');
    }

    /**
     * @return HasMany<MemberAssignment, $this>
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(MemberAssignment::class);
    }

    /**
     * @return HasMany<BilanSubmission, $this>
     */
    public function bilanSubmissions(): HasMany
    {
        return $this->hasMany(BilanSubmission::class);
    }

    /**
     * @return HasMany<WeightLog, $this>
     */
    public function weightLogs(): HasMany
    {
        return $this->hasMany(WeightLog::class)->orderByDesc('week_start');
    }

    /**
     * @return HasMany<HydrationLog, $this>
     */
    public function hydrationLogs(): HasMany
    {
        return $this->hasMany(HydrationLog::class)->orderByDesc('day');
    }

    /**
     * @return HasMany<MealLog, $this>
     */
    public function mealLogs(): HasMany
    {
        return $this->hasMany(MealLog::class)->orderByDesc('day');
    }

    /**
     * @return HasOne<MemberMealPlan, $this>
     */
    public function mealPlan(): HasOne
    {
        return $this->hasOne(MemberMealPlan::class);
    }

    /**
     * @return HasMany<CoachMessage, $this>
     */
    public function coachMessages(): HasMany
    {
        return $this->hasMany(CoachMessage::class, 'member_id');
    }

    /**
     * @return HasOne<CoachMessage, $this>
     */
    public function latestCoachMessage(): HasOne
    {
        return $this->hasOne(CoachMessage::class, 'member_id')->latestOfMany();
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeMembers(Builder $query): Builder
    {
        return $query->where('role', UserRole::Member);
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereNull('deactivated_at');
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeDeactivated(Builder $query): Builder
    {
        return $query->whereNotNull('deactivated_at');
    }
}
