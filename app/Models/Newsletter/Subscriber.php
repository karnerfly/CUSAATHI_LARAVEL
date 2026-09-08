<?php

namespace App\Models\Newsletter;

use App\Mail\NewsletterVerifyEmailMail;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $email
 * @property bool $active
 * @property Carbon|null $verified_at
 * @property string|null $verification_token
 * @property string $unsubscribe_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'email', 'active', 'verified_at', 'verification_token', 'unsubscribe_token'])]
class Subscriber extends Model implements Auditable
{
    use AuditableTrait;

    protected $table = 'newsletter.subscribers';

    protected $casts = [
        'verified_at' => 'datetime',
        'active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($subscriber) {
            $subscriber->unsubscribe_token = Str::random(48);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(Log::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true)->whereNotNull('verified_at');
    }

    public function sendVerificationMail(string $token)
    {
        $verification_url = rtrim(config('app.client_url'), '/').'/newsletter/verify?token='.urlencode($token);
        Mail::to($this->email)->queue(new NewsletterVerifyEmailMail($verification_url));
    }
}
