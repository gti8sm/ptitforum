<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Group extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_private',
        'created_by',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function threads(): HasMany
    {
        return $this->hasMany(Thread::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(GroupEvent::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(GroupMembership::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_memberships')
            ->withPivot(['role', 'joined_at'])
            ->withTimestamps();
    }

    public function membershipForUser(User $user): HasOne
    {
        return $this->hasOne(GroupMembership::class)->where('user_id', $user->id);
    }

    public function isMember(User $user): bool
    {
        return $this->memberships()->where('user_id', $user->id)->exists();
    }

    public function isModerator(User $user): bool
    {
        return $this
            ->memberships()
            ->where('user_id', $user->id)
            ->whereIn('role', ['owner', 'moderator'])
            ->exists();
    }

    public function isOwner(User $user): bool
    {
        return $this
            ->memberships()
            ->where('user_id', $user->id)
            ->where('role', 'owner')
            ->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
