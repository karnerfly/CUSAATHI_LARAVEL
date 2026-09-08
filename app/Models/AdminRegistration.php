<?php

namespace App\Models;

use App\Mail\AdminStartRegistrationMail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

/**
 * @property string $email
 * @property string $token
 * @property string $payload
 * @property mixed $details
 * @property int|null $expiration
 * @property Carbon|null $sent_at
 * @property Carbon|null $created_at
 */
#[Fillable(['email', 'token', 'payload', 'expiration', 'sent_at'])]
#[Hidden(['payload'])]
class AdminRegistration extends Model
{
    protected $keyType = 'string';
    protected $primaryKey = 'email';

    public $incrementing = false;

    const UPDATED_AT = null;

    protected static function booted(): void
    {
        static::creating(function ($registration) {
            $registration->payload = base64_encode(serialize($registration->payload));
        });
    }

    public function getDetails()
    {
        return unserialize(base64_decode($this->payload));
    }

    public function sendRegistrationMail(string $spa_url)
    {
        $registration_url = rtrim($spa_url, '/') . '/registration/complete?token=' . urlencode($this->token);
        Mail::to($this->email)->queue(new AdminStartRegistrationMail($registration_url));
    }
}
