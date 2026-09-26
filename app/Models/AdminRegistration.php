<?php

namespace App\Models;

use App\Mail\AdminStartRegistrationMail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string $email
 * @property int $admin_registration_campaign_id
 * @property string $token
 * @property string $payload
 * @property mixed $details
 * @property int|null $expiration
 * @property Carbon|null $sent_at
 * @property Carbon|null $created_at
 */
#[Fillable(['email', 'admin_registration_campaign_id', 'token', 'payload', 'expiration', 'sent_at'])]
#[Hidden(['payload'])]
class AdminRegistration extends Model implements Auditable
{
    use AuditableTrait;

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

    public function getDetailsAttribute()
    {
        return unserialize(base64_decode($this->payload));
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdminRegistrationCampaign::class, 'admin_registration_campaign_id');
    }

    public function sendRegistrationMail(string $spa_url)
    {
        $registration_url =
            rtrim($spa_url, '/') .
            '/registration/complete?token=' .
            urlencode($this->token) .
            'cmpid=' .
            urlencode($this->admin_registration_campaign_id);
        Mail::to($this->email)->queue(new AdminStartRegistrationMail($registration_url));
    }
}
