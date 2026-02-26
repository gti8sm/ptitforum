<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Poll extends Model
{
    protected $fillable = [
        'thread_id',
        'question',
        'is_multiple_choice',
        'closes_at',
    ];

    protected function casts(): array
    {
        return [
            'is_multiple_choice' => 'boolean',
            'closes_at' => 'datetime',
        ];
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(PollOption::class)->orderBy('sort_order');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PollVote::class);
    }

    public function isClosed(): bool
    {
        if (! $this->closes_at) {
            return false;
        }

        return $this->closes_at->isPast();
    }
}
