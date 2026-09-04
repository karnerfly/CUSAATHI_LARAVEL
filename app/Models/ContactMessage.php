<?php

namespace App\Models;

use App\Enums\ContactMessageStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

/**
 * Contact message submitted through the public contact form.
 *
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $subject
 * @property string $message
 * @property ContactMessageStatus $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['user_id', 'name', 'email', 'phone', 'subject', 'message', 'status'])]
class ContactMessage extends Model implements Auditable
{
    use AuditableTrait;

    protected function casts(): array
    {
        return [
            'status' => ContactMessageStatus::class,
        ];
    }

    /**
     * Get the user who submitted the contact message.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
