<?php

namespace App\Models;

use App\Enums\NewsLetterFrequency;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $email
 * @property boolean $active
 * @property NewsLetterFrequency $frequency
 * @property Carbon|null $verified_at
 * @property string|null $verification_token
 * @property string $unsubscribe_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'email', 'frequency', 'verified_at', 'unsubscribe_token', 'is_active'])]
class NewsletterSubscriber extends Model
{
    protected $table = 'newsletter_subscribers';

    protected $casts = [
        'verified_at' => 'datetime',
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($subscriber) {
            $subscriber->unsubscribe_token = Str::random(40);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(NewsletterTopic::class, 'newsletter_subscriber_topic', 'subscriber_id', 'topic_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(NewsletterLog::class, 'subscriber_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true)->whereNotNull('verified_at');
    }
}
