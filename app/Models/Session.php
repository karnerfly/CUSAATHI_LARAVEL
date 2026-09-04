<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * @property string $id
 * @property int|null $user_id
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string $payload
 * @property bool $revoked
 * @property int $last_activity
 */
#[Fillable('id', 'user_id', 'ip_address', 'user_agent', 'payload', 'last_activity', 'revoked')]
class Session extends Model implements Auditable
{
    use AuditableTrait;

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }
}
