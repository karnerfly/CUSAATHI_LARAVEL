<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $admin_id
 * @property bool $active
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 */
#[Fillable(['admin_id', 'active', 'expires_at'])]
class AdminRegistrationCampaign extends Model
{
    const UPDATED_AT = null;

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(AdminRegistration::class);
    }

    public function active(): bool
    {
        return $this->active && $this->expires_at && $this->expires_at->timestamp > now()->timestamp;
    }
}
