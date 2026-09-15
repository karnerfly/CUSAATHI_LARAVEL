<?php

namespace App\Models;

use App\Enums\UserAffiliation;
use App\Notifications\User\EmailVerificationNotification;
use App\Notifications\User\PasswordResetNotification;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Override;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string|null $profile_url
 * @property UserAffiliation|null $affiliation
 * @property string|null $password
 * @property string $provider
 * @property string|null $provider_id
 * @property string|null $remember_token
 * @property boolean $active
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
#[
    Fillable([
        'name',
        'email',
        'email_verified_at',
        'profile_url',
        'affiliation',
        'password',
        'provider',
        'provider_id',
        'active',
    ]),
]
#[Hidden(['password'])]
class User extends Authenticatable implements Auditable, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use AuditableTrait, HasFactory, Notifiable, SoftDeletes;

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
        ];
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class, 'user_id');
    }

    #[Override]
    public function sendPasswordResetNotification($token)
    {
        return $this->notify(new PasswordResetNotification($token));
    }

    #[Override]
    public function sendEmailVerificationNotification()
    {
        return $this->notify(new EmailVerificationNotification());
    }

    public function isCompleted(): bool
    {
        return $this->affiliation != null;
    }
}
